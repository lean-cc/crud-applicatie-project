DROP DATABASE IF EXISTS `4tube`;
CREATE DATABASE `4tube`;
USE `4tube`;

CREATE TABLE `users` (
    userId int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username varchar(25),
    description varchar(500),
    password varchar(255),
    profilePic varchar(255),
    registrationDate DATE NOT NULL DEFAULT CURRENT_DATE
);
CREATE TABLE `videos` (
    videoId int AUTO_INCREMENT PRIMARY KEY,
    userId int UNSIGNED,
    FOREIGN KEY (userId) REFERENCES users(userId),
    title varchar(255),
    description varchar(2000),
    uploadDate varchar(200),
    videoUrl varchar(255)
);

CREATE TABLE `history` (
    historyId int AUTO_INCREMENT PRIMARY KEY,
    userId int UNSIGNED,
    FOREIGN KEY (userId) REFERENCES users(userId),
    videoId int,
    FOREIGN KEY (videoId) REFERENCES videos(videoId),
    date varchar(255)
);

CREATE TABLE `likes` (
    likeId int AUTO_INCREMENT PRIMARY KEY,
    userId int UNSIGNED,
    FOREIGN KEY (userId) REFERENCES users(userId),
    videoId int,
    FOREIGN KEY (videoId) REFERENCES videos(videoId),
    type boolean
);

CREATE TABLE `comments` (
    commentId int AUTO_INCREMENT PRIMARY KEY,
    userId int UNSIGNED,
    FOREIGN KEY (userId) REFERENCES users(userId),
    videoId int,
    FOREIGN KEY (videoId) REFERENCES videos(videoId),
    comment varchar(2000),
    commentDate DATE NOT NULL DEFAULT CURRENT_DATE
);

INSERT INTO users (`username`, `password`, `profilePic`, `description`)
VALUES (
        'admin',
        '$2y$10$vXnbxTSyl6ESSlnDoEdydubJ87qkds05XHjhG2UEnatVWOcAtGWFK',
        'assets/images/default.jpg',
        "Hello i'm admin!"
    );

INSERT INTO videos (
        `userId`,
        `title`,
        `description`,
        `videoUrl`,
        `uploadDate`
    )
VALUES (
        1,
        'guayando',
        'test description for the video.',
        'videos/guayando.mp4',
        '2024-04-05'
    ),
    (
        1,
        'dancing',
        'Music',
        'videos/video1.mp4',
        '2024-04-05'
    ),
    (
        1,
        'British Ship',
        'Gaming',
        'videos/british_ship.mp4',
        '2024-04-19'
    ),
    (
        1,
        'chip',
        'Music',
        'videos/chip.mp4',
        '2024-04-19'
    ),
    (
        1,
        'kars',
        'Movies',
        'videos/kars.mp4',
        '2024-04-19'
    ),
    (
        1,
        'Pro-Palestinian demonstrators arrested at Columbia University',
        'News',
        'videos/arrested.mp4',
        '2024-04-19'
    ),
    (
        1,
        '"Wat is non-binair?" Baudet (FVD) CLASHT met Jetten (D66) in debat over LHBTQI+',
        'News Podcasts',
        'videos/non-binair.mp4',
        '2024-04-19'
    ),
    (
        1,
        'The Problem w/ Tate’s Response to Bishop Attack',
        'Podcasts',
        'videos/tatepdocast.mp4',
        '2024-04-19'
    ),
    (
        1,
        'Violent clashes between Napoli and Frankfurt football fans',
        'Sports News',
        'videos/football.mp4',
        '2024-04-19'
    );

INSERT INTO comments (`userId`, `videoId`, `comment`)
VALUES (
        1,
        1,
        'Nice video!'
    );

INSERT INTO `likes` (`userId`, `videoId`, `type`) VALUES (1, 1, 1);