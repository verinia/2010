<?php
remove_action( 'wp_print_styles', 'print_emoji_styles' ); 

add_filter( 'smilies_src', 'rs2010_smilies_src', 10, 3 );
   function rs2010_smilies_src($img_src, $img, $siteurl){
       return $siteurl.'/wp-content/themes/RuneScape2010/theme-assets/forum/smileys/'.$img;
   }

function filter_smilies_size() {
    return "width:15px; height: 15px;";
}
add_filter('smilies_style', 'filter_smilies_size', 1, 10);

add_filter( 'smilies', function( $smilies )
{
    $smilies['^^'] = "raised.gif";
    $smilies[':@'] = "angry.gif";
    $smilies[':D'] = "bigsmile.gif";
    $smilies[':|'] = "nosmile.gif";
    $smilies['o.O'] = "o.O.gif";
    $smilies[':('] = "sad.gif";
    $smilies[':o'] = "shocked.gif";
    $smilies[':O'] = "shocked.gif";
    $smilies[':)'] = "smile.gif";
    $smilies[':p'] = "tongue.gif";
    $smilies[':P'] = "tongue.gif";
    $smilies[';)'] = "wink.gif";

    return $smilies;
} );

add_action( 'wp_enqueue_scripts', 'wpse_141344_admin_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'wpse_141344_add_mce_button' );

function wpse_141344_admin_enqueue_scripts() {
    wp_enqueue_script( 'wpse_141344-tinymce-scipt', get_stylesheet_directory_uri() . '/assets/js/tinymce.js' );
}

function wpse_141344_add_mce_button() {
    add_filter( 'mce_external_plugins', 'wpse_141344_add_tinymce_plugin' );
    add_filter( 'mce_buttons', 'wpse_141344_register_mce_button' );
}

function wpse_141344_add_tinymce_plugin( $plugin_array ) {
    $plugin_array['wpse_141344_tinymce_button'] =  get_stylesheet_directory_uri() . '/assets/js/tinymce.js';
    return $plugin_array;
}

function wpse_141344_register_mce_button( $buttons ) {
    array_push( $buttons, 'wpse_141344_tinymce_button' );
    return $buttons;
}

add_filter( 'mce_buttons', 'jivedig_remove_tiny_mce_buttons_from_editor');
function jivedig_remove_tiny_mce_buttons_from_editor( $buttons ) {

    $remove_buttons = array(
        'undo',
        'redo',
        'wp_adv', // kitchen sink toggle (if removed, kitchen sink will always display)
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }
    return $buttons;
}

