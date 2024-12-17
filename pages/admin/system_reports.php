<?php
date_default_timezone_set('Asia/Manila');
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
include '../../server/query/user.query.php';

$statistics = getUserStatistics();
?>

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>System Reports</h3>
                </div>
            </div>

            <div class="card-body">
                <div class="container-fluid p-0">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <h4 class="text-muted">Total Users</h4>
                                    <h2 class="display-4 text-primary"><?php echo $statistics['total_users']; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <canvas id="userRolesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <canvas id="loginActivityChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <canvas id="systemActivityChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button onclick="printReport()" class="btn btn-primary">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const userRolesData = <?php echo json_encode($statistics['users_by_role'] ?? []); ?>;
    const loginActivityData = <?php echo json_encode($statistics['login_activity'] ?? []); ?>;
    const systemLogsData = <?php echo json_encode($statistics['system_logs'] ?? []); ?>;

    // Function to format dates consistently
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    }

    // Updated Pie Chart for User Roles
    new Chart(document.getElementById('userRolesChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(userRolesData),
            datasets: [{
                data: Object.values(userRolesData),
                backgroundColor: ['#36A2EB', '#FFCE56', '#4BC0C0'], // Colors for Teacher, Staff, Student
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                },
                title: {
                    display: true,
                    text: 'Users by Type',
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${value} users`;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart for Login Activity
    new Chart(document.getElementById('loginActivityChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(loginActivityData).map(date => formatDate(date)),
            datasets: [{
                    label: 'Logged In',
                    data: Object.keys(loginActivityData).map(date =>
                        (loginActivityData[date] && loginActivityData[date].success) || 0
                    ),
                    backgroundColor: '#36A2EB',
                    borderColor: '#2693e6',
                    borderWidth: 1
                },
                {
                    label: 'Login Failed',
                    data: Object.keys(loginActivityData).map(date =>
                        (loginActivityData[date] && loginActivityData[date].failed) || 0
                    ),
                    backgroundColor: '#FF6384',
                    borderColor: '#ff4b6b',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Login Activity (Last 7 Days)'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });

    // Updated System Activity Stacked Bar Chart
    new Chart(document.getElementById('systemActivityChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(systemLogsData).map(date => formatDate(date)),
            datasets: [{
                    label: 'Good', // Changed from 'Success' to 'Good'
                    data: Object.keys(systemLogsData).map(date =>
                        (systemLogsData[date] && systemLogsData[date].success) || 0
                    ),
                    backgroundColor: '#4BC0C0'
                },
                {
                    label: 'Warning',
                    data: Object.keys(systemLogsData).map(date =>
                        (systemLogsData[date] && systemLogsData[date].warning) || 0
                    ),
                    backgroundColor: '#FFCE56'
                },
                {
                    label: 'Error',
                    data: Object.keys(systemLogsData).map(date =>
                        (systemLogsData[date] && systemLogsData[date].error) || 0
                    ),
                    backgroundColor: '#FF6384'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            scales: {
                x: {
                    stacked: false,
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'System Logs Activity (Last 7 Days)' // Updated title to be more specific
                }
            }
        }
    });

    function printReport() {
        window.print();
    }
</script>

<style>
    .content {
        padding: 20px;
        overflow-y: auto;
    }

    .card {
        transition: transform 0.2s;
        margin-bottom: 0;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .chart-container {
        position: relative;
        height: 100%;
    }

    @media print {
        .dashboard-container>*:not(.content) {
            display: none;
        }

        .card {
            break-inside: avoid;
        }
    }

    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
    }
</style>

<?php include '../../includes/footer.php'; ?>