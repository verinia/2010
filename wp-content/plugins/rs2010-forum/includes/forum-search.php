<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumSearch {
    private $rs2010forum = null;
    public $search_keywords_for_query = '';
    public $search_keywords_for_output = '';

    public function __construct($object) {
		$this->rs2010forum = $object;

        add_action('init', array($this, 'initialize'));
        add_action('rs2010forum_breadcrumbs_search', array($this, 'add_breadcrumbs'));
    }

    public function initialize() {
        if (!empty($_GET['keywords'])) {
            $keywords = trim($_GET['keywords']);
            $this->search_keywords_for_query = esc_sql($keywords);
            $this->search_keywords_for_output = stripslashes(esc_html($keywords));
        }
    }

    public function add_breadcrumbs() {
        $element_link = $this->rs2010forum->get_link('current');
        $element_title = __('Search', 'rs2010-forum');
        $this->rs2010forum->breadcrumbs->add_breadcrumb($element_link, $element_title);
    }

    public function show_search_input() {
        if ($this->rs2010forum->options['enable_search']) {
            echo '<div id="forum-search">';
            echo '<span class="search-icon fas fa-search"></span>';

            echo '<form method="get" action="'.$this->rs2010forum->get_link('search').'">';

            // Workaround for broken search when using plain permalink structure.
            if (!$this->rs2010forum->rewrite->use_permalinks) {
                echo '<input name="view" type="hidden" value="search">';
            }

            // Workaround for broken search in posts when using plain permalink structure.
            if (!empty($_GET['p'])) {
                $value = esc_html(trim($_GET['p']));
                echo '<input name="p" type="hidden" value="'.$value.'">';
            }

            // Workaround for broken search in pages when using plain permalink structure.
            if (!empty($_GET['page_id'])) {
                $value = esc_html(trim($_GET['page_id']));
                echo '<input name="page_id" type="hidden" value="'.$value.'">';
            }

            echo '<input name="keywords" type="search" placeholder="'.__('Search ...', 'rs2010-forum').'" value="'.$this->search_keywords_for_output.'">';
            echo '</form>';
            echo '</div>';
        }
    }

    public function show_search_results() {
        $results = $this->get_search_results();

        $paginationRendering = ($results) ? '<div class="pages-and-menu">'.$this->rs2010forum->pagination->renderPagination('search').'<div class="clear"></div></div>' : '';
        echo $paginationRendering;

        echo '<div class="title-element">';
            echo __('Search results:', 'rs2010-forum').' '.$this->search_keywords_for_output;
            echo '<span class="last-post-headline">'.__('Last post', 'rs2010-forum').'</span>';
        echo '</div>';
        echo '<div class="content-container">';

        if ($results) {
            foreach ($results as $topic) {
                $this->rs2010forum->render_topic_element($topic, 'topic-normal', true);
            }
        } else {
            $notice = __('No results found for:', 'rs2010-forum').'&nbsp;<b>'.$this->search_keywords_for_output.'</b>';
            $this->rs2010forum->render_notice($notice);
        }

        echo '</div>';

        echo $paginationRendering;
    }

    public function get_search_results() {
        if (!empty($this->search_keywords_for_query)) {
            $categories = $this->rs2010forum->content->get_categories();
            $categoriesFilter = array();

            foreach ($categories as $category) {
                $categoriesFilter[] = $category->term_id;
            }

            // Do not execute a search-query when no categories are accessible.
            if (empty($categoriesFilter)) {
                return false;
            }

            $where = 'AND f.parent_id IN ('.implode(',', $categoriesFilter).')';

            $start = $this->rs2010forum->current_page * $this->rs2010forum->options['topics_per_page'];
            $end = $this->rs2010forum->options['topics_per_page'];
            $limit = $this->rs2010forum->db->prepare("LIMIT %d, %d", $start, $end);

            $shortcodeSearchFilter = $this->rs2010forum->shortcode->shortcodeSearchFilter;

            $match_name = "MATCH (name) AGAINST ('{$this->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $match_text = "MATCH (text) AGAINST ('{$this->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $query_answers = "SELECT (COUNT(*) - 1) FROM {$this->rs2010forum->tables->posts} WHERE parent_id = t.id";
            $query_match_name = "SELECT id AS topic_id, {$match_name} AS score_name, 0 AS score_text FROM {$this->rs2010forum->tables->topics} WHERE {$match_name} GROUP BY topic_id";
            $query_match_text = "SELECT parent_id AS topic_id, 0 AS score_name, {$match_text} AS score_text FROM {$this->rs2010forum->tables->posts} WHERE {$match_text} GROUP BY topic_id";

            $query = "SELECT t.*, f.id AS forum_id, f.name AS forum_name, ({$query_answers}) AS answers, su.topic_id, SUM(su.score_name + su.score_text) AS score FROM ({$query_match_name} UNION {$query_match_text}) AS su, {$this->rs2010forum->tables->topics} AS t, {$this->rs2010forum->tables->forums} AS f WHERE su.topic_id = t.id AND t.parent_id = f.id AND t.approved = 1 {$where} {$shortcodeSearchFilter} GROUP BY su.topic_id ORDER BY score DESC, su.topic_id DESC {$limit}";

            $results = $this->rs2010forum->db->get_results($query);

            if (!empty($results)) {
                return $results;
            }
        }

        return false;
    }
}
