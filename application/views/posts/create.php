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
                    <?php if($this->session->flashdata('error')): ?>
                        <div class="alert-glass error mb-4">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <?php echo form_open_multipart('posts/store/'.$location->id); ?>
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Post Type</label>
                            <select name="topic_type" class="form-control form-control-glass" id="postType">
                                <option value="STANDARD">✨ What's New</option>
                                <option value="EVENT">📅 Event</option>
                                <option value="OFFER">🏷️ Offer</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Post Content</label>
                            <textarea name="content" class="form-control form-control-glass" rows="4" 
                                      placeholder="Share an update with your customers..." required></textarea>
                            <small style="color: var(--text-secondary);">Max 1500 characters</small>
                        </div>

                        <!-- Event Fields (hidden by default) -->
                        <div id="eventFields" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label" style="color: var(--text-secondary);">Event Start</label>
                                    <input type="datetime-local" name="event_start" class="form-control form-control-glass">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="color: var(--text-secondary);">Event End</label>
                                    <input type="datetime-local" name="event_end" class="form-control form-control-glass">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" style="color: var(--text-secondary);">Event Title</label>
                                <input type="text" name="event_title" class="form-control form-control-glass" 
                                       placeholder="Event name">
                            </div>
                        </div>

                        <!-- Offer Fields (hidden by default) -->
                        <div id="offerFields" style="display: none;">
                            <div class="mb-4">
                                <label class="form-label" style="color: var(--text-secondary);">Coupon Code (Optional)</label>
                                <input type="text" name="coupon_code" class="form-control form-control-glass" 
                                       placeholder="SAVE20">
                            </div>
                            <div class="mb-4">
                                <label class="form-label" style="color: var(--text-secondary);">Terms & Conditions</label>
                                <textarea name="offer_terms" class="form-control form-control-glass" rows="2" 
                                          placeholder="Offer terms..."></textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">
                                <i class="bi bi-image me-1"></i> Image (Optional)
                            </label>
                            <input type="file" name="image" class="form-control form-control-glass" accept="image/*">
                            <small style="color: var(--text-secondary);">JPG, PNG up to 5MB. Posts with images get 2x engagement!</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">
                                <i class="bi bi-link-45deg me-1"></i> Call to Action URL (Optional)
                            </label>
                            <input type="url" name="action_url" class="form-control form-control-glass" 
                                   placeholder="https://yourbusiness.com/offer">
                        </div>

                        <!-- Scheduling -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="schedulePost" name="schedule">
                                <label class="form-check-label" for="schedulePost" style="color: var(--text-secondary);">
                                    <i class="bi bi-clock me-1"></i> Schedule for later
                                </label>
                            </div>
                        </div>

                        <div id="scheduleFields" style="display: none;" class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Publish Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control form-control-glass">
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

    <script>
        // Show/hide fields based on post type
        document.getElementById('postType').addEventListener('change', function() {
            document.getElementById('eventFields').style.display = this.value === 'EVENT' ? 'block' : 'none';
            document.getElementById('offerFields').style.display = this.value === 'OFFER' ? 'block' : 'none';
        });

        // Show/hide schedule fields
        document.getElementById('schedulePost').addEventListener('change', function() {
            document.getElementById('scheduleFields').style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html>
