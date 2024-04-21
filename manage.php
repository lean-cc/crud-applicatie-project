<?php
// it is linked here with the connection.php
include_once("includes/connection.php");

// if the user is not logged in, it will redirect to the index page
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: index.php");
    die();
}
$userId = $_SESSION['loggedInUser'];

// check if the video id is set in the url
if (isset($_GET['v'])) {
    $videoID = $_GET['v'];
    // query for getting all the data from a video
    $query = "SELECT * FROM videos WHERE videoId = :videoId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':videoId', $videoID, PDO::PARAM_STR);
    $stmt->execute();
    $video = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($video)) {
        header("Location: manage.php");
    }
}
try {
    // query for getting all the videos from the user
    $query = "SELECT * FROM videos WHERE userId = :profile ORDER BY videoId DESC";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':profile', $userId, PDO::PARAM_STR);
    $stmt->execute();
    $videoData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}

// if the form is submitted, it will update the video description and name
//check if the user is the owner of the video
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save']) && isset($_SESSION['loggedInUser']) && $video['userId'] == $_SESSION['loggedInUser']) {
    $title = $_POST['name'];
    $description = $_POST['description'];
    // query for updating the video
    $updateQuery = $pdo->prepare("UPDATE videos SET `title` = :title, `description` = :description WHERE videos.`videoId` = :videoId;");
    $updateQuery->bindParam(':title', $title, PDO::PARAM_STR);
    $updateQuery->bindParam(':description', $description, PDO::PARAM_STR);
    $updateQuery->bindParam(':videoId', $videoID, PDO::PARAM_STR);

    try {
        $updateQuery->execute();
        header("Location: manage.php");
    } catch (\PDOException $th) {
        error_log($th);
        die();
    }
}

// if the delete button is clicked, it will delete the video
//check if the user is the owner of the video
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']) && isset($_SESSION['loggedInUser']) && $video['userId'] == $_SESSION['loggedInUser']) {
    $tables = ['comments', 'likes', 'history', 'videos'];
    
    foreach ($tables as $table) {
        $deleteQuery = $pdo->prepare("DELETE FROM $table WHERE videoId = :videoId");
        $deleteQuery->bindParam(':videoId', $videoID, PDO::PARAM_INT);
        
        try {
            $deleteQuery->execute();
            header("Location: manage.php");
        } catch (\PDOException $th) {
            error_log($th);
            die();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - manage</title>
    <link rel="stylesheet" href="assets/styles/style.css">

</head>

<body>
    <?php
    // it is linked here with the nav.php and the aside.php.
    include_once("includes/nav.php");
    include_once("includes/aside.php");
    ?>
    <main class="main-manage">
        <div class="manage-vid-form">

            <?php
            // get the video id from the url
            if (!isset($_GET["v"])) {
                echo "<h3>Choose a video to manage</h3>";
                //check if the user is the owner of the video
            } elseif ($video['userId'] == $_SESSION['loggedInUser']) {
                // display the form to manage the video
                echo '<h3>Manage video: ' . htmlspecialchars($video['title']) . '</h3>
                        <form class="manage" method="POST">
                        <input type="text" name="name" value="' . htmlspecialchars($video['title']) . '">
                        <textarea name="description">' . htmlspecialchars($video['description']) . '</textarea>
                        <div><button type="submit" name="save"><svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="green"/>
                        </svg></button>
                        <button type="submit" name="delete"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                      </svg></button></div>
                        </form>';
            } else {
                echo "<h3>You don't have permission to manage this video</h3>";
            }

            ?>

        </div>
        <div class="manage-vid-list">
            <?php
            // loop through all the videos and displaying them
            foreach ($videoData as $row) {
                echo '<div class="videomain">
                    <a href="?v=' . htmlspecialchars($row['videoId']) . '">
                    <video oncontextmenu="return false;" muted src="usercontent/' . htmlspecialchars($row['videoUrl']) . '"><video>
                    </a>
                    <h2>' . htmlspecialchars($row['title']) . '</h2>
                    </div>';
            }
            ?>
        </div>
    </main>
</body>

</html>