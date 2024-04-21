<?php
// it is linked here with connection.php
include_once("includes/connection.php");

if (!isset($_SESSION['loggedInUser'])) {
    header("Location: index.php");
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - settings</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php
    // it is linked here with the navbar and the aside
    include_once("includes/nav.php");
    include_once("includes/aside.php");
    ?>

    <main>
        <div class="forms-container">
            <form method="post" enctype="multipart/form-data">
                <h4>Avatar</h4>
                <input style="padding: 2px;" type="file" name="avatar">
                <button type="submit" name="submit">Upload</button>
                <button type="submit" name="delete">Delete</button>
            </form>

            <form method="post">
                <h4>Description</h4>
                <textarea name="description" placeholder="Your description.."><?= htmlspecialchars($user['description']) ?></textarea>
                <button type="submit">Submit</button>
            </form>
            <!-- form for password change -->
            <form method="post">
                <h4>Password</h4>
                <input type="password" placeholder="Current Password" name="current_password" required><br><br>
                <input type="password" placeholder="New Password" name="new_password" required><br><br>
                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>

        <?php
        //Code for the description
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["description"]) && isset($_SESSION['loggedInUser'])) {
            $query = $pdo->prepare("UPDATE `users` SET `description` = :description WHERE `users`.`userId` = :user;");
            $query->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
            $query->bindParam(':user', $user['userId'], PDO::PARAM_STR);
            //executes the query
            if ($query->execute()) {
                echo "Updated your description succesfully.";
            } else {
                echo "Error updating the database.";
            }
        }
        //Code for delete avatar
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"]) && isset($_SESSION['loggedInUser'])) {
            $target_file = "assets/images/default.jpg";
            $query = $pdo->prepare("UPDATE `users` SET `profilePic` = :pfp WHERE `users`.`userId` = :user;");
            $query->bindParam(':pfp', $target_file, PDO::PARAM_STR);
            $query->bindParam(':user', $user['userId'], PDO::PARAM_STR);
            //executes the query
            if ($query->execute()) {
                echo "Deleted your profile picture succesfully.";
            } else {
                echo "Error updating the database.";
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"]) && isset($_SESSION['loggedInUser'])) {

            $target_dir = "usercontent/pfp/";
            $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $error_message = "";

            // Check if a file is selected
            if ($_FILES["avatar"]["size"] == 0) {
                $error_message .= "Please select a file.";
                $uploadOk = 0;
            } else {
                // Allow certain file formats
                if (
                    $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif"
                ) {
                    $error_message .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                } else {
                    // Check if the selected file is an image
                    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
                    if ($check === false) {
                        $error_message .= "File is not a valid image.";
                        $uploadOk = 0;
                    }
                }
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["avatar"]["size"] > 8000000) {
                $error_message .= "Sorry, your file is too large. (8mb max)";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                $newFileName = $user['username'] . "-" . $user['userId'] . "." . $imageFileType; // Use the username as the file name
                $target_file = $target_dir . $newFileName;

                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    // Update the database with the new profile picture path
                    $query = $pdo->prepare("UPDATE `users` SET `profilePic` = :pfp WHERE `users`.`userId` = :user;");
                    $query->bindParam(':pfp', $target_file, PDO::PARAM_STR);
                    $query->bindParam(':user', $user['userId'], PDO::PARAM_STR);

                    if ($query->execute()) {
                        echo "Your new profile picture is set!";
                    } else {
                        echo "Error updating the database.";
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo $error_message;
            }
        }
        // checks if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"]) && isset($_SESSION['loggedInUser'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            // Prepare and execute a query to fetch the current hashed password from the database
            $query = $pdo->prepare("SELECT `password` FROM `users` WHERE `userId` = :userId;");
            $query->execute([':userId' => $user['userId']]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            // Verifies the current password with the one in the database
            if (password_verify($current_password, $result['password'])) {
                // hash the new password if the current password is correct
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                // Prepare and execute a query to update the password in the database
                $update_query = $pdo->prepare("UPDATE `users` SET `password` = :password WHERE `userId` = :userId;");
                if ($update_query->execute([':password' => $hashed_password, ':userId' => $user['userId']])) {
                    // Message for if the password change is succesfull
                    echo "Password changed successfully!";
                } else {
                    // Message for if there is an issue
                    echo "Something went wrong.";
                }
            } else {
                // message for if the current password is incorrect
                echo "Incorrect current password.";
            }
        }
        ?>

    </main>
</body>

</html>