<?php

// TODO: Maybe use Rs2010ForumUserQuery to increase performance.
if (!defined('ABSPATH')) exit;

class Rs2010ForumNotifications {
    private $rs2010forum = null;
    public $mailing_list = array();

    public function __construct($object) {
        $this->rs2010forum = $object;

        add_action('rs2010forum_prepare_subscriptions', array($this, 'set_subscription_level'));
        add_action('rs2010forum_bottom_navigation', array($this, 'show_subscription_navigation'), 10, 1);
        add_action('rs2010forum_breadcrumbs_subscriptions', array($this, 'add_breadcrumbs'));
    }

    public function add_breadcrumbs() {
        $element_link = $this->rs2010forum->get_link('subscriptions');
        $element_title = __('Subscriptions', 'rs2010-forum');
        $this->rs2010forum->breadcrumbs->add_breadcrumb($element_link, $element_title);
    }

    function show_subscription_navigation($current_view) {
        if ($this->rs2010forum->options['allow_subscriptions'] && is_user_logged_in()) {
            switch($current_view) {
                case 'topic':
                    $this->show_topic_subscription_link($this->rs2010forum->current_topic);
                break;
                case 'forum':
                    $this->show_forum_subscription_link($this->rs2010forum->current_forum);
                break;
            }
        }
    }

    // Generates an (un)subscription link based on subscription status for topics.
    public function show_topic_subscription_link($topic_id) {
        // Dont show the subscription-link when the topic is not approved.
        if (!$this->rs2010forum->approval->is_topic_approved($topic_id)) {
            return;
        }

        echo '<span id="topic-subscription" class="fas fa-envelope"></span>';

        $link = '';
        $text = '';
        $subscription_level = $this->get_subscription_level();

        if ($subscription_level == 3) {
            $link = $this->rs2010forum->get_link('subscriptions');
            $text = __('You are subscribed to <b>all</b> topics.', 'rs2010-forum');
        } else {
            if ($this->is_subscribed('topic', $topic_id)) {
                $link = $this->rs2010forum->get_link('topic', $topic_id, array('unsubscribe_topic' => $topic_id));
                $text = __('<b>Unsubscribe</b> from this topic.', 'rs2010-forum');
            } else {
                $link = $this->rs2010forum->get_link('topic', $topic_id, array('subscribe_topic' => $topic_id));
                $text = __('<b>Subscribe</b> to this topic.', 'rs2010-forum');
            }
        }

        echo '<a href="'.$link.'">'.$text.'</a>';
    }

    // Generates an (un)subscription link based on subscription status for forums.
    public function show_forum_subscription_link($element_id) {
        echo '<span id="forum-subscription" class="fas fa-envelope"></span>';

        $link = '';
        $text = '';
        $subscription_level = $this->get_subscription_level();

        if ($subscription_level > 1) {
            $link = $this->rs2010forum->get_link('subscriptions');
            $text = __('You are subscribed to <b>all</b> forums.', 'rs2010-forum');
        } else {
            if ($this->is_subscribed('forum', $element_id)) {
                $link = $this->rs2010forum->get_link('forum', $element_id, array('unsubscribe_forum' => $element_id));
                $text = __('<b>Unsubscribe</b> from this forum.', 'rs2010-forum');
            } else {
                $link = $this->rs2010forum->get_link('forum', $element_id, array('subscribe_forum' => $element_id));
                $text = __('<b>Subscribe</b> to this forum.', 'rs2010-forum');
            }
        }

        echo '<a href="'.$link.'">'.$text.'</a>';
    }

    // Generates an subscription option in the editor based on subscription status.
    public function show_editor_subscription_option() {
        // Dont show this option when this is a new topic and the forum requires approval.
        if ($this->rs2010forum->current_topic === false && $this->rs2010forum->approval->forum_requires_approval($this->rs2010forum->current_forum, get_current_user_id())) {
            return;
        }

        // Check if this functionality is enabled and if the user is logged-in.
        if ($this->rs2010forum->options['allow_subscriptions'] && is_user_logged_in()) {
            echo '<div class="editor-row">';

            $subscription_level = $this->get_subscription_level();

            if ($subscription_level == 3) {
                $link = $this->rs2010forum->get_link('subscriptions');
                echo '<a href="'.$link.'">'.__('You are subscribed to <b>all</b> topics.', 'rs2010-forum').'</a>';
            } else {
                echo '<label class="checkbox-label">';
                    echo '<input type="checkbox" name="subscribe_checkbox" '.checked($this->is_subscribed('topic', $this->rs2010forum->current_topic), true, false).'><span>'.__('<b>Subscribe</b> to this topic.', 'rs2010-forum').'</span>';
                echo '</label>';
            }

            echo '</div>';
        }
    }

