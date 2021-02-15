<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')){
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class Rs2010_Forum_Admin_UserGroups_Table extends WP_List_Table {
    var $table_data = array();

    function __construct($table_data) {
        $this->table_data = $table_data;

        parent::__construct(
            array(
                'singular'  => 'usergroup',
                'plural'    => 'usergroups',
                'ajax'      => false
            )
        );
    }

    function column_default($item, $column_name) {
        return $item[$column_name];
    }

    function column_name($item) {
        $users_i18n = number_format_i18n($item['users']);

        $columnHTML = '';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_name" value="'.esc_html(stripslashes($item['name'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_color" value="'.esc_html(stripslashes($item['color'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_visibility" value="'.esc_html(stripslashes($item['visibility'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_auto_add" value="'.esc_html(stripslashes($item['auto_add'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_icon" value="'.esc_html(stripslashes($item['icon'])).'">';


        $columnHTML .= '<div class="usergroup-color" style="background-color: '.$item['color'].';"></div>';
        $columnHTML .= '<a class="usergroup-name" href="'.admin_url('users.php?forum-user-group='.$item['term_id']).'" style="color: '.$item['color'].';">';

        if ($item['icon']) {
            $columnHTML .= '<i class="'.$item['icon'].'"></i>';
        }

        $columnHTML .= stripslashes($item['name']);
        $columnHTML .= '&nbsp;';
        $columnHTML .= '<span class="element-id">';
        $columnHTML .= '('.__('ID', 'rs2010-forum').': '.$item['term_id'].')';
        $columnHTML .= '</span>';
        $columnHTML .= '</a>';
        $columnHTML .= '<br>';
        $columnHTML .= '<span class="forum-description">';
        $columnHTML .= sprintf(_n('%s User', '%s Users', $item['users'], 'rs2010-forum'), $users_i18n);
        $columnHTML .= '</span>';

        return $columnHTML;
    }

    function column_visibility($item) {
        if ($item['visibility'] == 'hidden') {
            return __('Hidden', 'rs2010-forum');
        } else {
            return __('Visible', 'rs2010-forum');
        }
    }

    function column_auto_add($item) {
        if ($item['auto_add'] == 'yes') {
            return __('Yes', 'rs2010-forum');
        } else {
            return __('No', 'rs2010-forum');
        }
    }

    function column_actions($item) {
        $columnHTML = '';
        $columnHTML .= '<a href="#" class="usergroup-delete-link link-delete" data-value-id="'.$item['term_id'].'" data-value-editor-title="'.__('Delete Usergroup', 'rs2010-forum').'">'.__('Delete', 'rs2010-forum').'</a>';
        $columnHTML .= ' &middot; ';
        $columnHTML .= '<a href="#" class="usergroup-editor-link" data-value-id="'.$item['term_id'].'" data-value-category="'.$item['parent'].'" data-value-editor-title="'.__('Edit Usergroup', 'rs2010-forum').'">'.__('Edit', 'rs2010-forum').'</a>';

        return $columnHTML;
    }

    function get_columns() {
        $columns = array(
            'name'          => __('Name:', 'rs2010-forum'),
            'visibility'    => __('Visibility:', 'rs2010-forum'),
            'auto_add'      => __('Automatically Add:', 'rs2010-forum'),
            'actions'       => __('Actions:', 'rs2010-forum')
        );

        return $columns;
    }

    function prepare_items() {
        $columns = $this->get_columns();
        $this->_column_headers = array($columns);

        $data = array();

        foreach ($this->table_data as $usergroup) {
            $usergroup = (array)$usergroup; // Convert object to array.
            $usergroup['color'] = Rs2010ForumUserGroups::getUserGroupColor($usergroup['term_id']);
            $usergroup['visibility'] = Rs2010ForumUserGroups::get_usergroup_visibility($usergroup['term_id']);
            $usergroup['auto_add'] = Rs2010ForumUserGroups::get_usergroup_auto_add($usergroup['term_id']);
            $usergroup['users'] = Rs2010ForumUserGroups::countUsersOfUserGroup($usergroup['term_id']);
            $usergroup['icon'] = Rs2010ForumUserGroups::get_usergroup_icon($usergroup['term_id']);
            $data[] = $usergroup;
        }

        $this->items = $data;
    }
}
