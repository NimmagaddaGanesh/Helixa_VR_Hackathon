<?php
include("../config/db.php");
session_destroy();
header("Location: /web_medicine/public/index.php");
exit;
