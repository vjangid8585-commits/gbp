<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - GBP Agency</title>
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
                <a class="nav-link" href="<?php echo base_url('dashboard'); ?>">Dashboard</a>
                <a class="nav-link active" href="<?php echo base_url('users'); ?>">Users</a>
                <a class="nav-link" href="<?php echo base_url('auth/logout'); ?>">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="page-title"><i class="bi bi-people me-2"></i>Team Members</h1>
                <p class="page-subtitle">Manage agency staff and their access</p>
            </div>
            <a href="<?php echo base_url('users/create'); ?>" class="btn btn-primary-glow">
                <i class="bi bi-person-plus me-2"></i> Add New User
            </a>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                    <?php echo strtoupper(substr($user->name, 0, 1)); ?>
                                </div>
                                <strong><?php echo $user->name; ?></strong>
                            </div>
                        </td>
                        <td><?php echo $user->email; ?></td>
                        <td>
                            <span class="badge-glass <?php echo $user->role === 'admin' ? 'badge-primary' : 'badge-success'; ?>">
                                <?php echo ucfirst($user->role); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-glass badge-success">Active</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