    // Checks if the current user has a subscription for the current topic/forum.
    public function is_subscribed($checkFor, $elementID) {
        if ($elementID) {
            $status = get_user_meta(get_current_user_id(), 'rs2010forum_subscription_'.$checkFor);

            if ($status && in_array($elementID, $status)) {
                return true;
            }
        }

        return false;
    }

    // Subscribes the current user to the current topic.
    public function subscribe_topic($topic_id) {
        // Dont subscribe to a topic when it is not approved.
        if (!$this->rs2010forum->approval->is_topic_approved($topic_id)) {
            return;
        }

        // Check first if this topic exists.
        if ($this->rs2010forum->content->topic_exists($topic_id)) {
            // Only subscribe user if he is not already subscribed for this topic.
            if (!$this->is_subscribed('topic', $topic_id)) {
                add_user_meta(get_current_user_id(), 'rs2010forum_subscription_topic', $topic_id);
            }
        }
    }

    // Subscribes the current user to the current forum.
    public function subscribe_forum($forum_id) {
        // Check first if this forum exists.
        if ($this->rs2010forum->content->forum_exists($forum_id)) {
            // Only subscribe user if he is not already subscribed for this forum.
            if (!$this->is_subscribed('forum', $forum_id)) {
                add_user_meta(get_current_user_id(), 'rs2010forum_subscription_forum', $forum_id);
            }
        }
    }

    // Unsubscribes the current user from the current topic.
    public function unsubscribe_topic($topic_id) {
        // Check first if this topic exists.
        if ($this->rs2010forum->content->topic_exists($topic_id)) {
            delete_user_meta(get_current_user_id(), 'rs2010forum_subscription_topic', $topic_id);
        }
    }

    // Unsubscribes the current user from the current forum.
    public function unsubscribe_forum($forum_id) {
        // Check first if this forum exists.
        if ($this->rs2010forum->content->forum_exists($forum_id)) {
            delete_user_meta(get_current_user_id(), 'rs2010forum_subscription_forum', $forum_id);
        }
    }

    // Update the subscription-status for a topic based on the editor-checkbox.
    public function update_topic_subscription_status($topic_id) {
        if (isset($_POST['subscribe_checkbox']) && $_POST['subscribe_checkbox']) {
            $this->subscribe_topic($topic_id);
        } else {
            $this->unsubscribe_topic($topic_id);
        }
    }

    // Removes all subscriptions for a topic. This is used when a topic gets deleted.
    public function remove_all_topic_subscriptions($topic_id) {
        delete_metadata('user', 0, 'rs2010forum_subscription_topic', $topic_id, true);
    }

    // Removes all subscriptions for a forum. This is used when a forum gets deleted.
    public function remove_all_forum_subscriptions($forum_id) {
        delete_metadata('user', 0, 'rs2010forum_subscription_forum', $forum_id, true);
    }

