<?php
session_start();
require_once '../../config/database.php';
spl_autoload_register(function ($className) {
    require_once "../../app/models/$className.php";
});

$categoryModel = new Category();
$categories = $categoryModel->all();

if (!empty($_POST['name']) && !empty($_POST['preview']) && !empty($_POST['description']) && !empty($_POST['description2']) && !empty($_FILES['image']) && !empty($_POST['category-id'])) {
    $productModel = new Product();
    $name = $_POST['name'];
    $preview = $_POST['preview'];
    $description = $_POST['description'];
    $description2 = $_POST['description2'];
    $categoryId = $_POST['category-id'];

    $image = hash('sha256', time() . rand()) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $path = '../../public/images/' . $image;
    if (is_uploaded_file($_FILES['image']['tmp_name']) && move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
        if ($productModel->add($name, $preview, $description, $description2, $image, $categoryId)) {
            $_SESSION['notification'] = "Thêm thành công";
            header("Location: http://localhost/TB/admin/products/index.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h1>Add Product</h1>
        <form action="add.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="mb-3">
                <label for="preview" class="form-label">preview</label>
                <input type="text" class="form-control" id="preview" name="preview">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" name="description">
            </div>
            <div class="mb-3">
                <label for="description2" class="form-label">Description2</label>
                <input type="text" class="form-control" id="description2" name="description2">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <?php
                foreach ($categories as $category) :
                ?>
                    <input type="checkbox" class="btn-check" id="category-<?php echo $category['id'] ?>" autocomplete="off" value="<?php echo $category['id'] ?>" name="category-id[]">


                    <label class="btn btn-outline-primary" for="category-<?php echo $category['id'] ?>"><?php echo $category['name'] ?></label>
                <?php
                endforeach;
                ?>

            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
</body>

</html>