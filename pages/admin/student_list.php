<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/student.query.php';
include '../../server/query/course.query.php';
$students = getStudents();
$programs = getAllPrograms();
?>

<link href="../../assets/css/dashboard-styles.css" rel="stylesheet">
<link href="../../assets/css/table-style.css" rel="stylesheet">

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-user-graduate me-2"></i>Student List</h3>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Year</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th>Program</th>
                                <th>Major</th>
                                <th>ID-No</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_no']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($row['status'] ?? '') == 'Active' ? 'success' : 'secondary'; ?>">
                                            <?php echo htmlspecialchars($row['status'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['program'] ?? 'Unassigned'); ?></td>
                                    <td><?php echo htmlspecialchars($row['major'] ?? 'Undeclared'); ?></td>
                                    <td><?php echo htmlspecialchars($row['id_no']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-student" data-id="<?php echo $row['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-student" data-id="<?php echo $row['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add Student</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStudentForm">
                <!-- Student-specific form fields -->
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Student</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudentForm">
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="edit_student_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Year Level</label>
                            <select class="form-select" name="year" id="edit_year" required>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control" name="section" id="edit_section" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program</label>
                            <select class="form-select" name="program" id="edit_program" required>
                                <option value="">Select Program</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?php echo htmlspecialchars($program['program_code']); ?>">
                                        <?php echo htmlspecialchars($program['program_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Major</label>
                            <input type="text" class="form-control" name="major" id="edit_major" placeholder="Leave blank for undeclared">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Edit Student Button Click
        const editButtons = document.querySelectorAll('.edit-student'); // Changed from .btn-warning to .edit-student
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = this.dataset.id;

                // Get data from correct column indices (0-based)
                document.getElementById('edit_student_id').value = id;
                document.getElementById('edit_year').value = row.children[2].textContent.trim();
                document.getElementById('edit_section').value = row.children[3].textContent.trim();
                document.getElementById('edit_program').value = row.children[5].textContent.trim();
                document.getElementById('edit_major').value = row.children[6].textContent.trim();

                // Show modal
                new bootstrap.Modal(document.getElementById('editStudentModal')).show();
            });
        });

        // Handle Edit Student Form Submit
        document.getElementById('editStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../../server/query/update_student.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Student updated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the student.');
                });
        });
    });
</script>

<?php include '../../includes/footer.php'; ?>