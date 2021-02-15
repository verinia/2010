<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumProfile {
    private $rs2010forum = null;

    public function __construct($object) {
        $this->rs2010forum = $object;

        add_action('rs2010forum_breadcrumbs_profile', array($this, 'add_breadcrumbs_profile'));
        add_action('rs2010forum_breadcrumbs_history', array($this, 'add_breadcrumbs_history'));
    }

    // Checks if the profile functionality is enabled.
    public function functionalityEnabled() {
        return $this->rs2010forum->options['enable_profiles'];
    }

    // Checks if profile links should be hidden for the current user.
    public function hideProfileLink() {
        if (!is_user_logged_in() && $this->rs2010forum->options['hide_profiles_from_guests']) {
            return true;
        } else {
            return false;
        }
    }

    public function get_user_data($user_id) {
        return get_user_by('id', $user_id);
    }

    // Gets the current title.
    public function get_profile_title() {
        $currentTitle = __('Profile', 'rs2010-forum').$this->get_title_suffix();

        return $currentTitle;
    }

    public function get_history_title() {
        $currentTitle = __('Post History', 'rs2010-forum').$this->get_title_suffix();

        return $currentTitle;
    }

    private function get_title_suffix() {
        $suffix = '';
        $userData = $this->get_user_data($this->rs2010forum->current_element);

        if ($userData) {
            $suffix = ': '.$userData->display_name;
        }

        return $suffix;
    }

    // Sets the breadcrumbs.
    public function add_breadcrumbs_profile() {
        $elementLink = $this->rs2010forum->get_link('current');
        $elementTitle = __('Profile', 'rs2010-forum').$this->get_title_suffix();
        $this->rs2010forum->breadcrumbs->add_breadcrumb($elementLink, $elementTitle);
    }

    public function add_breadcrumbs_history() {
        $elementLink = $this->rs2010forum->get_link('current');
        $elementTitle = __('Post History', 'rs2010-forum').$this->get_title_suffix();
        $this->rs2010forum->breadcrumbs->add_breadcrumb($elementLink, $elementTitle);
    }

    public function show_profile_header($user_data) {
        $userOnline = ($this->rs2010forum->online->is_user_online($user_data->ID)) ? ' class="user-online"' : '';
        $background_style = '';
        $user_id = $user_data->ID;

        echo '<div id="profile-header"'.$userOnline.'>';
            if ($this->rs2010forum->options['enable_avatars']) {

                $url = get_avatar_url($user_id, 480);

                // Add filter for custom profile header
                $url = apply_filters('rs2010forum_filter_profile_header_image', $url, $user_id);

                $background_style = 'style="background-image: url(\''.$url.'\');"';
            }

            echo '<div class="background-avatar" '.$background_style.'></div>';
            echo '<div class="background-contrast"></div>';

            // Show avatar.
            if ($this->rs2010forum->options['enable_avatars']) {
                echo get_avatar($user_data->ID, 160, '', '', array('force_display' => true));
            }

            echo '<div class="user-info">';
                echo '<div class="profile-display-name">'.$user_data->display_name.'</div>';

                $role = $this->rs2010forum->permissions->getForumRole($user_id);

                // Special styling for banned users.
                if ($this->rs2010forum->permissions->get_forum_role($user_id) === 'banned') {
                    $role = '<span class="banned">'.$role.'</span>';
                }

                echo '<div class="profile-forum-role">';
                $count_posts = $this->rs2010forum->countPostsByUser($user_id);
                $this->rs2010forum->render_reputation_badges($count_posts);
                echo $role;
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    public function show_profile_navigation($user_data) {
        echo '<div id="profile-navigation">';
            $profile_link = $this->getProfileLink($user_data);
            $history_link = $this->get_history_link($user_data);

            // Profile link.
            if ($this->rs2010forum->current_view === 'profile') {
                echo '<a class="active" href="'.$profile_link.'">'.__('Profile', 'rs2010-forum').'</a>';
            } else {
                echo '<a href="'.$profile_link.'">'.__('Profile', 'rs2010-forum').'</a>';
            }

            // Subscriptions link.
            if ($this->rs2010forum->current_view === 'history') {
                echo '<a class="active" href="'.$history_link.'">'.__('Post History', 'rs2010-forum').'</a>';
            } else {
                echo '<a href="'.$history_link.'">'.__('Post History', 'rs2010-forum').'</a>';
            }

            do_action('rs2010forum_custom_profile_menu');
        echo '</div>';
    }

    public function count_post_history_by_user($user_id) {
        return count($this->get_post_history_by_user($user_id));
    }

    public function get_post_history_by_user($user_id, $limit = false) {
        // Get accessible categories for the current user first.
        $accessible_categories = $this->rs2010forum->content->get_categories_ids();

        if (empty($accessible_categories)) {
            // Cancel if the user cant access any categories.
            return false;
        } else {
            // Now load history-data based for an user based on the categories which are accessible for the current user.
            $accessible_categories = implode(',', $accessible_categories);

            $query_limit = "";

            if ($limit) {
                $elements_maximum = 50;
                $elements_start = $this->rs2010forum->current_page * $elements_maximum;

                $query_limit = "LIMIT {$elements_start}, {$elements_maximum}";
            }

            $query = "SELECT p.id, p.text, p.date, p.parent_id, t.name FROM {$this->rs2010forum->tables->posts} AS p, {$this->rs2010forum->tables->topics} AS t WHERE p.author_id = %d AND p.parent_id = t.id AND EXISTS (SELECT f.id FROM {$this->rs2010forum->tables->forums} AS f WHERE f.id = t.parent_id AND f.parent_id IN ({$accessible_categories})) AND t.approved = 1 ORDER BY p.id DESC {$query_limit};";

            return $this->rs2010forum->db->get_results($this->rs2010forum->db->prepare($query, $user_id));
        }
    }

    public function show_history() {
        $user_id = $this->rs2010forum->current_element;
        $userData = $this->get_user_data($user_id);

        if ($userData) {
            if ($this->hideProfileLink()) {
                _e('You need to login to have access to profiles.', 'rs2010-forum');
            } else {
                $this->show_profile_header($userData);
                $this->show_profile_navigation($userData);

                echo '<div id="profile-layer">';
                    $posts = $this->get_post_history_by_user($user_id, true);

                    if (empty($posts)) {
                        _e('No posts made by this user.', 'rs2010-forum');
                    } else {
                        $pagination = $this->rs2010forum->pagination->renderPagination('history', $user_id);

                        if ($pagination) {
                            echo '<div class="pages-and-menu">'.$pagination.'</div>';
                        }

                        foreach ($posts as $post) {
                            echo '<div class="history-element">';
                                echo '<div class="history-name">';
                                    $link = $this->rs2010forum->rewrite->get_post_link($post->id, $post->parent_id);
                                    $text = esc_html(stripslashes(strip_tags($post->text)));
                                    $text = $this->rs2010forum->cut_string($text, 100);

                                    echo '<a class="history-title" href="'.$link.'">'.$text.'</a>';

                                    $topic_link = $this->rs2010forum->rewrite->get_link('topic', $post->parent_id);
                                    $topic_name = esc_html(stripslashes($post->name));
                                    $topic_time = sprintf(__('%s ago', 'rs2010-forum'), human_time_diff(strtotime($post->date), current_time('timestamp')));

                                    echo '<span class="history-topic">'.__('In:', 'rs2010-forum').' <a href="'.$topic_link.'">'.$topic_name.'</a></span>';
                                echo '</div>';

                                echo '<div class="history-time">'.$topic_time.'</div>';
                            echo '</div>';
                        }

                        if ($pagination) {
                            echo '<div class="pages-and-menu">'.$pagination.'</div>';
                        }
                    }
                echo '</div>';
            }
        } else {
            _e('This user does not exist.', 'rs2010-forum');
        }
    }

    // Shows the profile of a user.
    public function showProfile() {
        $user_id = $this->rs2010forum->current_element;
        $userData = $this->get_user_data($user_id);

        if ($userData) {
            if ($this->hideProfileLink()) {
                _e('You need to login to have access to profiles.', 'rs2010-forum');
            } else {
                $this->show_profile_header($userData);
                $this->show_profile_navigation($userData);

                echo '<div id="profile-content">';
                    // Show first name.
                    if (!empty($userData->first_name)) {
                        $cellTitle = __('First Name:', 'rs2010-forum');
                        $cellValue = $userData->first_name;

                        $this->renderProfileRow($cellTitle, $cellValue);
                    }

                    // Show usergroups.
                    $userGroups = Rs2010ForumUserGroups::getUserGroupsOfUser($userData->ID, 'all', true);

                    if (!empty($userGroups)) {
                        $cellTitle = __('Usergroups:', 'rs2010-forum');
                        $cellValue = $userGroups;

                        $this->renderProfileRow($cellTitle, $cellValue, 'usergroups');
                    }

                    // Show website.
                    if (!empty($userData->user_url)) {
                        $cellTitle = __('Website:', 'rs2010-forum');
                        $cellValue = '<a href="'.$userData->user_url.'" rel="nofollow" target="_blank">'.$userData->user_url.'</a>';

                        $this->renderProfileRow($cellTitle, $cellValue);
                    }

                    // Show last seen.
                    if ($this->rs2010forum->online->functionality_enabled && $this->rs2010forum->options['show_last_seen']) {
                        $cellTitle = __('Last seen:', 'rs2010-forum');
                        $cellValue = $this->rs2010forum->online->last_seen($userData->ID);

                        $this->renderProfileRow($cellTitle, $cellValue);
                    }

                    // Show member since.
                    $cellTitle = __('Member Since:', 'rs2010-forum');
                    $cellValue = $this->rs2010forum->format_date($userData->user_registered, false);

                    $this->renderProfileRow($cellTitle, $cellValue);

                    // Show biographical info.
                    if (!empty($userData->description)) {
                        $cellTitle = __('Biographical Info:', 'rs2010-forum');
                        $cellValue = trim(wpautop(esc_html($userData->description)));

                        $this->renderProfileRow($cellTitle, $cellValue);
                    }

                    // Show signature.
                    $signature = $this->rs2010forum->get_signature($userData->ID);

                    if ($signature !== false) {
                        $cellTitle = __('Signature:', 'rs2010-forum');
                        $cellValue = $signature;

                        $this->renderProfileRow($cellTitle, $cellValue);
                    }

                    do_action('rs2010forum_profile_row', $userData);

                    echo '<div class="profile-section-header">';
                        echo '<span class="profile-section-header-icon fas fa-address-card"></span>';
                        echo __('Member Activity', 'rs2010-forum');
                    echo '</div>';

                    echo '<div class="profile-section-content">';
                        // Topics started.
                        $count_topics = $this->rs2010forum->countTopicsByUser($userData->ID);
                        Rs2010ForumStatistics::renderStatisticsElement(__('Topics Started', 'rs2010-forum'), $count_topics, 'far fa-comments');

                        // Replies created.
                        $count_posts = $this->rs2010forum->countPostsByUser($userData->ID);
                        $count_posts = $count_posts - $count_topics;
                        Rs2010ForumStatistics::renderStatisticsElement(__('Replies Created', 'rs2010-forum'), $count_posts, 'far fa-comment');

                        // Likes Received.
                        if ($this->rs2010forum->options['enable_reactions']) {
                            $count_likes = $this->rs2010forum->reactions->get_reactions_received($userData->ID, 'up');
                            Rs2010ForumStatistics::renderStatisticsElement(__('Likes Received', 'rs2010-forum'), $count_likes, 'fas fa-thumbs-up');
                        }
                    echo '</div>';

                    do_action('rs2010forum_custom_profile_content', $userData);

                    $current_user_id = get_current_user_id();

                    if ($userData->ID == $current_user_id) {
                        echo '<a href="'.get_edit_profile_url().'" class="edit-profile-link">';
                            echo '<span class="fas fa-pencil-alt"></span>';
                            echo __('Edit Profile', 'rs2010-forum');
                        echo '</a>';
                    }

                    // Check if the current user can ban this user.
                    if ($this->rs2010forum->permissions->can_ban_user($current_user_id, $userData->ID)) {
                        if ($this->rs2010forum->permissions->isBanned($userData->ID)) {
                            $url = $this->getProfileLink($userData, array('unban_user' => $userData->ID));
                            $nonce_url = wp_nonce_url($url, 'unban_user_'.$userData->ID);
                            echo '<a class="banned" href="'.$nonce_url.'">'.__('Unban User', 'rs2010-forum').'</a>';
                        } else {
                            $url = $this->getProfileLink($userData, array('ban_user' => $userData->ID));
                            $nonce_url = wp_nonce_url($url, 'ban_user_'.$userData->ID);
                            echo '<a class="banned" href="'.$nonce_url.'">'.__('Ban User', 'rs2010-forum').'</a>';
                        }
                    }
                echo '</div>';
            }
        } else {
            _e('This user does not exist.', 'rs2010-forum');
        }
    }

    public function renderProfileRow($cellTitle, $cellValue, $type = '') {
        echo '<div class="profile-row">';
            echo '<div>'.$cellTitle.'</div>';
            echo '<div>';

            if (is_array($cellValue)) {
                foreach ($cellValue as $value) {
                    if ($type == 'usergroups') {
                        echo Rs2010ForumUserGroups::render_usergroup_tag($value);
                    } else {
                        echo $value.'<br>';
                    }
                }
            } else {
                echo $cellValue;
            }

            echo '</div>';
        echo '</div>';
    }

    public function getProfileLink($userObject, $additional_parameters = false) {
        if ($this->hideProfileLink() || !$this->functionalityEnabled()) {
            return false;
        } else {
            $profileLink = $this->rs2010forum->get_link('profile', $userObject->ID, $additional_parameters, '', false);
            $profileLink = apply_filters('rs2010forum_filter_profile_link', $profileLink, $userObject);

            return $profileLink;
        }
    }

    public function get_history_link($userObject) {
        if ($this->hideProfileLink() || !$this->functionalityEnabled()) {
            return false;
        } else {
            $profileLink = $this->rs2010forum->get_link('history', $userObject->ID);
            $profileLink = apply_filters('rs2010forum_filter_history_link', $profileLink, $userObject);

            return $profileLink;
        }
    }

    // Renders a link to the own profile. The own profile is always available, even when the profile functionality is disabled.
    public function myProfileLink() {
        // First check if the user is logged in.
        if ($this->functionalityEnabled()) {

            $profileLink = '';

            // Only continue if the current user is logged in.
            if (is_user_logged_in()) {
                // Get current user.
                $currentUserObject = wp_get_current_user();

                // Get and build profile link.
                $profileLink = $this->getProfileLink($currentUserObject);

                return array(
                    'menu_class'        => 'profile-link',
                    'menu_link_text'    => esc_html__('Profile', 'rs2010-forum'),
                    'menu_url'          => $profileLink,
                    'menu_login_status' => 1,
                    'menu_new_tab'      => false
                );
            }
        }
    }
}
