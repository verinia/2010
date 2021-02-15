<?php get_header() ?>
    <div id="scroll">
    <div id="content" style="height: auto;">
        <div id="left">
        <?php  if ( !is_user_logged_in() ) { ?>
            <a href="register.php" class="createbutton" onmouseover="h(this)" onmouseout="u(this)">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/img/main/home/create.jpg" alt="Create a Free Account">
                <span class="shim"></span>
            </a>
        <?php } ?>


            <div id="features">
                <div class="narrowHeader">Website Features</div>
                <div class="section">
                    <div class="feature">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/img/main/home/feature_kbsearch_icon.jpg" alt="">
                        <div class="featureTitle">Search <?php bloginfo( 'name' ); ?></div>
                        <div class="featureDesc" style="padding: 2px 2px 0">
                            <form action="">
                                <input type="text" class="input" name="s" placeholder="Seach" length="16" size="16" style="margin-bottom: 5px;">
                                <input type="submit" value="Go" class="button-bg">
                            </form>
                        </div>
                    </div>
                    
                    <div class="feature">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/img/main/home/feature_upgrade_icon.jpg" alt="">
                        <div class="featureTitle">Upgrade Your Account</div>
                        <div class="featureDesc">Find out more about members' benefits and Upgrade Here</div>
                    </div>
                    
                    <div class="feature">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/img/main/home/feature_shop_icon.jpg" alt="">
                        <div class="featureTitle"><?php bloginfo( 'name' ); ?> Online Shop</div>
                        <div class="featureDesc">Visit our online store.<br> Click here...</div>
                    </div>
                    
                    <div class="feature">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/img/main/home/feature_poll_icon.jpg" alt="">
                        <div class="featureTitle">Server Status</div>
                        <div class="featureDesc">Player-submitted Polls #16 - (Free/Member) Vote Here</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="right">
                <div class="button-box">
                    <div id="buttons">
                        <a href="#" class="freebutton" onmouseover="h(this)" onmouseout="u(this)">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod('bootstrap_theme_name') ); ?>/img/main/home/free.png"><span class="shim"></span>
                        </a>
                        <a href="#" class="memberbutton" onmouseover="h(this)" onmouseout="u(this)">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/layout-<?php esc_html_e( get_theme_mod( 'bootstrap_theme_name' ) ); ?>/img/main/home/member.png"><span class="shim"></span>
                        </a>
                    </div>
                </div>

            <div id="latestcontent">
                <div class="sectionHeader">
                    <div class="left">
                    <div class="right">
                        <div class="plaque">Featured News</div>
                    </div>
                    </div>
                </div>
                <?php 
                   // the query
                   $the_query = new WP_Query( array(
                      'posts_per_page' => 1,
                   )); 
                ?>

                <?php if ( $the_query->have_posts() ) : ?>
                  <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                                <?php $featured_image = get_post_meta( get_the_ID(), 'meta_box_lrg_news', true ); ?>
                <div class="section">
                    <div class="sectionBody">
                        <div class="sectionHeight">
                            <div class="newsTitle"><h3><?php the_title(); ?></h3><span><?php date("D M Y", the_date()); ?></span></div>
                            <div class="newsImage"><img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/Large/<?php echo $featured_image;?>.jpg" alt=""></div>
                            <div class="newsDesc">
                                <p><?php the_excerpt(); ?></p><a href="<?php the_permalink(); ?>">Read more...</a>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>

                  <?php endwhile; ?>
                  <?php wp_reset_postdata(); ?>

                <?php else : ?>
                <div class="section">
                    <div class="sectionBody">
                        <div class="sectionHeight">
                            <div class="newsTitle"><h3>No News Found!</h3><span><?php date("D M Y"); ?></span></div>
                            <div class="newsImage"><img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/Large/update.jpg" alt=""></div>
                            <div class="newsDesc">
                                <p>We were unable to locate any news articles.</p>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
                <?php endif; ?>

            </div>

            <div id="recentnews">
                <div class="sectionHeader">
                    <div class="left">
                    <div class="right">
                        <div class="plaque">Recent News</div>
                    </div>
                    </div>
                </div>
                            <?php 
                   // the query
                   $the_query = new WP_Query( array(
                      'posts_per_page' => 5,
                   )); 
                
                    $i = 0;
                ?>

                <?php if ( $the_query->have_posts() ) : ?>
                  <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                    <?php $i++; ?>
                                <?php $featured_image2 = get_post_meta( get_the_ID(), 'meta_box_sml_news', true ); ?>
                        <?php if($i == 1) {
                            } else { ?>
                                <div class="section">
                                    <div class="sectionBody first">
                                        <div class="recentNews">
                                            <div class="newsTitle"><h3><?php the_title() ?></h3><span><?php date("D M Y", the_date()); ?></span></div>
                                            <div class="newsIcon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/news/<?php echo $featured_image2;?>.jpg" alt=""></div>
                                            <p><?php the_excerpt();?></p><a href="<?php the_permalink();?>">Read more...</a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>  


                  <?php endwhile; ?>
                  <?php wp_reset_postdata(); ?>

                <?php else : ?>
                <?php endif; ?>

                
                
            </div>
        </div>
    </div>
    </div>


<?php get_footer() ?>
