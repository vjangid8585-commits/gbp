<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GBP Agency Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card glass-card">
            <div class="logo">
                <i class="bi bi-geo-alt-fill"></i> GBP Agency
            </div>
            
            <h4 class="text-center mb-4" style="color: var(--text-secondary);">Sign in to your account</h4>
            
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert-glass error mb-4">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            
            <?php echo form_open('auth/login'); ?>
                <div class="mb-4">
                    <label class="form-label" style="color: var(--text-secondary);">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-glass" placeholder="admin@agency.com" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label" style="color: var(--text-secondary);">Password</label>
                    <input type="password" name="password" class="form-control form-control-glass" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-primary-glow w-100 py-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                </button>
            <?php echo form_close(); ?>
            
            <p class="text-center mt-4" style="color: var(--text-secondary); font-size: 0.9rem;">
                Internal Agency Portal • Authorized Users Only
            </p>
        </div>
    </div>
</body>
</html>
