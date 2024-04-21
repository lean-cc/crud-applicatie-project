<?php
// it is linked here with the connection.php
include_once("includes/connection.php");

// this is the query for the videos
$query = "SELECT videos.*, users.username, users.profilePic, COUNT(likes.likeId) AS totalLikes 
          FROM videos 
          JOIN users ON videos.userId = users.userId
          LEFT JOIN likes ON videos.videoId = likes.videoId
          GROUP BY videos.videoId
          ORDER BY totalLikes DESC 
          LIMIT 10";

$result = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - trending</title>
    <link rel="stylesheet" href="assets/styles/style.css">

</head>

<body>
    <?php
    // it is linked here with the nav.php and the aside.php.
    include_once("includes/nav.php");
    include_once("includes/aside.php");
    ?>
    <main>
        <div class="video-containermain trending">
            <?php
            $counter = 1;
            // Looping through the fetched results here
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

                // You display here through the video informations
                echo '<div class="videomain-T" class="rowclass" style="position: relative;">
                        <div class="video-number">#' . $counter . '</div>
                        <a href="video?v=' . htmlspecialchars($row['videoId']) . '">
                            <video oncontextmenu="return false;" muted src="usercontent/' . htmlspecialchars($row['videoUrl']) . '"><video>
                        </a>

                        <div class="videomain-T-info">
                            <h2>' . htmlspecialchars($row['title']) . '</h2>

                            <a href="profile?p=' . htmlspecialchars($row['username']) . '" class="user-infomain">
                            <p>' . htmlspecialchars($row['username']) .  '</p>
                            </a>

                            <p class="desc-T">' . substr(htmlspecialchars($row['description']), 0, 200) . '... </p>
                        </div>

                    </div>';

                // Here is the counter for the left hook numbers
                $counter++;
            }
            ?>
        </div>
    </main>
</body>

</html>