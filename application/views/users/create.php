<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - GBP Agency</title>
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
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo base_url('users'); ?>">
                    <i class="bi bi-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="mb-5 text-center">
                    <h1 class="page-title"><i class="bi bi-person-plus me-2"></i>Add Team Member</h1>
                    <p class="page-subtitle">Create a new user account</p>
                </div>

                <div class="glass-card">
                    <?php echo validation_errors('<div class="alert-glass error mb-4">', '</div>'); ?>
                    
                    <?php echo form_open('users/create'); ?>
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-glass" 
                                   placeholder="John Doe" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-glass" 
                                   placeholder="john@agency.com" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Password</label>
                            <input type="password" name="password" class="form-control form-control-glass" 
                                   placeholder="••••••••" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Role</label>
                            <select name="role" class="form-control form-control-glass">
                                <option value="staff">👤 Staff</option>
                                <option value="manager">👔 Manager</option>
                                <option value="admin">🛡️ Admin</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-glow flex-grow-1">
                                <i class="bi bi-person-check me-2"></i> Create User
                            </button>
                            <a href="<?php echo base_url('users'); ?>" class="btn btn-glass">Cancel</a>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
