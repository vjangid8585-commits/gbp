<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends MY_Controller {

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
        
        // Get services from local database
        $data['services'] = $this->db->where('location_id', $location_id)
                                      ->where('deleted_at', NULL)
                                      ->order_by('display_order', 'ASC')
                                      ->get('services')->result();

        $this->load->view('services/list', $data);
    }

    public function create($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $data['location'] = $location;
        $this->load->view('services/create', $data);
    }

    public function store($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $price = $this->input->post('price');
        $price_type = $this->input->post('price_type'); // fixed, hourly, free, varies
        $category = $this->input->post('category');

        if (empty($name)) {
            $this->session->set_flashdata('error', 'Service name is required!');
            redirect('services/create/'.$location_id);
            return;
        }

        // Get max display order
        $max_order = $this->db->select_max('display_order')
                              ->where('location_id', $location_id)
                              ->get('services')->row()->display_order;
        $display_order = ($max_order ?? 0) + 1;

        // Build service data for Google API
        $service_data = [
            'displayName' => $name,
            'description' => $description,
            'price' => [
                'currencyCode' => 'INR',
                'units' => (int)$price
            ]
        ];

        // Save to local database
        $insert_data = [
            'location_id' => $location_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'price_type' => $price_type,
            'category' => $category,
            'display_order' => $display_order,
            'service_data_json' => json_encode($service_data),
            'sync_status' => 'pending'
        ];

        $this->db->insert('services', $insert_data);
        $service_id = $this->db->insert_id();

        // Sync all services to Google
        $this->_sync_services_to_google($location);

        $this->session->set_flashdata('success', 'Service added successfully!');
        redirect('services/index/'.$location_id);
    }

    public function edit($service_id) {
        $service = $this->db->get_where('services', ['id' => $service_id])->row();
        if (!$service) show_404();

        $location = $this->db->get_where('locations', ['id' => $service->location_id])->row();
        
        $data['service'] = $service;
        $data['location'] = $location;
        $this->load->view('services/edit', $data);
    }

    public function update($service_id) {
        $service = $this->db->get_where('services', ['id' => $service_id])->row();
        if (!$service) show_404();

        $location = $this->db->get_where('locations', ['id' => $service->location_id])->row();

        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $price = $this->input->post('price');
        $price_type = $this->input->post('price_type');
        $category = $this->input->post('category');

        // Update local database
        $this->db->where('id', $service_id)->update('services', [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'price_type' => $price_type,
            'category' => $category,
            'sync_status' => 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Sync all services to Google
        $this->_sync_services_to_google($location);

        $this->session->set_flashdata('success', 'Service updated successfully!');
        redirect('services/index/'.$service->location_id);
    }

    public function delete($service_id) {
        $service = $this->db->get_where('services', ['id' => $service_id])->row();
        if (!$service) show_404();

        $location = $this->db->get_where('locations', ['id' => $service->location_id])->row();

        $this->db->where('id', $service_id)->update('services', [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        // Sync all services to Google (this will remove the deleted service)
        $this->_sync_services_to_google($location);

        $this->session->set_flashdata('success', 'Service deleted successfully!');
        redirect('services/index/'.$service->location_id);
    }

    // Reorder services
    public function reorder($location_id) {
        $order = $this->input->post('order');
        if (!$order) {
            echo json_encode(['success' => false]);
            return;
        }

        foreach ($order as $position => $service_id) {
            $this->db->where('id', $service_id)->update('services', [
                'display_order' => $position + 1
            ]);
        }

        echo json_encode(['success' => true]);
    }

    // Sync services from Google
    public function sync($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $location_name = $location->account_id . '/' . $location->google_location_id;
        $response = $this->google_api->get_services($location_name);

        if ($response['code'] == 200 && isset($response['body']['serviceItems'])) {
            foreach ($response['body']['serviceItems'] as $index => $google_service) {
                // Check if service exists locally
                $existing = $this->db->get_where('services', [
                    'location_id' => $location_id,
                    'name' => $google_service['displayName'] ?? ''
                ])->row();

                $service_data = [
                    'location_id' => $location_id,
                    'name' => $google_service['displayName'] ?? '',
                    'description' => $google_service['description'] ?? '',
                    'price' => $google_service['price']['units'] ?? 0,
                    'display_order' => $index + 1,
                    'sync_status' => 'synced',
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($existing) {
                    $this->db->where('id', $existing->id)->update('services', $service_data);
                } else {
                    $this->db->insert('services', $service_data);
                }
            }
            $this->session->set_flashdata('success', 'Services synced from Google!');
        } else {
            $error_msg = $response['body']['error']['message'] ?? ($response['error'] ?? 'No services found');
            $this->session->set_flashdata('warning', 'Sync issue: ' . $error_msg);
        }

        redirect('services/index/'.$location_id);
    }

    // Private helper to sync services to Google
    private function _sync_services_to_google($location) {
        $services = $this->db->where('location_id', $location->id)
                              ->where('deleted_at', NULL)
                              ->order_by('display_order', 'ASC')
                              ->get('services')->result();

        $service_items = [];
        foreach ($services as $service) {
            $item = [
                'displayName' => $service->name
            ];
            if (!empty($service->description)) {
                $item['description'] = $service->description;
            }
            if (!empty($service->price) && $service->price > 0) {
                $item['price'] = [
                    'currencyCode' => 'INR',
                    'units' => (int)$service->price
                ];
            }
            $service_items[] = $item;
        }

        $location_name = $location->account_id . '/' . $location->google_location_id;
        $response = $this->google_api->update_services($location_name, [
            'serviceItems' => $service_items
        ]);

        if ($response['code'] == 200) {
            // Mark all services as synced
            $this->db->where('location_id', $location->id)
                     ->where('deleted_at', NULL)
                     ->update('services', ['sync_status' => 'synced']);
            return true;
        }

        return false;
    }
}
?>
