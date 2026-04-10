<?php
require_once __DIR__ . '/../_base.php';

session_unset();
session_destroy();


redirect('/index.php');
?>