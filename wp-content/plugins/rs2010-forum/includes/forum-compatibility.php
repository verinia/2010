<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumCompatibility {
    private $rs2010forum = null;

    public function __construct($object) {
        $this->rs2010forum = $object;

        $this->compatibility_autoptimize();
        $this->compatibility_yoastseo();
        $this->compatibility_rankmathseo();
        $this->compatibility_toolset();
        $this->compatibility_permalinkmanager();
        $this->compatibility_allinoneseopack();
    }

    // AUTOPTIMIZE
    function compatibility_autoptimize() {
        add_filter('autoptimize_filter_js_exclude', array($this, 'comp_autoptimize_filter_js_exclude'), 10 , 1);
    }

    function comp_autoptimize_filter_js_exclude($exclude) {
        return $exclude.', wp-includes/js/tinymce';
    }

    // YOASTSEO
    function compatibility_yoastseo() {
        add_action('template_redirect', array($this, 'comp_yoastseo_template_redirect'));
        add_filter('rs2010forum_title_separator', array($this, 'comp_yoastseo_rs2010forum_title_separator'));
    }

    function comp_yoastseo_template_redirect() {
        if ($this->rs2010forum->executePlugin) {
            // Old API.
            global $wpseo_front;

            if ($wpseo_front) {
                remove_action('wp_head', array($wpseo_front, 'head'), 1);
                return;
            }

            // New API.
            if (class_exists('WPSEO_Frontend')) {
                $wpseo_front = WPSEO_Frontend::get_instance();
                remove_action('wp_head', array($wpseo_front, 'head'), 1);
            }

            // Another new API.
            if (class_exists('Yoast\WP\SEO\Integrations\Front_End_Integration')) {
                $wpseo_front = YoastSEO()->classes->get(Yoast\WP\SEO\Integrations\Front_End_Integration::class);
                remove_action('wpseo_head', array($wpseo_front, 'present_head'), -9999);
            }
        }
    }

    function comp_yoastseo_rs2010forum_title_separator($title_separator) {
        if ($this->rs2010forum->executePlugin) {
            if (class_exists('\WPSEO_Utils')) {
                $title_separator = \WPSEO_Utils::get_title_separator();
            }
        }

        return $title_separator;
    }

    // RANK MATH SEO
    function compatibility_rankmathseo() {
        add_action('template_redirect', array($this, 'comp_rankmathseo_template_redirect'));
        add_filter('rs2010forum_title_separator', array($this, 'comp_rankmathseo_rs2010forum_title_separator'));
    }

    function comp_rankmathseo_template_redirect() {
        if ($this->rs2010forum->executePlugin) {
            remove_all_actions('rank_math/head');
            add_filter('rank_math/frontend/remove_credit_notice', '__return_true');
            add_action('wp_head', '_wp_render_title_tag', 1);
        }
    }

    function comp_rankmathseo_rs2010forum_title_separator($title_separator) {
        if ($this->rs2010forum->executePlugin) {
            if (class_exists('\RankMath\Helper')) {
                $title_separator = \RankMath\Helper::get_settings('titles.title_separator');
            }
        }

        return $title_separator;
    }

    // TOOLSET
    function compatibility_toolset() {
        add_action('rs2010forum_execution_check', array($this, 'comp_toolset_rs2010forum_execution_check'));
    }

    function comp_toolset_rs2010forum_execution_check() {
        global $post;

        // Ensure that Toolset is active.
        if (!defined('WPV_VERSION')) {
            return;
        }

        // Ensure that the current post is a WP_Post.
        if (!is_a($post, 'WP_Post')) {
            return;
        }

        // Ensure that a content template is assigned to the current post.
        if (is_wpv_content_template_assigned($post->ID) == true || get_post_meta($post->ID, '_views_template', true) > 0) {
            // Get ID of the content template assigned to the post.
            $ct_id = get_post_meta($post->ID, '_views_template', true);

            // Get content of content template.
            $ct_content = get_post_field('post_content', $ct_id);

            // Check if the content template has the forum-shortcode.
            if (has_shortcode($ct_content, 'forum') || has_shortcode($ct_content, 'Forum')) {
                $this->rs2010forum->executePlugin = true;
                $this->rs2010forum->options['location'] = $post->ID;
            }
        }
    }

    // PERMALINK MANAGER
    function compatibility_permalinkmanager() {
        add_action('rs2010forum_prepare', array($this, 'comp_permalinkmanager_rs2010forum_prepare'));
    }

    function comp_permalinkmanager_rs2010forum_prepare() {
        global $wp_query;
        $wp_query->query_vars['do_not_redirect'] = 1;
    }

    // ALL IN ONE SEO PACK
    function compatibility_allinoneseopack() {
        // Check version
        if (defined('AIOSEO_VERSION')) {
            // Version >= 4.0.0
            add_filter('aioseo_disable', array($this, 'comp_allinoneseopack_aiosp_disable'), 10);
        } else if (defined('AIOSEOP_VERSION')) {
            // Older version
            add_filter('aiosp_disable', array($this, 'comp_allinoneseopack_aiosp_disable'), 10);
        }
    }

    function comp_allinoneseopack_aiosp_disable($disabled) {
        if ($this->rs2010forum->executePlugin) {
            $disabled = true;
        }

        return $disabled;
    }
}
