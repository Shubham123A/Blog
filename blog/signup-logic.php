<?php
require 'config/database.php';


//get sign form data if signup button was clicked
if(isset($_POST['submit'])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL );
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $avatar = $_FILES['avatar'];

    //validate input values
    if (!$firstname) {
        $_SESSION['signup'] ="Please enter your first name";
    } elseif (!$lastname) {
        $_SESSION['signup'] ="Please enter your last name";
    } elseif (!$username) {
        $_SESSION['signup'] ="Please enter your username";
    } elseif (!$email) {
        $_SESSION['signup'] ="Please enter your valid email ";
    } elseif (!$createpassword) {
        $_SESSION['signup'] ="Please enter your password";
    } elseif (!$avatar['name']) {
        $_SESSION['signup'] ="Please add avatar";
    } else {
        //check if password match
        if($createpassword != $confirmpassword) {
            $_SESSION['signup'] = "Password do not match";
        } else {
            //hash password
            $hashed_password =password_hash($createpassword,PASSWORD_DEFAULT);

            //check if username or email already exist in the database
            $user_check_query = "SELECT * FROM users WHERE username = '$username' OR email='$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);
            if(mysqli_num_rows($user_check_result) > 0) {
                $_SESSION['signup'] = "Username or Email  already exits ";
            } else {
                //work on avatar
                //rename avatar
                $time =time(); //make image unique 
                $avatar_name = $time.$avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_destination_path = 'images/' . $avatar_name;


                //make sure file is an image
                $allowed_files = ['png' , 'jpg' , 'jpeg'];
                $extention = explode('.', $avatar_name);
                $extention = end($extention);
                if(in_array($extention,$allowed_files)) {
                    //make sure image is small
                    if($avatar['size'] < 10000000 ) {
                        //upload avatar
                        move_uploaded_file($avatar_tmp_name , $avatar_destination_path);
                    } else {
                        $_SESSION['signup'] = "file size too big ";

                    }
                } else {
                    $_SESSION['signup'] = "Files should be image jpg , png ";
                }

            }
        }
    }

    //redirect back to signup page if there was any problem
    if(isset($_SESSION['signup'])) {
        //pass form data back to signup
        $_SESSION['signup-data'] = $_POST;
        header('location' .ROOT_URL . 'signup.php');
        die();
    } else {
        //insert new user into users table
        $insert_user_query ="INSERT INTO users SET firstname = '$firstname' , lastname = '$lastname' , username = '$username' , email = '$email' , password = '$hashed_password', avatar = '$avatar_name'  , is_admin = 0" ;
        $insert_user_result = mysqli_query($connection, $insert_user_query);
        if(!mysqli_errno($connection)) {
            // redirect to login page
            $_SESSION['signup-success'] = "Registration successful . Please login!";
            header('location: ' .ROOT_URL . 'signin.php');
            die();
        }
    }   

}else {
    //if button was not clicked , back to signup
    header('location: ' .ROOT_URL . 'signup.php');
    die();
}

