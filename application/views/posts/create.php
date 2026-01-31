<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - GBP Agency</title>
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
                <a class="nav-link" href="<?php echo base_url('posts/index/'.$location->id); ?>">
                    <i class="bi bi-arrow-left"></i> Back to Posts
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="mb-5 text-center">
                    <h1 class="page-title"><i class="bi bi-plus-circle me-2"></i>Create New Post</h1>
                    <p class="page-subtitle"><?php echo $location->business_name; ?></p>
                </div>

                <div class="glass-card">
                    <?php echo validation_errors('<div class="alert-glass error mb-4">', '</div>'); ?>
                    
                    <?php echo form_open('posts/create/'.$location->id); ?>
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Post Type</label>
                            <select name="topic_type" class="form-control form-control-glass">
                                <option value="STANDARD">✨ What's New</option>
                                <option value="EVENT">📅 Event</option>
                                <option value="OFFER">🏷️ Offer</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Content</label>
                            <textarea name="content" class="form-control form-control-glass" rows="5" 
                                      placeholder="Share an update with your customers..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">
                                <i class="bi bi-image me-1"></i> Image URL (Optional)
                            </label>
                            <input type="url" name="media_url" class="form-control form-control-glass" 
                                   placeholder="https://example.com/image.jpg">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-glow flex-grow-1">
                                <i class="bi bi-send me-2"></i> Publish Post
                            </button>
                            <a href="<?php echo base_url('posts/index/'.$location->id); ?>" class="btn btn-glass">Cancel</a>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
