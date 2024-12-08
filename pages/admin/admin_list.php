<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/admin.query.php';
$admins = getAdmins();
?>

<link href="../../assets/css/dashboard-styles.css" rel="stylesheet">
<link href="../../assets/css/table-style.css" rel="stylesheet">

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title mb-0"><i class="fas fa-user-shield me-2"></i>Admin List</h3>
                <button class="btn btn-light"><i class="fas fa-plus"></i> Add Admin</button>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Position</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $admins->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['role'] ?? ''); ?></td>
                                    <td><span class="badge bg-<?php echo ($row['status'] ?? '') == 'Active' ? 'success' : 'secondary'; ?>">
                                            <?php echo htmlspecialchars($row['status'] ?? ''); ?>
                                        </span></td>
                                    <td><?php echo htmlspecialchars($row['position'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['updated_at'] ?? ''); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <!-- Similar modal structure with admin-specific fields -->
</div>

<!-- Add Role Change Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-tag"></i> Change User Role</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="changeRoleForm">
                <div class="modal-body">
                    <input type="hidden" name="uid">
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <select class="form-select" name="type" required>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">System Role</label>
                        <select class="form-select" name="role" required>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle role change form submission
        document.getElementById('changeRoleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../../server/query/change_user_role.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('User role updated successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        });
    });
</script>

<?php include '../../includes/footer.php'; ?>