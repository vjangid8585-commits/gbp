<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Posts: <?php echo $location->business_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">GBP Agency</a>
            <div class="d-flex">
                 <a href="<?php echo site_url('locations'); ?>" class="btn btn-outline-light btn-sm">Back to List</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Posts: <?php echo $location->business_name; ?></h2>
            <div>
                 <?php if($this->session->userdata('role') === 'admin'): ?>
                    <a href="<?php echo site_url('sync/posts/'.$location->id); ?>" class="btn btn-secondary">Sync Posts</a>
                <?php endif; ?>
                <a href="<?php echo site_url('posts/create/'.$location->id); ?>" class="btn btn-primary">Create New Post</a>
            </div>
        </div>
        
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <div class="row">
            <?php foreach($posts as $post): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <?php if($post->media_url): ?>
                        <img src="<?php echo $post->media_url; ?>" class="card-img-top" alt="Post Image" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $post->topic_type; ?></h5>
                        <p class="card-text"><?php echo $post->content; ?></p>
                        <p class="card-text"><small class="text-muted"><?php echo $post->created_at; ?> (<?php echo $post->status; ?>)</small></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
