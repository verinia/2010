<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
    	<?php wp_head(); ?>
        <title> <?php bloginfo( 'name' ); ?></title>
    </head>
    
    <body>
        <style type="text/css">
        @import url(<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/css/global-5.css);
        @import url(<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/css/home-2.css);
        @import url(<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/forum/forum2-10.css);
        @import url(<?php echo get_stylesheet_directory_uri(); ?>/assets/css/style.css);
        </style>
        <div id="scroll">
        
        <div id="head">
            <div id="headOrangeTop"></div>
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/img/main/layout/head_image.jpg" alt="RuneScape">
                <div id="headImage"><a href="#" id="logo_select"></a>
                    <div id="player_no">Welcome to <?php bloginfo( 'name' ); ?></div>
                </div>
            <div id="headOrangeBottom"></div>
            <div id="menubox">
                <?php wp_nav_menu( array(
                    'theme_location'  => 'header-menu',
                    'container'       => 'div',
                    'container_class' => 'main_nav',
                    'container_id'    => '',
                    'menu_class'      => '',
                    'menu_id'         => '',
                ) ); ?>
            </div>
            

            
<!--
            <div id="menubox">
                <ul id="menus">
                    <li class="top"><a href="#" id="home" class="tl"><span class="ts">Home</span></a></li>
                    <li class="top"><a href="#" class="tl"><span class="ts">Play Now</span></a></li>
                    <li class="top"><a class="tl" href="#"><span class="ts">Account</span></a></li>
                    <li class="top"><a href="#" id="home" class="tl">Game Guide</a></li>
                    <li class="top"><a href="#" id="home" class="tl">Community</a>
                        <ul>
<<<<<<< Updated upstream
                            <li><a href="" class="fly"><span>Forums</span></a></li>
                            <li><a href="" class="fly"><span>Hiscores</span></a></li>
                            <li><a href="" class="fly"><span>Membership</span></a></li>
=======
                            <li><a href="#" class="fly"><span>Forums</span></a></li>
                            <li><a href="#" class="fly"><span>Hiscores</span></a></li>
                            <li><a href="#" class="fly"><span>Membership</span></a></li>
>>>>>>> Stashed changes
                        </ul>
                    </li>
                    <li class="top"><a href="#" id="home" class="tl">Help</a></li>
                    <li class="top"><a href="<?php wp_login_url() ?>" id="login" class="tl"><span class="ts">
                                <?php global $current_user; wp_get_current_user(); ?>
                                <?php
                                if ( is_user_logged_in() ) {
                                    echo 'Username: ' . $current_user->user_login . "\n";
                                } else {
                                    echo 'Login';
                                }
                                ?>
                            </span></a></li>
            </ul><br class="clear" />
            </div>
-->
        </div>

