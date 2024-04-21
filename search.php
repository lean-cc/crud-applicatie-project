<?php
// it is linked here with the connection.php
include_once("includes/connection.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - search</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php
    // it is linked here with the navbar and the aside
    include_once("includes/nav.php");
    include_once("includes/aside.php");
    ?>

    <main>
        <div class="video-containermain">
            <?php

            if (!$pdo) {
                die("Connection failed: " . $pdo->errorInfo()[2]);
            }
            // Handling search functionality
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                $zoekterm = '%' . $_GET['q'] . '%';
                // this is the query to search for the video based on the title or description
                $sql = "SELECT videos.*, users.username, users.profilePic 
                FROM videos 
                JOIN users ON videos.userId = users.userId 
                WHERE videos.title LIKE :term OR videos.description LIKE :term LIMIT 15;";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':term', $zoekterm, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // you display here the search results
                if ($result) {
                    foreach ($result as $row) {
                        echo '<div class="videomain">
                        <a href="video?v=' . htmlspecialchars($row['videoId']) . '">
                            <video oncontextmenu="return false;" muted src="usercontent/' . htmlspecialchars($row['videoUrl']) . '"><video>
                        </a>
                        <h2>' . htmlspecialchars($row['title']) . '</h2>
                        <a href="profile?p=' . htmlspecialchars($row['username']) . '" class="user-infomain">
                            <img src="' . htmlspecialchars($row['profilePic']) . '" alt="Profile Picture">
                            <p>' . htmlspecialchars($row['username']) .  '</p>
                        </a>
                    </div>';
                    }
                } else {
                    echo "No results found";
                }
            } else {
                echo "Please provide a search term";
            }

            ?>
        </div>
    </main>

</body>

</html>