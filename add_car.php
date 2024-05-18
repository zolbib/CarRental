<?php
include_once ('db_conn.php');
session_start();
$msg = '';
$userid = $_SESSION['user_id'];
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

$query = "SELECT owner_id FROM car_owners WHERE user_id = :userid";
$statement = $connection->prepare($query);
$statement->bindParam(':userid', $userid);
$statement->execute();
$ownerid = $statement->fetchColumn();

if (isset($_POST['logout'])) {
    session_destroy();

    header("Location: login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle other form inputs
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $color = $_POST['color'];
    $price = $_POST['price'];

    // Check if file is selected
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);

        // Specify the upload directory
        $uploadDirectory = 'cars/';

        // Move the uploaded file to the specified directory
        $destPath = $uploadDirectory . $fileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Insert the data into the database
            $query = "INSERT INTO cars (owner_id,brand, model, year, color, price, img) 
                      VALUES (:ownerid,:brand, :model, :year, :color, :price, :image_name)";
            $statement = $connection->prepare($query);
            $statement->bindParam(':ownerid',$ownerid);
            $statement->bindParam(':brand', $brand);
            $statement->bindParam(':model', $model);
            $statement->bindParam(':year', $year);
            $statement->bindParam(':color', $color);
            $statement->bindParam(':price', $price);
            $statement->bindParam(':image_name', $fileName);
            $statement->execute();

            header("Location: index_owner.php");
            exit();
        }
        } else {
            $msg = "Failed to move the uploaded file!";
        }
    } else {
        $msg = "No file selected or error occurred while uploading!";
    }



require_once('Layout/owner_layout.php');
?>


                <!-- Begin Page Content -->
                <div class="container-fluid">


                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Add New Car</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="brand">Brand:</label>
                                            <input type="text" class="form-control" id="brand" name="brand" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="model">Model:</label>
                                            <input type="text" class="form-control" id="model" name="model" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="year">Year:</label>
                                            <input type="number" class="form-control" id="year" name="year" min="1900"
                                                max="<?php echo date('Y'); ?>" step="1" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="color">Color:</label>
                                            <input type="text" class="form-control" id="color" name="color" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Price:</label>
                                            <input type="number" step="0.01" class="form-control" id="price"
                                                name="price" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="image">Image:</label>
                                            <div>
                                                <input type="file" class="form-control-file" id="image" name="image">
                                            </div>

                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                        <p id="form3Example5Help" class="form-text  text-success"><?=$msg?></p>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 ms-4">
                            <div class="d-flex justify-content-center">
                                <div>
                                    <img id="preview" src="#" alt="Image Preview"
                                        style="max-width: 400px; max-height: 400px; margin-top: 10px; display: none;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button id="removeImageBtn" type="button" class="btn btn-danger mt-2"
                                    style="display: none;">Remove Image</button>
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
        function previewImage(event) {
            var input = event.target;
            var preview = document.getElementById('preview');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }
    </script>
    <script>
        // Function to handle file input change event
        function handleFileInputChange(event) {
            var input = event.target;
            var preview = document.getElementById('preview');
            var removeBtn = document.getElementById('removeImageBtn');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    removeBtn.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Function to remove the selected image
        function removeImage() {
            var preview = document.getElementById('preview');
            var removeBtn = document.getElementById('removeImageBtn');
            var fileInput = document.getElementById('image');

            preview.src = '#';
            preview.style.display = 'none';
            removeBtn.style.display = 'none';
            fileInput.value = ''; // Clear the file input value
        }

        // Event listener for file input change event
        document.getElementById('image').addEventListener('change', handleFileInputChange);

        // Event listener for remove image button click event
        document.getElementById('removeImageBtn').addEventListener('click', removeImage);
    </script>


</body>

</html>