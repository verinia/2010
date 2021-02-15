<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumSearch_Widget extends WP_Widget {
    public function __construct() {
        $widget_ops = array('classname' => 'rs2010forumsearch_widget', 'description' => __('A search form for Rs2010 Forum.', 'rs2010-forum'));
		parent::__construct('rs2010forumsearch_widget', __('Rs2010 Forum: Search', 'rs2010-forum'), $widget_ops);
    }

    public function widget($args, $instance) {
        global $rs2010forum;
        $title = null;

        if ($instance['title']) {
            $title = $instance['title'];
        } else {
            $title = __('Forum Search', 'rs2010-forum');
        }

        echo $args['before_widget'];
        echo $args['before_title'].$title.$args['after_title'];

        $locationSetUp = Rs2010ForumWidgets::setUpLocation();

        if ($locationSetUp) {
            // TODO: Rewrite code so can use input-generation of search class.
            echo '<div class="rs2010forum-widget-search">';
            echo '<form method="get" action="'.$rs2010forum->get_link('search').'">';
                // Workaround for broken search in posts/pages when using plain permalink structure.
                if (!$rs2010forum->rewrite->use_permalinks) {
                    echo '<input name="view" type="hidden" value="search">';
                    echo '<input name="page_id" type="hidden" value="'.$rs2010forum->options['location'].'">';
                }

                echo '<input name="keywords" type="search" placeholder="'.__('Search ...', 'rs2010-forum').'" value="'.$rs2010forum->search->search_keywords_for_output.'">';
                echo '<button type="submit"><i class="fas fa-search"></i></button>';
            echo '</form>';
            echo '</div>';
        } else {
            _e('The forum has not been configured correctly.', 'rs2010-forum');
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Forum Search', 'rs2010-forum');

		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'">'.__('Title:', 'rs2010-forum').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'">';
		echo '</p>';
	}

    public function update($new_instance, $old_instance) {
        $instance = array();
		$instance['title'] = sanitize_text_field($new_instance['title']);
		return $instance;
	}
}
