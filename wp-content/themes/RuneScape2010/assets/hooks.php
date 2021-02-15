<?php 

function show_logo_or_title() {
	echo '<div id="logo_title">';
	if ( get_theme_mod( 'logo_image' ) ) { ?>
 		<img src="<?php echo get_theme_mod( 'logo_image' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" >
	<?php } else { ?>
		<h1><?php bloginfo( 'name' ); ?></h1>
		<div id="logo_tagline"><?php bloginfo( 'description' ); ?></div>
	<?php }
	echo '</div>';
}

function navbar() {
	echo 'Navbar Loading code here';
}


?>