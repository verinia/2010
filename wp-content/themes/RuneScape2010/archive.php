<?php get_header(); ?>

<div id="content">
<div id="article">
<div class="sectionHeader">
<div class="left">
<div class="right">
<div class="plaque">
<?php single_cat_title(); ?>

</div>
</div>
</div>
</div>
<div class="section">
<div class="brown_background">

<div id="inner_brown_background_login" class="inner_brown_background">
<div id="brown_box_login" class="brown_box">
<div class="subsectionHeader" style="width:100%;">
<?php single_cat_title( __( 'News Archives in:  ', 'textdomain' ) ); ?>
</div>
<?php
if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        the_title();
    endwhile;
else :
    _e( 'Sorry, no posts were found.', 'textdomain' );
endif;
?>
    </div> </div> </div> </div>
</div>
</div>




<?php get_footer();?>