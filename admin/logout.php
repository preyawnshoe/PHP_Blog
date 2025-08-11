<?php
include '../includes/auth.php';
logout_admin();
header('Location: login.php');
exit; 