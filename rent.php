<?php
include_once ('db_conn.php');
session_start();
$total_price = 0;
$username = $_SESSION['username'];
$role = $_SESSION['role'];
if ($role != 'regular') {
    header('Location: login.php');
}
if (!$username) {
    header("Location: login.php");
    exit();
}
$userid =$_SESSION['user_id'];
$query = "SELECT client_id FROM clients WHERE user_id = :userid";
$statement = $connection->prepare($query);
$statement->bindParam(':userid', $userid);
$statement->execute();
$clientid = $statement->fetchColumn();

if (isset($_POST['logout'])) {
    session_destroy();

    header("Location: login.php");
    exit();
}
if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];

    // Fetch car details from the database based on car_id
    $query = "SELECT * FROM cars WHERE id = :car_id";
    $statement = $connection->prepare($query);
    $statement->bindParam(':car_id', $car_id);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $total_price = $_POST['total_price'];

    // Prepare and execute SQL statement to insert into bookings table
    $query = "INSERT INTO bookings (car_id, start_date, end_date, total_price,client_id) VALUES (:car_id, :start_date, :end_date, :total_price,:client_id)";
    $statement = $connection->prepare($query);
    $statement->bindParam(':car_id', $car_id);
    $statement->bindParam(':start_date', $start_date);
    $statement->bindParam(':end_date', $end_date);
    $statement->bindParam(':total_price', $total_price);
    $statement->bindParam(':client_id',$clientid);
    $statement->execute();

    // Redirect the user to another page to prevent form resubmission
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .price-label {
            font-weight: bold;
            color: black;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <img style="width:50px;" src="logos/only-logo-no-background.png" alt="">
                </div>
                <div class="sidebar-brand-text mx-3 text-light"><img class="img-fluid"
                        src="logos/text-logo-no-background.png" alt=""></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Home</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-car"></i>
                    <span>My cars</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Options:</h6>
                        <a class="collapse-item" href="#">Edit cars</a>
                        <a class="collapse-item" href="#">Add my car</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-dollar-sign "></i>
                    <span>Earnings</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">




            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>


        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->




                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $username ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">


                    <!-- Content Row -->
                    <div class="row">
                        <?php if ($result) { ?>
                            <div class="col-lg-8 mb-4">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary"><?= $result['brand']; ?> -
                                            <?= $result['model'] ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <img src="cars/<?= $result['img']; ?>" class="img-thumbnail mb-4"
                                            alt="<?= $result['brand']; ?>">
                                        <p class="card-text"><span class="price-label">Price:</span>
                                            $<?= $result['price']; ?></p>
                                        <form id="rentalForm" action="" method="POST">
                                            <input type="hidden" name="car_id" value="<?= $result['id'] ?>">
                                            <input type="hidden" id="total_price_input" name="total_price" value="">
                                            <div class="form-group">
                                                <label for="start_date" class="price-label">Start Date:</label>
                                                <input type="date" id="start_date" name="start_date" class="form-control"
                                                    required min="<?= date('Y-m-d') ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="end_date" class="price-label">End Date:</label>
                                                <input type="date" id="end_date" name="end_date" class="form-control"
                                                    required min="<?= date('Y-m-d') ?>">
                                            </div>
                                            <button type="button" class="btn btn-primary"
                                                onclick="calculateTotalPriceAndOpenModal()">Calculate Total Price</button>
                                        </form>
                                        <br>
                                        <?php if ($total_price > 0) { ?>
                                            <label class="price-label">Total Price: $<?= $total_price ?></label>
                                        <?php }
                        } ?>
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
    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Confirm Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Start Date: <span id="start_date_modal"></span></p>
                    <p>End Date: <span id="end_date_modal"></span></p>
                    <p>Total Price: <span id="total_price_modal"></span></p>
                    <p>Are you sure you want to book?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="submitRentalForm()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to open the modal and populate it with values
        function openBookingModal(startDate, endDate, totalPrice) {
            // Set the values in the modal body
            document.getElementById("start_date_modal").textContent = startDate;
            document.getElementById("end_date_modal").textContent = endDate;
            document.getElementById("total_price_modal").textContent = totalPrice;

            // Set the value of the hidden input field in the form
            document.getElementById("total_price_input").value = totalPrice;

            // Open the modal
            $('#bookingModal').modal('show');
        }

        // Function to calculate total price and open booking modal
        function calculateTotalPriceAndOpenModal() {
            // Get start date, end date, and daily rental price
            var startDate = document.getElementById("start_date").value;
            var endDate = document.getElementById("end_date").value;
            var dailyPrice = <?= $result['price'] ?>; // Replace this with the actual daily rental price

            // Calculate number of days between start and end dates
            var start = new Date(startDate);
            var end = new Date(endDate);
            var timeDiff = Math.abs(end.getTime() - start.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

            // Calculate total price
            var totalPrice = diffDays * dailyPrice;

            // Open the booking modal with calculated values
            openBookingModal(startDate, endDate, totalPrice);
        }

        // Function to submit the rental form
        function submitRentalForm() {
            document.getElementById("rentalForm").submit();
        }

    </script>

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
    </script>
    <script>
        // Disable past dates in date inputs
        var todayw = new Date();
    todayw.setDate(todayw.getDate() + 1);
    var today = todayw.toISOString().split('T')[0];

    
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 2);
    var tomorrowFormatted = tomorrow.toISOString().split('T')[0];
        document.getElementById('start_date').setAttribute('min', today);
        document.getElementById('end_date').setAttribute('min', tomorrowFormatted);
    </script>
</body>


</html>