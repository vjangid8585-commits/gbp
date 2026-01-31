<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews: <?php echo $location->business_name; ?> - GBP Agency</title>
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
                <h1 class="page-title"><i class="bi bi-star me-2"></i>Reviews</h1>
                <p class="page-subtitle"><?php echo $location->business_name; ?></p>
            </div>
            <a href="<?php echo base_url('sync/reviews/'.$location->id); ?>" class="btn btn-primary-glow">
                <i class="bi bi-arrow-repeat me-2"></i> Sync Reviews
            </a>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($reviews)): ?>
            <div class="glass-card text-center py-5">
                <i class="bi bi-chat-square" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mt-3">No Reviews Yet</h4>
                <p class="text-secondary">Click "Sync Reviews" to fetch reviews from Google.</p>
            </div>
        <?php else: ?>
            <?php foreach($reviews as $review): ?>
            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1"><?php echo $review->reviewer_name; ?></h5>
                        <div class="review-stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?php echo $i <= $review->rating ? '-fill' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <span class="badge-glass"><?php echo date('M d, Y', strtotime($review->created_at)); ?></span>
                </div>
                
                <p style="color: var(--text-primary); line-height: 1.7;"><?php echo $review->comment; ?></p>
                
                <?php if($review->reply_text): ?>
                    <div class="mt-3 p-3" style="background: rgba(16, 185, 129, 0.1); border-radius: 12px; border-left: 3px solid var(--success);">
                        <small style="color: var(--success);"><i class="bi bi-reply me-1"></i> Your Reply:</small>
                        <p class="mb-0 mt-1"><?php echo $review->reply_text; ?></p>
                    </div>
                <?php else: ?>
                    <form action="<?php echo base_url('reviews/reply/'.$review->id); ?>" method="POST" class="mt-3">
                        <div class="d-flex gap-2">
                            <input type="text" name="reply" class="form-control form-control-glass" 
                                   placeholder="Write your reply..." required>
                            <button type="submit" class="btn btn-success-glow">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
