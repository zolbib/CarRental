<?php
$msg = '';
include_once ('db_conn.php');
session_start();

$username = $_SESSION['username'];
$role = $_SESSION['role'];
if ($role != 'car_owner') {
    header('Location: login.php');
    exit();
}
if (!$username) {
    header("Location: login.php");
    exit();
}


if (isset($_POST['logout'])) {
    session_destroy();

    header("Location: login.php");
    exit();
}

$query = "SELECT owner_id FROM car_owners WHERE user_id = :user_id";
$statement = $connection->prepare($query);
$statement->bindParam(':user_id', $_SESSION['user_id']);
$statement->execute();
$ownerMapping = $statement->fetch(PDO::FETCH_ASSOC);

if (!$ownerMapping) {
    // Handle case where owner ID not found for logged-in user
    header("Location: login.php");
    exit();
}

$ownerId = $ownerMapping['owner_id'];
$query = "SELECT earnings FROM car_owners WHERE owner_id = :owner_id";
$statement = $connection->prepare($query);
$statement->bindParam(':owner_id', $ownerId);
$statement->execute();
$earnings = $statement->fetchColumn();

$query = "SELECT
SUM(b.total_price) AS price,
DATE(b.created_at) AS date
FROM
bookings b
JOIN
cars c ON b.car_id = c.id
WHERE
c.owner_id = :owner_id  -- Replace with the appropriate owner_id condition
GROUP BY
DATE(b.created_at);
";

$statement = $connection->prepare($query);
$statement->bindParam(':owner_id', $ownerId);
$statement->execute();
$owner_chart_data = $statement->fetchAll(PDO::FETCH_ASSOC);

require_once ('Layout/Owner_layout.php')

    ?>


<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Earnings (total)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $earnings ?>$</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings by Date</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>





</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2021</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <form id="logoutForm" method="post">
                    <a class="btn btn-primary" href="#" onclick="submitLogoutForm();">Logout</a>
                    <input type="hidden" name="logout" value="1">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/chart-area-demo.js"></script>
<script src="js/demo/chart-pie-demo.js"></script>
<script>
    function submitLogoutForm() {
        document.getElementById('logoutForm').submit();
    }

    // Earnings Chart
    document.addEventListener('DOMContentLoaded', (event) => {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        const earningsData = <?= json_encode($owner_chart_data) ?>;
        const labels = earningsData.map(data => data.date);
        const prices = earningsData.map(data => data.price);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Earnings ($)',
                    data: prices,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return '$' + value;
                            }
                        },
                        gridLines: {
                            color: 'rgb(234, 236, 244)',
                            zeroLineColor: 'rgb(234, 236, 244)',
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }]
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: 'rgb(255,255,255)',
                    bodyFontColor: '#858796',
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10
                }
            }
        });
    });
</script>

</body>

</html>