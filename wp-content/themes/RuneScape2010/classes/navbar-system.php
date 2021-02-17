<?php

function mytheme_register_nav_menu(){
        register_nav_menus( array(
            'primary_menu' => __( 'Primary Menu', 'text_domain' ),
        ) );
    }
    add_action( 'after_setup_theme', 'mytheme_register_nav_menu', 0 );

function rs2010_add_nav_menu_metabox() {
  add_meta_box('rs2010', __('Login/Logout', 'rs2010'), 'rs2010_nav_menu_metabox', 'nav-menus', 'side', 'default');
}
add_action('admin_head-nav-menus.php', 'rs2010_add_nav_menu_metabox');


function rs2010_nav_menu_metabox($object) {
  global $nav_menu_selected_id;

  $elems = array(
    '#rs2010login#' => __('Log In', 'rs2010'),
    '#rs2010logout#' => __('Log Out', 'rs2010'),
    '#rs2010loginout#' => __('Log In', 'rs2010').'|'.__('Log Out', 'rs2010')
  );
  
  class rs2010LogItems {
    public $db_id = 0;
    public $object = 'rs2010log';
    public $object_id;
    public $menu_item_parent = 0;
    public $type = 'custom';
    public $title;
    public $url;
    public $target = '';
    public $attr_title = '';
    public $classes = array();
    public $xfn = '';
  }

  $elems_obj = array();

  foreach($elems as $value => $title) {
    $elems_obj[$title]              = new rs2010LogItems();
    $elems_obj[$title]->object_id		= esc_attr($value);
    $elems_obj[$title]->title			  = esc_attr($title);
    $elems_obj[$title]->url			    = esc_attr($value);
  }

  $walker = new Walker_Nav_Menu_Checklist(array());

  ?>
  <div id="login-links" class="loginlinksdiv">
    <div id="tabs-panel-login-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
      <ul id="login-linkschecklist" class="list:login-links categorychecklist form-no-clear">
        <?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $elems_obj), 0, (object) array('walker' => $walker)); ?>
      </ul>
    </div>
    <p class="button-controls">
      <span class="add-to-menu">
        <input type="submit"<?php disabled($nav_menu_selected_id, 0); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu', 'rs2010'); ?>" name="add-login-links-menu-item" id="submit-login-links" />
        <span class="spinner"></span>
      </span>
    </p>
  </div>
  <?php
}

/* Modify the "type_label" */
function rs2010_nav_menu_type_label($menu_item) {
  $elems = array('#rs2010login#', '#rs2010logout#', '#rs2010loginout#');
  if(isset($menu_item->object, $menu_item->url) && 'custom' == $menu_item->object && in_array($menu_item->url, $elems)) {
    $menu_item->type_label = __('Dynamic Link', 'rs2010');
  }

  return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'rs2010_nav_menu_type_label');

/* Used to return the correct title for the double login/logout menu item */
function rs2010_loginout_title($title) {
	$titles = explode('|', $title);

	if(!is_user_logged_in()) {
		return esc_html(isset($titles[0])?$titles[0]:__('Log In', 'rs2010'));
	} else {
		return esc_html(isset($titles[1]) ? $titles[1] : __('Log Out', 'rs2010'));
	}
}

/* The main code, this replace the #keyword# by the correct links with nonce ect */
function rs2010_setup_nav_menu_item($item) {
	global $pagenow;

	if($pagenow != 'nav-menus.php' && !defined('DOING_AJAX') && isset($item->url) && strstr($item->url, '#rs2010') != '') {
		$login_page_url       = get_option('rs2010_login_page_url', wp_login_url());
    $logout_redirect_url  = get_option('rs2010_logout_redirect_url', home_url());

		switch($item->url) {
			case '#rs2010login#':
        $item->url = $login_page_url;
        break;
			case '#rs2010logout#':
        $item->url = wp_logout_url($logout_redirect_url);
        break;
			default: //Should be #rs2010loginout#
        $item->url = (is_user_logged_in()) ? wp_logout_url($logout_redirect_url) : $login_page_url;
        $item->title = rs2010_loginout_title($item->title);
		}
	}

	return $item;
}
add_filter('wp_setup_nav_menu_item', 'rs2010_setup_nav_menu_item');

add_filter('nav_menu_css_class' , 'my_nav_special_class' , 10 , 2);
function my_nav_special_class($classes, $item){
    if($item->title == "Log In"){
            $classes[] = 'login';
        } else if($item->title == "Log Out") {
            $classes[] = 'logout';
    } else {
        $classes[] = '';
    }
    return $classes;
}

