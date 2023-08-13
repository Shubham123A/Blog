<?php
require 'config/constants.php';
//destroy all session and redirect user to login
session_destroy();
header('location:' . ROOT_URL);
die();