<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/user.query.php';

$usersPerPage = 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$totalUsers = getTotalUsersCount($search);
$totalPages = ceil($totalUsers / $usersPerPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $usersPerPage;
$users = getAllUsers($start, $usersPerPage, $search);
?>

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100 mb-5">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-users-cog me-2"></i>Users Management</h3>
                    <div class="d-flex gap-2">
                        <form class="d-flex" method="GET">
                            <input class="form-control me-2" type="search" name="search" placeholder="Search name or email" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus me-2"></i>Add User
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body d-flex flex-column p-0">
                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover table-bordered mb-0"> <!-- Add table-bordered and remove bottom margin -->
                        <thead class="table-light sticky-top"> <!-- Make header sticky -->
                            <tr>
                                <th>UID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Updated At</th>
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
                                        <span class="badge bg-<?php echo ($user['status'] === 'Inactive') ? 'danger' : 'success'; ?>">
                                            <?php echo htmlspecialchars($user['status'] ?? 'Active'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['account_created']); ?></td>
                                    <td><?php echo htmlspecialchars($user['info_updated']); ?></td>
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
    <!-- Add these script tags before your user_handler.js -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/user_handler.js"></script>

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

<!-- Update User Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Update User Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                <input type="hidden" name="uid" id="edit_uid">
                <div class="modal-body">
                    <div class="row">
                        <!-- Account Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Account Information</h6>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" id="edit_email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Change Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                                <div class="form-text">Only fill this if you want to change the password</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">System Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="User">User</option>
                                    <option value="Admin">Admin</option>
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

                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Personal Information</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" name="birth_date" id="edit_birth_date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender" id="edit_gender">
                                        <option value="">Select Gender</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" id="edit_phone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" id="edit_address" rows="2"></textarea>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>