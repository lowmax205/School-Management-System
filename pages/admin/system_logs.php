<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/user.query.php';

$logs = getSystemLogs();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$logsPerPage = 25;
$totalLogs = count($logs);
$totalPages = ceil($totalLogs / $logsPerPage);
$startIndex = ($page - 1) * $logsPerPage;
$logs = array_slice($logs, $startIndex, $logsPerPage);
?>

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-history me-2"></i>System Logs</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="logsTable">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($log['username']); ?></td>
                                    <td><?php echo htmlspecialchars($log['role']); ?></td>
                                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                                    <td><span class="badge bg-<?php echo $log['status'] == 'success' ? 'success' : ($log['status'] == 'warning' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($log['status']); ?>
                                        </span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" tabindex="-1">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/system_logs.js"></script>
<?php include '../../includes/footer.php'; ?>