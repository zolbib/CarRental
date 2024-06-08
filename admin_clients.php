<?php
include_once ('db_conn.php');
session_start();

$username = $_SESSION['username'];
$role = $_SESSION['role'];
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
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($searchTerm)) {
    $query = "SELECT * FROM clients WHERE CONCAT(first_name, ' ', last_name) LIKE '%$searchTerm%'";
} else {
    $query = "SELECT * FROM clients";
}

$statement = $connection->prepare($query);
if (!$statement) {
    die("Query preparation failed: " . $connection->error);
}

if (!$statement->execute()) {
    die("Query execution failed: " . $statement->error);
}

$clients = $statement->fetchAll(PDO::FETCH_ASSOC);   

//client delete

//client delete

if (isset($_POST['delete_client'])) {
    $clientId = $_POST['client_id'];

    // Delete associated bookings
    // $deleteBookingsQuery = "DELETE FROM bookings WHERE client_id = ?";
    // $deleteBookingsStatement = $connection->prepare($deleteBookingsQuery);
    // if (!$deleteBookingsStatement) {
    //     die("Delete bookings statement preparation failed: " . $connection->error);
    // }
    // if (!$deleteBookingsStatement->execute([$clientId])) {
    //     die("Bookings deletion failed: " . $deleteBookingsStatement->error);
    // }

    // Retrieve user_id associated with the client
    $userIdQuery = "SELECT user_id FROM clients WHERE client_id = ?";
    $userIdStatement = $connection->prepare($userIdQuery);
    if (!$userIdStatement) {
        die("User ID query preparation failed: " . $connection->error);
    }
    if (!$userIdStatement->execute([$clientId])) {
        die("User ID retrieval failed: " . $userIdStatement->error);
    }
    $userId = $userIdStatement->fetchColumn();

    // Delete from clients table
    // $deleteClientQuery = "DELETE FROM clients WHERE client_id = ?";
    // $deleteClientStatement = $connection->prepare($deleteClientQuery);
    // if (!$deleteClientStatement) {
    //     die("Delete client statement preparation failed: " . $connection->error);
    // }
    // if (!$deleteClientStatement->execute([$clientId])) {
    //     die("Client deletion failed: " . $deleteClientStatement->error);
    // }

    // Delete from users table
    $deleteUserQuery = "DELETE FROM users WHERE id = ?";
    $deleteUserStatement = $connection->prepare($deleteUserQuery);
    if (!$deleteUserStatement) {
        die("Delete user statement preparation failed: " . $connection->error);
    }
    if (!$deleteUserStatement->execute([$userId])) {
        die("User deletion failed: " . $deleteUserStatement->error);
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
                        <h2 class="pull-left">Clients Details</h2>
                        <a href="add_client.php" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New Client</a>
                    </div>
                    <?php if (count($clients) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                            <tr>
                                
                                <td><?= $client['first_name'] ?></td>
                                <td><?= $client['last_name'] ?></td>
                                <td><?= $client['email'] ?></td>
                                <td><?= $client['phone_number'] ?></td>
                                <td>
                                    <a href="view_client.php?id=<?= $client['client_id'] ?>" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>
                                    <a href="update_client.php?id=<?= $client['client_id'] ?>" class="mr-3" title="Edit Record" data-toggle="tooltip"><span class="fa fa-pencil-alt"></span></a>
                                    <button type="button" style="border:none;" class="delete-client-btn" data-toggle="modal" data-target="#deleteClientModal" data-client-id="<?= $client['client_id'] ?>" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash text-danger"></span></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
    <!-- Modal for confirming client deletion -->
<div class="modal fade" id="deleteClientModal" tabindex="-1" role="dialog" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteClientModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this client?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteClientForm" method="post" name="deleteClientForm">

                    <button type="submit" class="btn btn-danger" name="delete_client">Delete</button>
                    <input type="hidden" name="client_id" id="deleteClientId">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function submitLogoutForm() {
            document.getElementById('logoutForm').submit();
        }
    </script>
    <script>
$(document).ready(function() {
    $('.delete-client-btn').click(function() {
        var clientId = $(this).data('client-id');
        $('#deleteClientId').val(clientId);
        console.log(clientId);
    });
});
</script>


</body>

</html>