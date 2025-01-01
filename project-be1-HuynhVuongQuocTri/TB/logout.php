<?php
session_start();
session_destroy();
header("Location: http://localhost/TB/index.php");
