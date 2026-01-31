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

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert-glass error mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="bi bi-star-fill"></i></div>
                    <h2><?php echo number_format($avg_rating, 1); ?></h2>
                    <p>Average Rating</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="bi bi-chat-dots"></i></div>
                    <h2><?php echo count($reviews); ?></h2>
                    <p>Total Reviews</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="bi bi-reply"></i></div>
                    <h2><?php echo $pending_replies; ?></h2>
                    <p>Pending Replies</p>
                </div>
            </div>
        </div>

        <?php if(empty($reviews)): ?>
            <div class="glass-card text-center py-5">
                <i class="bi bi-star" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mt-3">No Reviews Yet</h4>
                <p class="text-secondary">Click "Sync Reviews" to fetch reviews from Google.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($reviews as $review): ?>
                    <div class="col-12">
                        <div class="glass-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar" style="width:50px;height:50px;background:var(--gradient-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:1.2rem;">
                                        <?php echo strtoupper(substr($review->reviewer_name, 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($review->reviewer_name); ?></h5>
                                        <small style="color: var(--text-secondary);">
                                            <?php echo date('M d, Y', strtotime($review->created_at)); ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="rating">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?php echo $i <= $review->rating ? '-fill' : ''; ?>" 
                                           style="color: <?php echo $i <= $review->rating ? '#fbbf24' : 'var(--text-secondary)'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <?php if($review->comment): ?>
                                <p class="mb-3" style="color: var(--text-primary);">
                                    "<?php echo htmlspecialchars($review->comment); ?>"
                                </p>
                            <?php endif; ?>

                            <?php if($review->reply_text): ?>
                                <div class="reply-box p-3 rounded mb-3" style="background: rgba(99, 102, 241, 0.1); border-left: 3px solid var(--primary);">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small style="color: var(--primary);"><i class="bi bi-reply-fill me-1"></i>Your Reply</small>
                                        <a href="<?php echo base_url('reviews/delete_reply/'.$review->id); ?>" 
                                           class="btn btn-sm btn-glass" onclick="return confirm('Delete this reply?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                    <p class="mb-0" style="color: var(--text-primary);">
                                        <?php echo htmlspecialchars($review->reply_text); ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <!-- Reply Form -->
                                <form action="<?php echo base_url('reviews/reply/'.$review->id); ?>" method="POST" class="mt-3">
                                    <div class="input-group">
                                        <input type="text" name="reply" class="form-control form-control-glass" 
                                               placeholder="Write a reply..." required>
                                        <button type="submit" class="btn btn-primary-glow">
                                            <i class="bi bi-send"></i> Reply
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
