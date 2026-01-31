<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews: <?php echo $location->business_name; ?></title>
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
            <h2>Reviews: <?php echo $location->business_name; ?></h2>
             <?php if($this->session->userdata('role') === 'admin'): ?>
                <a href="<?php echo site_url('sync/reviews/'.$location->id); ?>" class="btn btn-success">Sync Reviews</a>
            <?php endif; ?>
        </div>
        
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <?php if(empty($reviews)): ?>
            <div class="alert alert-warning">No reviews found. Sync required.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach($reviews as $review): ?>
                <div class="list-group-item list-group-item-action" aria-current="true">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo $review->reviewer_name; ?> 
                            <small class="text-warning"><?php echo str_repeat('★', $review->rating); ?><?php echo str_repeat('☆', 5 - $review->rating); ?></small>
                        </h5>
                        <small><?php echo $review->created_at; ?></small>
                    </div>
                    <p class="mb-1"><?php echo $review->comment; ?></p>
                    
                    <?php if($review->reply_text): ?>
                        <div class="alert alert-secondary mt-2">
                            <strong>Your Reply:</strong> <?php echo $review->reply_text; ?>
                        </div>
                    <?php else: ?>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm<?php echo $review->id; ?>">
                                Reply
                            </button>
                            <div class="collapse mt-2" id="replyForm<?php echo $review->id; ?>">
                                <?php echo form_open('reviews/reply/'.$review->id); ?>
                                    <div class="input-group">
                                        <input type="text" name="reply_text" class="form-control" placeholder="Write a reply..." required>
                                        <button class="btn btn-primary" type="submit">Post</button>
                                    </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
