<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumPagination {
    private $rs2010forum = null;

    public function __construct($object) {
        $this->rs2010forum = $object;
    }

    public function renderTopicOverviewPagination($topic_id, $topic_counter) {
        $num_pages = ceil($topic_counter / $this->rs2010forum->options['posts_per_page']);

        // Only show pagination when there is more than one page.
        if ($num_pages > 1) {
            echo '&nbsp;&middot;&nbsp;<div class="pages">';

            if ($num_pages <= 5) {
                for ($i = 1; $i <= $num_pages; $i++) {
                    echo $this->page_link('topic', $topic_id, $i);
                }
            } else {
                for ($i = 1; $i <= 3; $i++) {
                    echo $this->page_link('topic', $topic_id, $i);
                }

                $link = $this->rs2010forum->get_link('topic', $topic_id, array('part' => $num_pages));
                echo '<a href="'.$link.'">'._x('Last', 'Last topic', 'rs2010-forum').'&nbsp;&raquo;</a>';
            }

            echo '</div>';
        }
    }

    public function page_link($location, $id, $page) {
        $link = $this->rs2010forum->get_link($location, $id, array('part' => $page));

        return '<a href="'.$link.'">'.number_format_i18n($page).'</a>';
    }

    public function renderPagination($location, $sourceID = false, $element_counter = false) {
        $current_page = $this->rs2010forum->current_page;
        $num_pages = 0;
        $select_url = $this->rs2010forum->get_link('current', false, false, '', false);

        if ($location == $this->rs2010forum->tables->posts) {
            $count = $this->rs2010forum->db->get_var($this->rs2010forum->db->prepare("SELECT COUNT(*) FROM {$location} WHERE parent_id = %d;", $sourceID));
            $num_pages = ceil($count / $this->rs2010forum->options['posts_per_page']);
        } else if ($location == $this->rs2010forum->tables->topics) {
            $count = $this->rs2010forum->db->get_var($this->rs2010forum->db->prepare("SELECT COUNT(*) FROM {$location} WHERE parent_id = %d AND approved = 1 AND sticky = 0;", $sourceID));
            $num_pages = ceil($count / $this->rs2010forum->options['topics_per_page']);
        } else if ($location === 'search') {
            $categories = $this->rs2010forum->content->get_categories();
            $categoriesFilter = array();

            foreach ($categories as $category) {
                $categoriesFilter[] = $category->term_id;
            }

            $where = 'AND f.parent_id IN ('.implode(',', $categoriesFilter).')';
            $shortcodeSearchFilter = $this->rs2010forum->shortcode->shortcodeSearchFilter;

            $query_match_name = "SELECT id AS topic_id FROM {$this->rs2010forum->tables->topics} WHERE MATCH (name) AGAINST ('{$this->rs2010forum->search->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $query_match_text = "SELECT parent_id AS topic_id FROM {$this->rs2010forum->tables->posts} WHERE MATCH (text) AGAINST ('{$this->rs2010forum->search->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $count = $this->rs2010forum->db->get_var("SELECT COUNT(*) FROM (({$query_match_name}) UNION ({$query_match_text})) AS su, {$this->rs2010forum->tables->topics} AS t, {$this->rs2010forum->tables->forums} AS f WHERE su.topic_id = t.id AND t.parent_id = f.id AND t.approved = 1 {$where} {$shortcodeSearchFilter};");
            $count = (int) $count;
            $num_pages = ceil($count / $this->rs2010forum->options['topics_per_page']);
        } else if ($location === 'members') {
            // Count the users based on the filter.
            $count = 0;

            if ($this->rs2010forum->memberslist->filter_type === 'role') {
                $count = count($this->rs2010forum->permissions->get_users_by_role($this->rs2010forum->memberslist->filter_name));
            } else if ($this->rs2010forum->memberslist->filter_type === 'group') {
                $count = Rs2010ForumUserGroups::countUsersOfUserGroup($this->rs2010forum->memberslist->filter_name);
            }

            $num_pages = ceil($count / $this->rs2010forum->options['members_per_page']);
        } else if ($location === 'activity') {
            $count = $this->rs2010forum->activity->load_activity_data(true);
            $num_pages = ceil($count / $this->rs2010forum->options['activities_per_page']);
        } else if ($location === 'history') {
            $user_id = $this->rs2010forum->current_element;
            $count = $this->rs2010forum->profile->count_post_history_by_user($user_id);
            $num_pages = ceil($count / 50);
        } else if ($location === 'unread') {
            $num_pages = ceil($element_counter / 50);
        } else if ($location === 'unapproved') {
            $num_pages = ceil($element_counter / 50);
        }

        // Only show pagination when there is more than one page.
        if ($num_pages > 1) {
            $out = '<div class="pages">';

            if ($num_pages <= 5) {
                for ($i = 1; $i <= $num_pages; $i++) {
                    if ($i == ($current_page + 1)) {
                        $out .= '<strong>'.number_format_i18n($i).'</strong>';
                    } else {
                        $link = add_query_arg('part', $i, $select_url);
                        $out .= '<a href="'.$link.'">'.number_format_i18n($i).'</a>';
                    }
                }
            } else {
                if ($current_page >= 3) {
                    $link = remove_query_arg('part', $select_url);
                    $out .= '<a href="'.$link.'">&laquo;&nbsp;'.__('First', 'rs2010-forum').'</a>';
                }

                for ($i = 2; $i > 0; $i--) {
                    if ((($current_page + 1) - $i) > 0) {
                        $link = add_query_arg('part', (($current_page + 1) - $i), $select_url);
                        $out .= '<a href="'.$link.'">'.number_format_i18n(($current_page + 1) - $i).'</a>';
                    }
                }

                $out .= '<strong>'.number_format_i18n($current_page + 1).'</strong>';

                for ($i = 1; $i <= 2; $i++) {
                    if ((($current_page + 1) + $i) <= $num_pages) {
                        $link = add_query_arg('part', (($current_page + 1) + $i), $select_url);
                        $out .= '<a href="'.$link.'">'.number_format_i18n(($current_page + 1) + $i).'</a>';
                    }
                }

                if ($num_pages - $current_page >= 4) {
                    $link = add_query_arg('part', $num_pages, $select_url);
                    $out .= '<a href="'.$link.'">'._x('Last', 'Last Page', 'rs2010-forum').'&nbsp;&raquo;</a>';
                }
            }

            $out .= '</div>';
            return $out;
        } else {
            return false;
        }
    }
}
