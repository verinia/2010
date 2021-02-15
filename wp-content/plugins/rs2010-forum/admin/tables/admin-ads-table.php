<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')){
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class Rs2010ForumAdminTableAds extends WP_List_Table {
    var $table_data = array();

    function __construct($table_data) {
        $this->table_data = $table_data;

        parent::__construct(
            array(
                'singular'  => 'ad',
                'plural'    => 'ads',
                'ajax'      => false
            )
        );
    }

    function column_name($item) {
        $columnHTML = '';
        $columnHTML .= '<input type="hidden" id="ad_'.$item['id'].'_name" value="'.esc_html(stripslashes($item['name'])).'">';
        $columnHTML .= '<input type="hidden" id="ad_'.$item['id'].'_code" value="'.esc_html(stripslashes($item['code'])).'">';
        $columnHTML .= '<input type="hidden" id="ad_'.$item['id'].'_active" value="'.$item['active'].'">';
        $columnHTML .= '<input type="hidden" id="ad_'.$item['id'].'_locations" value="'.$item['locations'].'">';

        $columnHTML .= '<span class="make-bold">'.stripslashes($item['name']).'</span>';

        return $columnHTML;
    }

    function column_status($item) {
        if ($item['active'] == '1') {
            return __('Active', 'rs2010-forum');
        } else {
            return __('Inactive', 'rs2010-forum');
        }
    }

    function column_locations($item) {
        global $rs2010forum;

        $first = true;
        $columnHTML = '';
        $locations = explode(',', $item['locations']);

        foreach ($locations as $location) {
            $name = $rs2010forum->ads->get_location_name($location);

            if ($name) {
                if ($first) {
                    $first = false;
                } else {
                    $columnHTML .= ', ';
                }

                $columnHTML .= $name;
            }
        }

        return $columnHTML;
    }

    function column_actions($item) {
        $columnHTML = '';
        $columnHTML .= '<a href="#" class="ad-delete-link link-delete" data-value-id="'.$item['id'].'" data-value-editor-title="'.__('Delete Ad', 'rs2010-forum').'">'.__('Delete', 'rs2010-forum').'</a>';
        $columnHTML .= ' &middot; ';
        $columnHTML .= '<a href="#" class="ad-editor-link" data-value-id="'.$item['id'].'" data-value-editor-title="'.__('Edit Ad', 'rs2010-forum').'">'.__('Edit', 'rs2010-forum').'</a>';

        return $columnHTML;
    }

    function get_columns() {
        $columns = array(
            'name'      => __('Name:', 'rs2010-forum'),
            'status'    => __('Status:', 'rs2010-forum'),
            'locations' => __('Locations:', 'rs2010-forum'),
            'actions'   => __('Actions:', 'rs2010-forum')
        );

        return $columns;
    }

    function prepare_items() {
        $columns = $this->get_columns();
        $this->_column_headers = array($columns);

        $data = array();

        foreach ($this->table_data as $ad) {
            $data[] = (array)$ad;
        }

        $this->items = $data;
    }
}
