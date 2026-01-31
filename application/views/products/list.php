<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products: <?php echo $location->business_name; ?> - GBP Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-glass navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url('dashboard'); ?>">
                <i class="bi bi-geo-alt-fill"></i> GBP Agency
            </a>
            <div class="navbar-nav ms-auto d-flex flex-row gap-2">
                <a class="nav-link" href="<?php echo base_url('locations/view/'.$location->id); ?>">
                    <i class="bi bi-arrow-left"></i> Back to Location
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="page-title"><i class="bi bi-bag me-2"></i>Products</h1>
                <p class="page-subtitle"><?php echo $location->business_name; ?></p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo base_url('products/sync/'.$location->id); ?>" class="btn btn-glass">
                    <i class="bi bi-arrow-repeat me-2"></i> Sync from Google
                </a>
                <a href="<?php echo base_url('products/create/'.$location->id); ?>" class="btn btn-primary-glow">
                    <i class="bi bi-plus-lg me-2"></i> Add Product
                </a>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('warning')): ?>
            <div class="alert-glass warning mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo $this->session->flashdata('warning'); ?>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert-glass error mb-4">
                <i class="bi bi-x-circle me-2"></i>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($products)): ?>
            <div class="glass-card text-center py-5">
                <i class="bi bi-bag" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mt-3">No Products Yet</h4>
                <p class="text-secondary">Add products to showcase on your Google Business Profile.</p>
                <a href="<?php echo base_url('products/create/'.$location->id); ?>" class="btn btn-primary-glow mt-3">
                    <i class="bi bi-plus-lg me-2"></i> Add Your First Product
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($products as $product): ?>
                <div class="col-md-4">
                    <div class="glass-card h-100">
                        <?php if($product->image_url): ?>
                            <img src="<?php echo $product->image_url; ?>" class="rounded mb-3" 
                                 style="width: 100%; height: 180px; object-fit: cover;" alt="<?php echo $product->name; ?>">
                        <?php else: ?>
                            <div class="rounded mb-3 d-flex align-items-center justify-content-center" 
                                 style="width: 100%; height: 180px; background: rgba(255,255,255,0.05);">
                                <i class="bi bi-image" style="font-size: 3rem; color: var(--text-secondary);"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 style="color: var(--text-primary); margin: 0;"><?php echo $product->name; ?></h5>
                            <span class="badge-glass <?php echo $product->sync_status == 'synced' ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo ucfirst($product->sync_status); ?>
                            </span>
                        </div>
                        
                        <?php if($product->category): ?>
                            <span class="badge-glass badge-primary mb-2"><?php echo $product->category; ?></span>
                        <?php endif; ?>
                        
                        <p class="mb-2" style="color: var(--text-secondary); font-size: 0.9rem;">
                            <?php echo $product->description ? substr($product->description, 0, 100) . (strlen($product->description) > 100 ? '...' : '') : 'No description'; ?>
                        </p>
                        
                        <?php if($product->price): ?>
                            <p class="mb-3" style="color: var(--accent-primary); font-weight: 600; font-size: 1.2rem;">
                                ₹<?php echo number_format($product->price, 2); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2 mt-auto">
                            <a href="<?php echo base_url('products/edit/'.$product->id); ?>" class="btn btn-glass btn-sm flex-grow-1">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <a href="<?php echo base_url('products/delete/'.$product->id); ?>" 
                               class="btn btn-glass btn-sm text-danger"
                               onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
