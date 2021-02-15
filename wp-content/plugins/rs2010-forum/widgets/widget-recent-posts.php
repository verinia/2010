<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumRecentPosts_Widget extends WP_Widget {
    private $rs2010forum = null;

    public function __construct() {
        global $rs2010forum;
        $this->rs2010forum = $rs2010forum;
        $widget_ops = array('classname' => 'rs2010forumrecentposts_widget', 'description' => __('Shows recent posts in Rs2010 Forum.', 'rs2010-forum'));
		parent::__construct('rs2010forumrecentposts_widget', __('Rs2010 Forum: Recent Posts', 'rs2010-forum'), $widget_ops);
    }

    public function widget($args, $instance) {
        // Ensure that the correct location is set.
        $location_check = Rs2010ForumWidgets::setUpLocation();

        if (!$location_check) {
            $output = __('The forum has not been configured correctly.', 'rs2010-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Ensure that there are accessible categories available.
        $available_categories = $this->rs2010forum->content->get_categories_ids();

        if (empty($available_categories)) {
            $output = __('No topics yet!', 'rs2010-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Generate stringified list of available categories.
        $available_categories = implode(',', $available_categories);

        // Ensure that there are accessible forums available.
        $available_forums = $this->rs2010forum->db->get_col("SELECT id FROM {$this->rs2010forum->tables->forums} WHERE parent_id IN ({$available_categories});");

        if (empty($available_forums)) {
            $output = __('No topics yet!', 'rs2010-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Ensure that there are forums available after applying possible filters.
        $forum_filters = !empty($instance['forum_filter']) ? $instance['forum_filter'] : array();

        if (!empty($forum_filters)) {
            $available_forums = array_intersect($available_forums, $forum_filters);
        }

        if (empty($available_forums)) {
            $output = __('No topics yet!', 'rs2010-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Generate stringified list of available forums.
        $available_forums = implode(',', $available_forums);

        // Try to get forum posts.
        $number = ($instance['number']) ? absint($instance['number']) : 3;
        $group = isset($instance['group_by_topic']) ? $instance['group_by_topic'] : true;

        $post_ids = array();

        if ($group) {
            $post_ids = $this->rs2010forum->db->get_col("SELECT MAX(p.id) AS id FROM {$this->rs2010forum->tables->posts} AS p LEFT JOIN {$this->rs2010forum->tables->topics} AS t ON (t.id = p.parent_id) WHERE p.forum_id IN({$available_forums}) AND t.approved = 1 GROUP BY p.parent_id ORDER BY MAX(p.id) DESC LIMIT {$number};");
        } else {
            $post_ids = $this->rs2010forum->db->get_col("SELECT p.id FROM {$this->rs2010forum->tables->posts} AS p LEFT JOIN {$this->rs2010forum->tables->topics} AS t ON (t.id = p.parent_id) WHERE p.forum_id IN({$available_forums}) AND t.approved = 1 ORDER BY p.id DESC LIMIT {$number};");
        }

        // Ensure that there are forum posts available.
        if (empty($post_ids)) {
            $output = __('No topics yet!', 'rs2010-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Generate stringified list of available forums.
        $post_ids = implode(',', $post_ids);

        // Get post details.
        $elements = $this->rs2010forum->db->get_results("SELECT p.id, p.text, p.date, p.parent_id, p.author_id, t.name, (SELECT COUNT(*) FROM {$this->rs2010forum->tables->posts} WHERE parent_id = p.parent_id) AS post_counter FROM {$this->rs2010forum->tables->posts} AS p LEFT JOIN {$this->rs2010forum->tables->topics} AS t ON (t.id = p.parent_id) WHERE p.id IN ({$post_ids}) ORDER BY p.id DESC;");

        // Get options.
        $show_avatar = isset($instance['show_avatar']) ? $instance['show_avatar'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? $instance['show_excerpt'] : false;

        // Get custom values.
        $title_length = apply_filters('rs2010forum_filter_widget_title_length', 33);
        $excerpt_length = apply_filters('rs2010forum_widget_excerpt_length', 66);
        $avatar_size = apply_filters('rs2010forum_filter_widget_avatar_size', 30);

        // Generate output.
        $output = '<div class="rs2010forum-widget">';

        foreach ($elements as $element) {
            $output .= '<div class="widget-element">';

            // Add avatars
            if ($show_avatar) {
                $output .= '<div class="widget-avatar">'.get_avatar($element->author_id, $avatar_size, '', '', array('force_display' => true)).'</div>';
            }

            $output .= '<div class="widget-content">';
                // Generate link.
                $page = ceil($element->post_counter / $this->rs2010forum->options['posts_per_page']);
                $link = $this->rs2010forum->get_link('topic', $element->parent_id, array('part' => $page), '#postid-'.$element->id);

                $output .= '<span class="post-link"><a href="'.$link.'" title="'.esc_html(stripslashes($element->name)).'">'.esc_html($this->rs2010forum->cut_string(stripslashes($element->name), $title_length)).'</a></span>';
                $output .= '<span class="post-author">'.__('by', 'rs2010-forum').'&nbsp;<b>'.$this->rs2010forum->getUsername($element->author_id).'</b></span>';

                if ($show_excerpt) {
                    $text = esc_html(stripslashes(strip_tags(strip_shortcodes($element->text))));
                    $text = $this->rs2010forum->cut_string($text, $excerpt_length);

                    if (!empty($text)) {
                        $output .= '<span class="post-excerpt">'.$text.'&nbsp;<a class="post-read-more" href="'.$link.'">'.__('Read More', 'rs2010-forum').'</a></span>';
                    }
                }

                $output .= '<span class="post-date">'.sprintf(__('%s ago', 'rs2010-forum'), human_time_diff(strtotime($element->date), current_time('timestamp'))).'</span>';

                $custom_content = apply_filters('rs2010forum_widget_recent_posts_custom_content', '', $element->id);
                $output .= $custom_content;

            $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '</div>';

        $this->widget_output($args, $instance, $output);
    }

    public function widget_output($args, $instance, $output) {
        // Generate title.
        $title = __('Recent Forum Posts', 'rs2010-forum');

        if ($instance['title']) {
            $title = $instance['title'];
        }

        // Generate final output.
        echo $args['before_widget'];
        echo $args['before_title'];
        echo $title;
        echo $args['after_title'];
        echo $output;
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Recent forum posts', 'rs2010-forum');
        $number = isset($instance['number']) ? absint($instance['number']) : 3;
        $show_avatar = isset($instance['show_avatar']) ? (bool)$instance['show_avatar'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? (bool)$instance['show_excerpt'] : false;
        $group_by_topic = isset($instance['group_by_topic']) ? (bool)$instance['group_by_topic'] : true;
        $forum_filter = isset($instance['forum_filter']) ? $instance['forum_filter'] : array();

		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'">'.__('Title:', 'rs2010-forum').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'">';
		echo '</p>';

        echo '<p>';
		echo '<label for="'.$this->get_field_id('number').'">'.__('Number of topics to show:', 'rs2010-forum').'</label>&nbsp;';
		echo '<input class="tiny-text" id="'.$this->get_field_id('number').'" name="'.$this->get_field_name('number').'" type="number" step="1" min="1" value="'.$number.'" size="3">';
		echo '</p>';

        echo '<p>';
        echo '<input class="checkbox" type="checkbox" '.checked($show_avatar, true, false).' id="'.$this->get_field_id('show_avatar').'" name="'.$this->get_field_name('show_avatar').'">';
		echo '<label for="'.$this->get_field_id('show_avatar').'">'.__('Show avatars', 'rs2010-forum').'</label>';
        echo '</p>';

        echo '<p>';
        echo '<input class="checkbox" type="checkbox" '.checked($show_excerpt, true, false).' id="'.$this->get_field_id('show_excerpt').'" name="'.$this->get_field_name('show_excerpt').'">';
		echo '<label for="'.$this->get_field_id('show_excerpt').'">'.__('Show excerpt', 'rs2010-forum').'</label>';
        echo '</p>';

        echo '<p>';
        echo '<input class="checkbox" type="checkbox" '.checked($group_by_topic, true, false).' id="'.$this->get_field_id('group_by_topic').'" name="'.$this->get_field_name('group_by_topic').'">';
		echo '<label for="'.$this->get_field_id('group_by_topic').'">'.__('Group posts by topic', 'rs2010-forum').'</label>';
        echo '</p>';

        echo '<p>';
        echo '<label for="'.$this->get_field_id('forum_filter').'">'.__('Forum filter:', 'rs2010-forum').'</label>';
        echo '<select name="'.$this->get_field_name('forum_filter').'[]" id="'.$this->get_field_id('forum_filter').'" class="widefat" size="6" multiple>';

        // Generate list of available forums.
        $categories = $this->rs2010forum->content->get_categories(false);

        if ($categories) {
            foreach ($categories as $category) {
                $forums = $this->rs2010forum->get_forums($category->term_id, 0);

                if ($forums) {
                    echo '<option disabled="disabled">'.$category->name.':</option>';

                    foreach ($forums as $forum) {
                        if (in_array($forum->id, $forum_filter)) {
                            echo '<option value="'.$forum->id.'" selected="selected">- '.esc_html($forum->name).'</option>';
                        } else {
                            echo '<option value="'.$forum->id.'">- '.esc_html($forum->name).'</option>';
                        }

                        if ($forum->count_subforums > 0) {
                            $subforums = $this->rs2010forum->get_forums($category->term_id, $forum->id);

                            foreach ($subforums as $subforum) {
                                if (in_array($subforum->id, $forum_filter)) {
                                    echo '<option value="'.$subforum->id.'" selected="selected">-- '.esc_html($subforum->name).'</option>';
                                } else {
                                    echo '<option value="'.$subforum->id.'">-- '.esc_html($subforum->name).'</option>';
                                }
                            }
                        }
                    }
                }
            }
        }

        echo '</select>';
        echo '</p>';
	}

    public function update($new_instance, $old_instance) {
        $instance = array();
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['number'] = (int)$new_instance['number'];
        $instance['show_avatar'] = isset($new_instance['show_avatar']) ? (bool)$new_instance['show_avatar'] : false;
        $instance['show_excerpt'] = isset($new_instance['show_excerpt']) ? (bool)$new_instance['show_excerpt'] : false;
        $instance['group_by_topic'] = isset($new_instance['group_by_topic']) ? (bool)$new_instance['group_by_topic'] : false;
        $instance['forum_filter'] = isset($new_instance['forum_filter']) ? $new_instance['forum_filter'] : array();
		return $instance;
	}
}