    // TODO: This function generates tons of queries (especially the filtering). We need some improvements.
    public function notify_about_new_post($post_id, $ignore_list = false) {
        // Cancel if this functionality is not enabled.
        if (!$this->rs2010forum->options['allow_subscriptions']) {
            return false;
        }

        // Load required data.
        $post = $this->rs2010forum->content->get_post($post_id);
        $topic = $this->rs2010forum->content->get_topic($post->parent_id);
        $forum = $this->rs2010forum->content->get_forum($topic->parent_id);

        // Get more data.
        $post_link = $this->rs2010forum->rewrite->get_post_link($post_id, $topic->id);
        $topic_name = esc_html(stripslashes($topic->name));
        $author_name = $this->rs2010forum->getUsername($post->author_id);

        // Prepare subject.
        $notification_subject = $this->rs2010forum->options['mail_template_new_post_subject'];
        $notification_subject = str_replace('###TITLE###', wp_specialchars_decode($topic_name, ENT_QUOTES), $notification_subject);

        // Prepare message-content.
        $message_content = wpautop(stripslashes($post->text));
        $message_content .= $this->rs2010forum->uploads->show_uploaded_files($post->id, $post->uploads);

        // Prepare message-template.
        $replacements = array(
            '###AUTHOR###'  => $author_name,
            '###LINK###'    => '<a href="'.$post_link.'">'.$post_link.'</a>',
            '###TITLE###'   => $topic_name,
            '###CONTENT###' => $message_content
        );

        $notification_message = $this->rs2010forum->options['mail_template_new_post_message'];
        $notification_message = apply_filters('rs2010forum_filter_notify_topic_subscribers_message', $notification_message, $replacements);

        // Prepare mailing-list.
        $topic_subscribers = array();

        // Get topic subscribers.
        $topic_subscribers_query = array(
            'fields'        => array('id', 'user_email'),
            'exclude'       => array(get_current_user_id()),
            'meta_key'      => 'rs2010forum_subscription_topic',
            'meta_value'    => $topic->id,
            'meta_compare'  => '='
        );

        $get_users_result = get_users($topic_subscribers_query);

        if (!empty($get_users_result)) {
            $topic_subscribers = array_merge($topic_subscribers, $get_users_result);
        }

        // Get global post subscribers.
        $topic_subscribers_query = array(
            'fields'        => array('id', 'user_email'),
            'exclude'       => array(get_current_user_id()),
            'meta_key'      => 'rs2010forum_subscription_global_posts',
            'meta_compare'  => 'EXISTS'
        );

        $get_users_result = get_users($topic_subscribers_query);

        if (!empty($get_users_result)) {
            $topic_subscribers = array_merge($topic_subscribers, $get_users_result);
        }

        // Remove banned users from mailing list.
        foreach ($topic_subscribers as $key => $subscriber) {
            if ($this->rs2010forum->permissions->isBanned($subscriber->id)) {
                unset($topic_subscribers[$key]);
            }
        }

        // Remove non-moderators from mailing list.
        if ($this->rs2010forum->category_access_level == 'moderator') {
            foreach ($topic_subscribers as $key => $subscriber) {
                if (!$this->rs2010forum->permissions->isModerator($subscriber->id)) {
                    unset($topic_subscribers[$key]);
                }
            }
        }

        // Generate mailing list.
        foreach($topic_subscribers as $subscriber) {
            $this->add_to_mailing_list($subscriber->user_email);
        }

        // Filter mailing list based on usergroups configuration.
        $this->mailing_list = Rs2010ForumUserGroups::filterSubscriberMails($this->mailing_list, $forum->parent_id);

        // Remove receivers which are inside the ignore-list.
        if ($ignore_list !== false) {
            $this->mailing_list = array_diff($this->mailing_list, $ignore_list);
        }

        // Apply custom filters before sending.
        $this->mailing_list = apply_filters('rs2010forum_subscriber_mails_new_post', $this->mailing_list);

        // Send notifications.
        $this->send_notifications($this->mailing_list, $notification_subject, $notification_message, $replacements);
    }

