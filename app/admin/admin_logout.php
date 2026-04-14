<?php
require_once '../_base.php';

session_unset();
session_destroy();


redirect('/index.php');
?>