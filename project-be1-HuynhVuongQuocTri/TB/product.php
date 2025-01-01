<?php
session_start();
require_once 'config/database.php';
spl_autoload_register(function ($className) {
    require_once "app/models/$className.php";
});
$id = $_GET['id'];
$productModel = new Product();
$product = $productModel->find($id);

$categoryModel = new Category();
$categories = $categoryModel->all();
$commentModel = new Comment();


$recentView = [];

// Da vo nhieu lan
if (isset($_COOKIE['recentView'])) {
    $recentView = json_decode($_COOKIE['recentView']);
    // Neu so luong dat gioi han 5 san pham
    if (count($recentView) === 5) {
        array_shift($recentView);
    }
}
if (in_array($id, $recentView) == true) {
    $recentView = array_values(array_diff($recentView, [$id]));
}
array_push($recentView, $id);
setcookie('recentView', json_encode($recentView), time() + 3600 * 24);

if (!empty($_POST['comment'])) {
    if (isset($_SESSION['userId'])) {
        $commentModel->add($_POST['comment'], $id, $_SESSION['userId']);
    } else {
        header('http://localhost/TB/login.php');
    }
}
$comments = $commentModel->find($id);

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

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">NEW Express</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php
                    foreach ($categories as $category) :
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="category.php?id=<?php echo $category['id'] ?>"><?php echo $category['name'] ?></a>
                        </li>
                    <?php
                    endforeach;
                    ?>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <form class="d-flex" role="search" action="search.php" method="get">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="q">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                    <?php
                    if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) :
                    ?>
                        <li class="nav-item text-light">
                            Hello, <?php echo $_SESSION['username'] ?>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php
                    else :
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php
                    endif;
                    ?>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container">
        <div class="row">
            <div class="col-2">
            </div>
            <div class="col-8">
                <h1><?php echo $product['name'] ?></h1><br>
                <?php echo $product['description'] ?><br>
                <img src="public/images/<?php echo $product['image'] ?>" alt="" class="img-fluid"><br>
                <?php echo $product['description2'] ?>
                <ul>
                    <?php
                    foreach ($comments as $comment) :
                    ?>
                        <li><?php echo $comment['content'] ?></li>
                    <?php
                    endforeach;
                    ?>
                </ul>
                <form action="product.php?id=<?php echo $product['id'] ?>" method="post">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="comment" name="comment">
                        <button type="submit" class="btn btn-outline-primary">Send</button>
                    </div>
                </form>
            </div>
            <div class="col-2">
            </div>
        </div>
    </div>
</body>

</html>