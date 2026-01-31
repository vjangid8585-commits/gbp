<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insights: <?php echo $location->business_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <h2>Insights: <?php echo $location->business_name; ?></h2>
             <?php if($this->session->userdata('role') === 'admin'): ?>
                <a href="<?php echo site_url('sync/insights/'.$location->id); ?>" class="btn btn-success">Sync Latest Insights</a>
            <?php endif; ?>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <?php if(empty($insights)): ?>
            <div class="alert alert-warning">No insights data available. Sync required.</div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <canvas id="insightsChart"></canvas>
                </div>
            </div>
            
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Calls</th>
                        <th>Website Clicks</th>
                        <th>Direction Requests</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_reverse($insights) as $row): ?>
                    <tr>
                        <td><?php echo $row->date; ?></td>
                        <td><?php echo $row->calls; ?></td>
                        <td><?php echo $row->website_clicks; ?></td>
                        <td><?php echo $row->direction_requests; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <script>
                const ctx = document.getElementById('insightsChart');
                const data = <?php echo json_encode($insights); ?>;
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(row => row.date),
                        datasets: [
                            {
                                label: 'Calls',
                                data: data.map(row => row.calls),
                                borderColor: 'red',
                                tension: 0.1
                            },
                            {
                                label: 'Website Clicks',
                                data: data.map(row => row.website_clicks),
                                borderColor: 'blue',
                                tension: 0.1
                            },
                            {
                                label: 'Direction Requests',
                                data: data.map(row => row.direction_requests),
                                borderColor: 'green',
                                tension: 0.1
                            }
                        ]
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
