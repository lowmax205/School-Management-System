<?php
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
                                    <canvas id="logActivityChart"></canvas>
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
const userRolesData = <?php echo json_encode($statistics['users_by_role']); ?>;
const loginActivityData = <?php echo json_encode($statistics['login_activity']); ?>;

// Pie Chart for User Roles
new Chart(document.getElementById('userRolesChart'), {
    type: 'pie',
    data: {
        labels: Object.keys(userRolesData),
        datasets: [{
            data: Object.values(userRolesData),
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.5,
        plugins: {
            legend: {
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'Users by Role'
            }
        }
    }
});

// Bar Chart for Login Activity
new Chart(document.getElementById('loginActivityChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(loginActivityData),
        datasets: [{
            label: 'Successful Logins',
            data: Object.values(loginActivityData),
            backgroundColor: '#36A2EB',
            borderColor: '#2693e6',
            borderWidth: 1
        }]
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
                text: 'Successful Logins (Last 7 Days)'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `Logins: ${context.raw}`;
                    }
                }
            }
        }
    }
});

// Stacked Bar Chart for Log Activity
new Chart(document.getElementById('logActivityChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(loginActivityData),
        datasets: [{
            label: 'Success',
            data: Object.values(loginActivityData).map(day => day.success),
            backgroundColor: '#4BC0C0'
        },
        {
            label: 'Warning',
            data: Object.values(loginActivityData).map(day => day.warning),
            backgroundColor: '#FFCE56'
        },
        {
            label: 'Error',
            data: Object.values(loginActivityData).map(day => day.error),
            backgroundColor: '#FF6384'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.5,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
            }
        },
        plugins: {
            legend: {
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'System Activity Logs (Last 7 Days)'
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
    .dashboard-container > *:not(.content) {
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