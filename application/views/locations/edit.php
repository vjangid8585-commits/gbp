<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit: <?php echo $location->business_name; ?> - GBP Agency</title>
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
                <a class="nav-link" href="<?php echo base_url('locations'); ?>">
                    <i class="bi bi-arrow-left"></i> Back to Locations
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="mb-5">
            <h1 class="page-title"><i class="bi bi-pencil-square me-2"></i><?php echo $location->business_name; ?></h1>
            <p class="page-subtitle">Edit business profile information</p>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert-glass error mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card">
                    <h5 class="mb-4"><i class="bi bi-info-circle me-2"></i>Editable Fields</h5>
                    <form action="<?php echo base_url('locations/update/'.$location->id); ?>" method="POST">
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Website URL</label>
                            <input type="url" name="websiteUri" class="form-control form-control-glass" 
                                   value="<?php echo $location->data['websiteUri'] ?? ''; ?>" 
                                   placeholder="https://yourbusiness.com">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Business Description</label>
                            <textarea name="description" class="form-control form-control-glass" rows="4" 
                                      placeholder="Describe your business..."><?php echo $location->data['profile']['description'] ?? ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success-glow">
                            <i class="bi bi-check-lg me-2"></i> Save Changes
                        </button>
                        <a href="<?php echo base_url('locations'); ?>" class="btn btn-glass ms-2">Cancel</a>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="glass-card mb-4">
                    <h5 class="mb-3"><i class="bi bi-shield-lock me-2"></i>Read-Only Fields</h5>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">
                        The following cannot be edited via API:
                    </p>
                    <ul style="color: var(--text-secondary); font-size: 0.9rem;">
                        <li>Business Name</li>
                        <li>Primary Category</li>
                        <li>Address</li>
                        <li>Phone Number</li>
                    </ul>
                </div>
                <div class="glass-card">
                    <h5 class="mb-3"><i class="bi bi-lightning me-2"></i>Quick Links</h5>
                    <div class="d-grid gap-2">
                        <a href="<?php echo base_url('insights/view/'.$location->id); ?>" class="btn btn-glass">
                            <i class="bi bi-graph-up me-2"></i> View Insights
                        </a>
                        <a href="<?php echo base_url('reviews/index/'.$location->id); ?>" class="btn btn-glass">
                            <i class="bi bi-star me-2"></i> Manage Reviews
                        </a>
                        <a href="<?php echo base_url('posts/index/'.$location->id); ?>" class="btn btn-glass">
                            <i class="bi bi-postcard me-2"></i> Manage Posts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
