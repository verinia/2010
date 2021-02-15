<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumActivity {
    private $rs2010forum = null;

    public function __construct($object) {
        $this->rs2010forum = $object;

        add_action('rs2010forum_breadcrumbs_activity', array($this, 'add_breadcrumbs'));
    }

    public function functionality_enabled() {
        return $this->rs2010forum->options['enable_activity'];
    }

    public function add_breadcrumbs() {
        $element_link = $this->rs2010forum->get_link('activity');
        $element_title = __('Activity', 'rs2010-forum');
        $this->rs2010forum->breadcrumbs->add_breadcrumb($element_link, $element_title);
    }

    public function show_activity() {
        $activity_days = (int) $this->rs2010forum->options['activity_days'];
        $activity_days_i18n = number_format_i18n($activity_days);
        echo '<div class="main-description">'.sprintf(_n('Activity in the last day.', 'Activity in the last %s days.', $activity_days, 'rs2010-forum'), $activity_days_i18n).'</div>';

        $pagination_rendering = $this->rs2010forum->pagination->renderPagination('activity');
        $paginationRendering = ($pagination_rendering) ? '<div class="pages-and-menu">'.$pagination_rendering.'<div class="clear"></div></div>' : '';
        echo $paginationRendering;

        $data = $this->load_activity_data();

        if (!empty($data)) {
            $date_today = date($this->rs2010forum->date_format);
            $date_yesterday = date($this->rs2010forum->date_format, strtotime('-1 days'));
            $last_time = false;
            $first_group = true;

            foreach ($data as $activity) {
                $current_time = date($this->rs2010forum->date_format, strtotime($activity->date));
                $human_time_diff = sprintf(__('%s ago', 'rs2010-forum'), human_time_diff(strtotime($activity->date), current_time('timestamp')));

                if ($current_time == $date_today) {
                    $current_time = __('Today', 'rs2010-forum');
                } else if ($current_time == $date_yesterday) {
                    $current_time = __('Yesterday', 'rs2010-forum');
                } else {
                    $current_time = $human_time_diff;
                }

                if ($last_time != $current_time) {
                    $last_time = $current_time;

                    if ($first_group) {
                        $first_group = false;
                    } else {
                        echo '</div>';
                    }

                    echo '<div class="title-element">'.$current_time.'</div>';
                    echo '<div class="content-container">';
                }

                $name_author = $this->rs2010forum->getUsername($activity->author_id);
                $name_topic = esc_html(stripslashes($activity->name));
                $read_status = $this->rs2010forum->unread->get_status_post($activity->id, $activity->author_id, $activity->date, $activity->parent_id);

                if ($this->rs2010forum->is_first_post($activity->id, $activity->parent_id)) {
                    $link = $this->rs2010forum->get_link('topic', $activity->parent_id);
                    $link_html = '<a href="'.$link.'">'.$name_topic.'</a>';
                    echo '<div class="content-element activity-element">';
                    echo '<span class="activity-icon fas fa-comments '.$read_status.'"></span>';
                    echo sprintf(__('New topic %s created by %s.', 'rs2010-forum'), $link_html, $name_author).' <i class="activity-time">'.$human_time_diff.'</i>';
                    echo '</div>';
                } else {
                    $link = $this->rs2010forum->rewrite->get_post_link($activity->id, $activity->parent_id);
                    $link_html = '<a href="'.$link.'">'.$name_topic.'</a>';
                    echo '<div class="content-element activity-element">';
                    echo '<span class="activity-icon fas fa-comment '.$read_status.'"></span>';
                    echo sprintf(__('%s replied in %s.', 'rs2010-forum'), $name_author, $link_html).' <i class="activity-time">'.$human_time_diff.'</i>';
                    echo '</div>';
                }
            }

            echo '</div>';
        } else {
            echo '<div class="title-element"></div>';
            echo '<div class="content-container">';
            $this->rs2010forum->render_notice(__('No activity yet!', 'rs2010-forum'));
            echo '</div>';
        }

        echo $paginationRendering;
    }

    public function load_activity_data($count_all = false) {
        $ids_categories = $this->rs2010forum->content->get_categories_ids();

        if (empty($ids_categories)) {
            return false;
        } else {
            $ids_categories = implode(',', $ids_categories);

            // Calculate activity end-time.
            $time_current = time();
            $time_end = $time_current - ((int) $this->rs2010forum->options['activity_days'] * 24 * 60 * 60);
            $time_end = date('Y-m-d H:i:s', $time_end);

            if ($count_all) {
                return $this->rs2010forum->db->get_var("SELECT COUNT(*) FROM {$this->rs2010forum->tables->posts} p, {$this->rs2010forum->tables->topics} t, (SELECT id FROM {$this->rs2010forum->tables->forums} WHERE parent_id IN ({$ids_categories})) f WHERE p.parent_id = t.id AND t.parent_id = f.id AND t.approved = 1 AND p.date > '{$time_end}';");
            } else {
                $start = $this->rs2010forum->current_page * $this->rs2010forum->options['activities_per_page'];
                $end = $this->rs2010forum->options['activities_per_page'];

                return $this->rs2010forum->db->get_results("SELECT p.id, p.parent_id, p.date, p.author_id, t.name FROM {$this->rs2010forum->tables->posts} p, {$this->rs2010forum->tables->topics} t, (SELECT id FROM {$this->rs2010forum->tables->forums} WHERE parent_id IN ({$ids_categories})) f WHERE p.parent_id = t.id AND t.parent_id = f.id AND t.approved = 1 AND p.date > '{$time_end}' ORDER BY p.id DESC LIMIT {$start}, {$end};");
            }
        }
    }

    public function show_activity_link() {
        if ($this->functionality_enabled()) {
            $activity_link = $this->rs2010forum->get_link('activity');

            return array(
                'menu_class'        => 'activity-link',
                'menu_link_text'    => esc_html__('Activity', 'rs2010-forum'),
                'menu_url'          => $activity_link,
                'menu_login_status' => 0,
                'menu_new_tab'      => false
            );
        }
    }
}
