<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $location->business_name; ?> - GBP Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <style>
        :root {
            --gradient-start: #0f172a;
            --gradient-end: #1e293b;
            --card-bg: rgba(30, 41, 59, 0.6);
            --card-border: rgba(71, 85, 105, 0.4);
            --accent-cyan: #22d3ee;
            --accent-emerald: #34d399;
            --accent-amber: #fbbf24;
            --accent-rose: #fb7185;
            --accent-violet: #a78bfa;
            --accent-sky: #38bdf8;
            --text-white: #f8fafc;
            --text-muted: #94a3b8;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(56, 189, 248, 0.15);
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(ellipse at top right, rgba(56, 189, 248, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .hero-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 8px;
        }
        
        .hero-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .hero-subtitle i {
            color: var(--accent-cyan);
        }
        
        .stat-pill {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(71, 85, 105, 0.5);
            border-radius: 16px;
            padding: 20px 24px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .stat-pill .number {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-sky) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }
        
        .stat-pill .label {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        
        @media (max-width: 1200px) {
            .action-grid { grid-template-columns: repeat(3, 1fr); }
        }
        
        @media (max-width: 768px) {
            .action-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        .action-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--card-accent);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .action-card:hover {
            transform: translateY(-6px);
            border-color: var(--card-accent);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .action-card:hover::before {
            transform: scaleX(1);
        }
        
        .action-card .icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.5rem;
        }
        
        .action-card.insights { --card-accent: var(--accent-cyan); }
        .action-card.insights .icon-wrap { background: rgba(34, 211, 238, 0.15); color: var(--accent-cyan); }
        
        .action-card.reviews { --card-accent: var(--accent-amber); }
        .action-card.reviews .icon-wrap { background: rgba(251, 191, 36, 0.15); color: var(--accent-amber); }
        
        .action-card.posts { --card-accent: var(--accent-emerald); }
        .action-card.posts .icon-wrap { background: rgba(52, 211, 153, 0.15); color: var(--accent-emerald); }
        
        .action-card.products { --card-accent: var(--accent-sky); }
        .action-card.products .icon-wrap { background: rgba(56, 189, 248, 0.15); color: var(--accent-sky); }
        
        .action-card.services { --card-accent: var(--accent-violet); }
        .action-card.services .icon-wrap { background: rgba(167, 139, 250, 0.15); color: var(--accent-violet); }
        
        .action-card.settings { --card-accent: var(--accent-rose); }
        .action-card.settings .icon-wrap { background: rgba(251, 113, 133, 0.15); color: var(--accent-rose); }
        
        .action-card .count {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-white);
            line-height: 1;
        }
        
        .action-card .title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-white);
            margin-top: 8px;
        }
        
        .action-card .subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .info-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 28px;
        }
        
        .info-card h5 {
            color: var(--text-white);
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-card h5 i {
            color: var(--accent-cyan);
        }
        
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid rgba(71, 85, 105, 0.3);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            width: 140px;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .info-value {
            flex: 1;
            color: var(--text-white);
            font-size: 0.9rem;
        }
        
        .info-value a {
            color: var(--accent-cyan);
            text-decoration: none;
        }
        
        .info-value a:hover {
            text-decoration: underline;
        }
        
        .btn-sync {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-sky));
            border: none;
            color: #0f172a;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-sync:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(34, 211, 238, 0.3);
            color: #0f172a;
        }
        
        .btn-edit {
            background: rgba(71, 85, 105, 0.4);
            border: 1px solid rgba(71, 85, 105, 0.6);
            color: var(--text-white);
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            background: rgba(71, 85, 105, 0.6);
            border-color: rgba(148, 163, 184, 0.5);
            color: var(--text-white);
        }
        
        .section-title {
            color: var(--text-white);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--accent-emerald);
        }
        
        .id-badge {
            background: rgba(71, 85, 105, 0.3);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-family: 'SF Mono', 'Fira Code', monospace;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-glass navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url('dashboard'); ?>">
                <i class="bi bi-geo-alt-fill"></i> GBP Agency
            </a>
            <div class="navbar-nav ms-auto d-flex flex-row gap-2">
                <a class="nav-link" href="<?php echo base_url('locations'); ?>">
                    <i class="bi bi-arrow-left"></i> All Locations
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('warning')): ?>
            <div class="alert-glass warning mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo $this->session->flashdata('warning'); ?>
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <div class="hero-section">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="hero-title"><?php echo $location->business_name; ?></h1>
                    <?php 
                    $address = json_decode($location->address_json, true);
                    if ($address): 
                    ?>
                        <p class="hero-subtitle mb-2">
                            <i class="bi bi-geo-alt-fill"></i>
                            <?php 
                            $addr_parts = [];
                            if (!empty($address['addressLines'])) $addr_parts[] = implode(', ', $address['addressLines']);
                            if (!empty($address['locality'])) $addr_parts[] = $address['locality'];
                            if (!empty($address['administrativeArea'])) $addr_parts[] = $address['administrativeArea'];
                            echo implode(', ', $addr_parts);
                            ?>
                        </p>
                    <?php endif; ?>
                    <span class="id-badge">
                        <i class="bi bi-hash"></i> <?php echo $location->google_location_id; ?>
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo base_url('locations/edit/'.$location->id); ?>" class="btn btn-edit">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <a href="<?php echo base_url('sync/location/'.$location->id); ?>" class="btn btn-sync">
                        <i class="bi bi-arrow-repeat me-1"></i> Sync All Data
                    </a>
                </div>
            </div>
            
            <!-- Stats Row -->
            <div class="row g-3 mt-4">
                <div class="col-6 col-md-3">
                    <div class="stat-pill">
                        <div class="number"><?php echo number_format($insights_summary->total_calls ?? 0); ?></div>
                        <div class="label"><i class="bi bi-telephone me-1"></i> Calls</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-pill">
                        <div class="number"><?php echo number_format($insights_summary->total_clicks ?? 0); ?></div>
                        <div class="label"><i class="bi bi-cursor me-1"></i> Clicks</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-pill">
                        <div class="number"><?php echo number_format($insights_summary->total_directions ?? 0); ?></div>
                        <div class="label"><i class="bi bi-signpost me-1"></i> Directions</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-pill">
                        <div class="number"><?php echo $reviews_count; ?></div>
                        <div class="label"><i class="bi bi-star me-1"></i> Reviews</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h4 class="section-title"><i class="bi bi-grid-3x3-gap"></i> Quick Actions</h4>
        <div class="action-grid">
            <a href="<?php echo base_url('insights/view/'.$location->id); ?>" class="action-card insights">
                <div class="icon-wrap"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="title">Insights</div>
                <div class="subtitle">Analytics & trends</div>
            </a>
            <a href="<?php echo base_url('reviews/index/'.$location->id); ?>" class="action-card reviews">
                <div class="icon-wrap"><i class="bi bi-star-fill"></i></div>
                <div class="count"><?php echo $reviews_count; ?></div>
                <div class="subtitle">Reviews</div>
            </a>
            <a href="<?php echo base_url('posts/index/'.$location->id); ?>" class="action-card posts">
                <div class="icon-wrap"><i class="bi bi-megaphone-fill"></i></div>
                <div class="count"><?php echo $posts_count; ?></div>
                <div class="subtitle">Posts</div>
            </a>
            <a href="<?php echo base_url('products/index/'.$location->id); ?>" class="action-card products">
                <div class="icon-wrap"><i class="bi bi-box-seam-fill"></i></div>
                <div class="count"><?php echo $products_count; ?></div>
                <div class="subtitle">Products</div>
            </a>
            <a href="<?php echo base_url('services/index/'.$location->id); ?>" class="action-card services">
                <div class="icon-wrap"><i class="bi bi-tools"></i></div>
                <div class="count"><?php echo $services_count; ?></div>
                <div class="subtitle">Services</div>
            </a>
            <a href="<?php echo base_url('locations/edit/'.$location->id); ?>" class="action-card settings">
                <div class="icon-wrap"><i class="bi bi-sliders"></i></div>
                <div class="title">Settings</div>
                <div class="subtitle">Edit profile</div>
            </a>
        </div>

        <!-- Business Info -->
        <div class="info-card">
            <h5><i class="bi bi-building"></i> Business Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">Website</span>
                        <span class="info-value">
                            <?php if(!empty($location->data['websiteUri'])): ?>
                                <a href="<?php echo $location->data['websiteUri']; ?>" target="_blank">
                                    <?php echo $location->data['websiteUri']; ?> <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-muted);">Not set</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone</span>
                        <span class="info-value">
                            <?php echo $location->data['phoneNumbers']['primaryPhone'] ?? '<span style="color: var(--text-muted);">Not set</span>'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="badge-glass badge-success">
                                <i class="bi bi-check-circle me-1"></i><?php echo ucfirst($location->sync_status); ?>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">Category</span>
                        <span class="info-value">
                            <?php echo $location->data['categories']['primaryCategory']['displayName'] ?? '<span style="color: var(--text-muted);">Not set</span>'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account ID</span>
                        <span class="info-value" style="font-family: 'SF Mono', monospace; font-size: 0.85rem;">
                            <?php echo $location->account_id; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Last Updated</span>
                        <span class="info-value">
                            <?php echo date('M d, Y \a\t h:i A', strtotime($location->updated_at)); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if(!empty($location->data['profile']['description'])): ?>
                <div class="mt-4 pt-3" style="border-top: 1px solid rgba(71, 85, 105, 0.3);">
                    <span class="info-label d-block mb-2">Description</span>
                    <p style="color: var(--text-white); line-height: 1.6; margin: 0;">
                        <?php echo nl2br($location->data['profile']['description']); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
