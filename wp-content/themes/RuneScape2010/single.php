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
                <div class="plaque"><?php the_title(); ?></div>
            </div>
            </div>
        </div>

    <div class="section" style="padding: 0;">
        <div class="article">
            <div class="topshadow">
                <div class="bottombordershad">
                <div class="leftshadow">
                <div class="rightshadow">
                <div class="leftcorner">
                <div class="rightcorner">
                <div class="bottomleftshad">
                <div class="bottomrightshad">
                    <div class="pagepad">
                        <div class="newsJustify" style="padding: 45px;">
                            <?php the_content();?>
                        </div>
                      <div class="clear"></div>
                    </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<?php get_footer();?>