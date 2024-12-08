<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/teacher.query.php';
$teachers = getTeachers();
?>

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Teacher List</h3>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $teachers->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><?php echo $row['contact']; ?></td>
                                    <td><span class="badge bg-<?php echo $row['status'] == 'Active' ? 'success' : 'secondary'; ?>"><?php echo $row['status']; ?></span></td>
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

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Teacher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTeacherForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">UID</label>
                            <input type="text" class="form-control" name="uid" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department" required>
                                <option value="">Select Department</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Mathematics">Mathematics</option>
                                <option value="Physics">Physics</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact</label>
                            <input type="tel" class="form-control" name="contact" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Teacher Modal -->
<div class="modal fade" id="editTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Teacher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTeacherForm">
                <input type="hidden" name="teacher_id">
                <div class="modal-body">
                    <!-- Same form fields as Add Teacher Modal -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">UID</label>
                            <input type="text" class="form-control" name="uid" required>
                        </div>
                        <!-- ... other fields same as add modal ... -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Update Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Teacher Modal -->
<div class="modal fade" id="viewTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye"></i> View Teacher Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">UID:</label>
                        <p id="view-uid"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Name:</label>
                        <p id="view-name"></p>
                    </div>
                    <!-- ... other view fields ... -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-dialog {
        max-width: 800px;
    }

    .modal-content {
        border-radius: 8px;
    }

    .modal-header {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .form-label {
        font-weight: 500;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate UID when Add Teacher modal is opened
        $('#addTeacherModal').on('show.bs.modal', function() {
            fetch('../../server/query/generate_teacher_uid.php')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#addTeacherForm input[name="uid"]').value = data.uid;
                });
        });

        // Handle Add Teacher Form Submit
        document.getElementById('addTeacherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../../server/query/add_teacher.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Teacher added successfully!');
                        location.reload(); // Refresh the page to show new data
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the teacher.');
                });
        });

        // Handle Edit Teacher Button Click
        document.querySelectorAll('.btn-warning').forEach(button => {
            button.addEventListener('click', function() {
                const teacherId = this.closest('tr').querySelector('td:first-child').textContent;
                // Fetch teacher details and populate edit form
                $('#editTeacherModal').modal('show');
            });
        });

        // Handle View Teacher Button Click
        document.querySelectorAll('.btn-info').forEach(button => {
            button.addEventListener('click', function() {
                const teacherId = this.closest('tr').querySelector('td:first-child').textContent;
                // Fetch and display teacher details
                $('#viewTeacherModal').modal('show');
            });
        });
    });
</script>

<?php include '../../includes/footer.php'; ?>