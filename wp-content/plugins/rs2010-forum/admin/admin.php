<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumAdmin {
    private $rs2010forum = null;
    var $saved = false;
    var $error = false;
    var $option_views = false;

    function __construct($object) {
        $this->rs2010forum = $object;

        // Set the views for the available options.
        $this->set_option_views();

        add_action('wp_loaded', array($this, 'save_settings'));
        add_action('admin_menu', array($this, 'add_admin_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // User profile options.
        add_action('edit_user_profile', array($this, 'user_profile_fields'));
        add_action('show_user_profile', array($this, 'user_profile_fields'));
        add_action('edit_user_profile_update', array($this, 'user_profile_fields_update'));
        add_action('personal_options_update', array($this, 'user_profile_fields_update'));

        // Users list in administration.
        add_filter('manage_users_columns', array($this, 'manage_users_columns'));
        add_action('manage_users_custom_column', array($this, 'manage_users_custom_column'), 10, 3);
    }

    function set_option_views() {
        $this->option_views = array(
            'general' => array(
                'label' => __('General', 'rs2010-forum'),
                'icon' => 'fas fa-sliders-h'
            ),
            'features' => array(
                'label' => __('Features', 'rs2010-forum'),
                'icon' => 'fas fa-plug'
            ),
            'urls' => array(
                'label' => __('URLs & SEO', 'rs2010-forum'),
                'icon' => 'fas fa-link'
            ),
            'permissions' => array(
                'label' => __('Permissions', 'rs2010-forum'),
                'icon' => 'fas fa-user-shield'
            ),
            'breadcrumbs' => array(
                'label' => __('Breadcrumbs', 'rs2010-forum'),
                'icon' => 'fas fa-map-marked'
            ),
            'notifications' => array(
                'label' => __('Notifications', 'rs2010-forum'),
                'icon' => 'fas fa-envelope'
            ),
            'mentioning' => array(
                'label' => __('Mentioning', 'rs2010-forum'),
                'icon' => 'fas fa-at'
            ),
            'memberslist' => array(
                'label' => __('Members List', 'rs2010-forum'),
                'icon' => 'fas fa-users'
            ),
            'profiles' => array(
                'label' => __('Profiles', 'rs2010-forum'),
                'icon' => 'fas fa-user'
            ),
            'uploads' => array(
                'label' => __('Uploads', 'rs2010-forum'),
                'icon' => 'fas fa-upload'
            ),
            'reports' => array(
                'label' => __('Reports', 'rs2010-forum'),
                'icon' => 'fas fa-exclamation-triangle'
            ),
            'signatures' => array(
                'label' => __('Signatures', 'rs2010-forum'),
                'icon' => 'fas fa-signature'
            ),
            'activity' => array(
                'label' => __('Activity', 'rs2010-forum'),
                'icon' => 'fas fa-bullhorn'
            ),
            'ads' => array(
                'label' => __('Ads', 'rs2010-forum'),
                'icon' => 'fas fa-ad'
            ),
            'polls' => array(
                'label' => __('Polls', 'rs2010-forum'),
                'icon' => 'fas fa-poll-h'
            ),
            'spoilers' => array(
                'label' => __('Spoilers', 'rs2010-forum'),
                'icon' => 'fas fa-eye-slash'
            ),
            'reputation' => array(
                'label' => __('Reputation', 'rs2010-forum'),
                'icon' => 'fas fa-medal'
            )
        );
    }

    function render_options_header($option) {
        echo '<div class="settings-header">';
            echo '<span class="'.$this->option_views[$option]['icon'].'"></span>';
            echo $this->option_views[$option]['label'];
        echo '</div>';
    }

    function user_profile_fields($user) {
        $output = '';

        // Show settings only when current user is admin ...
        if (current_user_can('manage_options')) {
            // ... and he edits a non-admin user.
            if (!user_can($user->ID, 'manage_options')) {
                $role = $this->rs2010forum->permissions->get_forum_role($user->ID);

                $output .= '<tr>';
                $output .= '<th><label for="rs2010forum_role">'.__('Forum Role', 'rs2010-forum').'</label></th>';
                $output .= '<td>';

                $output .= '<select name="rs2010forum_role" id="rs2010forum_role">';
                $output .= '<option value="normal" '.selected($role, 'normal', false).'>'.__('User', 'rs2010-forum').'</option>';
                $output .= '<option value="moderator" '.selected($role, 'moderator', false).'>'.__('Moderator', 'rs2010-forum').'</option>';
                $output .= '<option value="administrator" '.selected($role, 'administrator', false).'>'.__('Administrator', 'rs2010-forum').'</option>';
                $output .= '<option value="banned" '.selected($role, 'banned', false).'>'.__('Banned', 'rs2010-forum').'</option>';
                $output .= '</select>';

                $output .= '</td>';
                $output .= '</tr>';
            }

            $output .= Rs2010ForumUserGroups::showUserProfileFields($user->ID);
        }

        if ($this->rs2010forum->options['enable_mentioning']) {
            $output .= '<tr>';
            $output .= '<th><label for="rs2010forum_mention_notify">'.__('Notify me when I get mentioned', 'rs2010-forum').'</label></th>';
            $output .= '<td><input type="checkbox" name="rs2010forum_mention_notify" id="rs2010forum_mention_notify" value="1" '.checked($this->rs2010forum->mentioning->user_wants_notification($user->ID), true, false).'></td>';
            $output .= '</tr>';
        }

        if ($this->rs2010forum->options['allow_signatures']) {
            // Ensure that the user has permission to use a signature.
            if ($this->rs2010forum->permissions->can_use_signature($user->ID)) {
                $output .= '<tr>';
                $output .= '<th><label for="rs2010forum_signature">'.__('Signature', 'rs2010-forum').'</label></th>';
                $output .= '<td>';
                $output .= '<textarea rows="5" cols="30" name="rs2010forum_signature" id="rs2010forum_signature">'.get_user_meta($user->ID, 'rs2010forum_signature', true).'</textarea>';

                // Show info about allowed HTML tags.
                if ($this->rs2010forum->options['signatures_html_allowed']) {
                    $output .= '<p class="description">';
                    $output .= __('You can use the following HTML tags in signatures:', 'rs2010-forum');
                    $output .= '&nbsp;<code>'.esc_html($this->rs2010forum->options['signatures_html_tags']).'</code>';
                    $output .= '</p>';
                } else {
                    $output .= '<p class="description">'.__('HTML tags are not allowed in signatures.', 'rs2010-forum').'</p>';
                }

                $output .= '</td>';
                $output .= '</tr>';
            }
        }

        if (!empty($output)) {
            echo '<h2>'.__('Forum', 'rs2010-forum').'</h2>';
            echo '<table class="form-table">';
            echo $output;
            echo '</table>';
        }
    }

    function user_profile_fields_update($user_id) {
        $user_id = absint($user_id);

        if (current_user_can('manage_options')) {
            if (!user_can($user_id, 'manage_options')) {
                if (isset($_POST['rs2010forum_role'])) {
                    $this->rs2010forum->permissions->set_forum_role($user_id, $_POST['rs2010forum_role']);
                }
            }

            Rs2010ForumUserGroups::updateUserProfileFields($user_id);
        }

        if ($this->rs2010forum->options['enable_mentioning']) {
            if (isset($_POST['rs2010forum_mention_notify'])) {
                update_user_meta($user_id, 'rs2010forum_mention_notify', 'yes');
            } else {
                update_user_meta($user_id, 'rs2010forum_mention_notify', 'no');
            }
        }

        if ($this->rs2010forum->options['allow_signatures']) {
            // Ensure that the user has permission to use a signature.
            if ($this->rs2010forum->permissions->can_use_signature($user_id)) {
                if (isset($_POST['rs2010forum_signature'])) {
                    if ($this->rs2010forum->options['signatures_html_allowed']) {
                        update_user_meta($user_id, 'rs2010forum_signature', trim(wp_kses_post(strip_tags($_POST['rs2010forum_signature'], $this->rs2010forum->options['signatures_html_tags']))));
                    } else {
                        update_user_meta($user_id, 'rs2010forum_signature', trim(wp_kses_post(strip_tags($_POST['rs2010forum_signature']))));
                    }
                } else {
                    delete_user_meta($user_id, 'rs2010forum_signature');
                }
            }
        }
    }

    // Add all required pages to the menu.
    function add_admin_pages() {
        if ($this->rs2010forum->permissions->isAdministrator('current')) {
            add_menu_page(__('Forum', 'rs2010-forum'), __('Forum', 'rs2010-forum'), 'read', 'rs2010forum-structure', array($this, 'structure_page'), 'none');
            add_submenu_page('rs2010forum-structure', __('Structure', 'rs2010-forum'), __('Structure', 'rs2010-forum'), 'read', 'rs2010forum-structure', array($this, 'structure_page'));
            add_submenu_page('rs2010forum-structure', __('Appearance', 'rs2010-forum'), __('Appearance', 'rs2010-forum'), 'read', 'rs2010forum-appearance', array($this, 'appearance_page'));
            add_submenu_page('rs2010forum-structure', __('Usergroups', 'rs2010-forum'), __('Usergroups', 'rs2010-forum'), 'read', 'rs2010forum-usergroups', array($this, 'usergroups_page'));

            if ($this->rs2010forum->options['enable_ads']) {
                add_submenu_page('rs2010forum-structure', __('Ads', 'rs2010-forum'), __('Ads', 'rs2010-forum'), 'read', 'rs2010forum-ads', array($this, 'ads_page'));
            }

            do_action('rs2010forum_add_admin_submenu_page');

            add_submenu_page('rs2010forum-structure', __('Settings', 'rs2010-forum'), __('Settings', 'rs2010-forum'), 'read', 'rs2010forum-options', array($this, 'options_page'));
        }
    }

    function options_page() {
        require('views/options.php');
    }

    function structure_page() {
        require('views/structure.php');
    }

    function appearance_page() {
        require('views/appearance.php');
    }

    function usergroups_page() {
        require('views/usergroups.php');
    }

    function ads_page() {
        require('views/ads.php');
    }

    function enqueue_admin_scripts($hook) {
        wp_enqueue_style('rs2010forum-fontawesome', $this->rs2010forum->plugin_url.'libs/fontawesome/css/all.min.css', array(), $this->rs2010forum->version);
        wp_enqueue_style('rs2010forum-fontawesome-compat-v4', $this->rs2010forum->plugin_url.'libs/fontawesome/css/v4-shims.min.css', array(), $this->rs2010forum->version);
        wp_enqueue_style('rs2010forum-admin-css', $this->rs2010forum->plugin_url.'admin/css/admin.css', array(), $this->rs2010forum->version);

        if (strstr($hook, 'rs2010forum') !== false) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_code_editor(array('type' => 'text/html'));
            wp_enqueue_script('rs2010forum-admin-js', $this->rs2010forum->plugin_url.'admin/js/admin.js', array('jquery', 'wp-color-picker'), $this->rs2010forum->version, true);
        }
    }

    function save_settings() {
        // Only save changes when the user is an forum/site administrator.
        if ($this->rs2010forum->permissions->isAdministrator('current')) {
            if (isset($_POST['af_options_submit'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_save_options');

                $this->save_options();
            } else if (isset($_POST['af_appearance_submit'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_save_appearance');

                $this->save_appearance();
            } else if (isset($_POST['rs-create-edit-forum-submit'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_save_forum');

                $this->save_forum();
            } else if (isset($_POST['rs2010-forum-delete-forum'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_delete_forum');

                if (!empty($_POST['forum-id']) && is_numeric($_POST['forum-id']) && !empty($_POST['forum-category']) && is_numeric($_POST['forum-category'])) {
                    $this->delete_forum($_POST['forum-id'], $_POST['forum-category']);
                }
            } else if (isset($_POST['rs-create-edit-category-submit'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_save_category');

                $this->save_category();
            } else if (isset($_POST['rs2010-forum-delete-category'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_delete_category');

                if (!empty($_POST['category-id']) && is_numeric($_POST['category-id'])) {
                    $this->delete_category($_POST['category-id']);
                }
            } else if (isset($_POST['rs-create-edit-usergroup-category-submit'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_save_usergroup_category');

                $saveStatus = Rs2010ForumUserGroups::saveUserGroupCategory();

                if (is_wp_error($saveStatus)) {
                    $this->error = $saveStatus->get_error_message();
                } else {
                    $this->saved = $saveStatus;
                }
            } else if (isset($_POST['rs-create-edit-usergroup-submit'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_save_usergroup');

                $saveStatus = Rs2010ForumUserGroups::saveUserGroup();

                if (is_wp_error($saveStatus)) {
                    $this->error = $saveStatus->get_error_message();
                } else {
                    $this->saved = $saveStatus;
                }
            } else if (isset($_POST['rs2010-forum-delete-usergroup'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_delete_usergroup');

                if (!empty($_POST['usergroup-id']) && is_numeric($_POST['usergroup-id'])) {
                    Rs2010ForumUserGroups::deleteUserGroup($_POST['usergroup-id']);
                }
            } else if (isset($_POST['rs2010-forum-delete-usergroup-category'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_delete_usergroup_category');

                if (!empty($_POST['usergroup-category-id']) && is_numeric($_POST['usergroup-category-id'])) {
                    Rs2010ForumUserGroups::deleteUserGroupCategory($_POST['usergroup-category-id']);
                }
            } else if (isset($_POST['rs-create-edit-ad-submit'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_save_ad');

                $ad_id          = $_POST['ad_id'];
                $ad_name        = trim($_POST['ad_name']);
                $ad_code        = trim($_POST['ad_code']);
                $ad_active      = isset($_POST['ad_active']) ? 1 : 0;
                $ad_locations   = isset($_POST['ad_locations']) ? implode(',', $_POST['ad_locations']) : '';

                $this->rs2010forum->ads->save_ad($ad_id, $ad_name, $ad_code, $ad_active, $ad_locations);
                $this->saved = true;
            } else if (isset($_POST['rs2010-forum-delete-ad'])) {
                // Verify nonce first.
                check_admin_referer('rs2010_forum_delete_ad');

                if (!empty($_POST['ad_id']) && is_numeric($_POST['ad_id'])) {
                    $this->rs2010forum->ads->delete_ad($_POST['ad_id']);
                    $this->saved = true;
                }
            }
        }
    }

    /* OPTIONS */
    function save_options() {
        $saved_ops = array();

        foreach ($this->rs2010forum->options_default as $k => $v) {
            if (isset($_POST[$k])) {
                if (is_numeric($v)) {
                    $saved_ops[$k] = ((int)$_POST[$k] >= 0) ? (int)$_POST[$k] : $v;
                } else if (is_bool($v)) {
                    $saved_ops[$k] = (bool)$_POST[$k];
                } else if ($k === 'allowed_filetypes') {
                    $tmp = stripslashes(strtolower(trim($_POST[$k])));
                    $saved_ops[$k] = (!empty($tmp)) ? $tmp : $v;
                } else {
                    $tmp = stripslashes(trim($_POST[$k]));
                    $saved_ops[$k] = (!empty($tmp)) ? $tmp : $v;
                }
            } else {
                if (is_bool($v)) {
                    $saved_ops[$k] = false;
                } else {
                    $saved_ops[$k] = $v;
                }
            }
        }

        $this->rs2010forum->save_options($saved_ops);
        $this->saved = true;
    }

    function save_appearance() {
        $saved_ops = array();

        foreach ($this->rs2010forum->appearance->options_default as $k => $v) {
            if (isset($_POST[$k])) {
                $tmp = stripslashes(trim($_POST[$k]));
                $saved_ops[$k] = (!empty($tmp)) ? $tmp : $v;
            } else {
                $saved_ops[$k] = $v;
            }
        }

        $this->rs2010forum->appearance->save_options($saved_ops);
        $this->saved = true;
    }

    /* STRUCTURE */
    function save_category() {
        $category_id        = $_POST['category_id'];
        $category_name      = trim($_POST['category_name']);
        $category_access    = trim($_POST['category_access']);
        $category_order     = (is_numeric($_POST['category_order'])) ? $_POST['category_order'] : 1;

        if (!empty($category_name)) {
            if ($category_id === 'new') {
                $newTerm = wp_insert_term($category_name, 'rs2010forum-category');

                // Return possible error.
                if (is_wp_error($newTerm)) {
                    $this->error = $newTerm->get_error_message();
                    return;
                }

                $category_id = $newTerm['term_id'];
            } else {
                wp_update_term($category_id, 'rs2010forum-category', array('name' => $category_name));
            }

            update_term_meta($category_id, 'category_access', $category_access);
            update_term_meta($category_id, 'order', $category_order);
            Rs2010ForumUserGroups::saveUserGroupsOfForumCategory($category_id);

            $this->saved = true;
        }
    }

    function save_forum() {
        // ID of the forum.
        $forum_id           = $_POST['forum_id'];

        // Determine parent IDs.
        $parent_ids          = explode('_', $_POST['forum_parent']);
        $forum_category     = $parent_ids[0];
        $forum_parent_forum = $parent_ids[1];

        // Additional data.
        $forum_name         = trim($_POST['forum_name']);
        $forum_description  = trim($_POST['forum_description']);
        $forum_icon         = trim($_POST['forum_icon']);
        $forum_icon         = (empty($forum_icon)) ? 'news_announcements_2' : $forum_icon;
        $forum_status       = $_POST['forum_status'];
        $forum_order        = (is_numeric($_POST['forum_order'])) ? $_POST['forum_order'] : 0;

        if (!empty($forum_name)) {
            if ($forum_id === 'new') {
                $this->rs2010forum->content->insert_forum($forum_category, $forum_name, $forum_description, $forum_parent_forum, $forum_icon, $forum_order, $forum_status);
            } else {
                // Update forum.
                $this->rs2010forum->db->update(
                    $this->rs2010forum->tables->forums,
                    array('name' => $forum_name, 'description' => $forum_description, 'icon' => $forum_icon, 'sort' => $forum_order, 'forum_status' => $forum_status, 'parent_id' => $forum_category, 'parent_forum' => $forum_parent_forum),
                    array('id' => $forum_id),
                    array('%s', '%s', '%s', '%d', '%s', '%d', '%d'),
                    array('%d')
                );

                // Update category ids of sub-forums in case the forum got moved.
                $this->rs2010forum->db->update(
                    $this->rs2010forum->tables->forums,
                    array('parent_id' => $forum_category),
                    array('parent_forum' => $forum_id),
                    array('%d'),
                    array('%d')
                );

                // Approve all unapproved topics in a forum if its status is not set to approval.
                if ($forum_status != 'approval') {
                    // Get all unapproved topics from this forum.
                    $unapproved_topics = $this->rs2010forum->approval->get_unapproved_topics($forum_id);

                    // Approve those topics if found.
                    if (!empty($unapproved_topics)) {
                        foreach ($unapproved_topics as $topic) {
                            $this->rs2010forum->approval->approve_topic($topic->id);
                        }
                    }
                }
            }

            $this->saved = true;
        }
    }

    function delete_category($categoryID) {
        $forums = $this->rs2010forum->db->get_col("SELECT id FROM {$this->rs2010forum->tables->forums} WHERE parent_id = {$categoryID};");

        if (!empty($forums)) {
            foreach ($forums as $forum) {
                $this->delete_forum($forum, $categoryID);
            }
        }

        wp_delete_term($categoryID, 'rs2010forum-category');
    }

    function delete_forum($forum_id, $category_id) {
        // Delete all subforums first
        $subforums = $this->rs2010forum->get_forums($category_id, $forum_id);

        if (count($subforums) > 0) {
            foreach ($subforums as $subforum) {
                $this->delete_forum($subforum->id, $category_id);
            }
        }

        // Delete all topics.
        $topics = $this->rs2010forum->db->get_col("SELECT id FROM {$this->rs2010forum->tables->topics} WHERE parent_id = {$forum_id};");

        if (!empty($topics)) {
            foreach ($topics as $topic) {
                $this->rs2010forum->delete_topic($topic, true, false);
            }
        }

        // Delete subscriptions for this forum.
        $this->rs2010forum->notifications->remove_all_forum_subscriptions($forum_id);

        // Last but not least delete the forum
        $this->rs2010forum->db->delete($this->rs2010forum->tables->forums, array('id' => $forum_id), array('%d'));

        $this->saved = true;
    }

    /* USERGROUPS */
    function render_admin_header($title, $titleUpdated) {
        // Workaround to ensure that admin-notices are shown outside of our panel.
        echo '<h1 id="rs2010-panel-notice-area"></h1>';

        echo '<div id="rs2010-panel">';
            echo '<div class="header-panel">';
                echo '<div class="sub-panel-left">';
                    echo '<img src="'.$this->rs2010forum->plugin_url.'admin/images/logo.png">';
                echo '</div>';
                echo '<div class="sub-panel-left">';
                    echo '<h1>'.$title.'</h1>';
                echo '</div>';
                echo '<div class="sub-panel-right">';
                    echo '<a href="https://www.rs2010.de/support/" target="_blank">';
                        echo '<span class="rs2010-panel-icon fas fa-user"></span>';
                        echo __('Official Support Forum', 'rs2010-forum');
                    echo '</a>';
                    echo '&bull;';
                    echo '<a href="https://www.rs2010.de/docs/" target="_blank">';
                        echo '<span class="rs2010-panel-icon fas fa-book"></span>';
                        echo __('Documentation', 'rs2010-forum');
                    echo '</a>';
                    echo '&bull;';
                    echo '<a href="https://www.rs2010.de/donate/" target="_blank">';
                        echo '<span class="rs2010-panel-icon donate-icon fas fa-heart"></span>';
                        echo __('Donate', 'rs2010-forum');
                    echo '</a>';
                echo '</div>';
                echo '<div class="clear"></div>';
            echo '</div>';

            if ($this->error) {
                echo '<div class="error-panel"><p>'.$this->error.'</p></div>';
            } else if ($this->saved) {
                echo '<div class="updated-panel"><p>'.$titleUpdated.'</p></div>';
            }

        echo '</div>';
    }

    // Users List in Administration.
    public function manage_users_columns($columns) {
        $columns['forum-user-posts'] = __('Forum Posts', 'rs2010-forum');
        return $columns;
  	}

    public function manage_users_custom_column($output, $column_name, $user_id) {
		if ($column_name === 'forum-user-posts') {
            $output .= $this->rs2010forum->content->count_posts_by_user($user_id);
		}

        return $output;
	}
}
