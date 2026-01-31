<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts: <?php echo $location->business_name; ?> - GBP Agency</title>
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
                <a class="nav-link" href="<?php echo base_url('locations'); ?>">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="page-title"><i class="bi bi-postcard me-2"></i>Posts</h1>
                <p class="page-subtitle"><?php echo $location->business_name; ?></p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo base_url('sync/posts/'.$location->id); ?>" class="btn btn-glass">
                    <i class="bi bi-arrow-repeat me-2"></i> Sync Posts
                </a>
                <a href="<?php echo base_url('posts/create/'.$location->id); ?>" class="btn btn-primary-glow">
                    <i class="bi bi-plus-lg me-2"></i> Create Post
                </a>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($posts)): ?>
            <div class="glass-card text-center py-5">
                <i class="bi bi-file-earmark-post" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mt-3">No Posts Yet</h4>
                <p class="text-secondary">Create your first post to engage with customers.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($posts as $post): ?>
                <div class="col-md-4">
                    <div class="glass-card h-100">
                        <?php if($post->media_url): ?>
                            <img src="<?php echo $post->media_url; ?>" class="rounded mb-3" 
                                 style="width: 100%; height: 180px; object-fit: cover;" alt="Post Image">
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge-glass badge-primary"><?php echo $post->topic_type; ?></span>
                            <span class="badge-glass badge-success"><?php echo $post->status; ?></span>
                        </div>
                        <p class="mb-3" style="color: var(--text-primary);"><?php echo $post->content; ?></p>
                        <small style="color: var(--text-secondary);">
                            <i class="bi bi-calendar me-1"></i>
                            <?php echo date('M d, Y', strtotime($post->created_at)); ?>
                        </small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
