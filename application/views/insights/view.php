<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insights: <?php echo $location->business_name; ?> - GBP Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .date-range-picker {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .date-preset-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .date-preset-btn:hover, .date-preset-btn.active {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: white;
        }
        .date-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .date-input {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: var(--text-primary) !important;
            padding: 6px 12px;
            border-radius: 8px;
            width: 130px;
            font-size: 0.9rem;
        }
        .comparison-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 6px;
            margin-top: 8px;
        }
        .comparison-badge.up {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        .comparison-badge.down {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        .comparison-badge.neutral {
            background: rgba(148, 163, 184, 0.2);
            color: #94a3b8;
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
                <a class="nav-link" href="<?php echo base_url('locations/view/'.$location->id); ?>">
                    <i class="bi bi-arrow-left"></i> Back to Location
                </a>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="page-title"><i class="bi bi-graph-up me-2"></i>Performance Insights</h1>
                <p class="page-subtitle"><?php echo $location->business_name; ?></p>
            </div>
            <div class="dropdown">
                <button class="btn btn-success-glow dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-arrow-repeat me-2"></i> Sync from Google
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo base_url('sync/insights/'.$location->id.'?days=90'); ?>">
                        <i class="bi bi-calendar3 me-2"></i> Last 90 Days
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo base_url('sync/insights/'.$location->id.'?days=180'); ?>">
                        <i class="bi bi-calendar3 me-2"></i> Last 180 Days
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo base_url('sync/insights/'.$location->id.'?days=365'); ?>">
                        <i class="bi bi-calendar3 me-2"></i> Last 365 Days
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo base_url('sync/insights_historical/'.$location->id); ?>">
                        <i class="bi bi-clock-history me-2"></i> Full History (18 months)
                    </a></li>
                </ul>
            </div>
        </div>

        <!-- Date Range Picker -->
        <div class="glass-card mb-4">
            <form method="get" action="<?php echo base_url('insights/view/'.$location->id); ?>" id="dateFilterForm">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span style="color: var(--text-secondary);"><i class="bi bi-calendar3 me-1"></i> Period:</span>
                        <button type="button" class="date-preset-btn" data-days="7">Last 7 Days</button>
                        <button type="button" class="date-preset-btn" data-days="30">Last 30 Days</button>
                        <button type="button" class="date-preset-btn" data-days="90">Last 90 Days</button>
                        <button type="button" class="date-preset-btn" data-days="custom">Custom</button>
                    </div>
                    <div class="date-input-group" id="customDateRange">
                        <input type="text" name="start_date" id="startDate" class="date-input" 
                               value="<?php echo $start_date; ?>" placeholder="Start Date">
                        <span style="color: var(--text-secondary);">to</span>
                        <input type="text" name="end_date" id="endDate" class="date-input" 
                               value="<?php echo $end_date; ?>" placeholder="End Date">
                        <button type="submit" class="btn btn-primary-glow btn-sm">
                            <i class="bi bi-search"></i> Apply
                        </button>
                    </div>
                </div>
            </form>
            <div class="mt-2" style="color: var(--text-secondary); font-size: 0.85rem;">
                <i class="bi bi-info-circle me-1"></i>
                Data available: <?php echo date('M d, Y', strtotime($min_available_date)); ?> - <?php echo date('M d, Y', strtotime($max_available_date)); ?>
            </div>
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

        <?php if(empty($insights)): ?>
            <div class="glass-card text-center py-5">
                <i class="bi bi-graph-down" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mt-3">No Insights Data</h4>
                <p class="text-secondary">No data found for the selected date range. Try a different period or sync from Google.</p>
            </div>
        <?php else: ?>
            <?php 
            // Convert objects to arrays for array_column
            $insights_array = array_map(function($obj) { return (array) $obj; }, $insights);
            $total_calls = array_sum(array_column($insights_array, 'calls'));
            $total_clicks = array_sum(array_column($insights_array, 'website_clicks'));
            $total_directions = array_sum(array_column($insights_array, 'direction_requests'));
            
            // Calculate comparison percentages
            $prev_calls = $prev_period->calls ?? 0;
            $prev_clicks = $prev_period->website_clicks ?? 0;
            $prev_directions = $prev_period->direction_requests ?? 0;
            
            $calls_change = $prev_calls > 0 ? round((($total_calls - $prev_calls) / $prev_calls) * 100, 1) : 0;
            $clicks_change = $prev_clicks > 0 ? round((($total_clicks - $prev_clicks) / $prev_clicks) * 100, 1) : 0;
            $directions_change = $prev_directions > 0 ? round((($total_directions - $prev_directions) / $prev_directions) * 100, 1) : 0;
            ?>
            
            <!-- Date Range Display -->
            <div class="mb-4" style="color: var(--text-secondary);">
                <i class="bi bi-calendar-range me-1"></i>
                Showing data from <strong style="color: var(--text-primary);"><?php echo date('M d, Y', strtotime($start_date)); ?></strong> 
                to <strong style="color: var(--text-primary);"><?php echo date('M d, Y', strtotime($end_date)); ?></strong>
                (<?php echo count($insights); ?> days)
            </div>
            
            <!-- Stats Summary -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon primary"><i class="bi bi-telephone"></i></div>
                        <h2><?php echo number_format($total_calls); ?></h2>
                        <p>Total Calls</p>
                        <?php if($prev_calls > 0): ?>
                            <div class="comparison-badge <?php echo $calls_change >= 0 ? 'up' : 'down'; ?>">
                                <i class="bi bi-arrow-<?php echo $calls_change >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo abs($calls_change); ?>% vs previous period
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card success">
                        <div class="stat-icon success"><i class="bi bi-cursor-fill"></i></div>
                        <h2><?php echo number_format($total_clicks); ?></h2>
                        <p>Website Clicks</p>
                        <?php if($prev_clicks > 0): ?>
                            <div class="comparison-badge <?php echo $clicks_change >= 0 ? 'up' : 'down'; ?>">
                                <i class="bi bi-arrow-<?php echo $clicks_change >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo abs($clicks_change); ?>% vs previous period
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card warning">
                        <div class="stat-icon warning"><i class="bi bi-signpost-2"></i></div>
                        <h2><?php echo number_format($total_directions); ?></h2>
                        <p>Direction Requests</p>
                        <?php if($prev_directions > 0): ?>
                            <div class="comparison-badge <?php echo $directions_change >= 0 ? 'up' : 'down'; ?>">
                                <i class="bi bi-arrow-<?php echo $directions_change >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo abs($directions_change); ?>% vs previous period
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Chart -->
            <div class="chart-container">
                <h5 class="mb-4"><i class="bi bi-bar-chart me-2"></i>Daily Performance Trend</h5>
                <canvas id="insightsChart" height="100"></canvas>
            </div>

            <script>
                const ctx = document.getElementById('insightsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode(array_column($insights_array, 'date')); ?>,
                        datasets: [
                            {
                                label: 'Calls',
                                data: <?php echo json_encode(array_map('intval', array_column($insights_array, 'calls'))); ?>,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Website Clicks',
                                data: <?php echo json_encode(array_map('intval', array_column($insights_array, 'website_clicks'))); ?>,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Direction Requests',
                                data: <?php echo json_encode(array_map('intval', array_column($insights_array, 'direction_requests'))); ?>,
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
        <?php endif; ?>
    </div>

    <!-- Flatpickr for date picking -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize date pickers
        flatpickr("#startDate", {
            dateFormat: "Y-m-d",
            maxDate: "<?php echo $max_available_date; ?>",
            theme: "dark"
        });
        
        flatpickr("#endDate", {
            dateFormat: "Y-m-d",
            maxDate: "<?php echo $max_available_date; ?>",
            theme: "dark"
        });
        
        // Handle preset buttons
        document.querySelectorAll('.date-preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const days = this.dataset.days;
                
                // Remove active class from all buttons
                document.querySelectorAll('.date-preset-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                if (days === 'custom') {
                    document.getElementById('customDateRange').style.display = 'flex';
                    return;
                }
                
                // Calculate dates
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(startDate.getDate() - parseInt(days));
                
                // Format dates as YYYY-MM-DD
                const formatDate = (date) => date.toISOString().split('T')[0];
                
                // Set values and submit
                document.getElementById('startDate').value = formatDate(startDate);
                document.getElementById('endDate').value = formatDate(endDate);
                document.getElementById('dateFilterForm').submit();
            });
        });
        
        // Highlight active preset based on current selection
        const currentStart = new Date('<?php echo $start_date; ?>');
        const currentEnd = new Date('<?php echo $end_date; ?>');
        const daysDiff = Math.round((currentEnd - currentStart) / (1000 * 60 * 60 * 24));
        
        document.querySelectorAll('.date-preset-btn').forEach(btn => {
            if (btn.dataset.days == daysDiff) {
                btn.classList.add('active');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
