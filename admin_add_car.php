<?php
include_once ('db_conn.php');
session_start();
$msg = '';
$userid = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;

if ($role !== 'admin') {
    header('Location: login.php');
    exit();
}

if (!$username) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ownerid = $_POST['owner_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $color = $_POST['color'];
    $price = $_POST['price'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $uploadDirectory = 'cars/';
        $destPath = $uploadDirectory . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $query = "INSERT INTO cars (owner_id, brand, model, year, color, price, img) 
                      VALUES (:ownerid, :brand, :model, :year, :color, :price, :image_name)";
            $statement = $connection->prepare($query);
            $statement->bindParam(':ownerid', $ownerid);
            $statement->bindParam(':brand', $brand);
            $statement->bindParam(':model', $model);
            $statement->bindParam(':year', $year);
            $statement->bindParam(':color', $color);
            $statement->bindParam(':price', $price);
            $statement->bindParam(':image_name', $fileName);
            $statement->execute();

            header("Location: index_owner.php");
            exit();
        } else {
            $msg = "Failed to move the uploaded file!";
        }
    } else {
        $msg = "No file selected or error occurred while uploading!";
    }
}

$query = "SELECT owner_id, first_name, last_name FROM car_owners";
$statement = $connection->prepare($query);
$statement->execute();
$owners = $statement->fetchAll(PDO::FETCH_ASSOC);

require_once('Layout/admin_layout.php');
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
                            <label for="owner_id">Owner:</label>
                            <select class="form-control" id="owner_id" name="owner_id" required>
                                <?php foreach ($owners as $owner): ?>
                                    <option value="<?php echo $owner['owner_id']; ?>"><?php echo $owner['first_name']." ".$owner['last_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
                            <input type="number" class="form-control" id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" step="1" required>
                        </div>
                        <div class="form-group">
                            <label for="color">Color:</label>
                            <input type="text" class="form-control" id="color" name="color" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="image">Image:</label>
                            <div>
                                <input type="file" class="form-control-file" id="image" name="image" onchange="previewImage(event)">
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                        <p class="form-text text-success"><?php echo $msg; ?></p>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 ms-4">
            <div class="d-flex justify-content-center">
                <div>
                    <img id="preview" src="#" alt="Image Preview" style="max-width: 400px; max-height: 400px; margin-top: 10px; display: none;">
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <button id="removeImageBtn" type="button" class="btn btn-danger mt-2" style="display: none;" onclick="removeImage()">Remove Image</button>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<script>
function previewImage(event) {
    var input = event.target;
    var preview = document.getElementById('preview');
    var removeBtn = document.getElementById('removeImageBtn');

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            removeBtn.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    var preview = document.getElementById('preview');
    var removeBtn = document.getElementById('removeImageBtn');
    var fileInput = document.getElementById('image');

    preview.src = '#';
    preview.style.display = 'none';
    removeBtn.style.display = 'none';
    fileInput.value = '';
}
</script>
