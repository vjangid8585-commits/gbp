<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('google_api');
        $this->load->helper('form');
    }

    public function index($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        // Check authorization
        if ($this->session->userdata('role') === 'staff' && $location->internal_assignee_id != $this->session->userdata('user_id')) {
            show_error('Unauthorized', 403);
        }

        $data['location'] = $location;
        
        // Get products from local database
        $data['products'] = $this->db->where('location_id', $location_id)
                                      ->where('deleted_at', NULL)
                                      ->order_by('created_at', 'DESC')
                                      ->get('products')->result();

        $this->load->view('products/list', $data);
    }

    public function create($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $data['location'] = $location;
        $this->load->view('products/create', $data);
    }

    public function store($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $price = $this->input->post('price');
        $category = $this->input->post('category');

        if (empty($name)) {
            $this->session->set_flashdata('error', 'Product name is required!');
            redirect('products/create/'.$location_id);
            return;
        }

        // Handle image upload
        $image_url = null;
        if (!empty($_FILES['image']['name'])) {
            $upload_path = './uploads/products/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = time() . '_' . $_FILES['image']['name'];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('image')) {
                $upload_data = $this->upload->data();
                $image_url = base_url('uploads/products/' . $upload_data['file_name']);
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('products/create/'.$location_id);
                return;
            }
        }

        // Build product data for Google API
        $product_data = [
            'languageCode' => 'en-US',
            'productName' => $name,
            'productDescription' => $description,
            'price' => [
                'currencyCode' => 'INR',
                'units' => (int)$price
            ]
        ];

        if ($image_url) {
            $product_data['media'] = [
                ['mediaFormat' => 'PHOTO', 'sourceUrl' => $image_url]
            ];
        }

        // Save to local database
        $insert_data = [
            'location_id' => $location_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'image_url' => $image_url,
            'product_data_json' => json_encode($product_data),
            'sync_status' => 'pending'
        ];

        $this->db->insert('products', $insert_data);
        $product_id = $this->db->insert_id();

        // Try to sync with Google
        $location_name = $location->account_id . '/' . $location->google_location_id;
        $response = $this->google_api->update_product($location_name, $product_data);

        if ($response['code'] == 200) {
            $this->db->where('id', $product_id)->update('products', [
                'google_product_id' => $response['body']['name'] ?? null,
                'sync_status' => 'synced'
            ]);
            $this->session->set_flashdata('success', 'Product added and synced to Google!');
        } else {
            $error_msg = $response['body']['error']['message'] ?? ($response['error'] ?? 'Unknown error');
            $this->session->set_flashdata('warning', 'Product saved locally. Google API: ' . $error_msg);
        }

        redirect('products/index/'.$location_id);
    }

    public function edit($product_id) {
        $product = $this->db->get_where('products', ['id' => $product_id])->row();
        if (!$product) show_404();

        $location = $this->db->get_where('locations', ['id' => $product->location_id])->row();
        
        $data['product'] = $product;
        $data['location'] = $location;
        $this->load->view('products/edit', $data);
    }

    public function update($product_id) {
        $product = $this->db->get_where('products', ['id' => $product_id])->row();
        if (!$product) show_404();

        $location = $this->db->get_where('locations', ['id' => $product->location_id])->row();

        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $price = $this->input->post('price');
        $category = $this->input->post('category');

        // Handle image upload
        $image_url = $product->image_url;
        if (!empty($_FILES['image']['name'])) {
            $upload_path = './uploads/products/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 5120;
            $config['file_name'] = time() . '_' . $_FILES['image']['name'];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('image')) {
                $upload_data = $this->upload->data();
                $image_url = base_url('uploads/products/' . $upload_data['file_name']);
            }
        }

        // Update local database
        $this->db->where('id', $product_id)->update('products', [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'image_url' => $image_url,
            'sync_status' => 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Product updated successfully!');
        redirect('products/index/'.$product->location_id);
    }

    public function delete($product_id) {
        $product = $this->db->get_where('products', ['id' => $product_id])->row();
        if (!$product) show_404();

        $this->db->where('id', $product_id)->update('products', [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Product deleted successfully!');
        redirect('products/index/'.$product->location_id);
    }

    // Sync products from Google
    public function sync($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $location_name = $location->account_id . '/' . $location->google_location_id;
        $response = $this->google_api->get_products($location_name);

        if ($response['code'] == 200 && isset($response['body']['products'])) {
            foreach ($response['body']['products'] as $google_product) {
                // Check if product exists locally
                $existing = $this->db->get_where('products', [
                    'google_product_id' => $google_product['name']
                ])->row();

                $product_data = [
                    'location_id' => $location_id,
                    'google_product_id' => $google_product['name'],
                    'name' => $google_product['productName'] ?? '',
                    'description' => $google_product['productDescription'] ?? '',
                    'price' => $google_product['price']['units'] ?? 0,
                    'sync_status' => 'synced',
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($existing) {
                    $this->db->where('id', $existing->id)->update('products', $product_data);
                } else {
                    $this->db->insert('products', $product_data);
                }
            }
            $this->session->set_flashdata('success', 'Products synced from Google!');
        } else {
            $error_msg = $response['body']['error']['message'] ?? ($response['error'] ?? 'No products found');
            $this->session->set_flashdata('warning', 'Sync issue: ' . $error_msg);
        }

        redirect('products/index/'.$location_id);
    }
}
?>
