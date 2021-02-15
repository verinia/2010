<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap" id="rs-structure">
    <?php
    $title = __('Structure', 'rs2010-forum');
    $titleUpdated = __('Structure updated.', 'rs2010-forum');
    $this->render_admin_header($title, $titleUpdated);

    $categories = $this->rs2010forum->content->get_categories(false);
    ?>

    <div id="hidden-data" style="display: none;">
        <select id="data-forum-parent-one-level">
            <?php
            if ($categories) {
                foreach ($categories as $category) {
                    echo '<option value="'.$category->term_id.'_0">'.$category->name.'</option>';
                }
            }
            ?>
        </select>

        <select id="data-forum-parent-two-level">
            <?php
            if ($categories) {
                foreach ($categories as $category) {
                    echo '<option value="'.$category->term_id.'_0">'.$category->name.'</option>';

                    $forums = $this->rs2010forum->get_forums($category->term_id, 0);

                    if ($forums) {
                        foreach ($forums as $forum) {
                            echo '<option value="'.$category->term_id.'_'.$forum->id.'">&mdash; '.esc_html($forum->name).'</option>';
                        }
                    }
                }
            }
            ?>
        </select>
    </div>

    <div id="editor-container" class="settings-box" style="display: none;">
        <div class="settings-header"></div>
        <div class="editor-instance" id="category-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_save_category'); ?>
                <input type="hidden" name="category_id" value="new">

                <table class="form-table">
                    <tr>
                        <th><label for="category_name"><?php _e('Name:', 'rs2010-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="200" name="category_name" id="category_name" value="" required></td>
                    </tr>
                    <tr>
                        <th><label for="category_access"><?php _e('Access:', 'rs2010-forum'); ?></label></th>
                        <td>
                            <select name="category_access" id="category_access">
                                <option value="everyone"><?php _e('Everyone', 'rs2010-forum'); ?></option>
                                <option value="loggedin"><?php _e('Logged in users only', 'rs2010-forum'); ?></option>
                                <option value="moderator"><?php _e('Moderators only', 'rs2010-forum'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="category_order"><?php _e('Order:', 'rs2010-forum'); ?></label></th>
                        <td><input type="number" size="4" id="category_order" name="category_order" value="" min="1"></td>
                    </tr>
                    <?php Rs2010ForumUserGroups::renderCategoryEditorFields(); ?>
                </table>

                <p class="submit">
                    <input type="submit" name="rs-create-edit-category-submit" value="<?php _e('Save', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance" id="forum-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_save_forum'); ?>
                <input type="hidden" name="forum_id" value="new">
                <input type="hidden" name="forum_category" value="0">
                <input type="hidden" name="forum_parent_forum" value="0">

                <table class="form-table">
                    <tr>
                        <th><label for="forum_name"><?php _e('Name:', 'rs2010-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="255" name="forum_name" id="forum_name" value="" required></td>
                    </tr>
                    <tr>
                        <th><label for="forum_description"><?php _e('Description:', 'rs2010-forum'); ?></label></th>
                        <td><input type="text" size="100" maxlength="255" id="forum_description" name="forum_description" value=""></td>
                    </tr>
                    <tr>
                        <th><label for="forum_parent"><?php _e('Parent:', 'rs2010-forum'); ?></label></th>
                        <td>
                            <select name="forum_parent" id="forum_parent"></select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="forum_icon"><?php _e('Icon:', 'rs2010-forum'); ?></label></th>
                        <td>
                            <select name="forum_icon" id="forum-icon-select">
                                <option value="discontinued_items_2">Discontinued Items</option>
                                <option value="diversions">Diversions</option>
                                <option value="duelling_2">Duelling</option>
                                <option value="dutch">Dutch</option>
                                <option value="events_2">Events</option>
                                <option value="farming_2">Farming</option>
                                <option value="feedback1">Feedback</option>
                                <option value="fin">Fin</option>
                                <option value="fletching_2">Fletching</option>
                                <option value="food_2">Food</option>
                                <option value="forum_feedback_2">Feedback</option>
                                <option value="forum_games_2">Games</option>
                                <option value="future_updates_2">Future Updates</option>
                                <option value="general_2">General</option>
                                <option value="goalsandachievements_2">Goals &amp; Achievments</option>
                                <option value="Guides">Guides</option>
                                <option value="herblore_2">Herblore</option>
                                <option value="item_discussion_2">Item Discussion</option>
                                <option value="jc">Jagex Cup</option>
                                <option value="Jl_icon">Crown</option>
                                <option value="locations_2">Map</option>
                                <option value="minigames2">Minigames</option>
                                <option value="misc_2">Misc</option>
                                <option value="monsters_2">Monsters</option>
                                <option value="news_announcements_2">News &amp; Announcements</option>
                                <option value="off_topic_2">Off Topic</option>
                                <option value="ores_bars_2">Ores &amp; Bars</option>
                                <option value="player_help_1b">Player Help</option>
                                <option value="player_vs_monster">PvM</option>
                                <option value="port">Port</option>
                                <option value="Production">Hammer</option>
                                <option value="quest">Quests</option>
                                <option value="rants_2">Rants</option>
                                <option value="recent_updates_2">Recent Updates</option>
                                <option value="recruitment_100_1">Recruitment 1</option>
                                <option value="recruitment_100_2">Recruitment 2</option>
                                <option value="Recruitment_looking">Recruitment Looking</option>
                                <option value="Recruitment_Specialist">Recruitment Special</option>
                                <option value="Resource">Resource</option>
                                <option value="roleplaying_2">Rolpelaying</option>
                                <option value="runes_2">Runes</option>
                                <option value="Shop-icon">Shop</option>
                                <option value="skill">Skills</option>
                                <option value="stories_2">Stories</option>
                                <option value="suggest_minigame">Suggestion: Minigame</option>
                                <option value="suggest_misc">Suggestion: Misc</option>
                                <option value="suggest_npc">Suggestion: NPC</option>
                                <option value="suggest_pvp">Suggestion: PVP</option>
                                <option value="suggest_quest">Suggestion: Quest</option>
                                <option value="summoning">Summoning</option>
                                <option value="tech_support_2">Tech Support</option>
                                <option value="treasure_trails_2">Treasure Trails</option>
                                <option value="weapons_forum_2">Weapons</option>
                                <option value="web_feedback_2">Feedback</option>
                                <option value="ach_diary">Achievement Diary</option>
                                <option value="area_and_achievement_diary">Achievement Diary 2</option>
                                <option value="armour_2">Armour</option>
                                <option value="billing">Billing</option>
                                <option value="bug">Bugs</option>
                                <option value="Character">Character</option>
                                <option value="clan">Clan</option>
                                <option value="clan_diplomacy">Clan1</option>
                                <option value="clan_discussions">Clan2</option>
                                <option value="clan_homes">Clan3</option>
                                <option value="clue_scrolls_2">Clue Scrolls</option>
                                <option value="Combat">Combat</option>
                                <option value="construction_2">Construction</option>
                                <option value="contact_us">Contact</option>
                                <option value="crafting_2">Crafting</option>
                            </select>
                                    <script>
                            jQuery(function ($) {
                                $('#forum-icon-select').change(function(){
                                    var url = "<?php echo plugin_dir_url( __DIR__ ); ?>/images/icons/" + $('#forum-icon-select').val() + ".gif";
                                    $('#icon-example').attr('src', url);
                                });
                            });
                        </script>
                            <img src="<?php echo plugin_dir_url( __DIR__ ).'/images/icons/discontinued_items_2.gif'; ?>" id="icon-example">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="forum_status"><?php _e('Status:', 'rs2010-forum'); ?></label></th>
                        <td>
                            <select name="forum_status" id="forum_status">
                                <option value="normal"><?php _e('Normal', 'rs2010-forum'); ?></option>
                                <option value="closed"><?php _e('Closed', 'rs2010-forum'); ?></option>
                                <option value="approval"><?php _e('Approval', 'rs2010-forum'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="forum_order"><?php _e('Order:', 'rs2010-forum'); ?></label></th>
                        <td><input type="number" size="4" id="forum_order" name="forum_order" value="" min="1"></td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="rs-create-edit-forum-submit" value="<?php _e('Save', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="category-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_delete_category'); ?>
                <input type="hidden" name="category-id" value="0">
                <p><?php _e('Deleting this category will also permanently delete all forums, sub-forums, topics and posts inside it. Are you sure you want to delete this category?', 'rs2010-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="rs2010-forum-delete-category" value="<?php _e('Delete', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="forum-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_delete_forum'); ?>
                <input type="hidden" name="forum-id" value="0">
                <input type="hidden" name="forum-category" value="0">
                <p><?php _e('Deleting this forum will also permanently delete all sub-forums, topics and posts inside it. Are you sure you want to delete this forum?', 'rs2010-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="rs2010-forum-delete-forum" value="<?php _e('Delete', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>
    </div>

    <a href="#" class="category-editor-link add-element" data-value-id="new" data-value-editor-title="<?php _e('Add Category', 'rs2010-forum'); ?>">
        <?php
        echo '<span class="fas fa-plus"></span>';
        _e('Add Category', 'rs2010-forum');
        ?>
    </a>

    <?php
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $term_meta = get_term_meta($category->term_id);
            $access = (!empty($term_meta['category_access'][0])) ? $term_meta['category_access'][0] : 'everyone';
            $order = (!empty($term_meta['order'][0])) ? $term_meta['order'][0] : 1;
            echo '<input type="hidden" id="category_'.$category->term_id.'_name" value="'.esc_html(stripslashes($category->name)).'">';
            echo '<input type="hidden" id="category_'.$category->term_id.'_access" value="'.$access.'">';
            echo '<input type="hidden" id="category_'.$category->term_id.'_order" value="'.$order.'">';
            Rs2010ForumUserGroups::renderHiddenFields($category->term_id);

            $forums = $this->rs2010forum->get_forums($category->term_id, 0, ARRAY_A);
            ?>
            <div class="settings-box">
                <div class="settings-header">
                    <span class="fas fa-box"></span>
                    <?php echo stripslashes($category->name); ?>&nbsp;
                    <span class="element-id">
                        <?php
                        echo '(';
                        echo __('ID', 'rs2010-forum').': '.$category->term_id;
                        echo ' &middot; ';
                        echo __('Access:', 'rs2010-forum').' ';
                        if ($access === 'everyone') {
                            _e('Everyone', 'rs2010-forum');
                        } else if ($access === 'loggedin') {
                            _e('Logged in users only', 'rs2010-forum');
                        } else if ($access === 'moderator') {
                            _e('Moderators only', 'rs2010-forum');
                        }
                        echo ' &middot; ';
                        echo __('Order:', 'rs2010-forum').' '.$order;
                        Rs2010ForumUserGroups::renderUserGroupsInCategory($category->term_id);
                        do_action('rs2010forum_admin_show_custom_category_data', $category->term_id);
                        echo ')';
                        ?>
                    </span>
                    <span class="category-actions">
                        <a href="#" class="category-delete-link action-delete" data-value-id="<?php echo $category->term_id; ?>" data-value-editor-title="<?php _e('Delete Category', 'rs2010-forum'); ?>"><?php _e('Delete Category', 'rs2010-forum'); ?></a>
                        &middot;
                        <a href="#" class="category-editor-link action-edit" data-value-id="<?php echo $category->term_id; ?>" data-value-editor-title="<?php _e('Edit Category', 'rs2010-forum'); ?>"><?php _e('Edit Category', 'rs2010-forum'); ?></a>
                    </span>
                </div>
                <?php
                if (!empty($forums)) {
                    $structureTable = new Rs2010_Forum_Admin_Structure_Table($forums);
                    $structureTable->prepare_items();
                    $structureTable->display();
                }
                ?>
                <a href="#" class="forum-editor-link add-element" data-value-id="new" data-value-category="<?php echo $category->term_id; ?>" data-value-parent-forum="0" data-value-editor-title="<?php _e('Add Forum', 'rs2010-forum'); ?>">
                    <?php
                    echo '<span class="fas fa-plus"></span>';
                    _e('Add Forum', 'rs2010-forum');
                    ?>
                </a>
            </div>
            <?php
        }

        echo '<a href="#" class="category-editor-link add-element" data-value-id="new" data-value-editor-title="'.__('Add Category', 'rs2010-forum').'">';
            echo '<span class="fas fa-plus"></span>';
            _e('Add Category', 'rs2010-forum');
        echo '</a>';
    }
    ?>
</div>
