<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')){
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class Rs2010_Forum_Admin_Structure_Table extends WP_List_Table {
    var $table_data = array();

    function __construct($table_data) {
        $this->table_data = $table_data;

        parent::__construct(
            array(
                'singular'  => 'forum',
                'plural'    => 'forums',
                'ajax'      => false
            )
        );
    }

    function column_default($item, $column_name) {
        return $item[$column_name];
    }

    function column_name($item) {
        $forumIcon = trim(esc_html(stripslashes($item['icon'])));
        $forumIcon = (empty($forumIcon)) ? 'fas fa-comments' : $forumIcon;

        $columnHTML = '';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_name" value="'.esc_html(stripslashes($item['name'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_description" value="'.esc_html(stripslashes($item['description'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_icon" value="'.$forumIcon.'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_status" value="'.esc_html(stripslashes($item['forum_status'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_order" value="'.esc_html(stripslashes($item['sort'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_count_subforums" value="'.esc_html(stripslashes($item['count_subforums'])).'">';

        if ($item['parent_forum']) {
            $columnHTML .= '<div class="subforum">';
        } else {
            $columnHTML .= '<div class="parentforum">';
        }


        $columnHTML .= '<span class="make-bold">';
        $forum_icon = trim(esc_html(stripslashes($item['icon'])));
        $forum_icon = (empty($forum_icon)) ? 'fas fa-comments' : $forum_icon;
        $columnHTML .= '<span class="forum-icon '.$forum_icon.'"></span>';

        $columnHTML .= stripslashes($item['name']).' <span class="element-id">('.__('ID', 'rs2010-forum').': '.$item['id'].')</span></span>';
        $columnHTML .= '<br>';
        $columnHTML .= '<span class="forum-description">';

        if (empty($item['description'])) {
            $columnHTML .= '<span class="element-id">'.__('No description yet ...', 'rs2010-forum').'</span>';
        } else {
            $columnHTML .= stripslashes($item['description']);
        }

        $columnHTML .= '</span>';
        $columnHTML .= '<div class="clear"></div>';
        $columnHTML .= '</div>';

        return $columnHTML;
    }

    function column_status($item) {
        switch ($item['forum_status']) {
            case 'normal':
                return __('Normal', 'rs2010-forum');
            break;
            case 'closed':
                return __('Closed', 'rs2010-forum');
            break;
            case 'approval':
                return __('Approval', 'rs2010-forum');
            break;
        }
    }

    function column_actions($item) {
        $actionHTML = '';
        $actionHTML .= '<a href="#" class="forum-delete-link link-delete" data-value-id="'.$item['id'].'" data-value-category="'.$item['parent_id'].'" data-value-editor-title="'.__('Delete Forum', 'rs2010-forum').'">';
        $actionHTML .= __('Delete Forum', 'rs2010-forum');
        $actionHTML .= '</a>';
        $actionHTML .= ' &middot; ';
        $actionHTML .= '<a href="#" class="forum-editor-link" data-value-id="'.$item['id'].'" data-value-category="'.$item['parent_id'].'" data-value-parent-forum="'.$item['parent_forum'].'" data-value-editor-title="'.__('Edit Forum', 'rs2010-forum').'">';
        $actionHTML .= __('Edit Forum', 'rs2010-forum');
        $actionHTML .= '</a>';

        if (!$item['parent_forum']) {
            $actionHTML .= ' &middot; ';
            $actionHTML .= '<a href="#" class="forum-editor-link" data-value-id="new" data-value-category="'.$item['parent_id'].'" data-value-parent-forum="'.$item['id'].'" data-value-editor-title="'.__('Add Sub-Forum', 'rs2010-forum').'">';
            $actionHTML .= __('Add Sub-Forum', 'rs2010-forum');
            $actionHTML .= '</a>';
        }

        return $actionHTML;
    }

    function get_columns() {
        $columns = array(
            'name'      => __('Name:', 'rs2010-forum'),
            'status'    => __('Status:', 'rs2010-forum'),
            'sort'      => __('Order:', 'rs2010-forum'),
            'actions'   => __('Actions:', 'rs2010-forum')
        );

        return $columns;
    }

    function prepare_items() {
        global $rs2010forum;

        $columns = $this->get_columns();
        $this->_column_headers = array($columns);

        $data = array();

        foreach ($this->table_data as $forum) {
            $data[] = $forum;

            if ($forum['count_subforums'] > 0) {
                $subforums = $rs2010forum->get_forums($forum['parent_id'], $forum['id'], ARRAY_A);

                if (!empty($subforums)) {
                    foreach ($subforums as $subforum) {
                        $data[] = $subforum;
                    }
                }
            }
        }

        $this->items = $data;
    }
}
