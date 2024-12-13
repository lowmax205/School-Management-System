<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/user.query.php';

$usersPerPage = 10;
$totalUsers = getTotalUsersCount();
$totalPages = ceil($totalUsers / $usersPerPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $usersPerPage;
$users = getAllUsers($start, $usersPerPage);
?>

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-users-cog me-2"></i>Users Management</h3>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus me-2"></i>Add User
                    </button>
                </div>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>UID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['uid']); ?></td>
                                    <td class="name-cell">
                                        <span class="name-text"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                                        <div class="name-edit d-none">
                                            <input type="text" class="form-control form-control-sm mb-1" placeholder="First Name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                            <input type="text" class="form-control form-control-sm" placeholder="Last Name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                        </div>
                                    </td>
                                    <td class="email-cell">
                                        <span class="email-text"><?php echo htmlspecialchars($user['email']); ?></span>
                                        <input type="email" class="form-control form-control-sm email-edit d-none" value="<?php echo htmlspecialchars($user['email']); ?>">
                                    </td>
                                    <td class="role-cell">
                                        <span class="role-text"><?php echo htmlspecialchars($user['role']); ?></span>
                                        <select class="form-select form-select-sm role-edit d-none">
                                            <option value="User" <?php echo $user['role'] === 'User' ? 'selected' : ''; ?>>User</option>
                                            <option value="Admin" <?php echo $user['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </td>
                                    <td class="type-cell">
                                        <span class="type-text"><?php echo htmlspecialchars($user['type'] ?? 'Unassigned'); ?></span>
                                        <select class="form-select form-select-sm type-edit d-none">
                                            <option value="Student" <?php echo $user['type'] === 'Student' ? 'selected' : ''; ?>>Student</option>
                                            <option value="Teacher" <?php echo $user['type'] === 'Teacher' ? 'selected' : ''; ?>>Teacher</option>
                                            <option value="Staff" <?php echo $user['type'] === 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                        </select>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['role'] === 'Admin' ? 'danger' : 'primary'; ?>">
                                            <?php echo htmlspecialchars($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-user" data-uid="<?php echo $user['uid']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success save-user d-none" data-uid="<?php echo $user['uid']; ?>">
                                            <i class="fas fa-save"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger cancel-edit d-none" data-uid="<?php echo $user['uid']; ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-user" data-uid="<?php echo $user['uid']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" tabindex="-1">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div id="addUserErrors" class="alert alert-danger d-none"></div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <select class="form-select" name="type" required>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Change Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-user-tag"></i> Change User Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                <input type="hidden" name="uid" id="edit_uid">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">System Role</label>
                        <select class="form-select" name="role" required>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <select class="form-select" name="type" required>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle role change button click
        document.querySelectorAll('.edit-role').forEach(button => {
            button.addEventListener('click', function() {
                const uid = this.dataset.uid;
                document.getElementById('edit_uid').value = uid;
                $('#roleModal').modal('show');
            });
        });

        // Handle role form submission
        document.getElementById('roleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../../server/query/update_user_role.php', {
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

        // Handle edit user button click
        document.querySelectorAll('.edit-user').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const uid = this.dataset.uid;

                // Show edit fields
                row.querySelectorAll('.name-text, .email-text, .role-text, .type-text').forEach(el => el.classList.add('d-none'));
                row.querySelectorAll('.name-edit, .email-edit, .role-edit, .type-edit').forEach(el => el.classList.remove('d-none'));

                // Show/hide buttons
                this.classList.add('d-none');
                row.querySelector('.save-user').classList.remove('d-none');
                row.querySelector('.cancel-edit').classList.remove('d-none');
            });
        });

        // Handle save user button click
        document.querySelectorAll('.save-user').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const uid = this.dataset.uid;

                const userData = {
                    uid: uid,
                    first_name: row.querySelector('.name-edit input:first-child').value,
                    last_name: row.querySelector('.name-edit input:last-child').value,
                    email: row.querySelector('.email-edit').value,
                    role: row.querySelector('.role-edit').value,
                    type: row.querySelector('.type-edit').value
                };

                fetch('../../server/query/update_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(userData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Update display
                            row.querySelector('.name-text').textContent = `${userData.first_name} ${userData.last_name}`;
                            row.querySelector('.email-text').textContent = userData.email;
                            row.querySelector('.role-text').textContent = userData.role;
                            row.querySelector('.type-text').textContent = userData.type;

                            // Reset view
                            resetRowView(row);
                            alert('User updated successfully');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            });
        });

        // Handle cancel edit button click
        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                resetRowView(row);
            });
        });

        function resetRowView(row) {
            row.querySelectorAll('.name-text, .email-text, .role-text, .type-text').forEach(el => el.classList.remove('d-none'));
            row.querySelectorAll('.name-edit, .email-edit, .role-edit, .type-edit').forEach(el => el.classList.add('d-none'));
            row.querySelector('.edit-user').classList.remove('d-none');
            row.querySelector('.save-user').classList.add('d-none');
            row.querySelector('.cancel-edit').classList.add('d-none');
        }

        // Handle edit type button click
        document.querySelectorAll('.edit-type').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const typeCell = row.querySelector('.type-cell');
                const typeText = typeCell.querySelector('.type-text');
                const typeEdit = typeCell.querySelector('.type-edit');
                const saveButton = row.querySelector('.save-type');

                // Show edit mode
                typeText.classList.add('d-none');
                typeEdit.classList.remove('d-none');
                this.classList.add('d-none');
                saveButton.classList.remove('d-none');

                // Set current value
                typeEdit.value = typeText.textContent.trim();
            });
        });

        // Handle save type button click
        document.querySelectorAll('.save-type').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const uid = this.dataset.uid;
                const typeEdit = row.querySelector('.type-edit');
                const newType = typeEdit.value;

                fetch('../../server/query/update_user_type.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `uid=${uid}&type=${newType}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const typeText = row.querySelector('.type-text');
                            const editButton = row.querySelector('.edit-type');

                            // Update display
                            typeText.textContent = newType;
                            typeText.classList.remove('d-none');
                            typeEdit.classList.add('d-none');
                            this.classList.add('d-none');
                            editButton.classList.remove('d-none');

                            alert('User type updated successfully');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            });
        });

        // Handle add user form submission
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const errorDiv = document.getElementById('addUserErrors');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            errorDiv.classList.add('d-none');

            fetch('../../server/query/add_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('User added successfully');
                        location.reload();
                    } else {
                        errorDiv.textContent = data.message;
                        errorDiv.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    errorDiv.textContent = 'An error occurred. Please try again.';
                    errorDiv.classList.remove('d-none');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Add User';
                });
        });

        // Add this new event listener for delete buttons
        document.querySelectorAll('.delete-user').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    const uid = this.dataset.uid;
                    fetch('../../server/query/delete_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                uid: uid
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                this.closest('tr').remove();
                                alert('User deleted successfully');
                            } else {
                                alert('Error: ' + data.message);
                            }
                        });
                }
            });
        });

    });
</script>

<style>
    .type-cell {
        min-width: 120px;
    }

    .type-edit {
        padding: 2px;
        height: auto;
    }

    .name-cell,
    .email-cell,
    .role-cell,
    .type-cell {
        min-width: 150px;
    }
</style>

<?php include '../../includes/footer.php'; ?>