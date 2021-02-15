<?php

require 'classes/breadcrumbs.php';
require 'classes/smilies.php';
require 'classes/avatar.php';


add_theme_support('menus');
add_theme_support('title-tag');
add_theme_support('widgets');

add_action( 'customize_register', 'customizer_settings' );

    function customizer_settings( $wp_customize ) {
        $wp_customize->add_section( 'parsmizban_options', 
         array(
            'title'       => __( 'Theme Options', 'parsmizban' ), //Visible title of section
            'priority'    => 20, //Determines what order this appears in
            'capability'  => 'edit_theme_options', //Capability needed to tweak
            'description' => __('Allows you to customize settings for Theme.', 'parsmizban'), //Descriptive tooltip
         ) 
        );
        
        
        $wp_customize->add_setting( 'bootstrap_theme_name', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default'    => '0', //Default setting/value to save
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            //'transport'  => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
         ) 
        );
        
        
        $wp_customize->add_control( new WP_Customize_Control(
             $wp_customize, //Pass the $wp_customize object (required)
             'parsmizban_theme_name', //Set a unique ID for the control
             array(
                'label'      => __( 'Select Theme Name', 'parsmizban' ), //Admin-visible name of the control
                'description' => __( 'Using this option you can change the theme colors' ),
                'settings'   => 'bootstrap_theme_name', //Which setting to load and manipulate (serialized is okay)
                'priority'   => 10, //Determines the order this control appears in for the specified section
                'section'    => 'parsmizban_options', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
                'type'    => 'select',
                'choices' => array(
                    '0' => 'Default',
                    '1' => 'Blue',
                    '2' => 'Halloween',
                    '3' => 'Christmas',
                    '4' => 'Castle',
                )
            )
            ) );
    }

function register_navwalker(){
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );


add_filter( 'wp_nav_menu_objects', function( $items ) {
      foreach ( $items as $item ) {
        if ($item->menu_item_parent) {
           $item->title = '<span>' . $item->title . '</span>';
        }
    }
    return $items;
});

add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add() {
    add_meta_box( 'small_icon', 'Homepage Icons', 'cd_meta_box_cb', 'post', 'normal', 'high' );
}

