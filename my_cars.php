<?php
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
$query = "SELECT COUNT(*) FROM cars where owner_id = :owner_id";
$statement = $connection->prepare($query);
$statement->bindParam(':owner_id', $ownerId);
$statement->execute();
$car_count = $statement->fetchColumn();

if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];

    $query = "SELECT * FROM cars WHERE owner_id = :owner_id AND (brand LIKE '%{$searchTerm}%' OR model LIKE '%{$searchTerm}%')";
} else {
    $query = "SELECT * FROM cars WHERE owner_id = :owner_id";
}

// Bind the owner ID parameter
$statement = $connection->prepare($query);
$statement->bindParam(':owner_id', $ownerId);
$statement->execute();
$result = $statement->fetchAll();


require_once('Layout/owner_layout.php');


?>
                <!-- Begin Page Content -->
                <div class="container-fluid">


                    <!-- Content Row -->
                    <div class="row">

                    </div>

                    <div class="row">
                        <?php foreach ($result as $car): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card custom-border">
                                    <div class="card-header">
                                        <strong style="color:black"><?= $car['brand'] ?> - <?= $car['model'] ?></strong>
                                    </div>
                                    <div class="card-header text-muted">
                                        Year: <?= $car['year'] ?>

                                    </div>
                                    <div class="card-body">
                                        <div style="height: 250px; overflow: hidden;">
                                            <img src="cars/<?= $car['img']; ?>" class="card-img-top"
                                                alt="<?= $car['brand']; ?>">
                                        </div>
                                        <ul class="list-group list-group-flush">


                                            <li class="list-group-item"><strong style="color:black">Price:</strong>
                                                $<?= $car['price'] ?></li>
                                        </ul>
                                    </div>
                                    <div class="card-footer" ><a href='owner_update.php?car_id=<?= $car['id'] ?>'><button class="btn btn-success">Edit</button></a>
                                                        <button class="btn btn-danger">Delete</button></div>

                                </div>
                            </div>
                        <?php endforeach; ?>
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