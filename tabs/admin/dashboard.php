<?php
include '../../database/db_connect.php';

// Capture date range from GET
$fromDate = isset($_GET['from_date']) && !empty($_GET['from_date']) ? $_GET['from_date'] : null;
$toDate   = isset($_GET['to_date']) && !empty($_GET['to_date']) ? $_GET['to_date'] : null;

// Helper function to build WHERE clause for a given column
function dateFilter($column, $from, $to) {
    if ($from && $to) {
        return "WHERE $column BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
    } elseif ($from) {
        return "WHERE $column >= '$from 00:00:00'";
    } elseif ($to) {
        return "WHERE $column <= '$to 23:59:59'";
    }
    return "";
}

// 1. Overall eBook counts
$ebookFilter = dateFilter('created_at', $fromDate, $toDate);
$ebookCountQuery = "SELECT COUNT(*) AS total FROM ebooks $ebookFilter";
$ebookCountResult = $conn->query($ebookCountQuery);
$ebookCount = $ebookCountResult->fetch_assoc()['total'];

// 2. User counts (use loggedin date as filter)
$userFilter = dateFilter('loggedin', $fromDate, $toDate);
$userCountQuery = "SELECT COUNT(*) AS total FROM account $userFilter";
$userCountResult = $conn->query($userCountQuery);
$userCount = $userCountResult->fetch_assoc()['total'];

// 3. Guest count
$guestFilter = dateFilter('created_at', $fromDate, $toDate);
$guestCountQuery = "SELECT COUNT(*) AS total FROM guestlog $guestFilter";
$guestCountResult = $conn->query($guestCountQuery);
$guestCount = $guestCountResult->fetch_assoc()['total'];

// 4. Research count per category
$researchFilter = dateFilter('created_at', $fromDate, $toDate);
$researchCategoryQuery = "SELECT category, COUNT(*) AS total FROM research $researchFilter GROUP BY category";
$researchCategoryResult = $conn->query($researchCategoryQuery);
$researchCategories = [];
while ($row = $researchCategoryResult->fetch_assoc()) {
    $researchCategories[] = $row;
}

// 5. Ebook categories for pie chart
$ebookCategoryQuery = "SELECT category, COUNT(*) AS total FROM ebooks $ebookFilter GROUP BY category";
$ebookCategoryResult = $conn->query($ebookCategoryQuery);
$ebookCategoryLabels = [];
$ebookCategoryData = [];
while ($row = $ebookCategoryResult->fetch_assoc()) {
    $ebookCategoryLabels[] = $row['category'];
    $ebookCategoryData[] = $row['total'];
}

// 6. Ebook locations for pie chart
$ebookLocationQuery = "SELECT location, COUNT(*) AS total FROM ebooks $ebookFilter GROUP BY location";
$ebookLocationResult = $conn->query($ebookLocationQuery);
$ebookLocationLabels = [];
$ebookLocationData = [];
while ($row = $ebookLocationResult->fetch_assoc()) {
    $ebookLocationLabels[] = $row['location'];
    $ebookLocationData[] = $row['total'];
}

// 7. Research count by department for bar chart
$researchDeptQuery = "SELECT Department, COUNT(*) AS total FROM research $researchFilter GROUP BY Department";
$researchDeptResult = $conn->query($researchDeptQuery);
$researchDeptLabels = [];
$researchDeptData = [];
while ($row = $researchDeptResult->fetch_assoc()) {
    $researchDeptLabels[] = $row['Department'];
    $researchDeptData[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .card-chart { min-height: 350px; }
</style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">eBook Dashboard</h1>

    <!-- Date Picker Filter -->
    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label for="from_date" class="form-label">From</label>
            <input type="date" id="from_date" name="from_date" class="form-control"
                   value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label">To</label>
            <input type="date" id="to_date" name="to_date" class="form-control"
                   value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="dashboard.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="row mb-4">
        <a href="generate_pdf.php?from_date=<?= $fromDate ?>&to_date=<?= $toDate ?>" target="_blank" class="btn btn-success mt-2">
            Print PDF Summary
        </a>
    </div>

    <!-- Count Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total eBooks</h5>
                    <p class="card-text fs-3"><?= $ebookCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text fs-3"><?= $userCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Guests</h5>
                    <p class="card-text fs-3"><?= $guestCount ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Research per category -->
    <div class="card mb-4">
        <div class="card-header">Research Count per Category</div>
        <div class="card-body">
            <?php foreach ($researchCategories as $rc): ?>
                <div class="mb-2">
                    <span class="badge bg-info text-dark fs-6">
                        <?= $rc['category'] ?>: <?= $rc['total'] ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- eBook Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-3 card-chart">
                <div class="card-header">eBook Categories</div>
                <div class="card-body">
                    <canvas id="ebookCategoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3 card-chart">
                <div class="card-header">eBook Locations</div>
                <div class="card-body">
                    <canvas id="ebookLocationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Research by Department -->
    <div class="card mb-4 card-chart">
        <div class="card-header">Research Count by Department</div>
        <div class="card-body">
            <canvas id="researchDeptChart"></canvas>
        </div>
    </div>
</div>

<script>
const ebookCategoryChart = new Chart(document.getElementById('ebookCategoryChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($ebookCategoryLabels) ?>,
        datasets: [{
            label: 'eBook Categories',
            data: <?= json_encode($ebookCategoryData) ?>,
            backgroundColor: [
                '#007bff','#28a745','#ffc107','#dc3545','#17a2b8','#6f42c1','#fd7e14','#20c997','#6610f2','#e83e8c'
            ],
            borderWidth: 1
        }]
    },
    options: { responsive:true, maintainAspectRatio:false }
});

const ebookLocationChart = new Chart(document.getElementById('ebookLocationChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($ebookLocationLabels) ?>,
        datasets: [{
            label: 'eBook Locations',
            data: <?= json_encode($ebookLocationData) ?>,
            backgroundColor: [
                '#007bff','#28a745','#ffc107','#dc3545','#17a2b8','#6f42c1','#fd7e14','#20c997','#6610f2','#e83e8c'
            ],
            borderWidth: 1
        }]
    },
    options: { responsive:true, maintainAspectRatio:false }
});

const researchDeptChart = new Chart(document.getElementById('researchDeptChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($researchDeptLabels) ?>,
        datasets: [{
            label: 'Research Count',
            data: <?= json_encode($researchDeptData) ?>,
            backgroundColor: '#007bff'
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } } }
});
</script>
</body>
</html>