function cd_meta_box_cb( $post ) {
$values = get_post_custom( $post->ID );
$selected = isset( $values['meta_box_sml_news'] ) ? esc_attr( $values['meta_box_sml_news'][0] ) : "article";
$selected2 = isset( $values['meta_box_lrg_news'] ) ? esc_attr( $values['meta_box_lrg_news'][0] ) : "update";
    
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
echo '
    <p style="text-align: center">
    <label for="meta_box_sml_news"><strong>Small icon</strong></label>';?>
        <script>
            jQuery(function ($) {
                $('#meta_box_sml_news').change(function(){
                    var url = "<?php echo get_template_directory_uri(); ?>/theme-assets/news/" + $('#meta_box_sml_news').val() + ".jpg";
                    $('#demo_img').attr('src', url);
                });
            });
        </script>
        <select name="meta_box_sml_news" id="meta_box_sml_news">
            <option value="article" <?php selected( $selected, 'article' ); ?>>Article</option>
            <option value="articlequestion" <?php selected( $selected, 'articlequestion' ); ?>>Article Question</option>
            <option value="axe" <?php selected( $selected, 'axe' ); ?>>Axe</option>
            <option value="bookcase" <?php selected( $selected, 'bookcase' ); ?>>Bookcase</option>
            <option value="bookopen" <?php selected( $selected, 'bookopen' ); ?>>Book Open</option>
            <option value="brimhaven" <?php selected( $selected, 'brimhaven' ); ?>>Brimhaven</option>
            <option value="bug" <?php selected( $selected, 'bug' ); ?>>Bug</option>
            <option value="clanwars" <?php selected( $selected, 'clanwars' ); ?>>Clanwars</option>
            <option value="filetick" <?php selected( $selected, 'filetick' ); ?>>File Tick</option>
            <option value="gallery" <?php selected( $selected, 'gallery' ); ?>>Gallery</option>
            <option value="grandmaster_quest" <?php selected( $selected, 'grandmaster_quest' ); ?>>Grandmaster Quest</option>
            <option value="handsword" <?php selected( $selected, 'handsword' ); ?>>Hand Sword</option>
            <option value="loot" <?php selected( $selected, 'loot' ); ?>>Loot</option>
            <option value="magearena" <?php selected( $selected, 'magearena' ); ?>>Mage Arena</option>
            <option value="pc" <?php selected( $selected, 'pc' ); ?>>PC</option>
            <option value="smiley" <?php selected( $selected, 'smiley' ); ?>>Smiley</option>
            <option value="snake" <?php selected( $selected, 'snake' ); ?>>Snake</option>
            <option value="workinprogress" <?php selected( $selected, 'workinprogress' ); ?>>Work In Progress</option>
        </select><br /><br />
        <img src="<?php echo get_template_directory_uri(); ?>/theme-assets/news/<?php echo $selected; ?>.jpg" id="demo_img">
    <?php echo '</p>';
    echo '
    <p style="text-align: center">
    <label for="meta_box_sml_news"><strong>Large Icon</strong></label>';?>
        <script>
            jQuery(function ($) {
                $('#meta_box_lrg_news').change(function(){
                    var url = "<?php echo get_template_directory_uri(); ?>/theme-assets/Large/" + $('#meta_box_lrg_news').val() + ".jpg";
                    $('#demo_img_lrg').attr('src', url);
                });
            });
        </script>
        <select name="meta_box_lrg_news" id="meta_box_lrg_news">
            <optgroup label="Misc Updates">
                <option value="update" <?php selected( $selected2, 'update' ); ?>>Update</option>
                <option value="godwars" <?php selected( $selected2, 'godwars' ); ?>>God Wars</option>
                <option value="soulwars" <?php selected( $selected2, 'soulwars' ); ?>>Soul Wars</option>
                <option value="halloween" <?php selected( $selected2, 'halloween' ); ?>>Halloween</option>
                <option value="christmas" <?php selected( $selected2, 'christmas' ); ?>>Christmas</option>
                <option value="pvp" <?php selected( $selected2, 'pvp' ); ?>>PVP</option>
                <option value="pvp2" <?php selected( $selected2, 'pvp2' ); ?>>PvP Improvments</option>
            </optgroup>
            <optgroup label="Monthly Updates">
                <option value="bts-jan" <?php selected( $selected2, 'bts-jan' ); ?>>BTS January</option>
                <option value="bts-feb" <?php selected( $selected2, 'bts-feb' ); ?>>BTS Febuary</option>
                <option value="bts-mar" <?php selected( $selected2, 'bts-mar' ); ?>>BTS March</option>
                <option value="bts-apr" <?php selected( $selected2, 'bts-apr' ); ?>>BTS April</option>
                <option value="bts-may" <?php selected( $selected2, 'bts-may' ); ?>>BTS May</option>
                <option value="bts-jun" <?php selected( $selected2, 'bts-jun' ); ?>>BTS June</option>
                <option value="bts-jul" <?php selected( $selected2, 'bts-jul' ); ?>>BTS July</option>
                <option value="bts-aug" <?php selected( $selected2, 'bts-aug' ); ?>>BTS August</option>
                <option value="bts-sep" <?php selected( $selected2, 'bts-sep' ); ?>>BTS September</option>
                <option value="bts-oct" <?php selected( $selected2, 'bts-oct' ); ?>>BTS October</option>
                <option value="bts-nov" <?php selected( $selected2, 'bts-nov' ); ?>>BTS November</option>
                <option value="bts-dec" <?php selected( $selected2, 'bts-dec' ); ?>>BTS December</option>
            </optgroup>
        </select><br /><br />
        <img src="<?php echo get_template_directory_uri(); ?>/theme-assets/Large/<?php echo $selected2; ?>.jpg" id="demo_img_lrg">
    <?php echo '</p>';
        
}

add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id ) {
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
    if( !current_user_can( 'edit_post' ) ) return;
     
    if( isset( $_POST['meta_box_sml_news'] ) )
        update_post_meta( $post_id, 'meta_box_sml_news', esc_attr( $_POST['meta_box_sml_news'] ) );
    if( isset( $_POST['meta_box_lrg_news'] ) )
        update_post_meta( $post_id, 'meta_box_lrg_news', esc_attr( $_POST['meta_box_lrg_news'] ) );
         
}
add_filter( 'excerpt_length', function($length) {
    return 40;
} );
?>
