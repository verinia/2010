<?php
function registration_form( $username, $password, $password2, $email) {
 
//    echo '
//    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
//    <div>
//    <label for="username">Username <strong>*</strong></label>
//    <input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '" maxlength="12" onfocus="display(this.parentNode);" onkeypress="display(this.parentNode);uncross(this);" onblur="validate_username(true);" class="">
//    </div>
//     
//    <div>
//    <label for="password">Password <strong>*</strong></label>
//    <input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '" autocomplete="off" maxlength="20" onfocus="display(this.parentNode.parentNode);uncross(this)" class="">
//    </div>
//    
//    <div>
//    <label for="password2">Password2 <strong>*</strong></label>
//    <input type="password" name="password2" value="' . ( isset( $_POST['password2'] ) ? $password2 : null ) . '" autocomplete="off" maxlength="20" onfocus="display(this.parentNode.parentNode);uncross(this)" class="">
//    </div>
//     
//    <div>
//    <label for="email">Email <strong>*</strong></label>
//    <input type="text" name="email" value="' . ( isset( $_POST['email']) ? $email : null ) . '">
//    </div>
//
//
//    <input type="submit" name="submit" value="Register" class="button button-primary"/>
//    </form>
//    ';
    
   echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
   <div id="formBoxes" class="inner_brown_box brown_box_stack brown_box_padded" style="padding-bottom: 50px">
    <div class="formDesc" id="usr_desc">
        <p id="username_constraints_characters">Usernames can be a maximum of 12 characters long and may contain letters, numbers, spaces and underscores.</p>
        <p>They should not contain your real name, birth date, or other personally identifiable information, to better protect your identity.</p>
        <p>They should not be offensive or break our Terms &amp; Conditions.</p>
    </div>

    <div class="formSuperGroup single_line">
        <div class="formSection" id="usr" style="margin-bottom: 20px;">
        <label for="username">Your Username:</label>
        <input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '" maxlength="12" onfocus="display(this.parentNode);" onkeypress="display(this.parentNode);uncross(this);" class="">
        <br class="clear">
        </div>
    <br class="clear">
    </div>

    <div class="formSuperGroup double_line">
        <div class="formDesc" id="pass_desc">
            <p>NEVER give anyone your password, not even to Jagex staff. Jagex staff will never ask you for your password.</p>
            <p id="pass_constraints" class="">Passwords must be between 5 and 20 characters long and may contain letters and numbers.</p>
            <p>We recommend you use a mixture of numbers and letters in your password to make it harder for someone to guess.</p>
        </div>
        <div id="pass" class="formGroup" style="margin-bottom: 20px;">
            <div class="formSection" style="margin-bottom: 10px;">
            <label for="password1">Your Password:</label>
                <input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '" autocomplete="off" maxlength="20" onfocus="display(this.parentNode.parentNode);uncross(this)" class="" required>
            </div>
            <div class="formSection">
            <label for="password2">Re-enter Password:</label>
                <input type="password" name="password2" value="' . ( isset( $_POST['password2'] ) ? $password2 : null ) . '" autocomplete="off" maxlength="20" onfocus="display(this.parentNode.parentNode);uncross(this)" class="" required>
            </div>
        <br class="clear">
        </div>
    <div class="error formError" id="errorPassword">&nbsp;</div>
    <br class="clear">
    </div>

    <div class="formDesc" id="email_desc">
        <p>Your email address is required for account creation. We will not share it with any third parties.</p>
        <p>Your email address will be used for logging in to your account and account recovery only</p>
        <p>for more information, You can view our Privacy policy.</p>
    </div>

    <div class="formSuperGroup single_line" style="margin-bottom: 20px;">
        <div class="formSection" id="email">
        <label for="email">Your Email Address:</label>
        <input type="email" name="email" value="' . ( isset( $_POST['email']) ? $email : null ) . '" autocomplete="off" onfocus="display(this.parentNode);uncross(this)" class="" required>
        <br class="clear">
        </div>
    <br class="clear">
    <div id="jmesgBg" class="input_details"><div id="jmesg" class="input_details" style="background-position: 0px 15px;">
<p id="username_constraints_characters">Usernames can be a maximum of 12 characters long and may contain letters, numbers, spaces and underscores.</p>
<p>They should not contain your real name, birth date, or other personally identifiable information, to better protect your identity.</p>
<p>They should not be offensive or break our Terms and Conditions</p>
</div></div>
    </div></div>
    <div class="inner_brown_box brown_box_padded">
        <input type="submit" name="submit" value="" id="submitbutton"/>
    </div></form>
';
}

function registration_validation( $username, $password, $password2, $email )  {
global $reg_errors;
$reg_errors = new WP_Error;
	
if ( empty( $username ) || empty( $password ) || empty( $password2 ) || empty( $email ) ) {
    $reg_errors->add('field', 'Required form field is missing');
}
if ( username_exists( $username ) ) {
    $reg_errors->add('user_name', 'Sorry, that username already exists!');
}
if ( 5 > strlen( $password ) ) {
        $reg_errors->add( 'password', 'Password length must be greater than 5' );
}
if ( !is_email( $email ) ) {
    $reg_errors->add( 'email_invalid', 'Email is not valid' );
}
if ( email_exists( $email ) ) {
    $reg_errors->add( 'email', 'Email Already in use' );
}
if (strcmp($password, $password2) !== 0) {
    $reg_errors->add( 'password2', 'Passwords do not match!' );
}
    

if ( is_wp_error( $reg_errors ) ) {
        echo '<div class="inner_brown_box" style="margin: 10px 0;">';
    foreach ( $reg_errors->get_error_messages() as $error ) { 
         echo '<strong>ERROR</strong>: ';
         echo $error . '<br/>';   
   }
    echo '</div>';
}
}

function complete_registration() {
    global $reg_errors, $username, $password, $password2, $email, $nickname;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_pass2'    =>   $password2
        );
        $user = wp_insert_user( $userdata );
        echo '<div class="inner_brown_box" style="margin: 10px 0; text-align: center">
            <h4>Registration Succesfull: You can now Login.</h4>
        </div>';
    }
}

function custom_registration_function() {
    $username = "";
    $password = "";
    $password2 = "";
    $email = "";
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['password2'],
        $_POST['email']
        );
         
        // sanitize user form input
        global $username, $password, $password2, $email;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        $password2   =   esc_attr( $_POST['password2'] );
        $email      =   sanitize_email( $_POST['email'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $password2,
        $email
        );
    }
 
    registration_form(
        $username,
        $password,
        $password2,
        $email
     );
}