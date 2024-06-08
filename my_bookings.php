<?php
include_once('db_conn.php');
session_start();
$msg = '';
$userid = $_SESSION['user_id'];
$username = $_SESSION['username'];

if (!$username) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
if ($role != 'regular') {  
    header('Location: login.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if cancel button is clicked
    if (isset($_POST["cancel_booking"])) {
        $booking_id = $_POST["booking_id"];

        $delete_query = "DELETE FROM bookings WHERE id = :booking_id";
        $delete_statement = $connection->prepare($delete_query);
        $delete_statement->bindParam(':booking_id', $booking_id);
        $delete_statement->execute();

        header("Location: my_bookings.php");
        exit();
    }


}

// Prepare the SQL query
$query = "
SELECT 
    b.id AS booking_id,
    c.model AS car_name,
    c.brand AS car_brand,
    b.total_price AS total_price,
    b.start_date,
    b.end_date,
    b.total_price,
    b.payment_status,
    clc.first_name AS client_first_name,
    clc.last_name AS client_last_name
FROM
    bookings b
        INNER JOIN
    cars c ON b.car_id = c.id
        INNER JOIN
    clients clc ON b.client_id = clc.client_id
WHERE
    clc.user_id = :user_id;
";

// Prepare and execute the query
$statement = $connection->prepare($query);
$statement->bindParam(':user_id', $userid);
$statement->execute();

// Fetch all the results as an associative array
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

// Output the results (for demonstration purposes)
require_once('Layout/Client_layout.php');
?>
<div class="container-fluid">
    <h1 class="title text-primary">My bookings:</h1>
    <?php if (count($results) > 0): ?>
    <div class="row">
        <?php foreach ($results as $row): ?>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                            <input type="hidden" name="total_price" value="<?= $row['total_price'] ?>">
                            <h5 class="card-title"><b>Booking Details</b></h5>
                            <hr>
                            <p class="card-text text-dark"><b>Car Name:</b> <?= $row['car_brand'] ?> <?= $row['car_name'] ?></p>
                            <p class="card-text text-dark"><b>Start Date:</b> <?= $row['start_date'] ?></p>
                            <p class="card-text text-dark"><b>End Date:</b> <?= $row['end_date'] ?></p>
                            <p class="card-text text-dark"><b>Total Price:</b> <?= $row['total_price'] ?></p>
                            <p class="card-text text-dark"><b>Client Name:</b> <?= $row['client_first_name'] ?> <?= $row['client_last_name'] ?></p>
                            <p class="card-text text-dark"><b>Payment Status:</b> <?= $row['payment_status'] ?></p>
                            <button type="submit" name="cancel_booking" class="btn-sm btn btn-danger">Cancel Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-danger"><em>No cars rented. rent a car <a href="index.php"> here</a> :</em></div>
            <?php endif; ?>
    </div>
</div>
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
