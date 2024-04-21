<?php
// it is linked with the connection.php here
include_once("includes/connection.php");

if (isset($_SESSION['loggedInUser'])) {
    header("Location: index.php");
    die();
}

unset($_SESSION['error']);

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Preparing the SQL query to fetch user data by username    
    $query = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();

    // Fetching user data
    $user = $query->fetch();

    if ($user !== false) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedInUser'] = $user['userId'];
            header("Location: index.php");
            die();
        }
    }
    // Setting error message if login fails
    $_SESSION['error'] = "Username or password is invalid.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - login</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php
// it is linked here with the navbar and the aside
    include_once("includes/nav.php");
    include_once("includes/aside.php");

    ?>
<!-- the form for the login -->
    <main class="registerloginmain">
        <form class="registerloginform" method="post">

            <?php if (isset($_SESSION['error'])) { ?>
                <div style="color: red;"><?= $_SESSION['error']; ?></div>
            <?php } ?>

            <input class="registerloginfield" autofocus minlength="2" maxlength="25" type="text" name="username" placeholder="Username" required>
            <input class="registerloginfield" minlength="5" maxlength="256" type="password" name="password" placeholder="Password" required>
<!-- login button and link to the register page -->
            <button type="submit">Login</button>
            <a href="register.php">Register</a>
        </form>
    </main>
</body>

</html>