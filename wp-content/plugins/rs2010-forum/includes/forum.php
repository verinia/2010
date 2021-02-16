<?php

if (!defined('ABSPATH')) exit;

class Rs2010Forum {
    var $version = '1.15.9';
    var $executePlugin = false;
    var $db = null;
    var $tables = null;
    var $plugin_url = '';
    var $plugin_path = '';
    var $date_format = '';
    var $time_format = '';
    var $error = false;
    var $current_description = false;
    var $current_category = false;
    var $current_forum = false;
    var $current_forum_name = false;
    var $current_topic = false;
    var $current_topic_name = false;
    var $current_post = false;
    var $current_view = false;
    var $current_element = false;
    var $current_page = 0;
    var $parent_forum = false;
    var $parent_forum_name = false;
    var $category_access_level = false;
    var $parents_set = false;

    // We need this flag if want to prevent modifications of core queries in certain cases.
    var $prevent_query_modifications = false;

    var $options = array();
    var $options_default = array(
        'forum_title'                       => '',
        'forum_description'                 => '',
        'location'                          => 0,
        'posts_per_page'                    => 10,
        'topics_per_page'                   => 20,
        'members_per_page'                  => 25,
        'allow_shortcodes'                  => false,
        'embed_content'                     => true,
        'allow_guest_postings'              => false,
        'allowed_filetypes'                 => 'jpg,jpeg,gif,png,bmp,pdf',
        'allow_file_uploads'                => false,
        'upload_permission'                 => 'loggedin',
        'hide_uploads_from_guests'          => false,
        'hide_profiles_from_guests'         => false,
        'uploads_maximum_number'            => 5,
        'uploads_maximum_size'              => 5,
        'uploads_show_thumbnails'           => true,
        'admin_subscriptions'               => false,
        'allow_subscriptions'               => true,
        'notification_sender_name'          => '',
        'notification_sender_mail'          => '',
        'receivers_admin_notifications'     => '',
        'mail_template_new_post_subject'    => '',
        'mail_template_new_post_message'    => '',
        'mail_template_new_topic_subject'   => '',
        'mail_template_new_topic_message'   => '',
        'mail_template_mentioned_subject'   => '',
        'mail_template_mentioned_message'   => '',
        'allow_signatures'                  => false,
        'signatures_permission'             => 'loggedin',
        'signatures_html_allowed'           => false,
        'signatures_html_tags'              => '<br><a><i><b><u><s><img><strong>',
        'enable_avatars'                    => true,
        'enable_mentioning'                 => true,
        'enable_mentioning_suggestions'     => true,
        'enable_reactions'                  => true,
        'enable_search'                     => true,
        'enable_profiles'                   => true,
        'enable_memberslist'                => true,
        'memberslist_filter_normal'         => true,
        'memberslist_filter_moderator'      => true,
        'memberslist_filter_administrator'  => true,
        'memberslist_filter_banned'         => true,
        'enable_rss'                        => false,
        'load_fontawesome'                  => true,
        'load_fontawesome_compat_v4'        => true,
        'count_topic_views'                 => true,
        'reports_enabled'                   => true,
        'reports_notifications'             => true,
        'memberslist_loggedin_only'         => false,
        'show_login_button'                 => true,
        'show_logout_button'                => true,
        'show_register_button'              => true,
        'show_who_is_online'                => true,
        'show_last_seen'                    => true,
        'show_newest_member'                => true,
        'show_statistics'                   => true,
        'enable_breadcrumbs'                => true,
        'breadcrumbs_show_category'         => true,
        'highlight_admin'                   => true,
        'highlight_authors'                 => true,
        'show_author_posts_counter'         => true,
        'show_edit_date'                    => true,
        'enable_edit_post'                  => true,
        'time_limit_edit_posts'             => 0,
        'enable_delete_post'                => false,
        'time_limit_delete_posts'           => 3,
        'enable_delete_topic'               => false,
        'time_limit_delete_topics'          => 3,
        'enable_open_topic'                 => false,
        'enable_close_topic'                => false,
        'show_description_in_forum'         => false,
        'require_login'                     => false,
        'require_login_posts'               => false,
        'create_blog_topics_id'             => 0,
        'enable_ads'                        => true,
        'ads_frequency_categories'          => 2,
        'ads_frequency_forums'              => 4,
        'ads_frequency_topics'              => 8,
        'ads_frequency_posts'               => 6,
        'approval_for'                      => 'guests',
        'enable_activity'                   => true,
        'activity_days'                     => 14,
        'activities_per_page'               => 50,
        'enable_polls'                      => true,
        'polls_permission'                  => 'loggedin',
        'polls_results_visible'             => false,
        'enable_seo_urls'                   => true,
        'seo_url_mode_content'              => 'slug',
        'seo_url_mode_profile'              => 'slug',
        'custom_url_login'                  => '',
        'custom_url_register'               => '',
        'view_name_activity'                => 'activity',
        'view_name_subscriptions'           => 'subscriptions',
        'view_name_search'                  => 'search',
        'view_name_forum'                   => 'forum',
        'view_name_topic'                   => 'topic',
        'view_name_addtopic'                => 'addtopic',
        'view_name_movetopic'               => 'movetopic',
        'view_name_addpost'                 => 'addpost',
        'view_name_editpost'                => 'editpost',
        'view_name_markallread'             => 'markallread',
        'view_name_members'                 => 'members',
        'view_name_profile'                 => 'profile',
        'view_name_history'                 => 'history',
        'view_name_unread'                  => 'unread',
        'view_name_unapproved'              => 'unapproved',
        'view_name_reports'                 => 'reports',
        'title_separator'                   => '-',
        'enable_spoilers'                   => true,
        'hide_spoilers_from_guests'         => false,
        'subforums_location'                => 'above',
        'enable_reputation'                 => true,
        'reputation_level_1_posts'          => 10,
        'reputation_level_2_posts'          => 25,
        'reputation_level_3_posts'          => 100,
        'reputation_level_4_posts'          => 250,
        'reputation_level_5_posts'          => 1000
    );
    var $options_editor = array(
        'media_buttons' => false,
        'editor_height' => 250,
        'teeny'         => false,
        'quicktags'     => false
    );
    var $cache          = array();   // Used to store selected database queries.
    var $rewrite        = null;
    var $shortcode      = null;
    var $reports        = null;
    var $profile        = null;
    var $editor         = null;
    var $reactions      = null;
    var $mentioning     = null;
    var $notifications  = null;
    var $appearance     = null;
    var $uploads        = null;
    var $search         = null;
    var $online         = null;
    var $content        = null;
    var $breadcrumbs    = null;
    var $activity       = null;
    var $memberslist    = null;
    var $pagination     = null;
    var $unread         = null;
    var $feed           = null;
    var $permissions    = null;
    var $ads            = null;
    var $approval       = null;
    var $spoilers       = null;
    var $polls          = null;

