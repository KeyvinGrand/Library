<?php
/*
Program description: Logout user by destroying session and redirecting to index.php
Author: Keyvin Grand
*/

session_start();
session_unset();
session_destroy();

header("Location: index.php");
exit();