    // TODO: This function generates tons of queries (especially the filtering). We need some improvements.
    public function notify_about_new_topic($topic_id, $ignore_list = false) {
        // Cancel if this functionality is not enabled.
        if (!$this->rs2010forum->options['admin_subscriptions'] && !$this->rs2010forum->options['allow_subscriptions']) {
            return false;
        }

        // Load required data.
        $post = $this->rs2010forum->content->get_first_post($topic_id);
        $topic = $this->rs2010forum->content->get_topic($post->parent_id);
        $forum = $this->rs2010forum->content->get_forum($topic->parent_id);

        // Get more data.
        $topic_link = $this->rs2010forum->rewrite->get_link('topic', $topic_id);
        $topic_name = esc_html(stripslashes($topic->name));
        $author_name = $this->rs2010forum->getUsername($post->author_id);

        // Prepare subject.
        $notification_subject = $this->rs2010forum->options['mail_template_new_topic_subject'];
        $notification_subject = str_replace('###TITLE###', wp_specialchars_decode($topic_name, ENT_QUOTES), $notification_subject);

        // Prepare message-content.
        $message_content = wpautop(stripslashes($post->text));
        $message_content .= $this->rs2010forum->uploads->show_uploaded_files($post->id, $post->uploads);

        // Prepare message-template.
        $replacements = array(
            '###AUTHOR###'  => $author_name,
            '###LINK###'    => '<a href="'.$topic_link.'">'.$topic_link.'</a>',
            '###TITLE###'   => $topic_name,
            '###CONTENT###' => $message_content
        );

        $notification_message = $this->rs2010forum->options['mail_template_new_topic_message'];
        $notification_message = apply_filters('rs2010forum_filter_notify_global_topic_subscribers_message', $notification_message, $replacements);

        // Prepare mailing-list.
        if ($this->rs2010forum->options['allow_subscriptions']) {
            $forum_subscribers = array();

            // Get forum subscribers.
            $forum_subscribers_query = array(
                'fields'        => array('id', 'user_email'),
                'exclude'       => array(get_current_user_id()),
                'meta_key'      => 'rs2010forum_subscription_forum',
                'meta_value'    => $topic->parent_id,
                'meta_compare'  => '='
            );

            $get_users_result = get_users($forum_subscribers_query);

            if (!empty($get_users_result)) {
                $forum_subscribers = array_merge($forum_subscribers, $get_users_result);
            }

            // Get global post subscribers.
            $forum_subscribers_query = array(
                'fields'        => array('id', 'user_email'),
                'exclude'       => array(get_current_user_id()),
                'meta_key'      => 'rs2010forum_subscription_global_posts',
                'meta_compare'  => 'EXISTS'
            );

            $get_users_result = get_users($forum_subscribers_query);

            if (!empty($get_users_result)) {
                $forum_subscribers = array_merge($forum_subscribers, $get_users_result);
            }

            // Get global topic subscribers.
            $forum_subscribers_query = array(
                'fields'        => array('id', 'user_email'),
                'exclude'       => array(get_current_user_id()),
                'meta_key'      => 'rs2010forum_subscription_global_topics',
                'meta_compare'  => 'EXISTS'
            );

            $get_users_result = get_users($forum_subscribers_query);

            if (!empty($get_users_result)) {
                $forum_subscribers = array_merge($forum_subscribers, $get_users_result);
            }

            // Remove banned users from mailing list.
            foreach ($forum_subscribers as $key => $subscriber) {
                if ($this->rs2010forum->permissions->isBanned($subscriber->id)) {
                    unset($forum_subscribers[$key]);
                }
            }

            // Remove non-moderators from mailing list.
            if ($this->rs2010forum->category_access_level == 'moderator') {
                foreach ($forum_subscribers as $key => $subscriber) {
                    if (!$this->rs2010forum->permissions->isModerator($subscriber->id)) {
                        unset($forum_subscribers[$key]);
                    }
                }
            }

            // Generate mailing list.
            foreach($forum_subscribers as $subscriber) {
                $this->add_to_mailing_list($subscriber->user_email);
            }

            // Filter mailing list based on usergroups configuration.
            $this->mailing_list = Rs2010ForumUserGroups::filterSubscriberMails($this->mailing_list, $forum->parent_id);
        }

        // Add receivers of administrative notifications to the mailing list when the corresponding option is enabled.
        if ($this->rs2010forum->options['admin_subscriptions']) {
            // Get receivers of admin-notifications.
            $receivers_admin_notifications = explode(',', $this->rs2010forum->options['receivers_admin_notifications']);

            // If found some, add them to the mailing-list.
            if (!empty($receivers_admin_notifications)) {
                foreach ($receivers_admin_notifications as $mail) {
                    $this->add_to_mailing_list($mail);
                }
            }
        }

        // Remove receivers which are inside the ignore-list.
        if ($ignore_list !== false) {
            $this->mailing_list = array_diff($this->mailing_list, $ignore_list);
        }

        // Apply custom filters before sending.
        $this->mailing_list = apply_filters('rs2010forum_subscriber_mails_new_topic', $this->mailing_list);

        // Send notifications.
        $this->send_notifications($this->mailing_list, $notification_subject, $notification_message, $replacements);
    }

    // Adds a mail to a mailing list. Ensures that this mail is not already included.
    public function add_to_mailing_list($mail) {
        if (!in_array($mail, $this->mailing_list)) {
            $this->mailing_list[] = $mail;
        }
    }

    // Apply all replacements in a message-template.
    public function apply_replacements($mail, $message_template, $replacements = array()) {
        // Replace username first.
        $user = get_user_by('email', $mail);

        // Only apply username-replacement when the user exists, other use a more general replacement.
        if ($user) {
            $message_template = str_replace('###USERNAME###', $user->display_name, $message_template);
        } else {
            $message_template = str_replace('###USERNAME###', __('User', 'rs2010-forum'), $message_template);
        }

        // Filter for adding custom user-replacements.
        $replacements = apply_filters('rs2010forum_user_replacements', $replacements, $user);

        // Apply other replacements now.
        foreach ($replacements as $key => $value) {
            $message_template = str_replace($key, $value, $message_template);
        }

        return $message_template;
    }

