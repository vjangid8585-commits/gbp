<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - <?php echo $location->business_name; ?> - GBP Agency</title>
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
                <a class="nav-link" href="<?php echo base_url('products/index/'.$location->id); ?>">
                    <i class="bi bi-arrow-left"></i> Back to Products
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="mb-5">
            <h1 class="page-title"><i class="bi bi-pencil me-2"></i>Edit Product</h1>
            <p class="page-subtitle"><?php echo $location->business_name; ?></p>
        </div>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert-glass error mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <?php echo form_open_multipart('products/update/'.$product->id); ?>
                <div class="mb-4">
                    <label class="form-label" style="color: var(--text-secondary);">Product Name *</label>
                    <input type="text" name="name" class="form-control form-control-glass" 
                           value="<?php echo $product->name; ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="color: var(--text-secondary);">Description</label>
                    <textarea name="description" class="form-control form-control-glass" rows="4"><?php echo $product->description; ?></textarea>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label" style="color: var(--text-secondary);">Price (₹)</label>
                        <input type="number" name="price" class="form-control form-control-glass" 
                               step="0.01" min="0" value="<?php echo $product->price; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="color: var(--text-secondary);">Category</label>
                        <input type="text" name="category" class="form-control form-control-glass" 
                               value="<?php echo $product->category; ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="color: var(--text-secondary);">Product Image</label>
                    <?php if($product->image_url): ?>
                        <div class="mb-3">
                            <img src="<?php echo $product->image_url; ?>" class="rounded" 
                                 style="max-width: 300px; max-height: 200px;" id="currentImage">
                            <p class="mt-2" style="color: var(--text-secondary); font-size: 0.9rem;">
                                Current image. Upload a new one to replace.
                            </p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control form-control-glass" 
                           accept="image/*" id="imageInput">
                    <small style="color: var(--text-secondary);">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                    <div id="imagePreview" class="mt-3" style="display: none;">
                        <img id="preview" class="rounded" style="max-width: 300px; max-height: 200px;">
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary-glow">
                        <i class="bi bi-check-lg me-2"></i> Update Product
                    </button>
                    <a href="<?php echo base_url('products/index/'.$location->id); ?>" class="btn btn-glass">
                        Cancel
                    </a>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>

    <script>
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                    const currentImage = document.getElementById('currentImage');
                    if (currentImage) currentImage.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
