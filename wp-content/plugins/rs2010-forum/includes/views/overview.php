<?php

if (!defined('ABSPATH')) exit;

if ($categories) {
    $forumsAvailable = false;

    foreach ($categories as $category) {
        // Reset forums-counter for ads when we enter a new category.
        $this->ads->counter_forums = 0;

        echo '<div class="brown-box-area"><div class="title-element" id="forum-category-'.$category->term_id.'">';
            echo $category->name;
            echo '<span class="last-post-headline">'.__('Last post', 'rs2010-forum').'</span>';
            echo '<span class="post_count-headline">'.__('Posts', 'rs2010-forum').'</span>';
            echo '<span class="topic_count-headline">'.__('Threads', 'rs2010-forum').'</span>';
        echo '</div>';
        echo '<div class="content-container">';
            $forums = $this->get_forums($category->term_id);
            if (empty($forums)) {
                $this->render_notice(__('In this category are no forums yet!', 'rs2010-forum'));
            } else {
                foreach ($forums as $forum) {
                    $forumsAvailable = true;

                    $this->render_forum_element($forum);
                }
            }
        echo '</div></div>';

        do_action('rs2010forum_after_category');
    }

    if ($forumsAvailable) {
        $this->unread->show_unread_controls();
    }

    Rs2010ForumStatistics::showStatistics();
} else {
    $this->render_notice(__('There are no categories yet!', 'rs2010-forum'));
}
