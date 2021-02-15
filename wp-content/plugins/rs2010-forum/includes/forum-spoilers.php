<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumSpoilers {
    private $rs2010forum = null;

    public function __construct($object) {
		$this->rs2010forum = $object;

        add_action('init', array($this, 'initialize'));
    }

    public function initialize() {
        if ($this->rs2010forum->options['enable_spoilers']) {
            add_shortcode('spoiler', array($this, 'render_spoiler'));
        }
    }

    public function render_spoiler($atts = false, $content = false) {
        $output = '';

        $output .= '<div class="spoiler">';
    	$output .= '<div class="spoiler-head closed"><span>'.__('Spoiler', 'rs2010-forum').'</span></div>';
    	$output .= '<div class="spoiler-body">';

        // Hide spoiler if the current user is not logged-in (based on the settings).
        if ($this->rs2010forum->options['hide_spoilers_from_guests'] && !is_user_logged_in()) {
            $output .= __('Sorry, only logged-in users can see spoilers.', 'rs2010-forum');
        } else {
            $output .= $content;
        }

        $output .= '</div>';
    	$output .= '</div>';

        return $output;
    }
}
