<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumContent {
    private $rs2010forum = null;
    private static $taxonomy_name = 'rs2010forum-category';
    private $action = false;
    private $data_subject;
    private $data_content;

    public function __construct($object) {
		$this->rs2010forum = $object;

        add_action('init', array($this, 'initialize'));
    }

    public function initialize() {
        self::initialize_taxonomy();
    }

    public static function initialize_taxonomy() {
        // Register the taxonomies.
        register_taxonomy(
			self::$taxonomy_name,
			null,
			array(
				'public'        => false,
				'rewrite'       => false
			)
		);
    }

    public function do_insertion() {
        if ($this->prepare_execution()) {
            $this->insert_data();
        }
    }

    private function get_action() {
        // If no action is set, try to determine one.
        if (!$this->action && ($_POST['submit_action'] === 'add_topic' || $_POST['submit_action'] === 'add_post' || $_POST['submit_action'] === 'edit_post')) {
            $this->action = $_POST['submit_action'];
        }

        return $this->action;
    }

    private function set_data() {
        if (isset($_POST['subject'])) {
            $this->data_subject = apply_filters('rs2010forum_filter_subject_before_insert', trim($_POST['subject']));
        }

        if (isset($_POST['message'])) {
            $this->data_content = apply_filters('rs2010forum_filter_content_before_insert', trim($_POST['message']));
        }
    }

    private function prepare_execution() {
        // Cancel if there is already an error.
        if (!empty($this->rs2010forum->error)) {
            return false;
        }

        // Cancel if the current user is not logged-in and guest postings are disabled.
        if (!is_user_logged_in() && !$this->rs2010forum->options['allow_guest_postings']) {
            $this->rs2010forum->error = __('You are not allowed to do this.', 'rs2010-forum');
            return false;
        }

        // Cancel if the current user is banned.
        if ($this->rs2010forum->permissions->isBanned('current')) {
            $this->rs2010forum->error = __('You are banned!', 'rs2010-forum');
            return false;
        }

        // Cancel if parents are not set. Prevents the creation of hidden content caused by spammers.
        if (!$this->rs2010forum->parents_set) {
            $this->rs2010forum->error = __('You are not allowed to do this.', 'rs2010-forum');
            return false;
        }

        // Cancel when no action could be determined.
        if (!$this->get_action()) {
            $this->rs2010forum->error = __('You are not allowed to do this.', 'rs2010-forum');
            return false;
        }

        // Set the data.
        $this->set_data();

        // Cancel if the current user is not allowed to edit that post.
        if ($this->get_action() === 'edit_post') {
            $user_id = $this->rs2010forum->permissions->currentUserID;

            if (!$this->rs2010forum->permissions->can_edit_post($user_id, $this->rs2010forum->current_post)) {
                $this->rs2010forum->error = __('You are not allowed to do this.', 'rs2010-forum');
                return false;
            }
        }

        // Cancel if the forum is closed for the current user.
        if ($this->get_action() === 'add_topic') {
            if (!$this->rs2010forum->forumIsOpen()) {
                $this->rs2010forum->error = __('You are not allowed to do this.', 'rs2010-forum');
                return false;
            }
        }

        // Cancel if the topic is closed and the user is not a moderator.
        if ($this->get_action() === 'add_post') {
            if ($this->rs2010forum->is_topic_closed($this->rs2010forum->current_topic) && !$this->rs2010forum->permissions->isModerator('current')) {
                $this->rs2010forum->error = __('You are not allowed to do this.', 'rs2010-forum');
                return false;
            }
        }

        // Cancel if subject is empty.
        if (($this->get_action() === 'add_topic' || ($this->get_action() === 'edit_post' && $this->rs2010forum->is_first_post($this->rs2010forum->current_post))) && empty($this->data_subject)) {
            $this->rs2010forum->add_notice(__('You must enter a subject.', 'rs2010-forum'));
            return false;
        }

        // Ensure we have a subject.
        if (empty($this->data_subject)) {
            $this->data_subject = $this->get_topic_title($this->rs2010forum->current_topic);
        }

        // Cancel if content is empty.
        if (empty($this->data_content)) {
            $this->rs2010forum->add_notice(__('You must enter a message.', 'rs2010-forum'));
            return false;
        }

        // Cancel when the file extension of uploads are not allowed.
        if (!$this->rs2010forum->uploads->check_uploads_extension()) {
            $this->rs2010forum->add_notice(__('You are not allowed to upload files with that file extension.', 'rs2010-forum'));
            return false;
        }

        // Cancel when the file size of uploads is too big.
        if (!$this->rs2010forum->uploads->check_uploads_size()) {
            $this->rs2010forum->add_notice(__('You are not allowed to upload files with that file size.', 'rs2010-forum'));
            return false;
        }

        // Do custom insert validation checks.
        $custom_check = apply_filters('rs2010forum_filter_insert_custom_validation', true);
        if (!$custom_check) {
            return false;
        }

        return true;
    }

    private function insert_data() {
        $link = '';
        $redirect = '';
        $upload_list = $this->rs2010forum->uploads->get_upload_list();
        $author_id = $this->rs2010forum->permissions->currentUserID;

        if ($this->get_action() === 'add_topic') {
            // Create the topic.
            $inserted_ids = $this->insert_topic($this->rs2010forum->current_forum, $this->data_subject, $this->data_content, $author_id, $upload_list);

            // Assign the inserted IDs.
            $this->rs2010forum->current_topic = $inserted_ids->topic_id;
            $this->rs2010forum->current_post = $inserted_ids->post_id;

            // Upload files.
            $this->rs2010forum->uploads->upload_files($this->rs2010forum->current_post, $upload_list);

            // Create link.
            $link = html_entity_decode($this->rs2010forum->rewrite->get_post_link($this->rs2010forum->current_post, $this->rs2010forum->current_topic));

            // Special instructions for un/approved topics.
            if ($this->rs2010forum->approval->is_topic_approved($this->rs2010forum->current_topic)) {
                // Send notifications about mentionings.
                $receivers = $this->rs2010forum->mentioning->mention_users($this->rs2010forum->current_post);

                // Send notifications about new topic.
                $this->rs2010forum->notifications->notify_about_new_topic($this->rs2010forum->current_topic, $receivers);

                // Set redirect.
                $redirect = $link;
            } else {
                // Notify siteowner about new unapproved topic.
                $this->rs2010forum->approval->notify_about_new_unapproved_topic($this->rs2010forum->current_topic);

                // Set redirect.
                $redirect = $this->rs2010forum->rewrite->get_link('home', false, array('new_unapproved_topic' => 1));
            }
        } else if ($this->get_action() === 'add_post') {
            // Create the post.
            $this->rs2010forum->current_post = $this->insert_post($this->rs2010forum->current_topic, $this->rs2010forum->current_forum, $this->data_content, $author_id, $upload_list);

            // Upload files.
            $this->rs2010forum->uploads->upload_files($this->rs2010forum->current_post, $upload_list);

            // Send notifications about mentionings.
            $receivers = $this->rs2010forum->mentioning->mention_users($this->rs2010forum->current_post);

            // Send notifications about new post.
            $this->rs2010forum->notifications->notify_about_new_post($this->rs2010forum->current_post, $receivers);

            // Create link.
            $link = html_entity_decode($this->rs2010forum->rewrite->get_post_link($this->rs2010forum->current_post, $this->rs2010forum->current_topic));

            // Set redirect.
            $redirect = $link;
        } else if ($this->get_action() === 'edit_post') {
            $date = $this->rs2010forum->current_time();
            $upload_list = $this->rs2010forum->uploads->upload_files($this->rs2010forum->current_post, $upload_list);
            $this->rs2010forum->db->update($this->rs2010forum->tables->posts, array('text' => $this->data_content, 'uploads' => maybe_serialize($upload_list), 'date_edit' => $date, 'author_edit' => $this->rs2010forum->permissions->currentUserID), array('id' => $this->rs2010forum->current_post), array('%s', '%s', '%s', '%d'), array('%d'));

            if ($this->rs2010forum->is_first_post($this->rs2010forum->current_post) && !empty($this->data_subject)) {
                $this->rs2010forum->db->update($this->rs2010forum->tables->topics, array('name' => $this->data_subject), array('id' => $this->rs2010forum->current_topic), array('%s'), array('%d'));
            }

            // Create link.
            $link = html_entity_decode($this->rs2010forum->rewrite->get_post_link($this->rs2010forum->current_post, $this->rs2010forum->current_topic));

            // Set redirect.
            $redirect = $link;
        }

        $this->rs2010forum->notifications->update_topic_subscription_status($this->rs2010forum->current_topic);

        do_action('rs2010forum_after_'.$this->get_action().'_submit', $this->rs2010forum->current_post, $this->rs2010forum->current_topic, $this->data_subject, $this->data_content, $link, $author_id);

        wp_redirect($redirect);
        exit;
    }

    //======================================================================
    // FUNCTIONS FOR INSERTING CONTENT.
    //======================================================================

    // Inserts a new forum.
    public function insert_forum($category_id, $name, $description, $parent_forum, $icon, $order, $status = 'normal') {
        // Get a slug for the new forum.
        $forum_slug = $this->rs2010forum->rewrite->create_unique_slug($name, $this->rs2010forum->tables->forums, 'forum');

        // Insert the forum.
        $this->rs2010forum->db->insert(
            $this->rs2010forum->tables->forums,
            array('name' => $name, 'parent_id' => $category_id, 'parent_forum' => $parent_forum, 'description' => $description, 'icon' => $icon, 'sort' => $order, 'forum_status' => $status, 'slug' => $forum_slug),
            array('%s', '%d', '%d', '%s', '%s', '%d', '%s', '%s')
        );

        // Return the ID of the inserted forum.
        return $this->rs2010forum->db->insert_id;
    }

    // Inserts a new topic.
    public function insert_topic($forum_id, $name, $text, $author_id = false, $uploads = array()) {
        // Set the author ID.
        if (!$author_id) {
            $author_id = $this->rs2010forum->permissions->currentUserID;
        }

        // Get a slug for the new topic.
        $topic_slug = $this->rs2010forum->rewrite->create_unique_slug($name, $this->rs2010forum->tables->topics, 'topic');

        // Set the approval-status for the topic.
        $approved = 1;

        if ($this->rs2010forum->approval->forum_requires_approval($forum_id, $author_id)) {
            $approved = 0;
        }

        // Insert the topic.
        $this->rs2010forum->db->insert(
            $this->rs2010forum->tables->topics,
            array('name' => $name, 'parent_id' => $forum_id, 'slug' => $topic_slug, 'approved' => $approved, 'author_id' => $author_id),
            array('%s', '%d', '%s', '%d', '%d')
        );

        // Save the ID of the new topic.
        $inserted_ids = new stdClass;
        $inserted_ids->topic_id = $this->rs2010forum->db->insert_id;

        // Now create a post inside this topic and save its ID as well.
        $inserted_ids->post_id = $this->insert_post($inserted_ids->topic_id, $forum_id, $text, $author_id, $uploads);

        // Return the IDs of the inserted content.
        return $inserted_ids;
    }

    // Inserts a new post.
    public function insert_post($topic_id, $forum_id, $text, $author_id = false, $uploads = array()) {
        // Set the author ID.
        if (!$author_id) {
            $author_id = $this->rs2010forum->permissions->currentUserID;
        }

        // Get the current time.
        $date = $this->rs2010forum->current_time();

        // Insert the post.
        $this->rs2010forum->db->insert($this->rs2010forum->tables->posts, array('text' => $text, 'parent_id' => $topic_id, 'forum_id' => $forum_id, 'date' => $date, 'author_id' => $author_id, 'uploads' => maybe_serialize($uploads)), array('%s', '%d', '%d', '%s', '%d', '%s'));

        // Return the ID of the inserted post.
        return $this->rs2010forum->db->insert_id;
    }

    //======================================================================
    // FUNCTIONS TO CHECK CONTENT-RELATED THINGS.
    //======================================================================

    // Checks if a category exists.
    public function category_exists($category_id) {
        if ($category_id) {
            $check = get_term($category_id, 'rs2010forum-category');

            if ($check) {
                return true;
            }
        }

        return false;
    }

    // Checks if a forum exists.
    public function forum_exists($forum_id) {
        if ($forum_id) {
            $check = $this->rs2010forum->db->get_var($this->rs2010forum->db->prepare("SELECT id FROM {$this->rs2010forum->tables->forums} WHERE id = %d", $forum_id));

            if ($check) {
                return true;
            }
        }

        return false;
    }

    // Checks if a topic exists.
    public function topic_exists($topic_id) {
        if ($topic_id) {
            $check = $this->rs2010forum->db->get_var($this->rs2010forum->db->prepare("SELECT id FROM {$this->rs2010forum->tables->topics} WHERE id = %d", $topic_id));

            if ($check) {
                return true;
            }
        }

        return false;
    }

    // Checks if a post exists.
    public function post_exists($post_id) {
        if ($post_id) {
            $check = $this->rs2010forum->db->get_var($this->rs2010forum->db->prepare("SELECT id FROM {$this->rs2010forum->tables->posts} WHERE id = %d", $post_id));

            if ($check) {
                return true;
            }
        }

        return false;
    }

    //======================================================================
    // FUNCTIONS FOR GETTING CONTENT.
    //======================================================================

    public function get_categories($enable_filtering = true) {
        $ids_categories_excluded = array();
        $ids_categories_included = array();
        $meta_query_filter = array();

        if ($enable_filtering) {
            $ids_categories_excluded = apply_filters('rs2010forum_filter_get_categories', array());
            $ids_categories_included = $this->rs2010forum->shortcode->includeCategories;
            $meta_query_filter = $this->get_categories_filter();
        }

        $categories_list = get_terms('rs2010forum-category', array(
            'hide_empty'    => false,
            'exclude'       => $ids_categories_excluded,
            'include'       => $ids_categories_included,
            'meta_query'    => $meta_query_filter
        ));

        // Filter categories by usergroups.
        if ($enable_filtering) {
            $categories_list = Rs2010ForumUserGroups::filterCategories($categories_list);
        }

        // Get information about ordering.
        foreach ($categories_list as $category) {
            $category->order = get_term_meta($category->term_id, 'order', true);
        }

        // Sort the categories based on ordering information.
        usort($categories_list, array($this, 'get_categories_compare'));

        return $categories_list;
    }

    public function get_categories_ids() {
        $categories = $this->get_categories(true);
        $categories_ids = array();

        foreach ($categories as $category) {
            $categories_ids[] = $category->term_id;
        }

        return $categories_ids;
    }

    public function get_categories_filter() {
        $meta_query_filter = array('relation' => 'AND');

        if (!$this->rs2010forum->permissions->isModerator('current')) {
            $meta_query_filter[] = array(
                'key'       => 'category_access',
                'value'     => 'moderator',
                'compare'   => 'NOT LIKE'
            );
        }

        if (!is_user_logged_in()) {
            $meta_query_filter[] = array(
                'key'       => 'category_access',
                'value'     => 'loggedin',
                'compare'   => 'NOT LIKE'
            );
        }

        if (sizeof($meta_query_filter) > 1) {
            return $meta_query_filter;
        } else {
            return array();
        }
    }

    public function get_categories_compare($a, $b) {
        return ($a->order < $b->order) ? -1 : (($a->order > $b->order) ? 1 : 0);
    }

    public function get_topic($topic_id) {
        return $this->rs2010forum->db->get_row("SELECT * FROM {$this->rs2010forum->tables->topics} WHERE id = {$topic_id};");
    }

    public function get_post($post_id) {
        return $this->rs2010forum->db->get_row("SELECT p1.*, (SELECT COUNT(*) FROM {$this->rs2010forum->tables->posts} AS p2 WHERE p2.author_id = p1.author_id) AS author_posts FROM {$this->rs2010forum->tables->posts} AS p1 WHERE p1.id = {$post_id};");
    }

    public function get_first_post($topic_id) {
        return $this->rs2010forum->db->get_row("SELECT * FROM {$this->rs2010forum->tables->posts} WHERE parent_id = {$topic_id} ORDER BY id ASC LIMIT 1;");
    }

    public function get_posts_by_user($user_id) {
        return $this->rs2010forum->db->get_results($this->rs2010forum->db->prepare("SELECT p.id, p.text, p.date, p.parent_id, t.name FROM {$this->rs2010forum->tables->posts} AS p, {$this->rs2010forum->tables->topics} AS t WHERE p.author_id = %d AND p.parent_id = t.id AND t.approved = 1 ORDER BY p.id DESC;", $user_id));
    }

    public function get_first_unread_post($topic_id) {
        if (isset($this->rs2010forum->unread->excluded_items[$topic_id])) {
            // If we have opened this topic already, we take the post with the next-higher ID.
            return $this->rs2010forum->db->get_row("SELECT p.* FROM {$this->rs2010forum->tables->posts} AS p WHERE p.parent_id = {$topic_id} AND p.id > {$this->rs2010forum->unread->excluded_items[$topic_id]} ORDER BY p.id ASC LIMIT 1;");
        } else {
            // If we havent opened it yet, we take the first post since last clearing-date.
            return $this->rs2010forum->db->get_row("SELECT p.* FROM {$this->rs2010forum->tables->posts} AS p WHERE p.parent_id = {$topic_id} AND p.date > '{$this->rs2010forum->unread->get_last_visit()}' ORDER BY p.id ASC LIMIT 1;");
        }
    }

    public function get_forum($forum_id) {
        return $this->rs2010forum->db->get_row("SELECT * FROM {$this->rs2010forum->tables->forums} WHERE id = {$forum_id};");
    }

    public function get_topic_title($topic_id) {
        return $this->rs2010forum->db->get_var("SELECT name FROM {$this->rs2010forum->tables->topics} WHERE id = {$topic_id};");
    }

    public function get_sticky_topics($forum_id) {
        // Get accessible categories first.
        $ids_categories = $this->get_categories_ids();

        // Cancel if there are no accessible categories.
        if (empty($ids_categories)) {
            return false;
        }

        $ids_categories = implode(',', $ids_categories);

        // Build query-part for ordering.
        $order = "(SELECT MAX(id) FROM {$this->rs2010forum->tables->posts} WHERE parent_id = t.id) DESC";
        $order = apply_filters('rs2010forum_filter_get_threads_order', $order);

        // Build additional sub-queries.
        $query_answers = "SELECT (COUNT(*) - 1) FROM {$this->rs2010forum->tables->posts} WHERE parent_id = t.id";

        // Build final query and get results.
        $query = "SELECT t.id, t.name, t.views, t.sticky, t.closed, t.author_id, ({$query_answers}) AS answers FROM {$this->rs2010forum->tables->topics} AS t, {$this->rs2010forum->tables->forums} AS f WHERE t.parent_id = f.id AND f.parent_id IN ({$ids_categories}) AND t.approved = 1 AND ((t.sticky = 2) OR (t.parent_id = %d AND t.sticky = 1)) ORDER BY ".$order.";";
        $results = $this->rs2010forum->db->get_results($this->rs2010forum->db->prepare($query, $forum_id));
        $results = apply_filters('rs2010forum_filter_get_threads', $results);

        return $results;
    }

    // Returns all subforums.
    var $get_all_subforums_cache = false;
    public function get_all_subforums() {
        if ($this->get_all_subforums_cache === false) {
            $query = "SELECT * FROM {$this->rs2010forum->tables->forums} WHERE parent_forum != 0;";

            $this->get_all_subforums_cache = $this->rs2010forum->db->get_results($query);
        }

        return $this->get_all_subforums_cache;
    }

    function get_topics($forum_id) {
        // Build query-part for pagination.
        $limit_end = $this->rs2010forum->options['topics_per_page'];
        $limit_start = $this->rs2010forum->current_page * $limit_end;
        $limit = $this->rs2010forum->db->prepare("LIMIT %d, %d", $limit_start, $limit_end);

        // Build query-part for ordering.
        $order = "(SELECT MAX(id) FROM {$this->rs2010forum->tables->posts} WHERE parent_id = t.id) DESC";
        $order = apply_filters('rs2010forum_filter_get_threads_order', $order);

        // Build additional sub-queries.
        $query_answers = "SELECT (COUNT(*) - 1) FROM {$this->rs2010forum->tables->posts} WHERE parent_id = t.id";

        // Build final query and get results.
        $query = "SELECT t.id, t.name, t.views, t.sticky, t.closed, t.author_id, ({$query_answers}) AS answers FROM {$this->rs2010forum->tables->topics} AS t WHERE t.parent_id = %d AND t.sticky = 0 AND t.approved = 1 ORDER BY {$order} {$limit};";
        $results = $this->rs2010forum->db->get_results($this->rs2010forum->db->prepare($query, $forum_id));
        $results = apply_filters('rs2010forum_filter_get_threads', $results);

        return $results;
    }

    public function count_posts_by_user($user_id) {
        return $this->rs2010forum->db->get_var($this->rs2010forum->db->prepare("SELECT COUNT(id) FROM {$this->rs2010forum->tables->posts} WHERE author_id = %d;", $user_id));
    }
}
