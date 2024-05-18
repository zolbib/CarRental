<?php
include_once('db_conn.php');
session_start();

$user = $_SESSION['username'];
$role = $_SESSION['role'];
if ($role != 'admin') {
    header('Location: login.php');
    exit();
}
if (!$user) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) { 
    session_destroy();
    header("Location: login.php");
    exit();
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($searchTerm)) {
    $query = "SELECT * FROM cars WHERE CONCAT(brand, ' ', model) LIKE '%$searchTerm%'";
} else {
    $query = "SELECT * FROM cars";
}

$statement = $connection->prepare($query);
if (!$statement) {
    die("Query preparation failed: " . $connection->error);
}

if (!$statement->execute()) {
    die("Query execution failed: " . $statement->error);
}

$cars = $statement->fetchAll(PDO::FETCH_ASSOC);

// Car delete
if (isset($_POST['delete_car'])) {
    $carId = $_POST['car_id'];

    // Delete associated bookings
    $deleteBookingsQuery = "DELETE FROM bookings WHERE car_id = ?";
    $deleteBookingsStatement = $connection->prepare($deleteBookingsQuery);
    if (!$deleteBookingsStatement) {
        die("Delete bookings statement preparation failed: " . $connection->error);
    }
    if (!$deleteBookingsStatement->execute([$carId])) {
        die("Bookings deletion failed: " . $deleteBookingsStatement->error);
    }

    // Delete from cars table
    $deleteCarQuery = "DELETE FROM cars WHERE id = ?";
    $deleteCarStatement = $connection->prepare($deleteCarQuery);
    if (!$deleteCarStatement) {
        die("Delete car statement preparation failed: " . $connection->error);
    }
    if (!$deleteCarStatement->execute([$carId])) {
        die("Car deletion failed: " . $deleteCarStatement->error);
    }

    // Redirect back to the same page after deletion
    header("refresh: 0");
    exit();
}

require_once('Layout/admin_layout.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="mt-5 mb-3 clearfix">
                <h2 class="pull-left">Car Details</h2>
                <a href="add_car.php" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New Car</a>
            </div>
            <?php if (count($cars) > 0): ?>
            <div class="row">
                <?php foreach ($cars as $car): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $car['brand'] . ' ' . $car['model'] ?></h5>
                            <div style="height: 200px; overflow: hidden;">
                                <img src="cars/<?= $car['img']; ?>" class="card-img-top" alt="<?= $row['brand']; ?>">
                            </div>
                            <p class="card-text">Year: <?= $car['year'] ?></p>
                            <p class="card-text">Color: <?= $car['color'] ?></p>
                            <a href="update_car.php?id=<?= $car['id'] ?>" class="btn btn-primary">Edit</a>
                            <button type="button" class="btn btn-danger delete-car-btn" data-toggle="modal" data-target="#deleteCarModal" data-car-id="<?= $car['id'] ?>">Delete</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-danger"><em>No records were found.</em></div>
            <?php endif; ?>
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
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
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

<!-- Modal for confirming car deletion -->
<div class="modal fade" id="deleteCarModal" tabindex="-1" role="dialog" aria-labelledby="deleteCarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCarModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this car?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteCarForm" method="post" name="deleteCarForm">
                    <button type="submit" class="btn btn-danger" name="delete_car">Delete</button>
                    <input type="hidden" name="car_id" id="deleteCarId">
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

<script>
function submitLogoutForm() {
    document.getElementById('logoutForm').submit();
}

$(document).ready(function() {
    $('.delete-car-btn').click(function() {
        var carId = $(this).data('car-id');
        $('#deleteCarId').val(carId);
    });
});
</script>

</body>
</html>
