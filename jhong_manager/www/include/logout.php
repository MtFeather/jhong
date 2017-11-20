<?php
session_start();
session_unset();
session_destroy();
$web='~JHong';
header("Location:  ../index.php");
exit;

?>
