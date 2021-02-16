<?php
function registration_form( $username, $password, $email) {
 
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    <div>
    <label for="username">Username <strong>*</strong></label>
    <input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '">
    </div>
     
    <div>
    <label for="password">Password <strong>*</strong></label>
    <input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '">
    </div>
    
    <div>
    <label for="password2">Password2 <strong>*</strong></label>
    <input type="password" name="password2" value="' . ( isset( $_POST['password2'] ) ? $password2 : null ) . '">
    </div>
     
    <div>
    <label for="email">Email <strong>*</strong></label>
    <input type="text" name="email" value="' . ( isset( $_POST['email']) ? $email : null ) . '">
    </div>


    <input type="submit" name="submit" value="Register" class="button button-primary"/>
    </form>
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
    foreach ( $reg_errors->get_error_messages() as $error ) { 
  		echo '<div>';
         echo '<strong>ERROR</strong>:';
         echo $error . '<br/>';
         echo '</div>';   

   }
}
}

function complete_registration() {
    global $reg_errors, $username, $password, $email, $nickname;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_pass2'    =>   $password2
        );
        $user = wp_insert_user( $userdata ); ?>
			<script type="text/javascript">
				window.alert("Registration complete. You can now login!");
			</script>
		<?
    }
}

function custom_registration_function() {
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