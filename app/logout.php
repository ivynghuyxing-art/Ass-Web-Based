<?php
require '_base.php';

session_unset();
session_destroy();


redirect('/index.php');
?>