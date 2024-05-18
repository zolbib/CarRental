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

// $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// if (!empty($searchTerm)) {
//     $query = "SELECT * FROM clients WHERE CONCAT(first_name, ' ', last_name) LIKE '%$searchTerm%'";
// } else {
//     $query = "SELECT * FROM clients";
// }

// $statement = $connection->prepare($query);
// if (!$statement) {
//     die("Query preparation failed: " . $connection->error);
// }

// if (!$statement->execute()) {
//     die("Query execution failed: " . $statement->error);
// }

// $clients = $statement->fetchAll(PDO::FETCH_ASSOC);   

// Define variables and initialize with empty values
$first_name = $last_name = $email = $phone_number = "";
$first_name_err = $last_name_err = $email_err  =$phone_number_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate first name
    $input_first_name = trim($_POST["first_name"]);
    if(empty($input_first_name)){
        $first_name_err = "Please enter a first name.";
    } else{
        $first_name = $input_first_name;
    }
    
    // Validate last name
    $input_last_name = trim($_POST["last_name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter a last name.";     
    } else{
        $last_name = $input_last_name;
    }
    
    // Validate email
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $email_err = "Please enter an email.";     
    } else{
        $email = $input_email;
    }
    
    // Validate phone number
    $input_phone_number = trim($_POST["phone_number"]);
    if(empty($input_phone_number)){
        $phone_number_err = "Please enter a phone number.";     
    } else{
        $phone_number = $input_phone_number;
    }
    
    // Check input errors before inserting in database
    if(empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($phone_number_err)){
        // Prepare an update statement
        $sql = "UPDATE car_owners SET first_name=?, last_name=?, email=?, phone_number=? WHERE owner_id=?";
         
        if($stmt = $connection->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(1, $param_first_name, PDO::PARAM_STR);
            $stmt->bindParam(2, $param_last_name, PDO::PARAM_STR);
            $stmt->bindParam(3, $param_email, PDO::PARAM_STR);
            $stmt->bindParam(4, $param_phone_number, PDO::PARAM_STR);
            $stmt->bindParam(5, $param_id, PDO::PARAM_INT);
            
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_email = $email;
            $param_phone_number = $phone_number;
            $param_id = $_GET["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                header("location: admin_owners.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($connection);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM car_owners WHERE owner_id = ?";
        if($stmt = $connection->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(1, $param_id, PDO::PARAM_INT);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if($result){
                    // Retrieve individual field value
                    $first_name = $result["first_name"];
                    $last_name = $result["last_name"];
                    $email = $result["email"];
                    $phone_number = $result["phone_number"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        unset($stmt);
        
        // Close connection
        unset($connection);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
require_once ('Layout/Admin_layout.php')

?>


                <!-- Begin Page Content -->
                <div class="container-fluid">

                <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Edit Client</h2>
                    <p>Please edit the input values and submit to update the client record.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $_GET["id"]); ?>" method="post">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $first_name; ?>" required>
                            <span class="invalid-feedback"><?php echo $first_name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>" required>
                            <span class="invalid-feedback"><?php echo $last_name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                            <span class="invalid-feedback"><?php echo $email_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input pattern="[0-9]{10}" maxlength="10" required type="text" name="phone_number" class="form-control <?php echo (!empty($phone_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone_number; ?>">
                            <span class="invalid-feedback"><?php echo $phone_number_err;?></span>
                            <small id="form3Example5Help" class="form-text text-muted" style="font-size: 0.7rem; margin-left: 13px;">Format: 06-00-11-22-33</small>

                        </div>
                        <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="admin_clients.php" class="btn btn-secondary ml-2">Cancel</a>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function submitLogoutForm() {
            document.getElementById('logoutForm').submit();
        }
    </script>

</body>

</html>