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
$userid = $_SESSION['user_id'];
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
    $query = "SELECT * FROM cars WHERE id = :car_id";
    $statement = $connection->prepare($query);
    $statement->bindParam(':car_id', $car_id);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $total_price = $_POST['total_price'];
    $connection->beginTransaction();
    try {
        $query = "INSERT INTO bookings (car_id, start_date, end_date, total_price, client_id) VALUES (:car_id, :start_date, :end_date, :total_price, :client_id)";
        $statement = $connection->prepare($query);
        $statement->bindParam(':car_id', $car_id);
        $statement->bindParam(':start_date', $start_date);
        $statement->bindParam(':end_date', $end_date);
        $statement->bindParam(':total_price', $total_price);
        $statement->bindParam(':client_id', $clientid);
        $statement->execute();
        $booking_id = $connection->lastInsertId();
        $query = "SELECT CONCAT(cars.brand, ' ', cars.model,' ', cars.year)  AS car_name, CONCAT(clients.first_name, ' ', clients.last_name) AS client_name, CONCAT(car_owners.first_name, ' ', car_owners.last_name) AS owner_name FROM cars JOIN car_owners ON cars.owner_id = car_owners.owner_id JOIN clients ON clients.client_id = :client_id WHERE cars.id = :car_id";
        $statement = $connection->prepare($query);
        $statement->bindParam(':car_id', $car_id);
        $statement->bindParam(':client_id', $clientid);
        $statement->execute();
        $details = $statement->fetch(PDO::FETCH_ASSOC);
        $query = "INSERT INTO rental_history (car_name, client_name, owner_name, start_date, end_date, TotalPrice) VALUES (:car_name, :client_name, :owner_name, :start_date, :end_date, :total_price)";
        $statement = $connection->prepare($query);
        $statement->bindParam(':car_name', $details['car_name']);
        $statement->bindParam(':client_name', $details['client_name']);
        $statement->bindParam(':owner_name', $details['owner_name']);
        $statement->bindParam(':start_date', $start_date);
        $statement->bindParam(':end_date', $end_date);
        $statement->bindParam(':total_price', $total_price);
        $statement->execute();
        $connection->commit();
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $connection->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}
require_once('Layout/Client_layout.php');
?>

<div class="container-fluid">
    <div class="row">
        <?php if ($result) { ?>
            <div class="col-lg-8 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $result['brand']; ?> - <?= $result['model'] ?></h6>
                    </div>
                    <div class="card-body">
                        <img src="cars/<?= $result['img']; ?>" class="img-thumbnail mb-4" alt="<?= $result['brand']; ?>">
                        <p class="card-text"><span class="price-label">Price:</span> $<?= $result['price']; ?></p>
                        <form id="rentalForm" action="" method="POST">
                            <input type="hidden" name="car_id" value="<?= $result['id'] ?>">
                            <input type="hidden" id="total_price_input" name="total_price" value="">
                            <div class="form-group">
                                <label for="start_date" class="price-label">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label for="end_date" class="price-label">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="total_price_display" class="price-label">Total Price:</label>
                                <input type="text" id="total_price_display" class="form-control" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary">Rent Now</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</div>
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Car Rental 2023</span>
        </div>
    </div>
</footer>
</div>
</div>
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <form action="index.php" method="POST">
                    <button type="submit" name="logout" class="btn btn-primary">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Booking Confirmation Modal -->
<div class="modal fade" id="bookingConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="bookingConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingConfirmationModalLabel">Confirm Booking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Start Date: <span id="start_date_confirmation"></span></p>
                <p>End Date: <span id="end_date_confirmation"></span></p>
                <p>Total Price: <span id="total_price_confirmation"></span></p>
                <p>Are you sure you want to proceed with the booking?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirmBookingButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const startDateInput = document.getElementById("start_date");
        const endDateInput = document.getElementById("end_date");
        const totalPriceDisplay = document.getElementById("total_price_display");
        const totalPriceInput = document.getElementById("total_price_input");

        function calculateTotalPrice() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            if (startDate && endDate && endDate > startDate) {
                const timeDiff = endDate - startDate;
                const days = timeDiff / (1000 * 3600 * 24);
                const pricePerDay = <?= $result['price']; ?>;
                const totalPrice = days * pricePerDay;
                totalPriceDisplay.value = totalPrice.toFixed(2);
                totalPriceInput.value = totalPrice.toFixed(2);
            } else {
                totalPriceDisplay.value = "";
                totalPriceInput.value = "";
            }
        }

        startDateInput.addEventListener("change", calculateTotalPrice);
        endDateInput.addEventListener("change", calculateTotalPrice);
    });
</script>

</body>

</html>
