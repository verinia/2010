<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumBreadCrumbs {
    private $rs2010forum = null;
    public $breadcrumbs_level = 4;
    public $breadcrumbs_links = array();
    private $breadcrumb_position = 0;

    public function __construct($object) {
        $this->rs2010forum = $object;
    }

    public function add_breadcrumb($link, $title) {
        $this->breadcrumbs_links[] = array(
            'link'      => $link,
            'title'     => $title
        );
    }

    public function show_breadcrumbs() {
        // Ensure that this feature is not disabled.
        if (!$this->rs2010forum->options['enable_breadcrumbs']) {
            return;
        }

        // Ensure that no error is thrown.
        if ($this->rs2010forum->error !== false) {
            return;
        }

        if ($this->breadcrumbs_level >= 4) {
            $element_link = $this->rs2010forum->get_link('home');
            $element_title = $this->rs2010forum->options['forum_title'];
            $this->add_breadcrumb($element_link, $element_title);
        }

        // Define category prefix.
        $category_prefix = '';

        if ($this->rs2010forum->options['breadcrumbs_show_category']) {
            if ($this->breadcrumbs_level >= 4 && $this->rs2010forum->current_category) {
                $category_name = $this->rs2010forum->get_category_name($this->rs2010forum->current_category);

                if ($category_name) {
                    $category_prefix = $category_name.': ';
                }
            }
        }

        // Define forum breadcrumbs.
        if ($this->breadcrumbs_level >= 3 && $this->rs2010forum->parent_forum && $this->rs2010forum->parent_forum > 0) {
            $element_link = $this->rs2010forum->get_link('forum', $this->rs2010forum->parent_forum);
            $element_title = $category_prefix.esc_html(stripslashes($this->rs2010forum->parent_forum_name));
            $this->add_breadcrumb($element_link, $element_title);
            $category_prefix = '';
        }

        if ($this->breadcrumbs_level >= 2 && $this->rs2010forum->current_forum) {
            $element_link = $this->rs2010forum->get_link('forum', $this->rs2010forum->current_forum);
            $element_title = $category_prefix.esc_html(stripslashes($this->rs2010forum->current_forum_name));
            $this->add_breadcrumb($element_link, $element_title);
        }

        if ($this->breadcrumbs_level >= 1 && $this->rs2010forum->current_topic) {
            $name = stripslashes($this->rs2010forum->current_topic_name);
            $element_link = $this->rs2010forum->get_link('topic', $this->rs2010forum->current_topic);
            $element_title = esc_html($this->rs2010forum->cut_string($name));
            $this->add_breadcrumb($element_link, $element_title);
        }

        if ($this->rs2010forum->current_view === 'addpost') {
            $element_link = $this->rs2010forum->get_link('current');
            $element_title = __('Post Reply', 'rs2010-forum');
            $this->add_breadcrumb($element_link, $element_title);
        } else if ($this->rs2010forum->current_view === 'editpost') {
            $element_link = $this->rs2010forum->get_link('current');
            $element_title = __('Edit Post', 'rs2010-forum');
            $this->add_breadcrumb($element_link, $element_title);
        } else if ($this->rs2010forum->current_view === 'addtopic') {
            $element_link = $this->rs2010forum->get_link('current');
            $element_title = __('New Topic', 'rs2010-forum');
            $this->add_breadcrumb($element_link, $element_title);
        } else if ($this->rs2010forum->current_view === 'movetopic') {
            $element_link = $this->rs2010forum->get_link('current');
            $element_title = __('Move Topic', 'rs2010-forum');
            $this->add_breadcrumb($element_link, $element_title);
        }

        do_action('rs2010forum_breadcrumbs_'.$this->rs2010forum->current_view);

        // Render breadcrumbs links.
        echo '<div id="forum-breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';
            echo '<span class="screen-reader-text">'.__('Forum breadcrumbs - You are here:', 'rs2010-forum').'</span>';
            echo '<span class="breadcrumb-icon fas fa-home"></span>';

            foreach ($this->breadcrumbs_links as $element) {
                $this->render_breadcrumb($element);
            }
        echo '</div>';
    }

    public function render_breadcrumb($element) {
        $this->breadcrumb_position++;

        echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a itemprop="item" href="'.$element['link'].'" title="'.$element['title'].'">';
                echo '<span itemprop="name">'.$element['title'].'</span>';
            echo '</a>';
            echo '<meta itemprop="position" content="'.$this->breadcrumb_position.'">';
        echo '</span>';

        echo '<span class="breadcrumb-icon fas fa-chevron-right separator"></span>';
    }
}
