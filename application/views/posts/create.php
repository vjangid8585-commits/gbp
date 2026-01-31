<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Post: <?php echo $location->business_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">GBP Agency</a>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Create Post for <?php echo $location->business_name; ?></h2>
        
        <div class="card mt-3">
            <div class="card-body">
                <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                <?php echo form_open('posts/create/'.$location->id); ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Post Type</label>
                        <select name="topic_type" class="form-control">
                            <option value="STANDARD">Standard (What's New)</option>
                            <option value="EVENT">Event</option>
                            <option value="OFFER">Offer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image URL (Optional)</label>
                        <input type="url" name="media_url" class="form-control" placeholder="https://example.com/image.jpg">
                    </div>

                    <button type="submit" class="btn btn-success">Publish Post</button>
                    <a href="<?php echo site_url('posts/index/'.$location->id); ?>" class="btn btn-secondary">Cancel</a>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</body>
</html>
