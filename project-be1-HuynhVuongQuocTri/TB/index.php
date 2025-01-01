<?php
session_start();

require_once 'config/database.php';
spl_autoload_register(function ($className) {
    require_once "app/models/$className.php";
});
$productModel = new Product();
//$products = $productModel->all();

$categoryModel = new Category();
$categories = $categoryModel->all();

if (isset($_POST['btn-like'])) {
    $id = $_POST['btn-like'];

    $likedProduct = [];
    // Da vo truoc đó
    if (isset($_COOKIE['likedProduct'])) {
        $likedProduct = json_decode($_COOKIE['likedProduct']);
    }
    if (in_array($id, $likedProduct) == false) {
        $productModel->like($_POST['btn-like']);
        array_push($likedProduct, $id);
        setcookie('likedProduct', json_encode($likedProduct), time() + 3600 * 24);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>project Trang báo điện tử</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


</head>

<body style="background-color:#189;">

    <header class="bg">
        <div class="container">
            <?php
            echo date("l,");
            echo date("d/m/Y");
            ?>
        </div>
    </header>


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
        <?php
        foreach ($categories as $category) :
            $products = $productModel->findByCategory($category['id'], 4);
        ?>
            <h2><?php echo $category['name'] ?></h2>

            <div class="border rounded p-3">
                <div class="row row-cols-8 row-cols-md-4 g-4">

                    <?php
                    foreach ($products as $product) :
                    ?>
                        <div class="col">
                            <div class="card">
                                <img src="public/images/<?php echo $product['image'] ?>" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title"> <a href="product.php?id=<?php echo $product['id'] ?>"><?php echo $product['name'] ?></a>
                                    </h5>
                                    <p class="card-text"><?php echo $product['preview'] ?></p>
                                    <form action="index.php" method="post">
                                        <button type="submit" class="btn btn-outline-danger" name="btn-like" value="<?php echo $product['id'] ?>">&hearts;
                                            <?php echo $product['likes'] ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php
                    endforeach;
                    ?>
                </div>
            </div>

        <?php
        endforeach;
        ?>
    </div>
    </div>

</body>

</html>