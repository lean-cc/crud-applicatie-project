<?php
// it is linked here with the connection.php
include_once("includes/connection.php");

if (!isset($_SESSION['loggedInUser'])) {
    header("Location: index.php");
    die();
}
//upload section
if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['submitVideo']) && isset($_SESSION['loggedInUser'])) {
    $target_dir = "usercontent/videos/"; //The target directory
    $filename = basename($_FILES["fileToUpload"]["name"]);
    $FileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $newFilename = $_SESSION['loggedInUser'] . "_" . $_POST['title'] . '.mp4'; //The new file name
    $newFilePath = $target_dir . $newFilename;

    if ($_FILES["fileToUpload"]["size"] > 500000000) {    //checks the file size
        echo "File is too large (500mb)";
    } elseif ($FileType != "mp4") {    //checks the filetype
        echo  "Incorrect file type.";
    } elseif (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newFilePath)) { //Moves the video from temp dir to usercontent folder with a new name
            $videoUrl = "videos/" . $newFilename;
            //Insert query to add the video into the database
            $query = $pdo->prepare("INSERT INTO videos (userId, title, description, uploadDate, videoUrl) VALUES (:id, :title, :description, CURRENT_DATE, :url)");
            $query->bindParam(':id', $_SESSION['loggedInUser']);
            $query->bindParam(':title', $_POST['title']);
            $query->bindParam(':description', $_POST['description']);
            $query->bindParam(':url', $videoUrl);

            try {
                $query->execute(); //Executes the query
                header("Location: manage.php");
            } catch (\PDOException $th) {
                error_log($th);
                die();
            }
        } else {
            echo "Unknown error.";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - upload</title>
    <link rel="stylesheet" href="assets/styles/style.css">

</head>

<body>
    <?php

    // it is linked here with the nav.php and the aside.php.
    include_once("includes/nav.php");
    include_once("includes/aside.php");

    ?>
    <main>
        <form class="videoform" method="post" enctype="multipart/form-data">
            <label for="video" class="drop-container" id="dropcontainer">
                <span class="drop-title">Drop your video file here</span>
                or
                <input name="fileToUpload" type="file" id="video" accept="video/*" required>
            </label>

            <input type="text" name="title" placeholder="Title here.." required>
            <textarea name="description" rows="7" placeholder="Description here.." required></textarea>

            <button name="submitVideo" type="submit">Upload</button>
        </form>
    </main>

    <script src="assets/js/drop.js"></script>
</body>

</html>