<?php
/**
 * Template Name: Login Page
 */
if(isset($_POST['login_Sbumit'])) {
	$creds                  = array();
	$creds['user_login']    = stripslashes( trim( $_POST['userName'] ) );
	$creds['user_password'] = stripslashes( trim( $_POST['passWord'] ) );
	$creds['remember']      = isset( $_POST['rememberMe'] ) ? sanitize_text_field( $_POST['rememberMe'] ) : '';
	$redirect_to            = esc_url_raw( $_POST['redirect_to'] );
	$secure_cookie          = null;
		
	if($redirect_to == '')
		$redirect_to= get_site_url(). '/dashboard/' ; 
		
		if ( ! force_ssl_admin() ) {
			$user = is_email( $creds['user_login'] ) ? get_user_by( 'email', $creds['user_login'] ) : get_user_by( 'login', sanitize_user( $creds['user_login'] ) );

		if ( $user && get_user_option( 'use_ssl', $user->ID ) ) {
			$secure_cookie = true;
			force_ssl_admin( true );
		}
	}

	if ( force_ssl_admin() ) {
		$secure_cookie = true;
	}

	if ( is_null( $secure_cookie ) && force_ssl_login() ) {
		$secure_cookie = false;
	}

	$user = wp_signon( $creds, $secure_cookie );

	if ( $secure_cookie && strstr( $redirect_to, 'wp-admin' ) ) {
		$redirect_to = str_replace( 'http:', 'https:', $redirect_to );
	}

	if ( ! is_wp_error( $user ) ) {
		wp_safe_redirect( $redirect_to );			
	} else {			
		if ( $user->errors ) {
			$errors['invalid_user'] = __('<strong>ERROR</strong>: Invalid user or password.'); 
		} else {
			$errors['invalid_user_credentials'] = __( 'Please enter your username and password to login.', 'kvcodes' );
		}
	}		 
}
get_header();
   

?>
<style type="text/css">
        @import url(<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/global/css/weblogin-6.css);
</style>


<div class="navigation">
    <div class="location">
        <?php the_breadcrumb(); ?>
    </div>
</div>
<div id="content">
<div id="article">
<div class="sectionHeader">
<div class="left">
<div class="right">
<div class="plaque">
<?php the_title(); ?>

</div>
</div>
</div>
</div>
<div class="section">
    <div class="brown_background" style="padding: 0;">
       <div id="login_background" class="inner_brown_background">
<div id="login_panel" class="brown_box">
<div class="bottom">
<div class="repeat">
 
<!--
    <form id="login1" name="form" action="<?php echo home_url(); ?>/login/" method="post">   
<div class="section_form" id="usernameSection">
<label for="username">Username:</label>
<input size="20" type="text" name="username" id="username" maxlength="12">
</div>
<div class="section_form" id="passwordSection">
<label for="password">Password:</label>
<input size="20" type="password" id="password" name="password" maxlength="20">
</div>
</div>
<div class="bottom_section">
<div id="remember">
<label for="rem">
<input type="checkbox" name="rem" id="rem" value="1" class="checkbox">
Check this box to remember username</label>
</div>
<div id="submit_button">
<button type="submit" value="Login Now!" onmouseover="this.style.backgroundPosition='bottom';" onmouseout="this.style.backgroundPosition='top';" onclick="return SetFocus();" style="background-position: center top;">Login Now!</button>
</div>
    </form>
-->
    
<?php if(!empty($errors)) {  //  to print errors,
	foreach($errors as $err )
	echo $err; 
} ?>
<form name="loginform" action="<?php echo home_url(); ?>/login/" method="post">
    <div class="top_section">
    <div id="message">
    Please enter your username and password to continue.
    </div>
    <div class="section_form" id="usernameSection">
        <label for="user_login">Username:</label>
        <input type="text" name="userName" id="user_login" class="input" value="" maxlength="12" size="20">
    </div>
    
    <div class="section_form" id="passwordSection">
        <label for="user_pass">Password:</label>
        <input type="password" name="passWord" id="user_pass" class="input" value="" >
    </div>
    </div>
    <div class="bottom_section">
        
    <div id="remember">
        <label for="rememberme">
        <input name="rememberMe" type="checkbox" id="rememberme" value="forever">
        Check this box to remember your account
        </label>
    </div>
        
    <div id="submit_button">
        <input type="hidden" name="login_Sbumit" >
        <input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>">
        <button type="submit" name="wp-submit" id="wp-submit" value="Login Now!" onmouseover="this.style.backgroundPosition='bottom';" onmouseout="this.style.backgroundPosition='top';" onclick="return SetFocus();" style="background-position: center top;">Login Now!</button>

    </div>
    </div>
</form>

</div>
</div>
</div>
<div class="buttons">
<a href="../m%3dcreate/index-2.html" class="createaccount"><span class="buttbg"></span>Create a New Account<br>Click Here!</a>
<a href="../../www.runescape.com/loginapplet/loginappletb4b5.html?mod=accountappeal&amp;dest=passwordchoice.ws" class="recoveraccount"><span class="butt1bg"></span>Lost Your Password?<br> Click Here!</a>
</div>

<br class="clear">
</div>
    </div>
    </div>
</div>
</div>



<?

get_footer(); 

?>