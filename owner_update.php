<?php
$msg = '';
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
if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];

    // Fetch car details from the database based on car_id
    $query = "SELECT * FROM cars WHERE id = :car_id";
    $statement = $connection->prepare($query);
    $statement->bindParam(':car_id', $car_id);
    $statement->execute();
    $car = $statement->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST["submit"])) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $color = $_POST['color'];
    $price = $_POST['price'];

    // Check if file is selected
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];

        // Specify the upload directory
        $uploadDirectory = 'cars/';

        // Move the uploaded file to the specified directory
        $destPath = $uploadDirectory . $fileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Update the data in the database
            $query = "UPDATE cars 
                      SET brand = :brand, model = :model, year = :year, color = :color, price = :price, img = :image_name 
                      WHERE id = :car_id";
            $statement = $connection->prepare($query);
            $statement->bindParam(':brand', $brand);
            $statement->bindParam(':model', $model);
            $statement->bindParam(':year', $year);
            $statement->bindParam(':color', $color);
            $statement->bindParam(':price', $price);
            $statement->bindParam(':image_name', $fileName);
            $statement->bindParam(':car_id', $car_id);
            $statement->execute();

            header("Location: my_cars.php");
            exit();
        } else {
            $msg = "Failed to move the uploaded file!";
        }
    } else {
        $query = "UPDATE cars 
        SET brand = :brand, model = :model, year = :year, color = :color, price = :price
        WHERE id = :car_id";
        $statement = $connection->prepare($query);
        $statement->bindParam(':brand', $brand);
        $statement->bindParam(':model', $model);
        $statement->bindParam(':year', $year);
        $statement->bindParam(':color', $color);
        $statement->bindParam(':price', $price);
        $statement->bindParam(':car_id', $car_id);
        $statement->execute();
        header("Location: my_cars.php");
        exit();
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index_owner.php">
                <div class="sidebar-brand-icon">
                    <img style="width:50px;" src="logos/only-logo-no-background.png" alt="">
                </div>
                <div class="sidebar-brand-text mx-3 text-light"><img class="img-fluid"
                        src="logos/text-logo-no-background.png" alt=""></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index_owner.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Home</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-car"></i>
                    <span>My cars</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Options:</h6>
                        <a class="collapse-item" href="my_cars.php">Edit cars</a>
                        <a class="collapse-item" href="add_car.php">Add my car</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-dollar-sign "></i>
                    <span>Earnings</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">




            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>


        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">






                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $username ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">


                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Edit my Car</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="brand">Brand:</label>
                                            <input type="text" class="form-control" id="brand" name="brand" required
                                                value="<?= $car['brand'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="model">Model:</label>
                                            <input type="text" class="form-control" id="model" name="model"
                                                value="<?= $car['model'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="year">Year:</label>
                                            <input type="number" value="<?= $car['year'] ?>" class="form-control"
                                                id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" step="1"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="color">Color:</label>
                                            <input type="text" class="form-control" id="color" name="color"
                                                value="<?= $car['color'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Price:</label>
                                            <input type="number" step="0.01" class="form-control" id="price"
                                                name="price" value="<?= $car['price'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="image">Image:</label>
                                            <div>
                                                <input type="file" class="form-control-file" id="image" name="image"
                                                    value="cars/<?= $car['img'] ?>">
                                            </div>

                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>

                                        <a href="my_cars.php" type="submit" name="submit"
                                            class="btn btn-danger">cancel</a>
                                        <p id="form3Example5Help" class="form-text  text-success"><?= $msg ?></p>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 ms-4">
                            <div class="d-flex justify-content-center">
                                <div>
                                    <hr>
                                    <h1 class="title text-primary">Image Preview :</h1>
                                    <hr>
                                    <img id="preview" src="cars/<?= $car['img'] ?>" alt="Image Preview"
                                        style="max-width: 400px; max-height: 400px; margin-top: 10px; display: block;">
                                    <hr>
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