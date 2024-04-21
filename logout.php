<?php

session_start();
// logout user
session_destroy();
header("Location: index.php");
die();