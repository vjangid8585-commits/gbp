<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locations - GBP Agency</title>
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
                <a class="nav-link" href="<?php echo base_url('dashboard'); ?>">Dashboard</a>
                <a class="nav-link active" href="<?php echo base_url('locations'); ?>">Locations</a>
                <a class="nav-link" href="<?php echo base_url('auth/logout'); ?>">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="page-title"><i class="bi bi-geo-alt me-2"></i>Locations</h1>
                <p class="page-subtitle">Manage all your Google Business Profile locations</p>
            </div>
            <?php if($this->session->userdata('role') === 'admin'): ?>
            <a href="<?php echo base_url('sync/locations'); ?>" class="btn btn-primary-glow">
                <i class="bi bi-arrow-repeat me-2"></i> Sync from Google
            </a>
            <?php endif; ?>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($locations)): ?>
            <div class="glass-card text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mt-3">No Locations Found</h4>
                <p class="text-secondary">Click "Sync from Google" to import your business locations.</p>
            </div>
        <?php else: ?>
            <div class="glass-card">
                <table class="table-glass">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($locations as $loc): ?>
                        <tr>
                            <td>
                                <strong><?php echo $loc->business_name; ?></strong>
                                <br><small style="color: var(--text-secondary);"><?php echo $loc->google_location_id; ?></small>
                            </td>
                            <td>
                                <span class="badge-glass badge-success">
                                    <i class="bi bi-check-circle me-1"></i> <?php echo ucfirst($loc->sync_status); ?>
                                </span>
                            </td>
                            <td><?php echo $loc->assignee_name ?? '<span style="color:var(--text-secondary)">Unassigned</span>'; ?></td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="<?php echo base_url('locations/edit/'.$loc->id); ?>" class="btn btn-glass btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="<?php echo base_url('insights/view/'.$loc->id); ?>" class="btn btn-glass btn-sm">
                                        <i class="bi bi-graph-up"></i> Insights
                                    </a>
                                    <a href="<?php echo base_url('reviews/index/'.$loc->id); ?>" class="btn btn-glass btn-sm">
                                        <i class="bi bi-star"></i> Reviews
                                    </a>
                                    <a href="<?php echo base_url('posts/index/'.$loc->id); ?>" class="btn btn-glass btn-sm">
                                        <i class="bi bi-postcard"></i> Posts
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
