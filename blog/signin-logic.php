<?php
require 'config/database.php';

if(isset($_POST['submit'])) {
    //get from data
    $username_email = filter_var($_POST['username_email'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($_POST['password'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(!$username_email) {
        $_SESSION['signin'] = "Username or Email Required";
    } elseif (!$password) {
        $_SESSION['signin'] ="password Required";
    } else {
        //fetch user from database
        $fetch_user_query = "SELECT * FROM users WHERE username = '$username_email' OR email = '$username_email'";
        $fetch_user_result = mysqli_query($connection, $fetch_user_query);

        if(mysqli_num_rows($fetch_user_result) == 1) {
            // convert the record into assoc array
            $user_record = mysqli_fetch_assoc($fetch_user_result);
            $db_password = $user_record['password'];
            // compare form password with database passowrd
            if(password_verify($password, $db_password)) {
                //set session for access control
                $_SESSION['user-id'] = $user_record['id'];
                //set session if user is admin
                if($user_record['is_admin'] == 1) {
                    $_SESSION['user_is_admin'] =true;
                }
                
                //log user in
                header('location: ' . ROOT_URL . 'admin/');
            } else {
                $_SESSION['signin'] = "Please check your input" ;
            }
        } else {
            $_SESSION['signin'] = "User not found" ;
        }
    }
    //if any problem , redirect back to sigin page with login details
    if(isset($_SESSION['sigin'])) {
        $_SESSION['sigin-data'] = $_POST;
        header('location: ' . ROOT_URL . 'sigin.php');
        die();

    }
} else {
    header('location: ' . ROOT_URL .'signin.php');
    die();
}