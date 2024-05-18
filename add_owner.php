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

// Define variables and initialize with empty values
$first_name = $last_name = $email = $username = $phone_number = "";
$first_name_err = $last_name_err = $username_err=  $email_err = $phone_number_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    $input_first_name = trim($_POST["first_name"]);
    if (empty($input_first_name)) {
        $first_name_err = "Please enter a first name.";
    } else {
        $first_name = $input_first_name;
    }

    // Validate last name
    $input_last_name = trim($_POST["last_name"]);
    if (empty($input_last_name)) {
        $last_name_err = "Please enter a last name.";
    } else {
        $last_name = $input_last_name;
    }

    // Validate email
    $input_email = trim($_POST["email"]);
    if (empty($input_email)) {
        $email_err = "Please enter an email.";
    } else {
        $email = $input_email;
    }

    // Validate phone number
    $input_phone_number = trim($_POST["phone_number"]);
    if (empty($input_phone_number)) {
        $phone_number_err = "Please enter a phone number.";
    } else {
        $phone_number = $input_phone_number;
    }

    // Check if username already exists
    $query_check_username = "SELECT id FROM users WHERE username = :username";
    $statement_check_username = $connection->prepare($query_check_username);
    $statement_check_username->bindParam(':username', $_POST['username']);
    $statement_check_username->execute();
    if ($statement_check_username->rowCount() > 0) {
        $username_err = "Username already exists.";
    }

    // Check input errors before inserting in database
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($phone_number_err) && empty($username_err)) {
        // Prepare an insert statement for users table
        $query = "INSERT INTO users (username, password , role) VALUES (:username, :password,'car_owner')";
        $statement = $connection->prepare($query);

        // Hash the password for security
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Bind parameters
        $statement->bindParam(':username', $_POST['username']);
        $statement->bindParam(':password', $hashedPassword);

        // Execute the statement
        $statement->execute();

        // Get the ID of the inserted user
        $userId = $connection->lastInsertId();

        // Prepare an insert statement for clients table
        $query = "INSERT INTO car_owners (user_id, email, first_name, last_name, phone_number) 
                  VALUES (:user_id, :email, :first_name, :last_name, :phone_number)";

        // Prepare and execute the second query
        $statement = $connection->prepare($query);
        $statement->bindParam(':user_id', $userId);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':phone_number', $phone_number);
        $statement->execute();

        // Redirect to landing page
        header("location: admin_owners.php");
        exit();
    }
}
require_once('Layout/admin_layout.php');

?>

                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="mt-5">Add car owner</h2>
                            <p>Please fill in the information below to add a new client.</p>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name"
                                        class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $first_name; ?>" required>
                                    <span class="invalid-feedback"><?php echo $first_name_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name"
                                        class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $last_name; ?>" required>
                                    <span class="invalid-feedback"><?php echo $last_name_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                    <span class="invalid-feedback text-danger"><?php echo $username_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $email; ?>" required>
                                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input pattern="[0-9]{10}" maxlength="10" required type="text" name="phone_number"
                                        class="form-control <?php echo (!empty($phone_number_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $phone_number; ?>">
                                        <small id="form3Example5Help" class="form-text text-muted" style="font-size: 0.7rem; margin-left: 13px;">Format: 06-00-11-22-33</small>
                                    <span class="invalid-feedback"><?php echo $phone_number_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <input type="submit" class="btn btn-primary" value="Submit">
                                <a href="admin_owners.php" class="btn btn-secondary ml-2">Cancel</a>
                            </form>
                        </div>
                    </div>

                    <!-- Content Row -->

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
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <input type="submit" class="btn btn-primary" name="logout" value="Logout">
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

</body>

</html>
