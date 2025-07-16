<?php
/**
 * Admin Logout
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
?>