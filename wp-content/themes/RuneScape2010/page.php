<?php get_header(); ?>


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

<?php the_content(); ?>
    </div> </div>
</div>
</div>


<?php get_footer(); ?>