<?php 
$errormsg = '';
include 'db_conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $username = $_POST['username'];
  $password = $_POST['password'];

  $query = "SELECT password FROM users WHERE username = :username";
  $statement = $connection->prepare($query);
  $statement->bindParam(':username', $username);
  $statement->execute();
  $hashedPassword = $statement->fetchColumn();

  if (password_verify($password, $hashedPassword)) {
    $query = "SELECT role, id FROM users WHERE username = :username";
    $statement = $connection->prepare($query);
    $statement->bindParam(':username', $username);
    $statement->execute();
    
    $userData = $statement->fetch(PDO::FETCH_ASSOC);
    if ($userData) {
        $role = $userData['role'];
        $userId = $userData['id'];
    }
      session_start();
      $_SESSION['user_id'] = $userId;
      $_SESSION['username'] = $username;
      $_SESSION['role']=$role;
      if ($role === 'regular') {
        header("Location: index.php");
      }
      elseif($role === 'admin') {
        header("Location: index_admin.php");
      }
      elseif ($role === 'car_owner') {
        header("Location: index_owner.php");
      }
      
      exit();
  } else {

      $errormsg= "Invalid username or password.";
  }
}



?><!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Login</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <style>
        .bg-login-image{
            background-image: url(logos/logo-no-background.png);
            background-position: center;
            background-size: 400px;
            background-repeat: no-repeat;
        }
        .form-control-user{
          padding: 0.375rem 0.75rem;
        }
    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" method="POST">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter username...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password"  class="form-control form-control-user" 
                                                id="exampleInputPassword" placeholder="Password">
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                       
                                    </form>
                                    <small class="text-danger"> <?=$errormsg ?></small>
                                    <hr>

                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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