function rs2010_login_redirect_override($redirect_to, $request, $user) {
  //If the login failed, or if the user is an Admin - let's not override the login redirect
  if(!is_a($user, 'WP_User') || user_can($user, 'manage_options')) {
    return $redirect_to;
  }

  $login_redirect_url = get_option('rs2010_login_redirect_url', home_url());
  return $login_redirect_url;
}
add_filter('login_redirect', 'rs2010_login_redirect_override', 11, 3);

function rs2010_settings_page() {
  $login_page_url       = get_option('rs2010_login_page_url', home_url().'/login');
  $login_redirect_url   = get_option('rs2010_login_redirect_url', home_url());
  $logout_redirect_url  = get_option('rs2010_logout_redirect_url', home_url());
  ?>
    <div class="wrap">
      <div class="icon32"></div>
      <h2><?php _e('Login or Logout Menu Item - Settings', 'rs2010'); ?></h2>
      <div class="rs2010_spacer" style="height:25px;"></div>

      <?php if(isset($_GET['rs2010saved'])): ?>
        <div id="message" class="updated notice notice-success is-dismissible below-h2">
          <p><?php _e('Settings saved.', 'rs2010'); ?></p>
        </div>
      <?php endif; ?>

      <form action="" method="post">
        <label for="rs2010_login_page_url"><?php _e('Login Page URL', 'rs2010'); ?></label><br/>
        <small><?php _e('URL where your login page is found.'); ?></small><br/>
        <input type="text" id="rs2010_login_page_url" name="rs2010_login_page_url" value="<?php echo $login_page_url; ?>" style="min-width:250px;width:60%;" /><br/><br/>

        <label for="rs2010_login_redirect_url"><?php _e('Login Redirect URL', 'rs2010'); ?></label><br/>
        <small><?php _e('URL to redirect a user to after logging in. Note: Some other plugins may override this URL.'); ?></small><br/>
        <input type="text" id="rs2010_login_redirect_url" name="rs2010_login_redirect_url" value="<?php echo $login_redirect_url; ?>" style="min-width:250px;width:60%;" /><br/><br/>

        <label for="rs2010_logout_redirect_url"><?php _e('Logout Redirect URL', 'rs2010'); ?></label><br/>
        <small><?php _e('URL to redirect a user to after logging out. Note: Some other plugins may override this URL.'); ?></small><br/>
        <input type="text" id="rs2010_logout_redirect_url" name="rs2010_logout_redirect_url" value="<?php echo $logout_redirect_url; ?>" style="min-width:250px;width:60%;" /><br/><br/>

        <?php wp_nonce_field('rs2010_nonce'); ?>
        <input type="submit" id="rs2010_settings_submit" name="rs2010_settings_submit" value="<?php _e('Save Settings', 'rs2010'); ?>" class="button button-primary" />
      </form>
    </div>
  <?php
}

function rs2010_setup_menus() {
  add_options_page('rs2010 Settings', 'Login or Logout', 'manage_options', 'rs2010-settings', 'rs2010_settings_page');
}
add_action('admin_menu', 'rs2010_setup_menus');

function rs2010_save_settings() {
  if(isset($_POST['rs2010_settings_submit'])) {
    if(!current_user_can('manage_options')) { die("Cheating eh?"); }
    check_admin_referer('rs2010_nonce');

    $login_page_url       = (isset($_POST['rs2010_login_page_url']) && !empty($_POST['rs2010_login_page_url'])) ? $_POST['rs2010_login_page_url'] : wp_login_url();
    $login_redirect_url   = (isset($_POST['rs2010_login_redirect_url']) && !empty($_POST['rs2010_login_redirect_url'])) ? $_POST['rs2010_login_redirect_url'] : home_url();
    $logout_redirect_url  = (isset($_POST['rs2010_logout_redirect_url']) && !empty($_POST['rs2010_logout_redirect_url'])) ? $_POST['rs2010_logout_redirect_url'] : home_url();

    update_option('rs2010_login_page_url', esc_url_raw($login_page_url));
    update_option('rs2010_login_redirect_url', esc_url_raw($login_redirect_url));
    update_option('rs2010_logout_redirect_url', esc_url_raw($logout_redirect_url));

    wp_redirect($_SERVER['REQUEST_URI']."&rs2010saved=true");
    die();
  }
}
add_action('admin_init', 'rs2010_save_settings');
