<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service - <?php echo $location->business_name; ?> - GBP Agency</title>
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
                <a class="nav-link" href="<?php echo base_url('services/index/'.$location->id); ?>">
                    <i class="bi bi-arrow-left"></i> Back to Services
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="mb-5">
            <h1 class="page-title"><i class="bi bi-pencil me-2"></i>Edit Service</h1>
            <p class="page-subtitle"><?php echo $location->business_name; ?></p>
        </div>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert-glass error mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <?php echo form_open('services/update/'.$service->id); ?>
                <div class="mb-4">
                    <label class="form-label" style="color: var(--text-secondary);">Service Name *</label>
                    <input type="text" name="name" class="form-control form-control-glass" 
                           value="<?php echo $service->name; ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="color: var(--text-secondary);">Description</label>
                    <textarea name="description" class="form-control form-control-glass" rows="4"><?php echo $service->description; ?></textarea>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label" style="color: var(--text-secondary);">Price Type</label>
                        <select name="price_type" class="form-select form-control-glass" id="priceType">
                            <option value="fixed" <?php echo $service->price_type == 'fixed' ? 'selected' : ''; ?>>Fixed Price</option>
                            <option value="hourly" <?php echo $service->price_type == 'hourly' ? 'selected' : ''; ?>>Per Hour</option>
                            <option value="free" <?php echo $service->price_type == 'free' ? 'selected' : ''; ?>>Free</option>
                            <option value="varies" <?php echo $service->price_type == 'varies' ? 'selected' : ''; ?>>Price Varies</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="priceField">
                        <label class="form-label" style="color: var(--text-secondary);">Price (₹)</label>
                        <input type="number" name="price" class="form-control form-control-glass" 
                               step="0.01" min="0" value="<?php echo $service->price; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="color: var(--text-secondary);">Category</label>
                        <input type="text" name="category" class="form-control form-control-glass" 
                               value="<?php echo $service->category; ?>">
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary-glow">
                        <i class="bi bi-check-lg me-2"></i> Update Service
                    </button>
                    <a href="<?php echo base_url('services/index/'.$location->id); ?>" class="btn btn-glass">
                        Cancel
                    </a>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>

    <script>
        document.getElementById('priceType').addEventListener('change', function() {
            const priceField = document.getElementById('priceField');
            if (this.value === 'free' || this.value === 'varies') {
                priceField.style.opacity = '0.5';
                priceField.querySelector('input').disabled = true;
            } else {
                priceField.style.opacity = '1';
                priceField.querySelector('input').disabled = false;
            }
        });
        
        // Trigger on page load
        document.getElementById('priceType').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
