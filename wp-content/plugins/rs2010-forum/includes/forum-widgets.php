<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumWidgets {
    private static $rs2010forum = null;

    public function __construct($object) {
        self::$rs2010forum = $object;

        add_action('widgets_init', array($this, 'initializeWidgets'));
    }

    public function initializeWidgets() {
        if (!self::$rs2010forum->options['require_login'] || is_user_logged_in()) {
            register_widget('Rs2010ForumRecentPosts_Widget');
            register_widget('Rs2010ForumRecentTopics_Widget');
            register_widget('Rs2010ForumSearch_Widget');
        }
    }

    public static function setUpLocation() {
        $locationSetUp = self::$rs2010forum->shortcode->checkForShortcode();

        // Try to get the forum-location when it is not set correctly.
        if (!$locationSetUp) {
            $pageID = self::$rs2010forum->db->get_var('SELECT ID FROM '.self::$rs2010forum->db->prefix.'posts WHERE post_type = "page" AND (post_content LIKE "%[forum]%" OR post_content LIKE "%[Forum]%");');
            if ($pageID) {
                self::$rs2010forum->options['location'] = $pageID;
                self::$rs2010forum->rewrite->set_links();
                $locationSetUp = true;
            }
        }

        return $locationSetUp;
    }
}
