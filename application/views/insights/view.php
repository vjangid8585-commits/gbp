<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insights: <?php echo $location->business_name; ?> - GBP Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h1 class="page-title"><i class="bi bi-graph-up me-2"></i>Performance Insights</h1>
                <p class="page-subtitle"><?php echo $location->business_name; ?> • Last 30 Days</p>
            </div>
            <a href="<?php echo base_url('sync/insights/'.$location->id); ?>" class="btn btn-success-glow">
                <i class="bi bi-arrow-repeat me-2"></i> Sync Latest Data
            </a>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert-glass success mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Summary -->
        <div class="row g-4 mb-5">
            <?php 
            $total_calls = array_sum(array_column($insights, 'calls'));
            $total_clicks = array_sum(array_column($insights, 'website_clicks'));
            $total_directions = array_sum(array_column($insights, 'direction_requests'));
            ?>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="bi bi-telephone"></i></div>
                    <h2><?php echo number_format($total_calls); ?></h2>
                    <p>Total Calls</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="bi bi-cursor-fill"></i></div>
                    <h2><?php echo number_format($total_clicks); ?></h2>
                    <p>Website Clicks</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="bi bi-signpost-2"></i></div>
                    <h2><?php echo number_format($total_directions); ?></h2>
                    <p>Direction Requests</p>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="chart-container">
            <h5 class="mb-4"><i class="bi bi-bar-chart me-2"></i>Daily Performance Trend</h5>
            <canvas id="insightsChart" height="100"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('insightsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($insights, 'date')); ?>,
                datasets: [
                    {
                        label: 'Calls',
                        data: <?php echo json_encode(array_column($insights, 'calls')); ?>,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Website Clicks',
                        data: <?php echo json_encode(array_column($insights, 'website_clicks')); ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Direction Requests',
                        data: <?php echo json_encode(array_column($insights, 'direction_requests')); ?>,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#94a3b8' }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#94a3b8' }
                    }
                }
            }
        });
    </script>
</body>
</html>
