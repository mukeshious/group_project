<?php
    session_start();
    include('connection.php');        
    $errotp ='';

    if(isset($_POST['verifyotp'])){
        $errotp='';
        $errcount=0;

        $otpnum =$_POST['otpnumber'];
        $otpnumber= (int)$otpnum;
        // echo $otpnumber;
        
        if(empty($_POST['otpnumber'])){
            $errcount+=1;
            $errotp ="OTP input field should not be empty";
        }
        if($otpnumber != $_SESSION['otp']){
            $errcount+=1;
            $errotp="OTP is INVALID";
        }
        if($errcount == 0 ){
                $role = $_GET['page'];
                
                if($role === 'customer'){

                    $verify = 'verified';
                    $sql = "UPDATE USER_I SET VERIFIED= :verify WHERE EMAIL= :uemail ";
                    $stid = oci_parse($connection,$sql);
                    oci_bind_by_name($stid, ':uemail', $_SESSION['email']);
                    oci_bind_by_name($stid, ':verify', $verify);

                    if(oci_execute($stid)){
                        unset($_SESSION['otp']);
                        header('location:login.php');
                    }
                }

                if($role === 'trader'){
                      
                    $verified='pending';
                    $sql1 = "UPDATE USER_I SET VERIFIED = :verify WHERE EMAIL = :uemail";
                    $stid1 = oci_parse($connection,$sql1);
                    oci_bind_by_name($stid1, ':uemail',$_SESSION['email']);
                    oci_bind_by_name($stid1, ':verify', $verified);
                    
                    if(oci_execute($stid1)){
                        unset($_SESSION['otp']);
                        header("location:insertcategory.php");
                    }
                }
                if($role == 'login'){
                    unset($_SESSION['otp']);
                    header("location:resetpassword.php?page=$role");
                }
            
        }
    }

    if(isset($_POST['resendOTP'])){
        unset($_SESSION['otp']);
        
        $otp_number = rand(100000,999999);
        $femail = $_SESSION['email'];
        $sub ="Please Verify Your Email address";
        $message="Dear User, Your Verification Code is: $otp_number";            
        include_once('sendmail.php');
        $_SESSION['otp'] =$otp_number;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
      
</style>
<body>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">Open modal for @mdo</button>

</body>
</html>

