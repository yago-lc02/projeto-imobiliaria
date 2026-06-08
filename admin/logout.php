<?php

session_start();
session_destroy();

header("Location: login.php"); // Manda de volta pro login

exit;

?>