<?php
include_once('db_conn.php');
session_start();

$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;
if ($role != 'admin') {
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

$query = "SELECT * FROM rental_history ";
$statement = $connection->prepare($query);


if (!$statement) {
    die("Query preparation failed: " . $connection->error);
}

if (!$statement->execute()) {
    die("Query execution failed: " . $statement->error);
}

$rentals = $statement->fetchAll(PDO::FETCH_ASSOC);

require_once('Layout/admin_layout.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="mt-5 mb-3 clearfix">
                <h2 class="pull-left">Rental History</h2>
            </div>
            <?php if (count($rentals) > 0): ?>
            <div class="row">
                <?php foreach ($rentals as $rental): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-dark"><b><?=$rental['car_name'] ?></b></h5>
                            <p class="card-text"><b class="text-dark">Rented By:</b> <?= $rental['client_name'] ?></p>
                            <p class="card-text"><b class="text-dark">Car Owner:</b> <?= $rental['owner_name'] ?></p>
                            <p class="card-text"><b class="text-dark">Rental Date:</b> <?= $rental['start_date'] ?></p>
                            <p class="card-text"><b class="text-dark">Return Date:</b> <?= $rental['end_date'] ?></p>
                            <p class="card-text"><b class="text-dark">Price:</b> <?= $rental['TotalPrice'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-danger"><em>No rental history found.</em></div>
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
