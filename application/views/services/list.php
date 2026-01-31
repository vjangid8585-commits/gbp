<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services: <?php echo $location->business_name; ?> - GBP Agency</title>
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
                <h1 class="page-title"><i class="bi bi-wrench me-2"></i>Services</h1>
                <p class="page-subtitle"><?php echo $location->business_name; ?></p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo base_url('services/sync/'.$location->id); ?>" class="btn btn-glass">
                    <i class="bi bi-arrow-repeat me-2"></i> Sync from Google
                </a>
                <a href="<?php echo base_url('services/create/'.$location->id); ?>" class="btn btn-primary-glow">
                    <i class="bi bi-plus-lg me-2"></i> Add Service
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

        <?php if(empty($services)): ?>
            <div class="glass-card text-center py-5">
                <i class="bi bi-wrench" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mt-3">No Services Yet</h4>
                <p class="text-secondary">Add services to showcase what your business offers.</p>
                <a href="<?php echo base_url('services/create/'.$location->id); ?>" class="btn btn-primary-glow mt-3">
                    <i class="bi bi-plus-lg me-2"></i> Add Your First Service
                </a>
            </div>
        <?php else: ?>
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="table table-dark-custom" id="servicesTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><i class="bi bi-grip-vertical"></i></th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortableServices">
                            <?php foreach($services as $service): ?>
                            <tr data-id="<?php echo $service->id; ?>">
                                <td class="drag-handle" style="cursor: grab;">
                                    <i class="bi bi-grip-vertical" style="color: var(--text-secondary);"></i>
                                </td>
                                <td style="color: var(--text-primary); font-weight: 500;">
                                    <?php echo $service->name; ?>
                                </td>
                                <td style="color: var(--text-secondary); font-size: 0.9rem;">
                                    <?php echo $service->description ? substr($service->description, 0, 60) . (strlen($service->description) > 60 ? '...' : '') : '-'; ?>
                                </td>
                                <td>
                                    <?php if($service->price && $service->price > 0): ?>
                                        <span style="color: var(--accent-primary); font-weight: 600;">
                                            ₹<?php echo number_format($service->price, 2); ?>
                                        </span>
                                        <?php if($service->price_type && $service->price_type != 'fixed'): ?>
                                            <small style="color: var(--text-secondary);">/<?php echo $service->price_type; ?></small>
                                        <?php endif; ?>
                                    <?php elseif($service->price_type == 'free'): ?>
                                        <span class="badge-glass badge-success">Free</span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($service->category): ?>
                                        <span class="badge-glass badge-primary"><?php echo $service->category; ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-glass <?php echo $service->sync_status == 'synced' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo ucfirst($service->sync_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo base_url('services/edit/'.$service->id); ?>" 
                                           class="btn btn-glass btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?php echo base_url('services/delete/'.$service->id); ?>" 
                                           class="btn btn-glass btn-sm text-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this service?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <p class="text-center mt-3" style="color: var(--text-secondary); font-size: 0.9rem;">
                <i class="bi bi-info-circle me-1"></i> Drag and drop to reorder services
            </p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Initialize drag and drop sorting
        const el = document.getElementById('sortableServices');
        if (el) {
            const sortable = Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function(evt) {
                    const order = [];
                    document.querySelectorAll('#sortableServices tr').forEach(row => {
                        order.push(row.dataset.id);
                    });
                    
                    // Send order to server
                    fetch('<?php echo base_url('services/reorder/'.$location->id); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'order=' + JSON.stringify(order)
                    });
                }
            });
        }
    </script>
</body>
</html>
