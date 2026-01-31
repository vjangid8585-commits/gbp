<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GBP Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">GBP Agency</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?php echo $this->session->userdata('name'); ?> (<?php echo ucfirst($this->session->userdata('role')); ?>)
                </span>
                <a href="<?php echo site_url('auth/logout'); ?>" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Dashboard</h1>
        <p>Welcome to the agency dashboard.</p>

        <?php if($this->session->userdata('role') === 'admin'): ?>
            <div class="mb-4">
                <a href="<?php echo site_url('oauth/connect'); ?>" class="btn btn-warning">Connect/Refresh Google Agency Account</a>
                <a href="<?php echo site_url('users'); ?>" class="btn btn-secondary">Manage Users</a>
            </div>
        <?php endif; ?>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Locations</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $this->db->where('deleted_at', NULL)->count_all_results('locations'); ?> Managed</h5>
                        <p class="card-text">View and manage your GBP locations.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Reviews</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $this->db->count_all('reviews'); ?> Reviews</h5>
                        <p class="card-text">Check customer feedback.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Pending Replies</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $this->db->where('reply_text', NULL)->count_all_results('reviews'); ?> Pending</h5>
                        <p class="card-text">Respond to reviews.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
