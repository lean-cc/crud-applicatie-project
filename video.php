<?php
// it is linked here with the connection.php
include_once("includes/connection.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - Video</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php
    // it is linked here with the navbar and the aside
    include_once("includes/nav.php");
    include_once("includes/aside.php");

    ?>

    <main>
        <?php

        try {
            if (isset($_GET['v'])) {
                $video = $_GET['v'];
            }

            // video history managment
            if (isset($_SESSION['loggedInUser']) && isset($_GET['v'])) {
                $date = date("d/m/Y");
                $historyQuery = "INSERT INTO history (userId, videoId, date) VALUES (:user, :video, :date);";
                $historyStmt = $pdo->prepare($historyQuery);
                $historyStmt->bindParam(':user', $_SESSION['loggedInUser'], PDO::PARAM_STR);
                $historyStmt->bindParam(':date', $date, PDO::PARAM_STR);
                $historyStmt->bindParam(':video', $video, PDO::PARAM_STR);
                try {
                    $historyStmt->execute();
                } catch (\PDOException $th) {
                    error_log($th);
                    die();
                }
            }
            // Retrieve video details
            $Query = "SELECT v.*, u.username, u.profilePic, 
                    SUM(CASE WHEN l.type = 1 THEN 1 ELSE 0 END) AS likes,
                    SUM(CASE WHEN l.type = 0 THEN 1 ELSE 0 END) AS dislikes
                    FROM videos v
                    JOIN users u ON v.userId = u.userId
                    LEFT JOIN likes l ON v.videoId = l.videoId
                    WHERE v.videoId = :video
                    GROUP BY v.videoId;";
            $Stmt = $pdo->prepare($Query);
            $Stmt->bindParam(':video', $video, PDO::PARAM_STR);
            $Stmt->execute();
            $videoData = $Stmt->fetchAll(PDO::FETCH_ASSOC);
            //checks if the video exists
            if (empty($videoData)) {
                throw new Exception("Video not found");
            }
            // Display video details
            foreach ($videoData as $video) { ?>

                <div class="video-container">
                    <video src='<?= "usercontent/" . htmlspecialchars($video['videoUrl']) ?>' controls></video>
                    <h2><?= htmlspecialchars($video['title']) ?></h2>
                    <div class="video-info">
                        <a href="profile?p=<?= htmlspecialchars($video['username']) ?>" class="video-profile">
                            <img src="<?= htmlspecialchars($video['profilePic']) ?>">
                            <p><?= htmlspecialchars($video['username']) ?></p>
                        </a>
                        <!-- Form for liking/disliking the video -->
                        <form method="post" class="likes">
                            <button name="like"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                                </svg><?= $video['likes'] ?></button>
                            <button name="dislike"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                                </svg><?= $video['dislikes'] ?></button>

                            <script>
                                function share() {
                                    var copyText = window.location.href;
                                    navigator.clipboard.writeText(copyText);
                                }
                            </script>

                            <button onclick="share()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                </svg>Share</button>
                        </form>
                    </div>
                    <!-- Display video description -->
                    <div class="description">
                        <h4><?= nl2br(htmlspecialchars($video['description'])) ?></h4>
                    </div>

                    <div class="comments">
                        <?php if (isset($_SESSION['loggedInUser'])) { ?>
                            <!-- Form for adding comments -->
                            <form method="post">
                                <input placeholder="Type your comment here.." name="comment" required>
                                <button type="submit">Comment</button>
                            </form>
                        <?php }

                        //Select statement for displaying all the comments and user info.
                        $Stmt = $pdo->prepare("SELECT comments.commentId, users.username, users.profilePic, comments.comment, comments.commentDate
                        FROM comments
                        INNER JOIN users ON comments.userId = users.userId
                        WHERE comments.videoId = :id ORDER BY commentId desc;");
                        $Stmt->bindParam(':id', $_GET['v']);
                        try {
                            $Stmt->execute();
                            $comments = $Stmt->fetchAll(); // Fetch all comments
                        } catch (\PDOException $th) {
                            error_log($th);
                            die();
                        }
                        //displays the comments with foreach loop
                        foreach ($comments as $comment) {
                            echo "<div class='comment'>
                                    <img src='" . htmlspecialchars($comment['profilePic']) . "'>
                                    <div id='userinfo'>
                                        <div id='info'>
                                            <span>" . htmlspecialchars($comment['username']) . "</span>
                                            <span>" . htmlspecialchars($comment['commentDate']) . "</span>
                                        </div>
                                        <span>" . htmlspecialchars($comment['comment']) . "</span>
                                    </div>
                                </div>";
                        }

                        ?>
                    </div>

                </div>

        <?php }
            //checks if like is set.
            if (isset($_POST['like']) && isset($_SESSION['loggedInUser'])) {

                $video = $_GET['v'];
                //Query for inserting likes
                $query = "INSERT INTO `likes` (`userId`, `videoId`, `type`) VALUES (:user, :video, 1);";
                $Stmt = $pdo->prepare($query);
                $Stmt->bindParam(':video', $video, PDO::PARAM_STR);
                $Stmt->bindParam(':user', $_SESSION['loggedInUser'], PDO::PARAM_STR);
                try {
                    //Executes the query
                    $Stmt->execute();
                } catch (\PDOException $th) {
                    error_log($th);
                    die();
                }
            }
            //checks if dislike is set.
            if (isset($_POST['dislike']) && isset($_SESSION['loggedInUser'])) {

                $video = $_GET['v'];
                //Query for inserting dislikes
                $query = "INSERT INTO `likes` (`userId`, `videoId`, `type`) VALUES (:user, :video, 0);";
                $Stmt = $pdo->prepare($query);
                $Stmt->bindParam(':video', $video, PDO::PARAM_STR);
                $Stmt->bindParam(':user', $_SESSION['loggedInUser'], PDO::PARAM_STR);
                try {
                    //Executes the query
                    $Stmt->execute();
                } catch (\PDOException $th) {
                    error_log($th);
                    die();
                }
            }
        } catch (\Throwable $th) {
            //displays error if video not fount.
            echo "Video not found";
            error_log("Error: " . $th->getMessage());
        }

        // the comments section
        if (isset($_POST['comment']) && isset($_SESSION['loggedInUser'])) {
            $video = $_GET['v'];
            $comment = $_POST['comment'];

            // Insert the comment into the database
            $Stmt = $pdo->prepare("INSERT INTO comments (userId, videoId, comment, commentDate) VALUES (:user, :video, :comment, CURRENT_DATE)");

            $Stmt->bindParam(':user', $_SESSION['loggedInUser']);
            $Stmt->bindParam(':video', $video);
            $Stmt->bindParam(':comment', $comment);
            try {
                //Executes the query
                $Stmt->execute();
            } catch (\PDOException $th) {
                error_log($th);
                die();
            }
        }

        ?>

    </main>

</body>

</html>