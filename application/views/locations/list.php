<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Locations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">GBP Agency</a>
             <div class="d-flex">
                <a href="<?php echo site_url('auth/logout'); ?>" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Managed Locations</h2>
            <?php if($this->session->userdata('role') === 'admin'): ?>
                <a href="<?php echo site_url('sync/locations'); ?>" class="btn btn-primary">Sync Locations</a>
            <?php endif; ?>
        </div>
        
        <table class="table table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Business Name</th>
                    <th>Address</th>
                    <th>Sync Status</th>
                    <th>Assignee</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($locations)): ?>
                    <tr><td colspan="5" class="text-center">No locations found. Ask Admin to Sync.</td></tr>
                <?php else: ?>
                    <?php foreach($locations as $loc): ?>
                    <tr>
                        <td><?php echo $loc->business_name; ?></td>
                        <td>
                            <?php 
                                $addr = json_decode($loc->address_json, true);
                                echo isset($addr['addressLines']) ? implode(', ', $addr['addressLines']) : 'N/A';
                            ?>
                        </td>
                        <td><?php echo $loc->sync_status; ?></td>
                        <td><?php echo $loc->assignee_name ? $loc->assignee_name : 'Unassigned'; ?></td>
                        <td>
                            <a href="<?php echo site_url('locations/edit/'.$loc->id); ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?php echo site_url('insights/view/'.$loc->id); ?>" class="btn btn-sm btn-info">Insights</a>
                            <a href="<?php echo site_url('reviews/index/'.$loc->id); ?>" class="btn btn-sm btn-warning">Reviews</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
