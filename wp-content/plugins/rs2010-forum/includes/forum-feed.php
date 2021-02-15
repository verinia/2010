<?php

if (!defined('ABSPATH')) exit;

class Rs2010ForumFeed {
    private $rs2010forum = null;

    public function __construct($object) {
        $this->rs2010forum = $object;

        add_action('rs2010forum_wp_head', array($this, 'add_feed_link'));
        add_action('rs2010forum_bottom_navigation', array($this, 'show_feed_navigation'), 20, 1);
        add_action('rs2010forum_prepare_topic', array($this, 'render_feed'));
        add_action('rs2010forum_prepare_forum', array($this, 'render_feed'));
    }

    function add_feed_link() {
        if ($this->rs2010forum->options['enable_rss']) {
            switch($this->rs2010forum->current_view) {
                case 'topic':
                    $title = $this->rs2010forum->current_topic_name.' &#8211; '.$this->rs2010forum->options['forum_title'];
                    $link = $this->rs2010forum->rewrite->get_link('topic', $this->rs2010forum->current_topic, array('showfeed' => 'rss2'));
                    echo '<link rel="alternate" type="application/rss+xml" title="'.$title.'" href="'.$link.'" />'.PHP_EOL;
                break;
                case 'forum':
                    $title = $this->rs2010forum->current_forum_name.' &#8211; '.$this->rs2010forum->options['forum_title'];
                    $link = $this->rs2010forum->rewrite->get_link('forum', $this->rs2010forum->current_forum, array('showfeed' => 'rss2'));
                    echo '<link rel="alternate" type="application/rss+xml" title="'.$title.'" href="'.$link.'" />'.PHP_EOL;
                break;
            }
        }
    }

    function show_feed_navigation($current_view) {
        if ($this->rs2010forum->options['enable_rss']) {
            switch($current_view) {
                case 'topic':
                    $link = $this->rs2010forum->rewrite->get_link('topic', $this->rs2010forum->current_topic, array('showfeed' => 'rss2'));
                    echo '<span class="fas fa-rss"></span>';
                    echo '<a href="'.$link.'" target="_blank">'.__('RSS Feed', 'rs2010-forum').'</a>';
                break;
                case 'forum':
                    $link = $this->rs2010forum->rewrite->get_link('forum', $this->rs2010forum->current_forum, array('showfeed' => 'rss2'));
                    echo '<span class="fas fa-rss"></span>';
                    echo '<a href="'.$link.'" target="_blank">'.__('RSS Feed', 'rs2010-forum').'</a>';
                break;
            }
        }
    }

    function render_feed() {
        if ($this->rs2010forum->options['enable_rss'] && !empty($_GET['showfeed'])) {
            // Abort feed creation when an error occured.
            if ($this->rs2010forum->error !== false) {
                return;
            }

            header('Content-Type: '.feed_content_type('rss2').'; charset='.get_option('blog_charset'), true);

            echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>'.PHP_EOL;
            echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;
            echo '<channel>'.PHP_EOL;

            if ($this->rs2010forum->current_view === 'forum') {
                echo '<title>'.esc_html(stripslashes($this->rs2010forum->current_forum_name)).'</title>'.PHP_EOL;
                echo '<link>'.$this->rs2010forum->rewrite->get_link('forum', $this->rs2010forum->current_forum).'</link>'.PHP_EOL;
            } else if ($this->rs2010forum->current_view === 'topic') {
                echo '<title>'.esc_html(stripslashes($this->rs2010forum->current_topic_name)).'</title>'.PHP_EOL;
                echo '<link>'.$this->rs2010forum->rewrite->get_link('topic', $this->rs2010forum->current_topic).'</link>'.PHP_EOL;
            }

            echo '<description>'.$this->rs2010forum->current_description.'</description>'.PHP_EOL;
            echo '<language>'.get_bloginfo('language').'</language>'.PHP_EOL;
            echo '<lastBuildDate>'.mysql2date('D, d M Y H:i:s +0000', date('Y-m-d H:i:s'), false).'</lastBuildDate>'.PHP_EOL;
            echo '<generator>Rs2010 Forum</generator>'.PHP_EOL;
            echo '<ttl>60</ttl>'.PHP_EOL;
            echo '<atom:link href="'.$this->rs2010forum->rewrite->get_link('current').'" rel="self" type="application/rss+xml" />'.PHP_EOL;

            $feed_data = false;

            if ($this->rs2010forum->current_view === 'forum') {
                $query_post_content = "SELECT p.text FROM {$this->rs2010forum->tables->posts} AS p WHERE p.parent_id = t.id ORDER BY p.id ASC LIMIT 1";
                $query_post_date = "SELECT p.date FROM {$this->rs2010forum->tables->posts} AS p WHERE p.parent_id = t.id ORDER BY p.id ASC LIMIT 1";

                $feed_data = $this->rs2010forum->db->get_results("SELECT t.id, t.name, ({$query_post_content}) AS text, ({$query_post_date}) AS date, t.author_id FROM {$this->rs2010forum->tables->topics} AS t WHERE t.parent_id = {$this->rs2010forum->current_forum} AND t.approved = 1 ORDER BY t.id DESC LIMIT 0, 50;");
            } else if ($this->rs2010forum->current_view === 'topic') {
                $feed_data = $this->rs2010forum->db->get_results("SELECT p.id, p.parent_id, t.name, p.date, p.text, p.author_id FROM {$this->rs2010forum->tables->posts} AS p, {$this->rs2010forum->tables->topics} AS t WHERE p.parent_id = {$this->rs2010forum->current_topic} AND t.id = p.parent_id ORDER BY p.id DESC LIMIT 0, 50;");
            }

            if (!empty($feed_data)) {
                foreach ($feed_data as $element) {
                    echo '<item>'.PHP_EOL;
                        echo '<title>'.esc_html(stripslashes($element->name)).'</title>'.PHP_EOL;

                        if ($this->rs2010forum->current_view === 'forum') {
                            echo '<link>'.$this->rs2010forum->rewrite->get_link('topic', $element->id).'</link>'.PHP_EOL;
                        } else if ($this->rs2010forum->current_view === 'topic') {
                            echo '<link>'.$this->rs2010forum->rewrite->get_post_link($element->id, $element->parent_id).'</link>'.PHP_EOL;
                        }

                        echo '<pubDate>'.mysql2date('D, d M Y H:i:s +0000', $element->date, false).'</pubDate>'.PHP_EOL;
                        echo '<description><![CDATA['.esc_html(strip_tags($element->text)).']]></description>'.PHP_EOL;
                        echo '<dc:creator>'.$this->rs2010forum->get_plain_username($element->author_id).'</dc:creator>'.PHP_EOL;

                        if ($this->rs2010forum->current_view === 'forum') {
                            echo '<guid isPermaLink="true">'.$this->rs2010forum->rewrite->get_link('topic', $element->id).'</guid>'.PHP_EOL;
                        } else if ($this->rs2010forum->current_view === 'topic') {
                            echo '<guid isPermaLink="true">'.$this->rs2010forum->rewrite->get_post_link($element->id, $element->parent_id).'</guid>'.PHP_EOL;
                        }
                    echo '</item>'.PHP_EOL;
                }
            }

            echo '</channel>'.PHP_EOL;
            echo '</rss>'.PHP_EOL;

            exit;
        }
    }
}
