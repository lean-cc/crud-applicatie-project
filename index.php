<?php

include_once("includes/connection.php");
//this query selects all the information from the videos table, profilepic
// and username from the users table and orders the videos displayed by random
$query =
    "SELECT videos.*, users.username, users.profilePic FROM videos 
          JOIN users ON videos.userId = users.userId
          ORDER BY RAND()";
$result = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - home</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php
    include_once("includes/nav.php");
    include_once("includes/aside.php");
    ?>

    <main>
        <div class="video-containermain">
            <?php
            //this creates the video boxes and displays the video's on the index page
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="videomain">
                        <a href="video?v=' . htmlspecialchars($row['videoId']) . '">
                            <video oncontextmenu="return false;" muted src="usercontent/'. htmlspecialchars($row['videoUrl']) .'"><video>
                        </a>
                        <h2>' . htmlspecialchars($row['title']) . '</h2>
                        <a href="profile?p=' . htmlspecialchars($row['username']) .'" class="user-infomain">
                            <img src="' . htmlspecialchars($row['profilePic']) . '" alt="Profile Picture">
                            <p>' . htmlspecialchars($row['username']) .  '</p>
                        </a>
                    </div>';
            }
            ?>
        </div>
    </main>