    public function send_notifications($receivers, $subject, $message_template, $replacements = array()) {
        // Create list of mails in array-format.
        $mails = array();

        if (is_array($receivers)) {
            $mails = $receivers;
        } else {
            $mails[] = $receivers;
        }

        // Prepare header and send mails.
        add_filter('wp_mail_content_type', array($this, 'wpdocs_set_html_mail_content_type'));

        $mail_headers = $this->get_mail_headers();

        foreach($mails as $mail) {
            $message = $this->apply_replacements($mail, $message_template, $replacements);

            wp_mail($mail, $subject, $message, $mail_headers);
        }

        remove_filter('wp_mail_content_type', array($this, 'wpdocs_set_html_mail_content_type'));

        // Clear mailing-list after sending notifications.
        $this->mailing_list = array();
    }

    public function wpdocs_set_html_mail_content_type() {
        return 'text/html';
    }

    public function get_mail_headers() {
        $sender_name = wp_specialchars_decode(esc_html(stripslashes($this->rs2010forum->options['notification_sender_name'])), ENT_QUOTES);;
        $sender_mail = wp_specialchars_decode(esc_html(stripslashes($this->rs2010forum->options['notification_sender_mail'])), ENT_QUOTES);;

        $header = array();
        $header[] = 'From: '.$sender_name.' <'.$sender_mail.'>';

        return $header;
    }

    public function show_subscription_overview_link() {
        if ($this->rs2010forum->options['allow_subscriptions'] ) {
            $subscription_link = $this->rs2010forum->get_link('subscriptions');

            return array(
                'menu_class'        => 'subscriptions-link',
                'menu_link_text'    => esc_html__('Subscriptions', 'rs2010-forum'),
                'menu_url'          => $subscription_link,
                'menu_login_status' => 1,
                'menu_new_tab'      => false
            );
        }
    }

    // Shows all subscriptions of a user (topics/forums).
    public function show_subscription_overview() {
        $user_id = get_current_user_id();

        // When site-owner notifications are enabled and we are the site owner, we need to print a notice.
        if ($this->rs2010forum->options['admin_subscriptions']) {
            // Get data of current user.
            $current_user = wp_get_current_user();
            $receivers_admin_notifications = explode(',', $this->rs2010forum->options['receivers_admin_notifications']);

            // Check if the user is a receiver of administrative notifications.
            if (!empty($receivers_admin_notifications)) {
                if (in_array($current_user->user_email, $receivers_admin_notifications)) {
                    $notice = __('You will automatically get notified about new topics because you are a receiver of administrative notifications.', 'rs2010-forum');
                    $this->rs2010forum->render_notice($notice, false, false, true);
                }
            }
        }

        // Get the subscription level.
        $subscription_level = $this->get_subscription_level();

        // Render subscription settings.
        echo '<div class="title-element title-element-dark">';
            echo '<span class="title-element-icon fas fa-envelope"></span>';
            echo __('Subscription Settings', 'rs2010-forum');
        echo '</div>';

        echo '<div id="subscriptions-panel" class="content-container">';
            echo '<form method="post" action="'.$this->rs2010forum->get_link('subscriptions').'">';
                echo '<div class="action-panel">';
                    echo '<label class="action-panel-option">';
                        echo '<input type="radio" name="subscription_level" value="1" '.checked($subscription_level, 1, false).'>'.__('Individual Subscriptions', 'rs2010-forum');
                        echo '<span class="action-panel-description">';
                            _e('You get notified about activity in forums and topics you are subscribed to.', 'rs2010-forum');
                        echo '</span>';
                    echo '</label>';
                    echo '<label class="action-panel-option">';
                        echo '<input type="radio" name="subscription_level" value="2" '.checked($subscription_level, 2, false).'>'.__('New Topics', 'rs2010-forum');
                        echo '<span class="action-panel-description">';
                            _e('You get notified about all new topics.', 'rs2010-forum');
                        echo '</span>';
                    echo '</label>';
                    echo '<label class="action-panel-option">';
                        echo '<input type="radio" name="subscription_level" value="3" '.checked($subscription_level, 3, false).'>'.__('New Topics & Posts', 'rs2010-forum');
                        echo '<span class="action-panel-description">';
                            _e('You get notified about all new topics and posts.', 'rs2010-forum');
                        echo '</span>';
                    echo '</label>';
                echo '</div>';
            echo '</form>';
        echo '</div>';

        // Topic subscriptions list always available when we are not subscribed to everything.
        $title = __('Notify about new posts in:', 'rs2010-forum');
        $subscribedTopics = get_user_meta($user_id, 'rs2010forum_subscription_topic');
        $all = ($subscription_level == 3) ? true : false;

        if (!empty($subscribedTopics)) {
            $subscribedTopics = $this->rs2010forum->getSpecificTopics($subscribedTopics);
            $subscribedTopics = $this->filter_list($subscribedTopics, $user_id);
        }

        $this->render_subscriptions_list($title, $subscribedTopics, 'topic', $all);

        $title = __('Notify about new topics in:', 'rs2010-forum');
        $subscribedForums = get_user_meta($user_id, 'rs2010forum_subscription_forum');
        $all = ($subscription_level > 1) ? true : false;

        if (!empty($subscribedForums)) {
            $subscribedForums = $this->rs2010forum->getSpecificForums($subscribedForums);
            $subscribedForums = $this->filter_list($subscribedForums, $user_id);
        }

        $this->render_subscriptions_list($title, $subscribedForums, 'forum', $all);
    }

