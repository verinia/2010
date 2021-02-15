<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumStatistics {
    private static $rs2010forum = null;

    public function __construct($object) {
		self::$rs2010forum = $object;
    }

    public static function showStatistics() {
        // Check if this functionality is enabled.
        if (self::$rs2010forum->options['show_statistics']) {
            $data = self::getData();
            echo '<div id="statistics">';
                echo '<div class="title-element title-element-dark">';
                    echo '<span class="title-element-icon fas fa-chart-pie"></span>';
                    echo __('Statistics', 'rs2010-forum');
                echo '</div>';
                echo '<div id="statistics-body">';
                    echo '<div id="statistics-elements">';
                        self::renderStatisticsElement(__('Topics', 'rs2010-forum'), $data->topics, 'far fa-comments');
                        self::renderStatisticsElement(__('Posts', 'rs2010-forum'), $data->posts, 'far fa-comment');

                        if (self::$rs2010forum->options['count_topic_views']) {
                            self::renderStatisticsElement(__('Views', 'rs2010-forum'), $data->views, 'far fa-eye');
                        }

                        self::renderStatisticsElement(__('Users', 'rs2010-forum'), $data->users, 'far fa-user');
                        self::$rs2010forum->online->render_statistics_element();
                        do_action('rs2010forum_statistics_custom_element');
                    echo '</div>';
                    self::$rs2010forum->online->render_online_information();
                echo '</div>';
                do_action('rs2010forum_statistics_custom_content_bottom');
                echo '<div class="clear"></div>';
            echo '</div>';
        }
    }

    public static function getData() {
        $queryTopics = 'SELECT COUNT(*) FROM '.self::$rs2010forum->tables->topics;
        $queryPosts = 'SELECT COUNT(*) FROM '.self::$rs2010forum->tables->posts;

        $queryViews = '0';

        if (self::$rs2010forum->options['count_topic_views']) {
            $queryViews = 'SELECT SUM(views) FROM '.self::$rs2010forum->tables->topics;
        }

        $data = self::$rs2010forum->db->get_row("SELECT ({$queryTopics}) AS topics, ({$queryPosts}) AS posts, ({$queryViews}) AS views");
        $data->users = self::$rs2010forum->count_users();
        return $data;
    }

    public static function renderStatisticsElement($title, $data, $iconClass) {
        echo '<div class="statistics-element">';
            echo '<div class="element-number">';
                echo '<span class="statistics-element-icon '.$iconClass.'"></span>';
                echo number_format_i18n($data);
            echo '</div>';
            echo '<div class="element-name">'.$title.'</div>';
        echo '</div>';
    }
}
