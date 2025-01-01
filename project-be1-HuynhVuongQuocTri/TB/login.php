<?php
session_start();
require_once 'config/database.php';
spl_autoload_register(function ($className) {
    require_once "app/models/$className.php";
});

$categoryModel = new Category();
$categories = $categoryModel->all();

if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userModel = new User();
    $user = $userModel->login($username, $password);
    if ($user != false) {
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['userId'] = $user['id'];
        $_SESSION['roleId'] = $user['role_id'];
        header("Location: http://localhost/TB/index.php");
    } else {
        $_SESSION['notification'] = "Tài khoản hoặc mật khẩu không chính xác!";
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
        <?php
        if (!empty($_SESSION['notification'])) :
        ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['notification'] ?>
            </div>
        <?php
            $_SESSION['notification'] = '';
        endif;
        ?>
        <h1>Login</h1>
        <form action="login.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>

</html>