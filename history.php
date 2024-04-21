<?php
// it is linked here with the connection.php
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
    <title>4tube - history</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php
    // it is linked here with the navbar and the aside
    include_once("includes/nav.php");
    include_once("includes/aside.php");

    try {
        // this is the query for the videos
        $query = "SELECT history.*, videos.*, users.username, users.profilePic
            FROM history
            JOIN videos ON history.videoId = videos.videoId
            JOIN users ON videos.userId = users.userId
            WHERE history.userId = :user
            ORDER BY history.historyId DESC;
            ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user', $_SESSION['loggedInUser'], PDO::PARAM_STR);
        $stmt->execute();
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }

    ?>
<!-- You display here through the information of the videos -->
    <main class="main-H">
        <?php
        if (empty($history)) {
            echo "<h3>No history found</h3>";
        } else {
            echo '<div class="video-containermain-H">';
            foreach ($history as $row) {
                echo '<div class="videomain-H" class="rowclass">
                        <a href="video?v=' . htmlspecialchars($row['videoId']) . '">
                            <video oncontextmenu="return false;" muted src="usercontent/' . htmlspecialchars($row['videoUrl']) . '"><video>
                        </a>

                        <div class="videomain-H-info">
                            <h2>' . htmlspecialchars($row['title']) . '</h2>

                            <a href="profile?p=' . htmlspecialchars($row['username']) . '" class="user-infomain">
                            <p>' . htmlspecialchars($row['username']) .  '</p>
                            </a>

                            <p class="desc-H">' . substr(htmlspecialchars($row['description']), 0, 80) . '... </p>
                        </div>

                    </div>';
            }
            echo '</div>';
        }
        ?>

    </main>
</body>

</html>