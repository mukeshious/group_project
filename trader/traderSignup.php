<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="signupt.css">
    <style>
        #customer:hover {
            background-color: green;
            color: white;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <?php
    include("../connection.php");

    $resgister_fname = $resgister_lname = $resgister_email = $resgister_number = $resgister_dob = $resgister_address = $resgister_gender = $resgister_category = $resgister_password = $resgister_cpassword =  $resgister_check = '';
    $errcount=0;
    if (isset($_POST['subtrader'])) {

        if (empty($_POST['fname'])) {
            $resgister_fname = "Name is required";
        }
        if (empty($_POST['lname'])) {
            $resgister_lname = "Last name is required";
        }

        if (empty($_POST['uemail'])) {
            $resgister_email = "Email is required";
        }

        if(empty($_POST['uphone'])){
            $resgister_number = "Phone number is required";
        }

        if(empty($_POST['dob'])){
            $resgister_dob = "Date of Birth is required";
        }

        if(empty($_POST['address'])){
            $resgister_address = "Address is required";
        }

        if (empty($_POST['gender'])) {
            $resgister_gender = "Gender is required";
        }

        if (empty($_POST['category'])) {
            $resgister_category = "Category is required";
        }

        if (empty($_POST['upassword'])) {
            $resgister_password = "Password is required";
        }
        if (empty($_POST['ucpassword'])) {
            $resgister_cpassword = "Confirm password is required";
        }

        if (empty($_POST['confirm'])) {
            $resgister_check = "Term and Conditions is required";
        } else {
            $user_fname =  trim($_POST['fname']);
            $user_lname =  trim($_POST['lname']);
            $user_email = trim($_POST['uemail']);
            $user_phone = $_POST['uphone'];
            $user_dob =  trim($_POST['dob']);
            $user_address =  trim($_POST['address']);
            $user_gender = trim($_POST['gender']);
            $user_category = $_POST['category'];
            $user_password = $_POST['upassword'];
            $user_cpassword = $_POST['ucpassword'];
            $confirm = $_POST['confirm'];

            $email = filter_var($user_email, FILTER_SANITIZE_EMAIL);

            $pattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $resgister_email = "Email you entered is invalid";
            }

            // phone number validation
            if(strlen(trim($user_phone)) != strlen($user_phone)){
                $errcount+=1;
                $resgister_number='You cannot input space as a phone number';
            }

            if(strlen($user_phone) < 10 || strlen($user_phone) > 10){
                $errcount+=1;
                $resgister_number = "Phone number length should be 10";
            }
            if(!preg_match("/^9[0-9]{9}$/", $user_phone)){
                $errcount+=1;
                $resgister_number = "Phone number is not valid. Please enter a valid Phone number";
            }
            $age = date_diff(date_create($user_dob), date_create('now'))->y;

            if($age < 12) {
                $errcount+=1;
                $resgister_dob = "The required is 12";
            }

            $contact = (int)$user_phone;
            $sql = 'SELECT * FROM "USER" WHERE EMAIL_ADDRESS = :demail OR PHONE_NUMBER = : dcontact';

            $stid1 = oci_parse($conn, $sql);
            oci_bind_by_name($stid1,':demail' ,$email);
            oci_bind_by_name($stid1, ':dcontact' ,$contact);
            oci_execute($stid1);

            $vemail=$vcontact='';

                while($row = oci_fetch_array($stid1,OCI_ASSOC)){
                    $vemail = $row['EMAIL_ADDRESS'];
                    $vcontact = (int)$row['PHONE_NUMBER'];
                }

                if($vemail === $email){
                    $errcount+=1;
                    $resgister_email="Email is already Exists";
                }
                if($vcontact === $contact){
                    $errcount+=1;
                    $resgister_number="Phone number is already Exists";
                }
            if ($user_password == $user_cpassword) {
                if (preg_match($pattern, $user_password) == false) {
                    $resgister_password = 'Password Does not match';
                }
                if($errcount==0){
                    $password = md5($user_password);
                    $role = "trader";
                    $sql = 'INSERT INTO "USER" (USER_ID,FIRSTNAME,LASTNAME,ROLE,EMAIL_ADDRESS,PHONE_NUMBER,DATE_OF_BIRTH,ADDRESS,CATEGORY,GENDER,PASSWORD)
                    VALUES(:user_id,:HFIRSTNAME,:HLASTNAME,:HROLE,:HEMAIL,:HPHONE,:HDOB,:HADDRESS,:HCATEGORY,:HGENDER,:HPASSWORD)';

                    $stid = oci_parse($conn, $sql);

                    oci_bind_by_name($stid, ':user_id', $user_id);
                    oci_bind_by_name($stid, ':HFIRSTNAME', $user_fname);
                    oci_bind_by_name($stid, ':HLASTNAME', $user_lname);
                    oci_bind_by_name($stid, ':HGENDER', $user_gender);
                    oci_bind_by_name($stid, ':HCATEGORY', $user_category);
                    oci_bind_by_name($stid, ':HEMAIL', $email);
                    oci_bind_by_name($stid, ':HPHONE', $user_phone);
                    oci_bind_by_name($stid, ':HDOB', $user_dob);
                    oci_bind_by_name($stid, ':HADDRESS', $user_address);
                    oci_bind_by_name($stid, ':HROLE', $role);
                    oci_bind_by_name($stid, ':HPASSWORD', $password);

                    if (oci_execute($stid)) {
                        header("location:../login.php");
                    } else {
                        echo "Data is not inserted";
                    }
                   
                }
            }
        }
    }
    ?>
    <div class="container">
        <div class="one">
            <img src="../customer/cart7.jpg" alt="">
            <div class="onei">
                <h3>Welcome to Heaton's Mart</h3>
                <p>Your Neighbourhood Grocers</p>
            </div>
        </div>
        <div class="two">
            <div class="twoi">
                <a href="../customer/customerSignup.php" id="customer">Customer</a>
                <a href="traderSignup.php " id="trader">Trader</a>
            </div>

            <div class="form">
                <h1>Signup</h1>

                <form method="POST" action="">
                    <div class="form-input">
                        <label>First Name<span>* <?php echo $resgister_fname; ?></span></label>
                        <input type="text" name="fname">
                    </div>
                    <div class="form-input">
                        <label>Last Name<span>* <?php echo $resgister_lname; ?></span></label>
                        <input type="text" name="lname">
                    </div>
                    <div class="form-input">
                        <label>Email<span>* <?php echo $resgister_email; ?></span></label>
                        <input type="text" name="uemail">
                    </div>

                    <div class="form-input">
                        <label>Phone Number<span>* <?php echo $resgister_number ; ?></span></label>
                        <input type="number" name="uphone">
                    </div>
                    <div class="form-input">
                        <label>Date of Birth<span>* <?php echo $resgister_dob ; ?></span></label>
                        <input type="date" name="dob">
                    </div>
                    <div class="form-input">
                        <label>Address<span>* <?php echo $resgister_address ; ?></span></label>
                        <input type="text" name="address">
                    </div>
                    <div class="form-input">
                        <label>Gender<span>* <?php echo $resgister_gender; ?></span></label>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="O">Other</option>
                        </select>
                    </div>
                    <div class="form-input">
                        <label>Category<span>* <?php echo $resgister_category; ?></span></label>
                        <select name="category">
                            <option value="category">Select Category</option>
                            <option value="Butcher">Butcher</option>
                            <option value="Bakrey">Bakrey</option>
                            <option value="Greengroccer">Greengroccer</option>
                            <option value="fishmonger">fishmonger</option>
                            <option value="delicatessens">delicatessens</option>
                        </select>
                    </div>
                    <div class="form-input">
                        <label>Password<span>* <?php echo $resgister_password; ?></span></label>
                        <input type="password" name="upassword">
                    </div>
                    <div class="form-input">
                        <label>Confirm Password<span>* <?php echo $resgister_cpassword; ?></span></label>
                        <input type="password" name="ucpassword">
                    </div>
                    <div class="check">
                        <input type="checkbox" name="confirm">
                        <p>Terms & Conditions <span class="error">*<?php echo $resgister_check; ?></span></p>
                    </div>
                    <div class="login">
                        <input type="submit" name="subtrader" value="Signup">
                    </div>
                    <div>
                        <P>
                            Already member of Heaton's Mart?
                            <a href="../login.php">Login</a>
                        </P>
                        <div class="social">
                            <img src="../img/facebook.png" alt="" height="30px" width="30px">
                            <img src="../img/twitter.png" alt="" height="30px" width="30px">
                            <img src="../img/google.png" alt="" height="30px" width="30px">
                        </div>
                    </div>




                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('trader').style.background = "green";
        document.getElementById('trader').style.color = "white";
    </script>

</body>

</html>