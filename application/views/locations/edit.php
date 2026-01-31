<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Location</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">GBP Agency</a>
            <div class="d-flex">
                <a href="<?php echo site_url('locations'); ?>" class="btn btn-outline-light btn-sm">Back to List</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Edit Location: <?php echo $location->business_name; ?></h2>
        
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php echo form_open('locations/update/'.$location->id); ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Business Name (Read Only)</label>
                        <input type="text" class="form-control" value="<?php echo $location->business_name; ?>" disabled>
                        <small class="text-muted">Name editing is disabled per policy.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Website URL</label>
                        <input type="url" name="websiteUri" class="form-control" value="<?php echo isset($location->data['websiteUri']) ? $location->data['websiteUri'] : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Business Description</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo isset($location->data['profile']['description']) ? $location->data['profile']['description'] : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</body>
</html>
