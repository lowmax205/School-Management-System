<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/subject.query.php';
$subjects = getSubjects();
?>

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title mb-0"><i class="fas fa-book me-2"></i>Subject List</h3>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    <i class="fas fa-plus"></i> Add Subject
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Units</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $subjects->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['subject_code']; ?></td>
                                    <td><?php echo $row['subject_name']; ?></td>
                                    <td><?php echo $row['units']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><span class="badge bg-<?php echo $row['status'] == 'Active' ? 'success' : 'secondary'; ?>"><?php echo $row['status']; ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-warning edit-btn"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger delete-btn"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add Subject</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubjectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject Code</label>
                        <input type="text" class="form-control" name="subject_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Units</label>
                        <input type="number" class="form-control" name="units" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Physics">Physics</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit & View modals following same pattern -->
// ...similar modal structures for edit and view...

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Subject Form Handler
        document.getElementById('addSubjectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add AJAX implementation
        });

        // Edit Button Handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                // Populate and show edit modal
            });
        });

        // View Button Handler
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                // Populate and show view modal
            });
        });
    });
</script>

<?php include '../../includes/footer.php'; ?>