    function __construct() {
        // Initialize database.
        global $wpdb;
        $database = new Rs2010ForumDatabase();
        $this->tables = $database->getTables();
        $this->db = $wpdb;

        $this->plugin_url = plugin_dir_url(dirname(__FILE__));
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        $this->load_options();
        $this->date_format = get_option('date_format');
        $this->time_format = get_option('time_format');

        add_action('wp', array($this, 'prepare'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_css_js'), 10);

        // Add certain classes to body-tag.
        add_filter('body_class', array($this, 'add_body_classes'));

        // Add filters for modifying the title of the page.
        add_filter('wp_title', array($this, 'change_wp_title'), 100, 3);
        add_filter('document_title_parts', array($this, 'change_document_title_parts'), 100);
        add_filter('pre_get_document_title', array($this, 'change_pre_get_document_title'), 100);
        add_filter('document_title_separator', array($this, 'change_document_title_separator'), 100);

        add_filter('oembed_dataparse', array($this, 'prevent_oembed_dataparse'), 10, 3);

        // Deleting an user.
        add_action('delete_user_form', array($this, 'delete_user_form_reassign'), 10, 2);
        add_action('deleted_user', array($this, 'deleted_user_reassign'), 10, 2);

        // Hooks for automatic topic creation.
        add_action('transition_post_status', array($this, 'post_transition_update'), 10, 3);
        add_action('load-post.php', array($this, 'add_topic_meta_box_setup'));
        add_action('load-post-new.php', array($this, 'add_topic_meta_box_setup'));

        new Rs2010ForumCompatibility($this);
        new Rs2010ForumStatistics($this);
        new Rs2010ForumUserGroups($this);
        new Rs2010ForumWidgets($this);

        $this->rewrite          = new Rs2010ForumRewrite($this);
        $this->shortcode        = new Rs2010ForumShortcodes($this);
        $this->reports          = new Rs2010ForumReports($this);
        $this->profile          = new Rs2010ForumProfile($this);
        $this->editor           = new Rs2010ForumEditor($this);
        $this->reactions        = new Rs2010ForumReactions($this);
        $this->mentioning       = new Rs2010ForumMentioning($this);
        $this->notifications    = new Rs2010ForumNotifications($this);
        $this->appearance       = new Rs2010ForumAppearance($this);
        $this->uploads          = new Rs2010ForumUploads($this);
        $this->search           = new Rs2010ForumSearch($this);
        $this->online           = new Rs2010ForumOnline($this);
        $this->content          = new Rs2010ForumContent($this);
        $this->breadcrumbs      = new Rs2010ForumBreadCrumbs($this);
        $this->activity         = new Rs2010ForumActivity($this);
        $this->memberslist      = new Rs2010ForumMembersList($this);
        $this->pagination       = new Rs2010ForumPagination($this);
        $this->unread           = new Rs2010ForumUnread($this);
        $this->feed             = new Rs2010ForumFeed($this);
        $this->permissions      = new Rs2010ForumPermissions($this);
        $this->ads              = new Rs2010ForumAds($this);
        $this->approval         = new Rs2010ForumApproval($this);
        $this->spoilers         = new Rs2010ForumSpoilers($this);
        $this->polls            = new Rs2010ForumPolls($this);
    }

    //======================================================================
    // FUNCTIONS FOR GETTING AND SETTING OPTIONS.
    //======================================================================

    function load_options() {
        // Get options and merge them with the default ones.
		$current_options = get_option('rs2010forum_options', array());
        $this->options = array_merge($this->options_default, $current_options);

        // Ensure default values if some needed inputs got deleted.
        if (empty($this->options['forum_title'])) {
            $this->options['forum_title'] = __('Forum', 'rs2010-forum');
        }

        if (empty($this->options['notification_sender_name'])) {
            $this->options['notification_sender_name'] = get_bloginfo('name');
        }

        if (empty($this->options['notification_sender_mail'])) {
            $this->options['notification_sender_mail'] = get_bloginfo('admin_email');
        }

        if (empty($this->options['receivers_admin_notifications'])) {
            $this->options['receivers_admin_notifications'] = get_bloginfo('admin_email');
        }

        if (empty($this->options['mail_template_new_post_subject'])) {
            $this->options['mail_template_new_post_subject'] = __('New reply: ###TITLE###', 'rs2010-forum');
        }

        if (empty($this->options['mail_template_new_post_message'])) {
            $this->options['mail_template_new_post_message'] = __('Hello ###USERNAME###,<br><br>You received this message because there is a new reply in a forum-topic you have subscribed to.<br><br>Topic:<br>###TITLE###<br><br>Author:<br>###AUTHOR###<br><br>Reply:<br>###CONTENT###<br><br>Link:<br>###LINK###<br><br>If you dont want to receive these mails anymore you can unsubscribe via the subscription-area. Please dont reply to this mail!', 'rs2010-forum');
        }

        if (empty($this->options['mail_template_new_topic_subject'])) {
            $this->options['mail_template_new_topic_subject'] = __('New topic: ###TITLE###', 'rs2010-forum');
        }

        if (empty($this->options['mail_template_new_topic_message'])) {
            $this->options['mail_template_new_topic_message'] = __('Hello ###USERNAME###,<br><br>You received this message because there is a new forum-topic.<br><br>Topic:<br>###TITLE###<br><br>Author:<br>###AUTHOR###<br><br>Text:<br>###CONTENT###<br><br>Link:<br>###LINK###<br><br>If you dont want to receive these mails anymore you can unsubscribe via the subscription-area. Please dont reply to this mail!', 'rs2010-forum');
        }

        if (empty($this->options['mail_template_mentioned_subject'])) {
            $this->options['mail_template_mentioned_subject'] = __('You have been mentioned!', 'rs2010-forum');
        }

        if (empty($this->options['mail_template_mentioned_message'])) {
            $this->options['mail_template_mentioned_message'] = __('Hello ###USERNAME###,<br><br>You have been mentioned in a forum-post.<br><br>Topic:<br>###TITLE###<br><br>Author:<br>###AUTHOR###<br><br>Text:<br>###CONTENT###<br><br>Link:<br>###LINK###', 'rs2010-forum');
        }

        if (empty($this->options['title_separator'])) {
            $this->options['title_separator'] = '-';
        }
    }

    function save_options($options) {
        update_option('rs2010forum_options', $options);

        // Reload options after saving them.
        $this->load_options();
    }

    //======================================================================
    // FUNCTIONS FOR PAGE TITLE.
    //======================================================================

    function change_wp_title($title, $sep, $seplocation) {
        return $this->get_title($title);
    }

    function change_document_title_parts($title) {
        $title['title'] = $this->get_title($title['title']);
        return $title;
    }

    function change_pre_get_document_title($title) {
        // Only modify it when a title is already set.
        if (!empty($title)) {
            $title = $this->get_title($title);
        }

        return $title;
    }

    function change_document_title_separator($document_title_separator) {
        if ($this->executePlugin) {
            $document_title_separator = $this->get_title_separator();
        }

        return $document_title_separator;
    }

    function get_title_separator() {
        // The default title-separator is based on the forum-settings.
        $title_separator = $this->options['title_separator'];

        // Allow other plugins to modify the title-separator.
        $title_separator = apply_filters('rs2010forum_title_separator', $title_separator);

        return $title_separator;
    }

    function get_title($title) {
        if ($this->executePlugin) {
            $metaTitle = $this->getMetaTitle();

            if ($metaTitle) {
                $title_separator = $this->get_title_separator();
                $title = $metaTitle.' '.$title_separator.' '.$title;
            }
        }

        return $title;
    }

    // Gets the pages meta title.
    public function getMetaTitle() {
        // Get the main title by default with disabled default title generation.
        $metaTitle = $this->getMainTitle(false);

        // Apply custom modifications.
        if (!$this->error && $this->current_view) {
            if ($this->current_view === 'forum' && $this->current_forum) {
                $metaTitle = $this->add_current_page_to_title($metaTitle);
            } else if ($this->current_view === 'topic' && $this->current_topic) {
                $metaTitle = $this->add_current_page_to_title($metaTitle);
            }
        }

        return $metaTitle;
    }

    // Adds the current page to a title.
    function add_current_page_to_title($someString) {
        if ($this->current_page > 0) {
            $currentPage = $this->current_page + 1;
            $title_separator = $this->get_title_separator();
            $someString .= ' '.$title_separator.' '.__('Page', 'rs2010-forum').' '.$currentPage;
        }

        return $someString;
    }

    function prepare() {
        global $post;

        if (is_a($post, 'WP_Post') && $this->shortcode->checkForShortcode($post)) {
            $this->executePlugin = true;
            $this->options['location'] = $post->ID;
        }

        do_action('rs2010forum_execution_check');

        // Set all base links.
        if ($this->executePlugin || get_post($this->options['location'])) {
            $this->rewrite->set_links();
        }

        if (!$this->executePlugin) {
            return;
        }

        // Parse the URL.
        $this->rewrite->parse_url();

        // Update online status.
        $this->online->update_online_status();

        do_action('rs2010forum_prepare');

        switch ($this->current_view) {
            case 'forum':
            case 'addtopic':
                $this->setParents($this->current_element, 'forum');
            break;
            case 'movetopic':
            case 'topic':
            case 'addpost':
                $this->setParents($this->current_element, 'topic');
            break;
            case 'editpost':
                $this->setParents($this->current_element, 'post');
            break;
            case 'markallread':
            case 'unread':
            break;
            case 'subscriptions':
                // Go back to the overview when this functionality is not enabled or the user is not logged-in.
                if (!$this->options['allow_subscriptions'] || !is_user_logged_in()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'search':
                // Go back to the overview when this functionality is not enabled.
                if (!$this->options['enable_search']) {
                    $this->current_view = 'overview';
                }
            break;
            case 'profile':
            case 'history':
                if (!$this->profile->functionalityEnabled()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'members':
                // Go back to the overview when this functionality is not enabled.
                if (!$this->memberslist->functionality_enabled()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'activity':
                if (!$this->activity->functionality_enabled()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'unapproved':
                // Ensure that the user is at least a moderator.
                if (!$this->permissions->isModerator('current')) {
                    $this->current_view = 'overview';
                }
            break;
            case 'reports':
                // Ensure that the user is at least a moderator.
                if (!$this->permissions->isModerator('current')) {
                    $this->current_view = 'overview';
                }
            break;
            default:
                $this->current_view = 'overview';
            break;
        }

        $this->shortcode->handleAttributes();

        // Check access.
        $this->check_access();

        // Override editor settings.
        $this->options_editor = apply_filters('rs2010forum_filter_editor_settings', $this->options_editor);

        // Prevent generation of some head-elements.
        remove_action('wp_head', 'rel_canonical');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');

        if (isset($_POST['submit_action'])) {
            $this->content->do_insertion();
        } else if (isset($_GET['move_topic'])) {
            $this->moveTopic();
        } else if (isset($_GET['delete_topic'])) {
            $this->delete_topic($this->current_topic);
        } else if (isset($_GET['remove_post'])) {
            $post_id = (!empty($_GET['post'])) ? absint($_GET['post']) : 0;
            $this->remove_post($post_id);
        } else if (!empty($_POST['sticky_topic']) || isset($_GET['sticky_topic'])) {
            $sticky_mode = 1;

            if (!empty($_POST['sticky_topic'])) {
                $sticky_mode = (int) $_POST['sticky_topic'];
            }

            $this->set_sticky($this->current_topic, $sticky_mode);
        } else if (isset($_GET['unsticky_topic'])) {
            $this->set_sticky($this->current_topic, 0);
        } else if (isset($_GET['open_topic'])) {
            $this->change_status('open');
        } else if (isset($_GET['close_topic'])) {
            $this->change_status('closed');
        } else if (isset($_GET['approve_topic'])) {
            $this->approval->approve_topic($this->current_topic);
        } else if (isset($_GET['subscribe_topic'])) {
            $topic_id = $_GET['subscribe_topic'];
            $this->notifications->subscribe_topic($topic_id);
        } else if (isset($_GET['unsubscribe_topic'])) {
            $topic_id = $_GET['unsubscribe_topic'];
            $this->notifications->unsubscribe_topic($topic_id);
        } else if (isset($_GET['subscribe_forum'])) {
            $forum_id = $_GET['subscribe_forum'];
            $this->notifications->subscribe_forum($forum_id);
        } else if (isset($_GET['unsubscribe_forum'])) {
            $forum_id = $_GET['unsubscribe_forum'];
            $this->notifications->unsubscribe_forum($forum_id);
        } else if (isset($_GET['report_add'])) {
            $post_id = (!empty($_GET['post'])) ? absint($_GET['post']) : 0;
            $reporter_id = get_current_user_id();

            $this->reports->add_report($post_id, $reporter_id);
        } else if (!empty($_GET['report_delete']) && is_numeric($_GET['report_delete'])) {
            $this->reports->remove_report($_GET['report_delete']);
        }

        do_action('rs2010forum_prepare_'.$this->current_view);
    }

    function check_access() {
        // Check login access.
        if ($this->options['require_login'] && !is_user_logged_in()) {
            $this->error = __('Sorry, only logged-in users can access the forum.', 'rs2010-forum');
            $this->error = apply_filters('rs2010forum_filter_error_message_require_login', $this->error);
            return;
        }

        // Check category access.
        $this->category_access_level = get_term_meta($this->current_category, 'category_access', true);

        if ($this->category_access_level) {
            if ($this->category_access_level === 'loggedin' && !is_user_logged_in()) {
                $this->error = __('Sorry, only logged-in users can access this category.', 'rs2010-forum');
                return;
            }

            if ($this->category_access_level === 'moderator' && !$this->permissions->isModerator('current')) {
                $this->error = __('Sorry, you cannot access this area.', 'rs2010-forum');
                return;
            }
        }

        // Check usergroups access.
        if (!Rs2010ForumUserGroups::checkAccess($this->current_category)) {
            $this->error = __('Sorry, you cannot access this area.', 'rs2010-forum');
            return;
        }

        if ($this->current_view === 'topic' || $this->current_view === 'addpost') {
            // Check topic-access.
            if ($this->options['require_login_posts'] && !is_user_logged_in()) {
                $this->error = __('Sorry, only logged-in users can access this topic.', 'rs2010-forum');
                return;
            }

            // Check unapproved-topic access.
            if (!$this->approval->is_topic_approved($this->current_topic) && !$this->permissions->isModerator('current')) {
                $this->rewrite->redirect('overview');
            }
        }

        // Check custom access.
        $custom_access = apply_filters('rs2010forum_filter_check_access', true, $this->current_category);

        if (!$custom_access) {
            $this->error = __('Sorry, you cannot access this area.', 'rs2010-forum');
            return;
        }

        // Add a login-notice if necessary.
        if (!is_user_logged_in() && !$this->options['allow_guest_postings']) {
            $show_login = $this->showLoginLink();
            $show_register = $this->showRegisterLink();

            $notice = '';

            if ($show_login) {
                $login_link = '<u><a class="'.$show_login['menu_class'].'" href="'.$show_login['menu_url'].'">'.$show_login['menu_link_text'].'</a></u>';

                if ($show_register) {
                    $register_link = '<u><a class="'.$show_register['menu_class'].'" href="'.$show_register['menu_url'].'">'.$show_register['menu_link_text'].'</a></u>';
                    $notice = sprintf(esc_html__('Please %s or %s to create posts and topics.', 'rs2010-forum'), $login_link, $register_link);
                } else {
                    $notice = sprintf(esc_html__('Please %s to create posts and topics.', 'rs2010-forum'), $login_link);
                }
            } else {
                $notice = __('You need to log in to create posts and topics.', 'rs2010-forum');
            }

            $notice = apply_filters('rs2010forum_filter_login_message', $notice);

            $this->add_notice($notice);
        }
    }

    function enqueue_css_js() {
        if ($this->options['load_fontawesome']) {
			wp_enqueue_style('rs-fontawesome', $this->plugin_url.'libs/fontawesome/css/all.min.css', array(), $this->version);
		}

		if ($this->options['load_fontawesome_compat_v4']) {
			wp_enqueue_style('rs-fontawesome-compat-v4', $this->plugin_url.'libs/fontawesome/css/v4-shims.min.css', array(), $this->version);
		}

        $themeurl = $this->appearance->get_current_theme_url();

		wp_enqueue_style('rs-widgets', $themeurl.'/widgets.css', array(), $this->version);

        if (!$this->executePlugin) {
            return;
        }

        wp_enqueue_style('rs-style', $themeurl.'/style.css', array(), $this->version);

        if (is_rtl()) {
            wp_enqueue_style('rs-rtl', $themeurl.'/rtl.css', array(), $this->version);
        }

        wp_enqueue_script('rs2010forum-js', $this->plugin_url.'js/script.js', array('jquery', 'wp-api'), $this->version);
        wp_localize_script('wp-api', 'wpApiSettings', array('root' => esc_url_raw(rest_url()), 'nonce' => wp_create_nonce('wp_rest')));

        if ($this->options['enable_spoilers']) {
            wp_enqueue_script('rs2010forum-js-spoilers', $this->plugin_url.'js/script-spoilers.js', array('jquery'), $this->version);
        }

        do_action('rs2010forum_enqueue_css_js');
    }

    // Add certain classes to body-tag.
    function add_body_classes($classes) {
        if ($this->executePlugin) {
            $classes[] = 'rs2010-forum';
            $classes[] = 'rs2010-forum-'.$this->current_view;
        }

        return $classes;
    }

    // Gets the pages main title.
    public function getMainTitle($setDefaultTitle = true) {
        $mainTitle = false;

        if ($setDefaultTitle) {
            $mainTitle = $this->options['forum_title'];
        }

        if (!$this->error && $this->current_view) {
            if ($this->current_view === 'forum' && $this->current_forum) {
                $mainTitle = esc_html(stripslashes($this->current_forum_name));
            } else if ($this->current_view === 'topic' && $this->current_topic) {
                $mainTitle = esc_html(stripslashes($this->current_topic_name));
            } else if ($this->current_view === 'editpost') {
                $mainTitle = __('Edit Post', 'rs2010-forum');
            } else if ($this->current_view === 'addpost') {
                $mainTitle = __('Post Reply', 'rs2010-forum').': '.esc_html(stripslashes($this->current_topic_name));
            } else if ($this->current_view === 'addtopic') {
                $mainTitle = __('New Topic', 'rs2010-forum');
            } else if ($this->current_view === 'movetopic') {
                $mainTitle = __('Move Topic', 'rs2010-forum');
            } else if ($this->current_view === 'search') {
                $mainTitle = __('Search', 'rs2010-forum');
            } else if ($this->current_view === 'subscriptions') {
                $mainTitle = __('Subscriptions', 'rs2010-forum');
            } else if ($this->current_view === 'profile') {
                $mainTitle = $this->profile->get_profile_title();
            } else if ($this->current_view === 'history') {
                $mainTitle = $this->profile->get_history_title();
            } else if ($this->current_view === 'members') {
                $mainTitle = __('Members', 'rs2010-forum');
            } else if ($this->current_view === 'activity') {
                $mainTitle = __('Activity', 'rs2010-forum');
            } else if ($this->current_view === 'unread') {
                $mainTitle = __('Unread Topics', 'rs2010-forum');
            } else if ($this->current_view === 'unapproved') {
                $mainTitle = __('Unapproved Topics', 'rs2010-forum');
            } else if ($this->current_view === 'reports') {
                $mainTitle = __('Reports', 'rs2010-forum');
            }
        }

        return $mainTitle;
    }

    // Holds all notices.
    private $notices = array();

    // Adds a new notice to the notices array.
    function add_notice($notice_message, $notice_link = false, $notice_icon = false) {
        $this->notices[] = array(
            'message'   => $notice_message,
            'link'      => $notice_link,
            'icon'      => $notice_icon
        );
    }

    // Renders a single given notice.
    function render_notice($notice_message, $notice_link = false, $notice_icon = false, $in_panel = false) {
        if ($in_panel) { echo '<div class="notices-panel">'; }

        echo '<div class="notice">';

        if ($notice_icon) {
            echo '<span class="notice-icon '.$notice_icon.'"></span>';
        }

        if ($notice_link) { echo '<a href="'.$notice_link.'">'; }

        echo $notice_message;

        if ($notice_link) { echo '</a>'; }

        echo '</div>';

        if ($in_panel) { echo '</div>'; }
    }

    // Renders all existing notices.
    function render_notices() {
        if (!empty($this->notices)) {
            echo '<div class="notices-panel">';

            foreach ($this->notices as $notice) {
                $this->render_notice($notice['message'], $notice['link'], $notice['icon']);
            }

            echo '</div>';
        }
    }

    function forum() {
        ob_start();
        echo '<div id="rs-wrapper">';

        do_action('rs2010forum_content_top');
        do_action('rs2010forum_'.$this->current_view.'_custom_content_top');

        // Show Header Area except for single posts.
        if ($this->current_view !== 'post' && apply_filters('rs2010forum_filter_show_header', true)) {
            $this->showHeader();

            do_action('rs2010forum_content_header');
        }

        if (!empty($this->error)) {
            echo '<div class="error">'.$this->error.'</div>';
        } else {
            if ($this->current_view === 'post') {
                $this->showSinglePost();
            } else {
                $this->render_notices();
                $this->showMainTitleAndDescription();

                switch ($this->current_view) {
                    case 'search':
                        $this->search->show_search_results();
                    break;
                    case 'subscriptions':
                        $this->notifications->show_subscription_overview();
                    break;
                    case 'movetopic':
                        $this->showMoveTopic();
                    break;
                    case 'forum':
                        $this->show_forum();
                    break;
                    case 'topic':
                        $this->showTopic();
                    break;
                    case 'addtopic':
                    case 'addpost':
                    case 'editpost':
                        $this->editor->showEditor($this->current_view);
                    break;
                    case 'profile':
                        $this->profile->showProfile();
                    break;
                    case 'history':
                        $this->profile->show_history();
                    break;
                    case 'members':
                        $this->memberslist->show_memberslist();
                    break;
                    case 'activity':
                        $this->activity->show_activity();
                    break;
                    case 'unread':
                        $this->unread->show_unread_topics();
                    break;
                    case 'unapproved':
                        $this->approval->show_unapproved_topics();
                    break;
                    case 'reports':
                        $this->reports->show_reports();
                    break;
                    default:
                        $this->overview();
                    break;
                }

                // Action hook for optional bottom navigation elements.
                echo '<div id="bottom-navigation">';
                do_action('rs2010forum_bottom_navigation', $this->current_view);
                echo '</div>';
            }
        }

        do_action('rs2010forum_content_bottom');
        do_action('rs2010forum_'.$this->current_view.'_custom_content_bottom');

        echo '<div class="clear"></div>';
        echo '</div>';
        return ob_get_clean();
    }

    function showMainTitleAndDescription() {
        $mainTitle = $this->getMainTitle();

        echo '<h1 class="main-title main-title-'.$this->current_view.'">';

        // Show lock symbol for closed topics.
        if ($this->current_view == 'topic' && $this->is_topic_closed($this->current_topic)) {
            echo '<span class="main-title-icon fas fa-lock"></span>';
        }

        echo $mainTitle;
        echo '</h1>';

        if ($this->current_view === 'forum' && $this->options['show_description_in_forum'] && !empty($this->current_description)) {
            $forum_object = $this->content->get_forum($this->current_forum);

            echo '<div class="main-description">'.stripslashes($forum_object->description).'</div>';
        }
    }

    function overview() {
        $categories = $this->content->get_categories();

        require('views/overview.php');
    }

    function showSinglePost() {
        $counter = 0;
        $topicStarter = $this->get_topic_starter($this->current_topic);
        $post = $this->content->get_post($this->current_post);

        echo '<div class="title-element"></div>';
        require('views/post-element.php');
    }

    function show_forum() {
        require('views/forum.php');
    }

    function render_forum_element($forum) {
        // Get counters and format them.
        $count_topics = $this->get_forum_topic_counter($forum->id);
        $count_topics_i18n = number_format_i18n($count_topics);
        $count_posts = $this->get_forum_post_counter($forum->id);
        $count_posts_i18n = number_format_i18n($count_posts);

        // Get the read/unread status of a forum.
        $unread_status = $this->unread->get_status_forum($forum->id, $count_topics);

        echo '<div class="content-element forum" id="forum-'.$forum->id.'">';
            $forum_icon = trim(esc_html(stripslashes($forum->icon)));
            $forum_icon = (empty($forum_icon)) ? 'news_announcements_2' : $forum_icon;

            echo '<div class="forum-status '.$unread_status.'"><img src="'.plugin_dir_url( __DIR__ ).'skin/images/icons/'.$forum_icon.'.gif"></div>';
            echo '<div class="forum-name">';
                echo '<a class="forum-title" href="'.$this->get_link('forum', $forum->id).'">'.esc_html(stripslashes($forum->name)).'</a>';

                // Show the description of the forum when it is not empty.
                $forum_description = stripslashes($forum->description);
                if (!empty($forum_description)) {
                    echo '<small class="forum-description">'.$forum_description.'</small>';
                }



                // Show subforums.
                if ($forum->count_subforums > 0) {
                    echo '<small class="forum-subforums">';
                    echo '<b>'.__('Subforums', 'rs2010-forum').':</b>&nbsp;';

                    $subforums = $this->get_forums($forum->parent_id, $forum->id);
                    $subforumsFirstDone = false;

                    foreach ($subforums as $subforum) {
                        echo ($subforumsFirstDone) ? '&nbsp;&middot;&nbsp;' : '';
                        echo '<a href="'.$this->get_link('forum', $subforum->id).'">'.esc_html(stripslashes($subforum->name)).'</a>';
                        $subforumsFirstDone = true;
                    }

                    echo '</small>';
                }
            echo '</div>';
        
        
        
            do_action('rs2010forum_custom_forum_column', $forum->id);
        
		echo '<div class="forum-post-header-count"><small>';
		                // Show forum Posts.
                    echo sprintf(_n('%s Post', '%s', $count_posts, 'rs2010-forum'), $count_posts_i18n);
		echo '</small></div>';
        echo '<div class="forum-topic-header-count"><small>';
		                // Show forum Topics.
                    echo sprintf(_n('%s Topic', '%s', $count_topics, 'rs2010-forum'), $count_topics_i18n);
		echo '</small></div>';
        
            echo '<div class="forum-poster"><small>'.$this->render_lastpost_in_forum($forum->id).'</small></div>';
        echo '</div>';

        do_action('rs2010forum_after_forum');
    }

    function render_topic_element($topic_object, $topic_type = 'topic-normal', $show_topic_location = false) {
        $lastpost_data = $this->get_lastpost_in_topic($topic_object->id);
        $unread_status = $this->unread->get_status_topic($topic_object->id);
        $topic_title = esc_html(stripslashes($topic_object->name));

        echo '<div class="content-element topic '.$topic_type.'">';
            echo '<div class="topic-status '.$unread_status.'"><i class="far fa-comments"></i></div>';
            echo '<div class="topic-name">';
                if ($this->is_topic_sticky($topic_object->id)) {
                    echo '<div class="topic-icon forum-sticky-img" title="'.__('This topic is sticked', 'rs2010-forum').'">&nbsp;</div>';
                }

                if ($this->is_topic_closed($topic_object->id)) {
                    echo '<div class="topic-icon forum-lock-img" title="'.__('This topic is closed', 'rs2010-forum').'">&nbsp;</div>';
                }

                if ($this->polls->has_poll($topic_object->id)) {
                    echo '<div class="topic-icon forum-poll-img" title="'.__('This topic contains a poll', 'rs2010-forum').'">&nbsp;</div>';
                }

                echo '<a href="'.$this->get_link('topic', $topic_object->id).'" title="'.$topic_title.'">';
                echo $topic_title;
                echo '</a>';

                echo '<small>';
                echo __('By', 'rs2010-forum').'&nbsp;'.$this->getUsername($topic_object->author_id);

                // Show the name of the forum in which a topic is located in. This is currently only used for search results.
                if ($show_topic_location) {
                    echo '&nbsp;&middot;&nbsp;';
                    echo __('In', 'rs2010-forum').'&nbsp;';
                    echo '<a href="'.$this->rewrite->get_link('forum', $topic_object->forum_id).'">';
                    echo esc_html(stripslashes($topic_object->forum_name));
                    echo '</a>';
                }

                $this->pagination->renderTopicOverviewPagination($topic_object->id, ($topic_object->answers + 1));

                echo '</small>';

                // Show topic stats.
                echo '<small class="topic-stats">';
                    $count_answers_i18n = number_format_i18n($topic_object->answers);
                    echo sprintf(_n('%s Reply', '%s Replies', $topic_object->answers, 'rs2010-forum'), $count_answers_i18n);

                    if ($this->options['count_topic_views']) {
                        $count_views_i18n = number_format_i18n($topic_object->views);
                        echo '&nbsp;&middot;&nbsp;';
                        echo sprintf(_n('%s View', '%s Views', $topic_object->views, 'rs2010-forum'), $count_views_i18n);
                    }
                echo '</small>';

                // Show lastpost info.
                echo '<small class="topic-lastpost-small">';
                    echo $this->render_lastpost_in_topic($topic_object->id, true);
                echo '</small>';
            echo '</div>';
            do_action('rs2010forum_custom_topic_column', $topic_object->id);
            echo '<div class="topic-poster">'.$this->render_lastpost_in_topic($topic_object->id).'</div>';
        echo '</div>';

        do_action('rs2010forum_after_topic');
    }

    // Renders all subforums inside of a category/forum combination.
    function render_subforums($category_id, $forum_id) {
        $subforums = $this->get_forums($category_id, $forum_id);

        if (!empty($subforums)) {
            echo '<div class="brown-box-area"><div class="title-element">';
                echo __('Subforums', 'rs2010-forum');
                echo '<span class="last-post-headline">'.__('Last post', 'rs2010-forum').'</span>';
            echo '</div></div>';

            echo '<div class="brown-box-area"><div class="content-container">';
                foreach ($subforums as $forum) {
                    $this->render_forum_element($forum);
                }
            echo '</div></div>';
        }
    }

    function showTopic() {
        // Create a unique slug for this topic if necessary.
        $topic = $this->content->get_topic($this->current_topic);

        if (empty($topic->slug)) {
            $slug = $this->rewrite->create_unique_slug($topic->name, $this->tables->topics, 'topic');
            $this->db->update($this->tables->topics, array('slug' => $slug), array('id' => $topic->id), array('%s'), array('%d'));
        }

        // Get posts of topic.
        $posts = $this->get_posts();

        if ($posts) {
            $this->incrementTopicViews();

            require('views/topic.php');
        } else {
            $this->render_notice(__('Sorry, but there are no posts.', 'rs2010-forum'));
        }
    }

    public function incrementTopicViews() {
        if ($this->options['count_topic_views']) {
            $this->db->query($this->db->prepare("UPDATE {$this->tables->topics} SET views = views + 1 WHERE id = %d", $this->current_topic));
        }
    }

    function showMoveTopic() {
        if ($this->permissions->isModerator('current')) {
            $strOUT = '<form method="post" action="'.$this->get_link('movetopic', $this->current_topic, array('move_topic' => 1)).'">';
            $strOUT .= '<div class="title-element">'.sprintf(__('Move "<strong>%s</strong>" to new forum:', 'rs2010-forum'), esc_html(stripslashes($this->current_topic_name))).'</div>';
            $strOUT .= '<div class="content-container">';
            $strOUT .= '<br><select name="newForumID">';

            $categories = $this->content->get_categories();

            if ($categories) {
                foreach ($categories as $category) {
                    $forums = $this->get_forums($category->term_id, 0);

                    if ($forums) {
                        foreach ($forums as $forum) {
                            $strOUT .= '<option value="'.$forum->id.'"'.($forum->id == $this->current_forum ? ' selected="selected"' : '').'>'.esc_html($forum->name).'</option>';

                            if ($forum->count_subforums > 0) {
                                $subforums = $this->get_forums($category->term_id, $forum->id);

                                foreach ($subforums as $subforum) {
                                    $strOUT .= '<option value="'.$subforum->id.'"'.($subforum->id == $this->current_forum ? ' selected="selected"' : '').'>&mdash; '.esc_html($subforum->name).'</option>';
                                }
                            }
                        }
                    }
                }
            }

            $strOUT .= '</select><br><input class="button button-normal" type="submit" value="'.__('Move', 'rs2010-forum').'"><br><br></div></form>';

            echo $strOUT;
        } else {
            $this->render_notice(__('You are not allowed to move topics.', 'rs2010-forum'));
        }
    }

    function get_category_name($category_id) {
        $category = get_term($category_id, 'rs2010forum-category');

        if ($category) {
            return $category->name;
        } else {
            return false;
        }
    }

    var $topic_counters_cache = false;
    function get_topic_counters() {
        // If the cache is not set yet, create it.
        if ($this->topic_counters_cache === false) {
            // Get all topic-counters of each forum first.
            $topic_counters = $this->db->get_results("SELECT parent_id AS forum_id, COUNT(*) AS topic_counter FROM {$this->tables->topics} WHERE approved = 1 GROUP BY parent_id;");

            // Assign topic-counter for each forum.
            if (!empty($topic_counters)) {
                foreach ($topic_counters as $counter) {
                    $this->topic_counters_cache[$counter->forum_id] = $counter->topic_counter;
                }
            }

            // Now get all subforums.
            $subforums = $this->content->get_all_subforums();

            // Re-assign topic-counter for each forum based on the topic-counters in its subforums.
            if (!empty($subforums)) {
                foreach ($subforums as $subforum) {
                    // Continue if the subforum has no topics.
                    if (!isset($this->topic_counters_cache[$subforum->id])) {
                        continue;
                    }

                    // Re-assign value when the parent-forum has no topics.
                    if (!isset($this->topic_counters_cache[$subforum->parent_forum])) {
                        $this->topic_counters_cache[$subforum->parent_forum] = $this->topic_counters_cache[$subforum->id];
                        continue;
                    }

                    // Otherwise add subforum-topics to the counter of the parent forum.
                    $this->topic_counters_cache[$subforum->parent_forum] = ($this->topic_counters_cache[$subforum->parent_forum] + $this->topic_counters_cache[$subforum->id]);
                }
            }
        }

        return $this->topic_counters_cache;
    }

    function get_forum_topic_counter($forum_id) {
        $counters = $this->get_topic_counters();

        if (isset($counters[$forum_id])) {
            return (int) $counters[$forum_id];
        } else {
            return 0;
        }
    }

    var $post_counters_cache = false;
    function get_post_counters() {
        // If the cache is not set yet, create it.
        if ($this->post_counters_cache === false) {
            // Get all post-counters of each forum first.
            $post_counters = $this->db->get_results("SELECT t.parent_id AS forum_id, COUNT(*) AS post_counter FROM {$this->tables->posts} AS p, {$this->tables->topics} AS t WHERE p.parent_id = t.id AND t.approved = 1 GROUP BY t.parent_id;");

            // Assign post-counter for each forum.
            if (!empty($post_counters)) {
                foreach ($post_counters as $counter) {
                    $this->post_counters_cache[$counter->forum_id] = $counter->post_counter;
                }
            }

            // Now get all subforums.
            $subforums = $this->content->get_all_subforums();

            // Re-assign post-counter for each forum based on the post-counters in its subforums.
            if (!empty($subforums)) {
                foreach ($subforums as $subforum) {
                    // Continue if the subforum has no posts.
                    if (!isset($this->post_counters_cache[$subforum->id])) {
                        continue;
                    }

                    // Re-assign value when the parent-forum has no posts.
                    if (!isset($this->post_counters_cache[$subforum->parent_forum])) {
                        $this->post_counters_cache[$subforum->parent_forum] = $this->post_counters_cache[$subforum->id];
                        continue;
                    }

                    // Otherwise add subforum-posts to the counter of the parent forum.
                    $this->post_counters_cache[$subforum->parent_forum] = ($this->post_counters_cache[$subforum->parent_forum] + $this->post_counters_cache[$subforum->id]);
                }
            }
        }

        return $this->post_counters_cache;
    }

    function get_forum_post_counter($forum_id) {
        $counters = $this->get_post_counters();

        if (isset($counters[$forum_id])) {
            return (int) $counters[$forum_id];
        } else {
            return 0;
        }
    }

    function get_forums($id = false, $parent_forum = 0, $output_type = OBJECT) {
        if ($id) {
            $query_count_subforums = "SELECT COUNT(*) FROM {$this->tables->forums} WHERE parent_forum = f.id";
            return $this->db->get_results($this->db->prepare("SELECT f.*, ({$query_count_subforums}) AS count_subforums FROM {$this->tables->forums} AS f WHERE f.parent_id = %d AND f.parent_forum = %d ORDER BY f.sort ASC;", $id, $parent_forum), $output_type);
        }
    }

    function getSpecificForums($ids) {
        $results = $this->db->get_results("SELECT id, parent_id AS category_id, name FROM {$this->tables->forums} WHERE id IN (".implode(',', $ids).") ORDER BY id ASC;");
        return $results;
    }

    function getSpecificTopics($ids) {
        $results = $this->db->get_results("SELECT t.id, f.parent_id AS category_id, t.name FROM {$this->tables->topics} AS t LEFT JOIN {$this->tables->forums} AS f ON (f.id = t.parent_id) WHERE t.id IN (".implode(',', $ids).") ORDER BY t.id ASC;");
        return $results;
    }

    function get_posts() {
        $start = $this->current_page * $this->options['posts_per_page'];
        $end = $this->options['posts_per_page'];

        $order = apply_filters('rs2010forum_filter_get_posts_order', 'p1.id ASC');
        $results = $this->db->get_results($this->db->prepare("SELECT p1.id, p1.text, p1.date, p1.date_edit, p1.author_id, p1.author_edit, (SELECT COUNT(*) FROM {$this->tables->posts} AS p2 WHERE p2.author_id = p1.author_id) AS author_posts, p1.uploads FROM {$this->tables->posts} AS p1 WHERE p1.parent_id = %d ORDER BY {$order} LIMIT %d, %d;", $this->current_topic, $start, $end));
        $results = apply_filters('rs2010forum_filter_get_posts', $results);
        return $results;
    }

    private $is_first_post_cache = array();
    function is_first_post($post_id, $topic_id = false) {
        // When we dont know the topic-id, we need to figure it out.
        if (!$topic_id) {
            // Check if there exists an current-topic-id.
            if ($this->current_topic) {
                $topic_id = $this->current_topic;
            }
        }

        if (empty($this->is_first_post_cache[$topic_id])) {
            $this->is_first_post_cache[$topic_id] = $this->db->get_var("SELECT id FROM {$this->tables->posts} WHERE parent_id = {$topic_id} ORDER BY id ASC LIMIT 1;");
        }

        if ($post_id == $this->is_first_post_cache[$topic_id]) {
            return true;
        } else {
            return false;
        }
    }

    function cut_string($string, $length = 33, $at_next_space = false) {
        $string_length = 0;

        // Get string-length first.
        if (function_exists('mb_strlen')) {
            $string_length = mb_strlen($string, 'UTF-8');
        } else {
            $string_length = strlen($string);
        }

        // Only cut string if it is longer than defined.
        if ($string_length > $length) {
            // Try to find position of next space if necessary.
            if ($at_next_space) {
                $space_position = false;

                // Get position of space.
                if (function_exists('mb_strpos')) {
                    $space_position = mb_strpos($string, ' ', $length, 'UTF-8');
                } else {
                    $space_position = strpos($string, ' ', $length);
                }   

                if ($space_position) {
                    $length = $space_position;
                } else {
                    return $string;
                }
            }

            // Return substring.
            if (function_exists('mb_substr')) {
                return mb_substr($string, 0, $length, 'UTF-8').' &#8230;';
            } else {
                return substr($string, 0, $length).' &#8230;';
            }
        }

        return $string;
    }

    // TODO: Clean up the complete username logic ...
    function get_plain_username($user_id) {
        if ($user_id) {
            $user = get_userdata($user_id);

            if ($user) {
                return $user->display_name;
            } else {
                return __('Deleted user', 'rs2010-forum');
            }
        } else {
            return __('Guest', 'rs2010-forum');
        }
    }

    /**
     * Returns and caches a username.
     */
    var $cacheGetUsername = array();
    function getUsername($user_id) {
        if ($user_id) {
            if (empty($this->cacheGetUsername[$user_id])) {
                $user = get_userdata($user_id);

                if ($user) {
                    $this->cacheGetUsername[$user_id] = $this->renderUsername($user);
                } else {
                    $this->cacheGetUsername[$user_id] = __('Deleted user', 'rs2010-forum');
                }
            }

            return $this->cacheGetUsername[$user_id];
        } else {
            return __('Guest', 'rs2010-forum');
        }
    }

    /**
     * Renders a username.
     */
    function renderUsername($userObject, $custom_name = false) {
        $user_name = $userObject->display_name;

        if ($custom_name) {
            $user_name = $custom_name;
        }

        $renderedUserName = $user_name;

        $profileLink = $this->profile->getProfileLink($userObject);

        if ($profileLink) {
            $renderedUserName = '<a class="profile-link" href="'.$profileLink.'">'.$user_name.'</a>';;
        } else {
            $renderedUserName = $user_name;
        }

        $renderedUserName = $this->highlight_username($userObject, $renderedUserName);

        return $renderedUserName;
    }

    /**
     * Highlights a username when he is an administrator/moderator.
     */
    function highlight_username($user, $string) {
        if ($this->options['highlight_admin']) {
            if ($this->permissions->isAdministrator($user->ID)) {
                return '<span class="highlight-admin">'.$string.'</span>';
            } else if ($this->permissions->isModerator($user->ID)) {
                return '<span class="highlight-moderator">'.$string.'</span>';
            }
        }

        return $string;
    }

    private $lastpost_forum_cache = false;
    function lastpost_forum_cache() {
        if ($this->lastpost_forum_cache === false) {
            // Get all lastpost-elements of each forum first. Selection on topics is needed here because we only want posts of approved topics.
            $lastpost_elements = $this->db->get_results("SELECT t.parent_id AS forum_id, MAX(p.id) AS id FROM {$this->tables->posts} AS p, {$this->tables->topics} AS t WHERE p.parent_id = t.id AND t.approved = 1 GROUP BY t.parent_id;");

            // Assign lastpost-ids for each forum.
            if (!empty($lastpost_elements)) {
                foreach ($lastpost_elements as $element) {
                    $this->lastpost_forum_cache[$element->forum_id] = $element->id;
                }
            }

            // Now get all subforums.
            $subforums = $this->content->get_all_subforums();

            // Re-assign lastpost-ids for each forum based on the lastposts in its subforums.
            if (!empty($subforums)) {
                foreach ($subforums as $subforum) {
                    // Continue if the subforum has no posts.
                    if (!isset($this->lastpost_forum_cache[$subforum->id])) {
                        continue;
                    }

                    // Re-assign value when the parent-forum has no posts.
                    if (!isset($this->lastpost_forum_cache[$subforum->parent_forum])) {
                        $this->lastpost_forum_cache[$subforum->parent_forum] = $this->lastpost_forum_cache[$subforum->id];
                    }

                    // Otherwise re-assign value when a subforum has a more recent post.
                    if ($this->lastpost_forum_cache[$subforum->id] > $this->lastpost_forum_cache[$subforum->parent_forum]) {
                        $this->lastpost_forum_cache[$subforum->parent_forum] = $this->lastpost_forum_cache[$subforum->id];
                    }
                }
            }
        }
    }

    private $get_lastpost_in_forum_cache = array();
    function get_lastpost_in_forum($forum_id) {
        if (!isset($this->get_lastpost_in_forum_cache[$forum_id])) {
            $this->lastpost_forum_cache();

            if (isset($this->lastpost_forum_cache[$forum_id])) {
                $this->get_lastpost_in_forum_cache[$forum_id] = $this->db->get_row("SELECT p.id, p.date, p.parent_id, p.author_id, t.name FROM {$this->tables->posts} AS p, {$this->tables->topics} AS t WHERE p.id = ".$this->lastpost_forum_cache[$forum_id]." AND t.id = p.parent_id;");
            } else {
                $this->get_lastpost_in_forum_cache[$forum_id] = false;
            }
        }

        return $this->get_lastpost_in_forum_cache[$forum_id];
    }

    function render_lastpost_in_forum($forum_id, $compact = false) {
        $lastpost = $this->get_lastpost_in_forum($forum_id);

        if ($lastpost === false) {
            return '<small class="no-topics">'.__('No topics yet!', 'rs2010-forum').'</small>';
        } else {
            $output = '';
            $post_link = $this->rewrite->get_post_link($lastpost->id, $lastpost->parent_id);

            if ($compact === true) {
                $output .= __('Last post:', 'rs2010-forum');
                $output .= '&nbsp;';
                $output .= '<a href="'.$post_link.'">'.esc_html($this->cut_string(stripslashes($lastpost->name), 34)).'</a>';
                $output .= '&nbsp;&middot;&nbsp;';
                $output .= '<a href="'.$post_link.'">'.sprintf(__('%s ago', 'rs2010-forum'), human_time_diff(strtotime($lastpost->date), current_time('timestamp'))).'</a>';
                $output .= '&nbsp;&middot;&nbsp;';
                $output .= $this->getUsername($lastpost->author_id);
            } else {
                // Avatar
                if ($this->options['enable_avatars']) {
                    $output .= '<div class="forum-poster-avatar">'.get_avatar($lastpost->author_id, 40, '', '', array('force_display' => true)).'</div>';
                }

                // Summary
                $output .= '<div class="forum-poster-summary">';
                $output .= '<a href="'.$post_link.'">'.esc_html($this->cut_string(stripslashes($lastpost->name), 25)).'</a><br>';
                $output .= '<small>';
                $output .= '<a href="'.$post_link.'">'.sprintf(__('%s ago', 'rs2010-forum'), human_time_diff(strtotime($lastpost->date), current_time('timestamp'))).'</a>';
                $output .= '<span>&nbsp;&middot;&nbsp;</span>';
                $output .= $this->getUsername($lastpost->author_id);
                $output .= '</small>';
                $output .= '</div>';
            }

            return $output;
        }
    }

    function get_lastpost_in_topic($topic_id) {
        if (empty($this->cache['get_lastpost_in_topic'][$topic_id])) {
            $this->cache['get_lastpost_in_topic'][$topic_id] = $this->db->get_row("SELECT id, date, author_id, parent_id FROM {$this->tables->posts} WHERE parent_id = {$topic_id} ORDER BY id DESC LIMIT 1;");
        }

        return $this->cache['get_lastpost_in_topic'][$topic_id];
    }

    function render_lastpost_in_topic($topic_id, $compact = false) {
        $lastpost = $this->get_lastpost_in_topic($topic_id);
        $output = '';
        $post_link = $this->rewrite->get_post_link($lastpost->id, $lastpost->parent_id);

        if ($compact === true) {
            $output .= __('Last post:', 'rs2010-forum');
            $output .= '&nbsp;';
            $output .= '<a href="'.$post_link.'">'.sprintf(__('%s ago', 'rs2010-forum'), human_time_diff(strtotime($lastpost->date), current_time('timestamp'))).'</a>';
            $output .= '&nbsp;&middot;&nbsp;';
            $output .= $this->getUsername($lastpost->author_id);

        } else {
            // Avatar
            if ($this->options['enable_avatars']) {
                $output .= '<div class="topic-poster-avatar">'.get_avatar($lastpost->author_id, 40, '', '', array('force_display' => true)).'</div>';
            }

            // Summary
            $output .= '<div class="forum-poster-summary">';
            $output .= '<a href="'.$post_link.'">'.sprintf(__('%s ago', 'rs2010-forum'), human_time_diff(strtotime($lastpost->date), current_time('timestamp'))).'</a><br>';
            $output .= '<small>';
            $output .= $this->getUsername($lastpost->author_id);
            $output .= '</small>';
            $output .= '</div>';
        }

        return $output;
    }

    function get_topic_starter($topic_id) {
        return $this->db->get_var($this->db->prepare("SELECT author_id FROM {$this->tables->topics} WHERE id = %d;", $topic_id));
    }

    function format_date($date, $full = true) {
        if ($full) {
            return date_i18n($this->date_format.', '.$this->time_format, strtotime($date));
        } else {
            return date_i18n($this->date_format, strtotime($date));
        }
    }

    function current_time() {
        return current_time('Y-m-d H:i:s');
    }

    function get_post_author($post_id) {
        return $this->db->get_var($this->db->prepare("SELECT author_id FROM {$this->tables->posts} WHERE id = %d;", $post_id));
    }

    function get_post_date($post_id) {
        return $this->db->get_var($this->db->prepare("SELECT `date` FROM {$this->tables->posts} WHERE id = %d;", $post_id));
    }

    // Returns the topics created by a user.
    function countTopicsByUser($user_id) {
        return $this->db->get_var("SELECT COUNT(*) FROM {$this->tables->topics} WHERE author_id = {$user_id};");
    }

    function countPostsByUser($userID) {
        return $this->db->get_var("SELECT COUNT(*) FROM {$this->tables->posts} WHERE author_id = {$userID};");
    }

    public function render_sticky_panel() {
        // Cancel if the current user is not at least a moderator.
        if (!$this->permissions->isModerator('current')) {
            return;
        }

        echo '<div id="sticky-panel">';
            echo '<div class="title-element title-element-dark">';
                echo '<span class="title-element-icon forum-sticky-img"></span>';
                echo __('Select Sticky Mode:', 'rs2010-forum');
            echo '</div>';
            echo '<div class="content-container">';
                echo '<form method="post" action="'.$this->get_link('topic', $this->current_topic).'">';
                    echo '<div class="action-panel">';
                        echo '<label class="action-panel-option">';
                            echo '<input type="radio" name="sticky_topic" value="1">';
                            echo '<span class="action-panel-title">';
                                echo '<span class="action-panel-icon forum-sticky-img"></span>';
                                echo __('Sticky', 'rs2010-forum');
                            echo '</span>';
                            echo '<span class="action-panel-description">';
                                _e('The topic will be sticked to the current forum.', 'rs2010-forum');
                            echo '</span>';
                        echo '</label>';
                        echo '<label class="action-panel-option">';
                            echo '<input type="radio" name="sticky_topic" value="2">';
                            echo '<span class="action-panel-title">';
                                echo '<span class="action-panel-icon fas fa-globe-europe"></span>';
                                echo __('Global Sticky', 'rs2010-forum');
                            echo '</span>';
                            echo '<span class="action-panel-description">';
                                _e('The topic will be sticked to all forums.', 'rs2010-forum');
                            echo '</span>';
                        echo '</label>';
                    echo '</div>';
                echo '</form>';
            echo '</div>';
        echo '</div>';
    }

    // Generate forum menu.
    function showForumMenu() {
        $menu = '';

        if ($this->forumIsOpen()) {
            if ((is_user_logged_in() && !$this->permissions->isBanned('current')) || (!is_user_logged_in() && $this->options['allow_guest_postings'])) {
                // New topic button.
                $menu .= '<div class="forum-menu">';
                $menu .= '<a class="button button-normal forum-editor-button" href="'.$this->get_link('addtopic', $this->current_forum).'">';
                    $menu .= '<span class="menu-icon fas fa-plus-square"></span>';
                    $menu .= __('New Topic', 'rs2010-forum');
                $menu .= '</a>';
                $menu .= '</div>';
            }
        }

        $menu = apply_filters('rs2010forum_filter_forum_menu', $menu);

        return $menu;
    }

    // Generate topic menu.
    function show_topic_menu($show_all_buttons = true) {
        $menu = '';

        $current_user_id = get_current_user_id();

        // Show reply and certain moderation-buttons only for approved topics.
        if ($this->approval->is_topic_approved($this->current_topic)) {
            if ($this->permissions->can_create_post($current_user_id)) {
                // Reply button.
                $menu .= '<a class="button button-normal forum-editor-button" href="'.$this->get_link('addpost', $this->current_topic).'">';
                    $menu .= '<span class="menu-icon fas fa-comment-dots"></span>';
                    $menu .= __('Reply', 'rs2010-forum');
                $menu .= '</a>';
            }

            if ($show_all_buttons) {
                if ($this->permissions->isModerator('current')) {
                    // Move button.
                    $menu .= '<a class="button button-normal" href="'.$this->get_link('movetopic', $this->current_topic).'">';
                        $menu .= '<span class="menu-icon fas fa-random"></span>';
                        $menu .= __('Move', 'rs2010-forum');
                    $menu .= '</a>';

                    if ($this->is_topic_sticky($this->current_topic)) {
                        // Undo sticky button.
                        $menu .= '<a class="button button-normal topic-button-unsticky" href="'.$this->get_link('topic', $this->current_topic, array('unsticky_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon forum-sticky-img"></span>';
                            $menu .= __('Unsticky', 'rs2010-forum');
                        $menu .= '</a>';
                    } else {
                        // Sticky button.
                        $menu .= '<a class="button button-normal topic-button-sticky" href="'.$this->get_link('topic', $this->current_topic, array('sticky_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon forum-sticky-img"></span>';
                            $menu .= __('Sticky', 'rs2010-forum');
                        $menu .= '</a>';
                    }
                }

                if ($this->is_topic_closed($this->current_topic)) {
                    // Open button.
                    if ($this->permissions->can_open_topic($current_user_id, $this->current_topic)) {
                        $menu .= '<a class="button button-normal" href="'.$this->get_link('topic', $this->current_topic, array('open_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon fas fa-unlock"></span>';
                            $menu .= __('Open', 'rs2010-forum');
                        $menu .= '</a>';
                    }
                } else {
                    // Close button.
                    if ($this->permissions->can_close_topic($current_user_id, $this->current_topic)) {
                        $menu .= '<a class="button button-normal" href="'.$this->get_link('topic', $this->current_topic, array('close_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon fas fa-lock"></span>';
                            $menu .= __('Close', 'rs2010-forum');
                        $menu .= '</a>';
                    }
                }
            }
        } else {
            if ($this->permissions->isModerator('current') && $show_all_buttons) {
                // Approve button.
                if (!$this->approval->is_topic_approved($this->current_topic)) {
                    $menu .= '<a class="button button-green" href="'.$this->get_link('topic', $this->current_topic, array('approve_topic' => 1)).'">';
                        $menu .= '<span class="menu-icon fas fa-check"></span>';
                        $menu .= __('Approve', 'rs2010-forum');
                    $menu .= '</a>';
                }
            }
        }

        if ($this->permissions->can_delete_topic($current_user_id, $this->current_topic) && $show_all_buttons) {
            // Delete button.
            $menu .= '<a class="button button-red" href="'.$this->get_link('topic', $this->current_topic, array('delete_topic' => 1)).'" onclick="return confirm(\''.__('Are you sure you want to remove this?', 'rs2010-forum').'\');">';
                $menu .= '<span class="menu-icon fas fa-trash-alt"></span>';
                $menu .= __('Delete', 'rs2010-forum');
            $menu .= '</a>';
        }

        $menu = (!empty($menu)) ? '<div class="forum-menu">'.$menu.'</div>' : $menu;
        $menu = apply_filters('rs2010forum_filter_topic_menu', $menu);

        return $menu;
    }

    // Generate post menu.
    function show_post_menu($post_id, $author_id, $counter, $post_date) {
        $menu = '';

        // Only show post-menu when the topic is approved.
        if ($this->approval->is_topic_approved($this->current_topic)) {
            if (is_user_logged_in()) {
                $current_user_id = get_current_user_id();

                if ($this->permissions->can_delete_post($current_user_id, $post_id, $author_id, $post_date) && ($counter > 1 || $this->current_page >= 1)) {
                    // Delete button.
                    $menu .= '<a class="delete-forum-post" onclick="return confirm(\''.__('Are you sure you want to remove this?', 'rs2010-forum').'\');" href="'.$this->get_link('topic', $this->current_topic, array('post' => $post_id, 'remove_post' => 1)).'">';
                        $menu .= '<span class="menu-icon fas fa-trash-alt"></span>';
                        $menu .= __('Delete', 'rs2010-forum');
                    $menu .= '</a>';
                }

                if ($this->permissions->can_edit_post($current_user_id, $post_id, $author_id, $post_date)) {
                    // Edit button.
                    $menu .= '<a href="'.$this->get_link('editpost', $post_id, array('part' => ($this->current_page + 1))).'">';
                        $menu .= '<span class="menu-icon fas fa-pencil-alt"></span>';
                        $menu .= __('Edit', 'rs2010-forum');
                    $menu .= '</a>';
                }
            }

            if ($this->permissions->isModerator('current') || (!$this->is_topic_closed($this->current_topic) && ((is_user_logged_in() && !$this->permissions->isBanned('current')) || (!is_user_logged_in() && $this->options['allow_guest_postings'])))) {
                // Quote button.
                $menu .= '<a class="forum-editor-quote-button" data-value-id="'.$post_id.'" href="'.$this->get_link('addpost', $this->current_topic, array('quote' => $post_id)).'">';
                    $menu .= '<span class="menu-icon fas fa-quote-left"></span>';
                    $menu .= __('Quote', 'rs2010-forum');
                $menu .= '</a>';
            }
        }

        $menu = (!empty($menu)) ? '<div class="forum-post-menu">'.$menu.'</div>' : $menu;
        $menu = apply_filters('rs2010forum_filter_post_menu', $menu);

        return $menu;
    }

    function showHeader() {
        echo '<div id="forum-header">';
            echo '<div id="forum-navigation-mobile">';
                echo '<a>';
                    echo '<span class="fas fa-bars"></span>';
                    echo __('Menu', 'rs2010-forum');
                echo '</a>';
            echo '</div>';

            echo '<span class="screen-reader-text">'.__('Forum Navigation', 'rs2010-forum').'</span>';

            echo '<div id="forum-navigation">';
                /**
                 * $menu_entries = array(
                 *     array(
                 *         'menu_class'         => 'HTML Class'
                 *         'menu_link_text'     => 'Link Text',
                 *         'menu_url'           => '/url',
                 *         'menu_login_status'  => '0', (0 = all, 1 = only logged in, 2 = only logged out)
                 *         'menu_new_tab'       => true (true = open in new tab, false = open in same tab)
                 *     )
                 * );
                 */

                $menu_entries = array();
                $menu_entries['home'] = $this->homeLink();
                $menu_entries['profile'] = $this->profile->myProfileLink();
                $menu_entries['memberslist'] = $this->memberslist->show_memberslist_link();
                $menu_entries['subscription'] = $this->notifications->show_subscription_overview_link();
                $menu_entries['activity'] = $this->activity->show_activity_link();
                $menu_entries['login'] = $this->showLoginLink();
                $menu_entries['register'] = $this->showRegisterLink();
                $menu_entries['logout'] = $this->showLogoutLink();

                // Apply filter to menu entries
                $menu_entries = apply_filters('rs2010forum_filter_header_menu', $menu_entries);

                $this->drawMenuEntries($menu_entries);
                do_action('rs2010forum_custom_header_menu');
            echo '</div>';

            $this->search->show_search_input();

            echo '<div class="clear"></div>';
        echo '</div>';

        $this->breadcrumbs->show_breadcrumbs();
    }

    function drawMenuEntries($menu_entries) {
        // Ensure that menu-entries are not empty.
        if (empty($menu_entries) || !is_array($menu_entries)) return;

        foreach ($menu_entries as $menu_entry) {
            // Check menu entry.
            if (empty($menu_entry)) continue;
            if (!isset($menu_entry['menu_new_tab'])) $menu_entry['menu_new_tab'] = false;
            if (!isset($menu_entry['menu_url'])) $menu_entry['menu_url'] = '/';
            if (!isset($menu_entry['menu_login_status'])) $menu_entry['menu_login_status'] = 0;
            if (!isset($menu_entry['menu_link_text'])) $menu_entry['menu_link_text'] = __('Link Text Missing', 'rs2010-forum');

            // Check login status.
            $login_status = is_user_logged_in();
            $menu_login_status = $menu_entry['menu_login_status'];

            if ($menu_login_status == 1 && ! $login_status) {
                continue;
            } else if ($menu_login_status == 2 && $login_status) {
                continue;
            }

            // Check if menu-URL is a slug.
            $menu_url = $menu_entry['menu_url'];

            if ($menu_url[0] == '/') {
                $menu_url = get_home_url().$menu_url;
            }

            // Check menu class.
            $menu_class = (isset($menu_entry['menu_class'])) ? 'class="'.$menu_entry['menu_class'].'"' : '';

            // Check if link has to open in a new tab.
            $new_tab = ($menu_entry['menu_new_tab']) ? 'target="_blank"' : '';

            echo '<a '.$menu_class.' href="'.$menu_url.'" '.$new_tab.'>'.$menu_entry['menu_link_text'].'</a>';
        }
    }

    function homeLink(){
        $home_url = $this->get_link('home');

        return array(
            'menu_class'        => 'home-link',
            'menu_link_text'    => esc_html__('Forum', 'rs2010-forum'),
            'menu_url'          => $home_url,
            'menu_login_status' => 0,
            'menu_new_tab'      => false
        );
    }

    function showLogoutLink() {
        if ($this->options['show_logout_button']) {
            $logout_url = wp_logout_url($this->get_link('current', false, false, '', false));

            return array(
                'menu_class'        => 'logout-link',
                'menu_link_text'    => esc_html__('Logout', 'rs2010-forum'),
                'menu_url'          => $logout_url,
                'menu_login_status' => 1,
                'menu_new_tab'      => false
            );
        }
    }

    function showLoginLink() {
        if ($this->options['show_login_button']) {
            $login_url = wp_login_url($this->get_link('current', false, false, '', false));

            if (!empty($this->options['custom_url_login'])) {
                $login_url = $this->options['custom_url_login'];
            }

            return array(
                'menu_class'        => 'login-link',
                'menu_link_text'    => esc_html__('Login', 'rs2010-forum'),
                'menu_url'          => $login_url,
                'menu_login_status' => 2,
                'menu_new_tab'      => false
            );
        }
    }

    function showRegisterLink() {
        if ( $this->options['show_register_button']) {
            $register_url = wp_registration_url();

            if (!empty($this->options['custom_url_register'])) {
                $register_url = $this->options['custom_url_register'];
            }

            return array(
                'menu_class'        => 'register-link',
                'menu_link_text'    => esc_html__('Register', 'rs2010-forum'),
                'menu_url'          => $register_url,
                'menu_login_status' => 2,
                'menu_new_tab'      => false
            );
        }
    }

    function delete_topic($topic_id, $admin_action = false, $permission_check = true) {
        // Cancel when no topic is given.
        if (!$topic_id) {
            return false;
        }

        // Cancel when topic does not exist.
        if (!$this->content->topic_exists($topic_id)) {
            return false;
        }

        // Cancel if user cannot delete topic.
        if ($permission_check) {
            $user_id = get_current_user_id();

            if (!$this->permissions->can_delete_topic($user_id, $topic_id)) {
                return false;
            }
        }

        // Continue ...
        do_action('rs2010forum_before_delete_topic', $topic_id);

        // Delete posts.
        $posts = $this->db->get_col($this->db->prepare("SELECT id FROM {$this->tables->posts} WHERE parent_id = %d;", $topic_id));
        foreach ($posts as $post) {
            $this->remove_post($post, false);
        }

        // Delete topic.
        $this->db->delete($this->tables->topics, array('id' => $topic_id), array('%d'));
        $this->notifications->remove_all_topic_subscriptions($topic_id);

        do_action('rs2010forum_after_delete_topic', $topic_id);

        if (!$admin_action) {
            wp_redirect(html_entity_decode($this->get_link('forum', $this->current_forum)));
            exit;
        }
    }

    function moveTopic() {
        $newForumID = $_POST['newForumID'];

        if ($this->permissions->isModerator('current') && $newForumID && $this->content->forum_exists($newForumID)) {
            $this->db->update($this->tables->topics, array('parent_id' => $newForumID), array('id' => $this->current_topic), array('%d'), array('%d'));
            $this->db->update($this->tables->posts, array('forum_id' => $newForumID), array('parent_id' => $this->current_topic), array('%d'), array('%d'));
            wp_redirect(html_entity_decode($this->get_link('topic', $this->current_topic)));
            exit;
        }
    }

    function remove_post($post_id, $permission_check = true) {
        // Cancel if no post is given.
        if (!$post_id) {
            return false;
        }

        // Cancel if post does not exist.
        if (!$this->content->post_exists($post_id)) {
            return false;
        }

        // Cancel if user cannot delete post.
        if ($permission_check) {
            $user_id = get_current_user_id();

            if (!$this->permissions->can_delete_post($user_id, $post_id)) {
                return false;
            }
        }

        // Delete post.
        do_action('rs2010forum_before_delete_post', $post_id);
        $this->uploads->delete_post_files($post_id);
        $this->reports->remove_report($post_id, false);
        $this->reactions->remove_all_reactions($post_id);
        $this->db->delete($this->tables->posts, array('id' => $post_id), array('%d'));
        do_action('rs2010forum_after_delete_post', $post_id);
    }

    function change_status($property) {
        $user_id = get_current_user_id();

        if ($property == 'closed') {
            if (!$this->permissions->can_close_topic($user_id, $this->current_topic)) {
                return false;
            }

            $this->db->update($this->tables->topics, array('closed' => 1), array('id' => $this->current_topic), array('%d'), array('%d'));
        } else if ($property == 'open') {
            if (!$this->permissions->can_open_topic($user_id, $this->current_topic)) {
                return false;
            }

            $this->db->update($this->tables->topics, array('closed' => 0), array('id' => $this->current_topic), array('%d'), array('%d'));
        }
    }

    function set_sticky($topic_id, $sticky_mode) {
        if (!$this->permissions->isModerator('current')) {
            return;
        }

        // Ensure that only correct values can get set.
        switch ($sticky_mode) {
            case 0:
            case 1:
            case 2:
                $this->db->update($this->tables->topics, array('sticky' => $sticky_mode), array('id' => $topic_id), array('%d'), array('%d'));
            break;
        }
    }

    function is_topic_sticky($topic_id) {
        $status = $this->db->get_var("SELECT sticky FROM {$this->tables->topics} WHERE id = {$topic_id};");

        if ((int) $status > 0) {
            return true;
        } else {
            return false;
        }
    }

    private $is_topic_closed_cache = array();
    function is_topic_closed($topic_id) {
        if (!isset($this->is_topic_closed_cache[$topic_id])) {
            $status = $this->db->get_var("SELECT closed FROM {$this->tables->topics} WHERE id = {$topic_id};");

            if ((int) $status === 1) {
                $this->is_topic_closed_cache[$topic_id] = true;
            } else {
                $this->is_topic_closed_cache[$topic_id] = false;
            }
        }

        return $this->is_topic_closed_cache[$topic_id];
    }

    // Returns TRUE if the forum is opened or the user has at least moderator rights.
    function forumIsOpen() {
        if (!$this->permissions->isModerator('current')) {
            $status = $this->db->get_var($this->db->prepare("SELECT forum_status FROM {$this->tables->forums} WHERE id = %d;", $this->current_forum));

            if ($status == 'closed') {
                return false;
            }
        }

        return true;
    }

    // Builds and returns a requested link.
    public function get_link($type, $elementID = false, $additionalParameters = false, $appendix = '', $escapeURL = true) {
        return $this->rewrite->get_link($type, $elementID, $additionalParameters, $appendix, $escapeURL);
    }

    // Checks if an element exists and sets all parent IDs based on the given id and its content type.
    public function setParents($id, $contentType) {
        // Set possible error messages.
        $error = array();
        $error['post']  = __('Sorry, this post does not exist.', 'rs2010-forum');
        $error['topic'] = __('Sorry, this topic does not exist.', 'rs2010-forum');
        $error['forum'] = __('Sorry, this forum does not exist.', 'rs2010-forum');

        if ($id) {
            $query = '';
            $results = false;

            // Build the query.
            switch ($contentType) {
                case 'post':
                    $query = "SELECT f.parent_id AS current_category, f.id AS current_forum, f.name AS current_forum_name, f.parent_forum AS parent_forum, pf.name AS parent_forum_name, t.id AS current_topic, t.name AS current_topic_name, p.id AS current_post, p.text AS current_description FROM {$this->tables->forums} AS f LEFT JOIN {$this->tables->forums} AS pf ON (pf.id = f.parent_forum) LEFT JOIN {$this->tables->topics} AS t ON (f.id = t.parent_id) LEFT JOIN {$this->tables->posts} AS p ON (t.id = p.parent_id) WHERE p.id = {$id};";
                break;
                case 'topic':
                    $query = "SELECT f.parent_id AS current_category, f.id AS current_forum, f.name AS current_forum_name, f.parent_forum AS parent_forum, pf.name AS parent_forum_name, t.id AS current_topic, t.name AS current_topic_name, (SELECT td.text FROM {$this->tables->posts} AS td WHERE td.parent_id = t.id ORDER BY td.id ASC LIMIT 1) AS current_description FROM {$this->tables->forums} AS f LEFT JOIN {$this->tables->forums} AS pf ON (pf.id = f.parent_forum) LEFT JOIN {$this->tables->topics} AS t ON (f.id = t.parent_id) WHERE t.id = {$id};";
                break;
                case 'forum':
                    $query = "SELECT f.parent_id AS current_category, f.id AS current_forum, f.name AS current_forum_name, f.parent_forum AS parent_forum, pf.name AS parent_forum_name, f.description AS current_description FROM {$this->tables->forums} AS f LEFT JOIN {$this->tables->forums} AS pf ON (pf.id = f.parent_forum) WHERE f.id = {$id};";
                break;
            }

            $results = $this->db->get_row($query);

            // When the element exists, set parents and exit function.
            if ($results) {
                if ($contentType === 'post' || $contentType === 'topic' || $contentType === 'forum') {
                    // Generate description.
                    $description = $results->current_description;
                    $description = strip_tags($description);
                    $description = esc_html($description);
                    $description = str_replace(array("\r", "\n"), '', $description);
                    $this->current_description = $this->cut_string($description, 155, true);

                    $this->current_category = $results->current_category;
                    $this->parent_forum = $results->parent_forum;
                    $this->parent_forum_name = $results->parent_forum_name;
                    $this->current_forum = $results->current_forum;
                    $this->current_forum_name = $results->current_forum_name;
                }

                $this->current_topic        = ($contentType === 'post' || $contentType === 'topic') ? $results->current_topic : false;
                $this->current_topic_name   = ($contentType === 'post' || $contentType === 'topic') ? $results->current_topic_name : false;
                $this->current_post         = ($contentType === 'post') ? $results->current_post : false;
                $this->parents_set          = true;
                return;
            }
        }

        // Assign error message, because when this location is reached, no parents has been set.
        $this->error = $error[$contentType];
    }

    // Returns the amount of users.
    public function count_users() {
        return $this->db->get_var("SELECT COUNT(u.ID) FROM {$this->db->users} u");
    }

    // Prevents oembed dataparsing for links which points to the own forum.
    function prevent_oembed_dataparse($return, $data, $url) {
        $url_check = strpos($url, $this->rewrite->get_link('home'));

        if ($url_check !== false) {
            return $url;
        }

        return $return;
    }

    public function create_file($path, $content) {
        // Create binding for the file first.
        $binding = @fopen($path, 'wb');

        // Check if the binding could get created.
        if ($binding) {
            // Write the content to the file.
            $writing = @fwrite($binding, $content);

            // Close the file.
            fclose($binding);

            // Clear stat cache so we can set the correct permissions.
            clearstatcache();

            // Get information about the file.
            $file_stats = @stat(dirname($path));

            // Update the permissions of the file.
            $file_permissions = $file_stats['mode'] & 0007777;
    		$file_permissions = $file_permissions & 0000666;
    		@chmod($path, $file_permissions);

            // Clear stat cache again so PHP is aware of the new permissions.
            clearstatcache();

            return true;
        }

        return false;
    }

    public function read_file($path) {
        // Create binding for the file first.
    	$binding = @fopen($path, 'r');

        // Check if the binding could get created.
    	if ($binding) {
            // Ensure that the file is not empty.
    		$file_size = @filesize($path);

    		if (isset($file_size) && $file_size > 0) {
                // Read the complete file.
    			$file_data = fread($binding, $file_size);

                // Close the file.
    			fclose($binding);

                // Return the file data.
    			return $file_data;
    		}
    	}

        return false;
    }

    public function delete_file($path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function delete_user_form_reassign($current_user, $userids) {
        // Remove own ID from users which should get deleted.
        $userids = array_diff($userids, array($current_user->ID));

        // Cancel if there are no users to delete.
        if (empty($userids)) {
            return;
        }

        // Check if users have posts or topics.
        $users_have_content = false;

		if ($this->db->get_var("SELECT ID FROM {$this->tables->posts} WHERE author_id IN(".implode(',', $userids).") LIMIT 1;")) {
			$users_have_content = true;
		}

        if ($this->db->get_var("SELECT ID FROM {$this->tables->topics} WHERE author_id IN(".implode(',', $userids).") LIMIT 1;")) {
			$users_have_content = true;
		}

        // Cancel if users have no posts.
        if ($users_have_content === false) {
            return;
        }

        // Show reassign-options.
        echo '<h2>'.__('Forum', 'rs2010-forum').'</h2>';

        echo '<fieldset>';
        echo '<p><legend>';

        // Count users to delete.
		$go_delete = count($userids);

        if ($go_delete === 1) {
            echo __('What should be done with forum posts owned by this user?', 'rs2010-forum');
        } else {
            echo __('What should be done with forum posts owned by these users?', 'rs2010-forum');
        }
        echo '</legend></p>';

	    echo '<ul style="list-style: none;">';
		echo '<li><input type="radio" id="forum_reassign0" name="forum_reassign" value="no" checked="checked">';
        echo '<label for="forum_reassign0">'.__('Do not reassign forum posts.', 'rs2010-forum').'</label></li>';

		echo '<li><input type="radio" id="forum_reassign1" name="forum_reassign" value="yes">';
		echo '<label for="forum_reassign1">'.__('Reassign all forum posts to:', 'rs2010-forum').'</label>&nbsp;';
        wp_dropdown_users(array('name' => 'forum_reassign_user', 'exclude' => $userids, 'show' => 'display_name_with_login'));
		echo '</li></ul></fieldset>';
    }

    public function deleted_user_reassign($id, $reassign) {

        // Ensure that correct values are passed.
        if (empty($_POST['forum_reassign'])) {
            return;
        }

        if ($_POST['forum_reassign'] != 'yes') {
            return;
        }

        if (empty($_POST['forum_reassign_user'])) {
            return;
        }

        // Reassign forum posts and topics.
        $this->db->update($this->tables->posts, array('author_id' => $_POST['forum_reassign_user']), array('author_id' => $id), array('%d'), array('%d'));
        $this->db->update($this->tables->topics, array('author_id' => $_POST['forum_reassign_user']), array('author_id' => $id), array('%d'), array('%d'));
    }

    // Extract the first URL of an image from a given string.
    public function extract_image_url($content) {
    	$images = array();

        // Check if content is given.
        if ($content) {
            // Try to find images.
            preg_match_all('#https?://[^\s\'\"<>]+\.(?:jpg|jpeg|png|gif)#isu', $content, $found, PREG_SET_ORDER);

            if (empty($found)) {
                preg_match_all('#//[^\s\'\"<>]+\.(?:jpg|jpeg|png|gif)#isu', $content, $found, PREG_SET_ORDER);
            }

            if (empty($found)) {
                preg_match_all('#https?://[^\s\'\"<>]+#isu', $content, $found, PREG_SET_ORDER);
            }

            if (empty($found)) {
                preg_match_all('#//[^\s\'\"<>]+#isu', $content, $found, PREG_SET_ORDER);
            }

            // If images found, check extensions.
            if (!empty($found)) {
                foreach ($found as $match) {
                    $extension = pathinfo($match[0], PATHINFO_EXTENSION);

                    if ($extension) {
                        $check = false;
                        $extension = strtolower($extension);

                    	if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {
                    		$check = true;
                    	}

                        if ($check) {
                            $images[] = $match[0];
                            break;
                        }
                    }
                }
            }
        }

    	if (empty($images)) {
            return false;
        } else {
            return $images[0];
        }
    }

    // Tries to get the signature for a given user.
    public function get_signature($user_id) {
        // Ensure signatures are enabled.
        if (!$this->options['allow_signatures']) {
            return false;
        }

        // Ensure that the user has the permission to use a signature.
        if (!$this->permissions->can_use_signature($user_id)) {
            return false;
        }

        // Try to get signature.
        $signature = get_user_meta($user_id, 'rs2010forum_signature', true);

        // Prepare signature based on settings.
        if ($this->options['signatures_html_allowed']) {
            $signature = strip_tags($signature, $this->options['signatures_html_tags']);
        } else {
            $signature = esc_html(strip_tags($signature));
        }

        // Trim it.
        $signature = trim($signature);

        // Allow filtering the signature.
        $signature = apply_filters('rs2010forum_signature', $signature);

        // Ensure signature is not empty.
        if (empty($signature)) {
            return false;
        }

        return $signature;
    }

    public function render_reputation_badges($number) {
        if ($this->options['enable_reputation']) {
            echo '<small class="reputation-badges">';

            if ($number < $this->options['reputation_level_1_posts']) {
                echo '<i class="far fa-star-half"></i>';
            } else {
                echo '<i class="fas fa-star"></i>';

                // Add an additional star for every remaining level.
                for ($i = 2; $i <= 5; $i++) {
                    if ($number >= $this->options['reputation_level_'.$i.'_posts']) {
                        echo '<i class="fas fa-star"></i>';
                    } else {
                        break;
                    }
                }
            }

            echo '</small>';
        }
    }

    /*******************************************/
    /* Functions for automatic topic creation. */
    /*******************************************/

    public function post_transition_update($new_status, $old_status, $post) {
        // Ensure that the required option is activated.
        if ($this->options['create_blog_topics_id'] == 0) {
            return;
        }

        if ($post->post_type == 'post' && $new_status == 'publish' && $old_status != 'publish') {
            $forum_id = $this->options['create_blog_topics_id'];

            $this->create_blog_topic($forum_id, $post);
        }
    }

    public function create_blog_topic($forum_id, $post_object) {
        if ($this->content->forum_exists($forum_id)) {
            $post_title = apply_filters('rs2010forum_filter_automatic_topic_title', $post_object->post_title, $post_object);
            $post_content = apply_filters('rs2010forum_filter_automatic_topic_content', $post_object->post_content, $post_object);

            $this->content->insert_topic($forum_id, $post_title, $post_content, $post_object->post_author);
        }
    }

    public function add_topic_meta_box_setup() {
        add_action('add_meta_boxes', array($this, 'add_topic_meta_box'), 10, 2);
        add_action('save_post', array($this, 'process_topic_meta_box'), 10, 2);
    }

    public function add_topic_meta_box($post_type, $post) {
        if (!current_user_can('publish_post', $post->ID)) {
            return;
        }

        $post_types = apply_filters('rs2010forum_filter_meta_post_type', array('post', 'page'));

        add_meta_box(
            'rs2010-forum-topic-meta-box',
            __('Create Forum Topic', 'rs2010-forum'),
            array($this, 'add_topic_meta_box_content'),
            $post_types,
            'side',
            'low'
        );
    }

    public function add_topic_meta_box_content($post) {
        echo '<select name="add_topic_in_forum" id="add_topic_in_forum">';
        echo '<option value="0"></option>';

        // Generate list of available forums.
        $categories = $this->content->get_categories(false);

        if ($categories) {
            foreach ($categories as $category) {
                $forums = $this->get_forums($category->term_id, 0);

                if ($forums) {
                    foreach ($forums as $forum) {
                        echo '<option value="'.$forum->id.'">'.esc_html($forum->name).'</option>';

                        if ($forum->count_subforums > 0) {
                            $subforums = $this->get_forums($category->term_id, $forum->id);

                            foreach ($subforums as $subforum) {
                                echo '<option value="'.$subforum->id.'">--- '.esc_html($subforum->name).'</option>';
                            }
                        }
                    }
                }
            }
        }

        echo '</select>';
        echo '<br>';
        echo '<label for="add_topic_in_forum">'.__('A topic is created in the selected forum when you save this document.', 'rs2010-forum').'</label>';
    }

    public function process_topic_meta_box($post_id, $post) {
        if (!current_user_can('publish_post', $post_id)) {
            return;
        }

        if (isset($_POST['add_topic_in_forum']) && $_POST['add_topic_in_forum'] > 0) {
            $this->create_blog_topic($_POST['add_topic_in_forum'], $post);
        }
    }
}
