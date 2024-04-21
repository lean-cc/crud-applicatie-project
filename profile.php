<?php

include_once("includes/connection.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4tube - Profile</title>
    <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
    <?php

    include_once("includes/nav.php");
    include_once("includes/aside.php");

    ?>

    <main>
        <?php
        //Gets the profile id
        try {
            if (isset($_GET['p'])) {
                $profile = $_GET['p'];
            }
            //gets the profile info via mysql query
            $Query = "SELECT * FROM `users` WHERE `username` = :profile";
            $Stmt = $pdo->prepare($Query);
            $Stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
            $Stmt->execute();
            $profileData = $Stmt->fetchAll(PDO::FETCH_ASSOC);
            //Checks if the profile exists
            if (empty($profileData)) {
                throw new Exception("User not found");
            }

            foreach ($profileData as $profile) { ?>

                <div class="profile">
                    <div class="profile-info">
                        <img src='<?= htmlspecialchars($profile['profilePic']) ?>'>
                        <h2><?= htmlspecialchars($profile['username']) ?></h2>
                        <p>Date joined: <?= htmlspecialchars($profile['registrationDate']) ?></p>
                        <p><?= nl2br(htmlspecialchars($profile['description'])) ?></p>
                        <?php
                        //checks if you view your own profile.
                        if (isset($user['username'])) {
                            if ($user['username'] == $profile['username']) {
                                echo "<a href='settings.php'>Edit profile</a>";
                            }
                        }
                        ?>
                    </div>
                    <div class="profile-videos">
                        <div class="video-containermain">
                            <?php
                            //Gets the profile id
                            if (isset($_GET['p'])) {
                                $profile = $_GET['p'];
                            }
                            //Gets the videos from the user via a mysql query
                            $Query = "SELECT videos.*, users.username, users.profilePic FROM videos 
                            JOIN users ON videos.userId = users.userId WHERE users.username = :profile ORDER BY videos.uploadDate DESC;";
                            $Stmt = $pdo->prepare($Query);
                            $Stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
                            $Stmt->execute();
                            $videoData = $Stmt->fetchAll(PDO::FETCH_ASSOC);
                            //loops through the data
                            foreach ($videoData as $row) {
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
                            ?>
                        </div>
                    </div>
                </div>

        <?php }
            //checks for any errors, if true it says user not found.
        } catch (\Throwable $th) {
            echo "User not found";
            error_log("Error: " . $th->getMessage());
        }

        ?>
    </main>
</body>

</html>