    public function set_subscription_level() {
        if (isset($_POST['subscription_level'])) {
            $user_id = get_current_user_id();

            if ($_POST['subscription_level'] == 1) {
                delete_user_meta($user_id, 'rs2010forum_subscription_global_posts');
                delete_user_meta($user_id, 'rs2010forum_subscription_global_topics');
            } else if ($_POST['subscription_level'] == 2) {
                delete_user_meta($user_id, 'rs2010forum_subscription_global_posts');
                update_user_meta($user_id, 'rs2010forum_subscription_global_topics', 1);
            } else if ($_POST['subscription_level'] == 3) {
                update_user_meta($user_id, 'rs2010forum_subscription_global_posts', 1);
                delete_user_meta($user_id, 'rs2010forum_subscription_global_topics');
            }
        }
    }

    public function get_subscription_level() {
        $user_id = get_current_user_id();

        $subscription_level = 1;
        $subscription_level_check = get_user_meta($user_id, 'rs2010forum_subscription_global_topics', true);

        if (!empty($subscription_level_check)) {
            $subscription_level = 2;
        } else {
            $subscription_level_check = get_user_meta($user_id, 'rs2010forum_subscription_global_posts', true);

            if (!empty($subscription_level_check)) {
                $subscription_level = 3;
            }
        }

        return $subscription_level;
    }

    // Renders a list of a certain subscription type for the current user.
    public function render_subscriptions_list($title, $data, $type, $all = false) {
        echo '<div class="title-element">'.$title.'</div>';
        echo '<div class="content-container">';

        if ($all) {
            if ($type == 'forum') {
                $this->rs2010forum->render_notice(__('You get notified about <b>all</b> new topics.', 'rs2010-forum'));
            } else if ($type == 'topic') {
                $this->rs2010forum->render_notice(__('You get notified about <b>all</b> new posts.', 'rs2010-forum'));
            }
        } else if (empty($data)) {
            $this->rs2010forum->render_notice(__('No subscriptions yet!', 'rs2010-forum'));
        } else {
            foreach ($data as $item) {
                echo '<div class="content-element subscription">';
                    echo '<a href="'.$this->rs2010forum->get_link($type, $item->id).'" title="'.esc_html(stripslashes($item->name)).'">'.esc_html(stripslashes($item->name)).'</a>';
                    echo '<a class="unsubscribe-link" href="'.$this->rs2010forum->get_link('subscriptions', false, array('unsubscribe_'.$type => $item->id)).'">'.__('Unsubscribe', 'rs2010-forum').'</a>';
                echo '</div>';
            }
        }

        echo '</div>';
    }

    public function filter_list($data, $user_id) {
        // Filter the list based on category.
        foreach ($data as $key => $item) {
            $canAccess = Rs2010ForumUserGroups::canUserAccessForumCategory($user_id, $item->category_id);

            if (!$canAccess) {
                unset($data[$key]);
            } else {
                $canPermAccess = $this->rs2010forum->permissions->canUserAccessForumCategory($user_id, $item->category_id);

                if (!$canPermAccess) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }
}

?>
