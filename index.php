<?php
include_once ('db_conn.php');
session_start();

$username = $_SESSION['username'];
$role = $_SESSION['role'];
if ($role != 'regular') {
    header('Location: login.php');
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
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $sortByPrice = isset($_GET['sort_price']) ? $_GET['sort_price'] : 'asc';

    $query = "SELECT * FROM cars WHERE brand LIKE '%{$searchTerm}%' OR model LIKE '%{$searchTerm}%'";
    $query .= " ORDER BY price $sortByPrice";

} else {
    $query = "SELECT * FROM cars";
}


$statement = $connection->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
require_once('Layout/client_layout.php');
?>

                <!-- Begin Page Content -->
                <div class="container-fluid">


                    <!-- Content Row -->

                    <div class="row">
    <?php
    if ($result) {
        foreach ($result as $row) {
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body" style="position: relative;">
                        <form action="">
                            <!-- Image container with fixed height -->
                            <h4 class="card-title"><?= $row['brand']; ?></h4>
                            <h5 class="card-title"><?= $row['model'] ?></h5>
                            <div style="height: 200px; overflow: hidden;">
                                <img src="cars/<?= $row['img']; ?>" class="card-img-top" alt="<?= $row['brand']; ?>">
                            </div>

                            
                            <!-- Price and Rent this car -->
                            <div style="margin: top 50px; bottom: 10px; left: 0; right: 0;">
                                <p class="card-text" style="margin-bottom: 5px;">Price: $<?= $row['price']; ?></p>
                                <a href='rent.php?car_id=<?= $row['id'] ?>' style="display: block;">Rent this car</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No cars found</p>";
    }
    ?>
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
    </script>
</body>

</html>