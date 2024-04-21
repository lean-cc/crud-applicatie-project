<?php
// it is linked here with the connection.php
include_once("includes/connection.php");

if (isset($_SESSION['loggedInUser'])) {
    header("Location: index.php");
    die();
}

unset($_SESSION['error']);
// Handling form submission for registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repeatPassword = $_POST['repeat'];

    if ($password !== $repeatPassword) {
        $_SESSION['error'] = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Preparing SQL query to insert new user into database
        $query = $pdo->prepare("INSERT INTO users (username, password, profilePic, registrationDate) VALUES (:username, :password, 'assets/images/default.jpg', CURRENT_DATE)");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        // Executing the query
        if ($query->execute()) {
            header("Location: login.php");
            die();
        } else {
        // Setting error message if registration fails
            $_SESSION['error'] = "Error registering user.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - register</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php
// it is linked here with the navbar and aside
    include_once("includes/nav.php");
    include_once("includes/aside.php");

    ?>
<!-- // this is the form for the register -->
    <main class="registerloginmain">
        <form class="registerloginform" method="post">
            <?php if (isset($_SESSION['error'])) { ?>
                <div style="color: red;"><?= $_SESSION['error']; ?></div>
            <?php } ?>
            <!-- Input fields for username, password, and repeat password -->            
            <input class="registerloginfield" autofocus minlength="2" maxlength="25" type="text" name="username" placeholder="Username" required>
            <input class="registerloginfield" minlength="5" maxlength="256" type="password" name="password" placeholder="Password" required>
            <input class="registerloginfield" minlength="5" maxlength="256" type="password" name="repeat" placeholder="Repeat Password" required>
            <!-- Register button and link to login page -->
            <button name="register" type="submit">Register</button>
            <a class="registerlogin" href="login.php">Login</a>
        </form>
    </main>
</body>

</html>