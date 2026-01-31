<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GBP Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-glass navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url('dashboard'); ?>">
                <i class="bi bi-geo-alt-fill"></i> GBP Agency
            </a>
            <div class="navbar-nav ms-auto d-flex flex-row gap-2">
                <a class="nav-link" href="<?php echo base_url('dashboard'); ?>">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <a class="nav-link" href="<?php echo base_url('locations'); ?>">
                    <i class="bi bi-geo me-1"></i> Locations
                </a>
                <?php if($this->session->userdata('role') === 'admin'): ?>
                <a class="nav-link" href="<?php echo base_url('users'); ?>">
                    <i class="bi bi-people me-1"></i> Users
                </a>
                <?php endif; ?>
                <a class="nav-link" href="<?php echo base_url('auth/logout'); ?>">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <!-- Welcome Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="page-title">Welcome back, <?php echo $this->session->userdata('name'); ?> 👋</h1>
                <p class="page-subtitle">Here's what's happening with your business profiles today.</p>
            </div>
            <?php if($this->session->userdata('role') === 'admin'): ?>
            <div class="d-flex gap-2">
                <a href="<?php echo base_url('sync/locations'); ?>" class="btn btn-primary-glow">
                    <i class="bi bi-arrow-repeat me-2"></i> Sync Locations
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h2><?php echo $this->db->where('deleted_at', NULL)->count_all_results('locations'); ?></h2>
                    <p>Managed Locations</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <div class="stat-icon success">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h2><?php echo $this->db->count_all('reviews'); ?></h2>
                    <p>Total Reviews</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <div class="stat-icon warning">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                    <h2><?php echo $this->db->where('reply_text', NULL)->count_all_results('reviews'); ?></h2>
                    <p>Pending Replies</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-card">
            <h4 class="mb-4"><i class="bi bi-lightning-fill text-warning me-2"></i>Quick Actions</h4>
            <div class="d-flex flex-wrap gap-3">
                <a href="<?php echo base_url('locations'); ?>" class="btn btn-glass">
                    <i class="bi bi-list-ul me-2"></i> View All Locations
                </a>
                <?php if($this->session->userdata('role') === 'admin'): ?>
                <a href="<?php echo base_url('oauth/connect'); ?>" class="btn btn-warning-glow">
                    <i class="bi bi-google me-2"></i> Connect Google Account
                </a>
                <a href="<?php echo base_url('users/create'); ?>" class="btn btn-glass">
                    <i class="bi bi-person-plus me-2"></i> Add New User
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
