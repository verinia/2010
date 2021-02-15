<?php

if (!defined('ABSPATH')) exit;

add_filter('mycred_all_references', 'mycred_all_rs2010forum_references', 20, 1);
add_filter('mycred_setup_hooks', 'mycred_setup_rs2010forum_hook', 20, 1);
add_action('mycred_load_hooks', 'mycred_load_rs2010forum_hook', 20);

function mycred_all_rs2010forum_references($references) {
    $references['new_forum_topic'] = __('Forum Topics (Rs2010 Forum)', 'rs2010-forum');
    $references['new_forum_post'] = __('Forum Posts (Rs2010 Forum)', 'rs2010-forum');
    $references['received_like'] = __('Received Likes (Rs2010 Forum)', 'rs2010-forum');
    $references['received_dislike'] = __('Received Dislikes (Rs2010 Forum)', 'rs2010-forum');

    return $references;
}

function mycred_setup_rs2010forum_hook($installed) {
    $installed['hook_rs2010forum'] = array(
        'title'       => __('Rs2010 Forum', 'rs2010-forum'),
        'description' => __('Awards %_plural% for Rs2010 Forum actions.', 'rs2010-forum'),
        'callback'    => array('myCRED_Rs2010Forum')
    );

    return $installed;
}

function mycred_load_rs2010forum_hook() {
    class myCRED_Rs2010Forum extends myCRED_Hook {
        private $rs2010forum = null;

        public function __construct($hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY) {
            global $rs2010forum;
            $this->rs2010forum = $rs2010forum;

            parent::__construct(array(
                'id'       => 'hook_rs2010forum',
                'defaults' => array(
                    'new_topic'     => array(
                        'creds'     => 1,
                        'log'       => __('%plural% for new forum topic', 'rs2010-forum'),
                        'limit'     => '0/x'
                    ),
                    'delete_topic'  => array(
                        'creds'     => -1,
                        'log'       => __('%singular% deduction for deleted forum topic', 'rs2010-forum')
                    ),
                    'new_post'      => array(
                        'creds'     => 1,
                        'log'       => __('%plural% for new forum post', 'rs2010-forum'),
                        'author'    => 0,
                        'limit'     => '0/x'
                    ),
                    'delete_post'   => array(
                        'creds'     => -1,
                        'log'       => __('%singular% deduction for deleted forum post', 'rs2010-forum')
                    ),
                    'received_like' => array(
                        'creds'     => 1,
                        'log'       => __('%plural% for received forum post like', 'rs2010-forum'),
                    ),
                    'received_dislike'   => array(
                        'creds'     => -1,
                        'log'       => __('%singular% deduction for received forum post dislike', 'rs2010-forum')
                    ),
                    'show_points'   => 0,
                    'show_badges'   => 0,
                    'show_ranks'    => 0
                )
            ), $hook_prefs, $type);
        }

        public function run() {
            if ($this->prefs['new_topic']['creds'] != 0) {
                add_action('rs2010forum_after_add_topic_submit', array($this, 'new_topic'), 20, 6);
            }

            if ($this->prefs['delete_topic']['creds'] != 0) {
                add_action('rs2010forum_before_delete_topic', array($this, 'delete_topic'), 20, 1);
            }

            if ($this->prefs['new_post']['creds'] != 0) {
                add_action('rs2010forum_after_add_post_submit', array($this, 'new_post'), 20, 6);
            }

            if ($this->prefs['delete_post']['creds'] != 0) {
                add_action('rs2010forum_before_delete_post', array($this, 'delete_post'), 20, 1);
            }

            if ($this->prefs['received_like']['creds'] != 0) {
                add_action('rs2010forum_after_add_reaction', array($this, 'received_like'), 20, 3);
                add_action('rs2010forum_after_update_reaction', array($this, 'received_like'), 20, 3);
            }

            if ($this->prefs['received_dislike']['creds'] != 0) {
                add_action('rs2010forum_after_add_reaction', array($this, 'received_dislike'), 20, 3);
                add_action('rs2010forum_after_update_reaction', array($this, 'received_dislike'), 20, 3);
            }

            if (isset($this->prefs['show_points']) && $this->prefs['show_points'] == 1) {
                add_action('rs2010forum_after_post_author', array($this, 'show_points'), 100, 2);
                add_action('rs2010forum_profile_row', array($this, 'show_points_profile'), 100, 1);
            }

            if (isset($this->prefs['show_badges']) && $this->prefs['show_badges'] == 1) {
                add_action('rs2010forum_after_post_author', array($this, 'show_badges'), 200, 2);
                add_action('rs2010forum_profile_row', array($this, 'show_badges_profile'), 200, 1);
            }

            if (isset($this->prefs['show_ranks']) && $this->prefs['show_ranks'] == 1) {
                add_action('rs2010forum_after_post_author', array($this, 'show_ranks'), 300, 2);
                add_action('rs2010forum_profile_row', array($this, 'show_ranks_profile'), 300, 1);
            }
        }

        public function show_points($user_id, $number_of_posts) {
            if (!$user_id) {
                return;
            }

            $balance = $this->core->get_users_balance($user_id, $this->mycred_type);
            $layout = $this->core->plural().': '.$this->core->format_creds($balance);

            echo '<small class="users-mycred-balance">'.$layout.'</small>';
        }

        public function show_points_profile($user_data) {
            $balance = $this->core->get_users_balance($user_data->ID, $this->mycred_type);

            $this->rs2010forum->profile->renderProfileRow($this->core->plural().':', $this->core->format_creds($balance));
        }

        public function show_badges($user_id, $number_of_posts) {
            if (!$user_id) {
                return;
            }

            mycred_display_users_badges($user_id);
        }

        public function show_badges_profile($user_data) {
            echo '<div class="profile-row">';
                echo '<div>'.__('Badges:', 'rs2010-forum').'</div>';
                echo '<div>';
                mycred_display_users_badges($user_data->ID);
                echo '</div>';
            echo '</div>';
        }

        public function show_ranks($user_id, $number_of_posts) {
            if (!$user_id) {
                return;
            }

            $rank_id = mycred_get_users_rank_id($user_id);
            echo mycred_get_rank_logo($rank_id);
        }

        public function show_ranks_profile($user_data) {
            $rank_id = mycred_get_users_rank_id($user_data->ID);
            $this->rs2010forum->profile->renderProfileRow(__('Rank:', 'rs2010-forum'), mycred_get_rank_logo($rank_id));
        }

        public function new_topic($post_id, $topic_id, $subject, $content, $link, $author_id) {
            if (!$author_id) {
                return;
            }

            if ($this->core->exclude_user($author_id)) {
                return;
            }

            if ($this->over_hook_limit('new_topic', 'new_forum_topic', $author_id)) {
                return;
            }

            if ($this->has_entry('new_forum_topic', $topic_id, $author_id)) {
                return;
            }

            $this->core->add_creds(
                'new_forum_topic',
                $author_id,
                $this->prefs['new_topic']['creds'],
                $this->prefs['new_topic']['log'],
                $topic_id,
                '',
                $this->mycred_type
            );
        }

        public function delete_topic($topic_id) {
            $author_id = $this->rs2010forum->get_topic_starter($topic_id);

            if ($this->has_entry('new_forum_topic', $topic_id, $author_id)) {
                $this->core->add_creds(
                    'deleted_topic',
                    $author_id,
                    $this->prefs['delete_topic']['creds'],
                    $this->prefs['delete_topic']['log'],
                    $topic_id,
                    '',
                    $this->mycred_type
                );
            }
        }

        public function new_post($post_id, $topic_id, $subject, $content, $link, $author_id) {
            if (!$author_id) {
                return;
            }

            if ($this->core->exclude_user($author_id)) {
                return;
            }

            if ((bool)$this->prefs['new_post']['author'] === false && $this->rs2010forum->get_topic_starter($topic_id) == $author_id) {
                return;
            }

            if ($this->over_hook_limit('new_post', 'new_forum_post', $author_id)) {
                return;
            }

            if ($this->has_entry('new_forum_post', $post_id, $author_id)) {
                return;
            }

            $this->core->add_creds(
                'new_forum_post',
                $author_id,
                $this->prefs['new_post']['creds'],
                $this->prefs['new_post']['log'],
                $post_id,
                '',
                $this->mycred_type
            );
        }

        public function delete_post($post_id) {
            $author_id = $this->rs2010forum->get_post_author($post_id);

            if ($this->has_entry('new_forum_post', $post_id, $author_id)) {
                $this->core->add_creds(
                    'deleted_post',
                    $author_id,
                    $this->prefs['delete_post']['creds'],
                    $this->prefs['delete_post']['log'],
                    $post_id,
                    '',
                    $this->mycred_type
                );
            }
        }

        public function received_like($post_id, $user_id, $reaction) {
            if ($reaction !== 'up') {
                return;
            }

            $user_id = $this->rs2010forum->get_post_author($post_id);

            if (!$user_id) {
                return;
            }

            if ($this->core->exclude_user($user_id)) {
                return;
            }

            if ($this->has_entry('received_like', $post_id, $user_id)) {
                return;
            }

            $this->core->add_creds(
                'received_like',
                $user_id,
                $this->prefs['received_like']['creds'],
                $this->prefs['received_like']['log'],
                $post_id,
                '',
                $this->mycred_type
            );
        }

        public function received_dislike($post_id, $user_id, $reaction) {
            if ($reaction !== 'down') {
                return;
            }

            $user_id = $this->rs2010forum->get_post_author($post_id);

            if (!$user_id) {
                return;
            }

            if ($this->core->exclude_user($user_id)) {
                return;
            }

            if ($this->has_entry('received_dislike', $post_id, $user_id)) {
                return;
            }

            $this->core->add_creds(
                'received_dislike',
                $user_id,
                $this->prefs['received_dislike']['creds'],
                $this->prefs['received_dislike']['log'],
                $post_id,
                '',
                $this->mycred_type
            );
        }

        public function preferences() {
            $prefs = $this->prefs;

            if (!isset($prefs['new_topic']['limit'])) {
                $prefs['new_topic']['limit'] = '0/x';
            }

            if (!isset($prefs['new_post']['limit'])) {
                $prefs['new_post']['limit'] = '0/x';
            }
            ?>
            <div class="hook-instance">
            <h3><?php _e('New Topic', 'rs2010-forum'); ?></h3>
            <div class="row">
                <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('new_topic', 'creds')); ?>"><?php echo $this->core->plural(); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('new_topic', 'creds')); ?>" id="<?php echo $this->field_id(array('new_topic', 'creds')); ?>" value="<?php echo $this->core->number($prefs['new_topic']['creds']); ?>" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('new_topic', 'limit')); ?>"><?php _e('Limit', 'rs2010-forum'); ?></label>
                        <?php echo $this->hook_limit_setting($this->field_name(array('new_topic', 'limit')), $this->field_id(array('new_topic', 'limit')), $prefs['new_topic']['limit']); ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('new_topic', 'log')); ?>"><?php _e('Log template', 'rs2010-forum'); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('new_topic', 'log')); ?>" id="<?php echo $this->field_id(array('new_topic', 'log')); ?>" placeholder="<?php _e('required', 'rs2010-forum'); ?>" value="<?php echo esc_attr($prefs['new_topic']['log']); ?>" class="form-control">
                        <span class="description"><?php echo $this->available_template_tags(array('general')); ?></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="hook-instance">
            <h3><?php _e('Deleted Topic', 'rs2010-forum'); ?></h3>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('delete_topic', 'creds')); ?>"><?php echo $this->core->plural(); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('delete_topic', 'creds')); ?>" id="<?php echo $this->field_id(array('delete_topic', 'creds')); ?>" value="<?php echo $this->core->number($prefs['delete_topic']['creds']); ?>" class="form-control">
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('delete_topic', 'log')); ?>"><?php _e('Log template', 'rs2010-forum'); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('delete_topic', 'log')); ?>" id="<?php echo $this->field_id(array('delete_topic', 'log')); ?>" placeholder="<?php _e('required', 'rs2010-forum'); ?>" value="<?php echo esc_attr($prefs['delete_topic']['log']); ?>" class="form-control">
                        <span class="description"><?php echo $this->available_template_tags(array('general')); ?></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="hook-instance">
            <h3><?php _e('New Post', 'rs2010-forum'); ?></h3>
            <div class="row">
                <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('new_post', 'creds')); ?>"><?php echo $this->core->plural(); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('new_post', 'creds')); ?>" id="<?php echo $this->field_id(array('new_post', 'creds')); ?>" value="<?php echo $this->core->number($prefs['new_post']['creds']); ?>" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('new_post', 'limit')); ?>"><?php _e('Limit', 'rs2010-forum'); ?></label>
                        <?php echo $this->hook_limit_setting($this->field_name(array('new_post', 'limit')), $this->field_id(array('new_post', 'limit')), $prefs['new_post']['limit']); ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('new_post', 'log')); ?>"><?php _e('Log template', 'rs2010-forum'); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('new_post', 'log')); ?>" id="<?php echo $this->field_id(array('new_post', 'log')); ?>" placeholder="<?php _e('required', 'rs2010-forum'); ?>" value="<?php echo esc_attr($prefs['new_post']['log']); ?>" class="form-control">
                        <span class="description"><?php echo $this->available_template_tags(array('general')); ?></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="radio">
                            <label for="<?php echo $this->field_id(array('new_post' => 'author')); ?>"><input type="checkbox" name="<?php echo $this->field_name(array('new_post' => 'author')); ?>" id="<?php echo $this->field_id(array('new_post' => 'author')); ?>" <?php checked($prefs['new_post']['author'], 1); ?> value="1"> <?php echo $this->core->template_tags_general(__('Topic authors can receive %_plural% for replying to their own topic.', 'rs2010-forum')); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="hook-instance">
            <h3><?php _e('Deleted Post', 'rs2010-forum'); ?></h3>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('delete_post', 'creds')); ?>"><?php echo $this->core->plural(); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('delete_post', 'creds')); ?>" id="<?php echo $this->field_id(array('delete_post', 'creds')); ?>" value="<?php echo $this->core->number($prefs['delete_post']['creds']); ?>" class="form-control">
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('delete_post', 'log')); ?>"><?php _e('Log template', 'rs2010-forum'); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('delete_post', 'log')); ?>" id="<?php echo $this->field_id(array('delete_post', 'log')); ?>" placeholder="<?php _e('required', 'rs2010-forum'); ?>" value="<?php echo esc_attr($prefs['delete_post']['log']); ?>" class="form-control">
                        <span class="description"><?php echo $this->available_template_tags(array('general')); ?></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="hook-instance">
            <h3><?php _e('Received Like', 'rs2010-forum'); ?></h3>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('received_like', 'creds')); ?>"><?php echo $this->core->plural(); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('received_like', 'creds')); ?>" id="<?php echo $this->field_id(array('received_like', 'creds')); ?>" value="<?php echo $this->core->number($prefs['received_like']['creds']); ?>" class="form-control">
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('received_like', 'log')); ?>"><?php _e('Log template', 'rs2010-forum'); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('received_like', 'log')); ?>" id="<?php echo $this->field_id(array('received_like', 'log')); ?>" placeholder="<?php _e('required', 'rs2010-forum'); ?>" value="<?php echo esc_attr($prefs['received_like']['log']); ?>" class="form-control">
                        <span class="description"><?php echo $this->available_template_tags(array('general')); ?></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="hook-instance">
            <h3><?php _e('Received Dislike', 'rs2010-forum'); ?></h3>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('received_dislike', 'creds')); ?>"><?php echo $this->core->plural(); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('received_dislike', 'creds')); ?>" id="<?php echo $this->field_id(array('received_dislike', 'creds')); ?>" value="<?php echo $this->core->number($prefs['received_dislike']['creds']); ?>" class="form-control">
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="<?php echo $this->field_id(array('received_dislike', 'log')); ?>"><?php _e('Log template', 'rs2010-forum'); ?></label>
                        <input type="text" name="<?php echo $this->field_name(array('received_dislike', 'log')); ?>" id="<?php echo $this->field_id(array('received_dislike', 'log')); ?>" placeholder="<?php _e('required', 'rs2010-forum'); ?>" value="<?php echo esc_attr($prefs['received_dislike']['log']); ?>" class="form-control">
                        <span class="description"><?php echo $this->available_template_tags(array('general')); ?></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="hook-instance">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="radio">
                            <label for="<?php echo $this->field_id('show_points'); ?>"><input type="checkbox" name="<?php echo $this->field_name('show_points'); ?>" id="<?php echo $this->field_id('show_points'); ?>" <?php checked($prefs['show_points'], 1); ?> value="1"> <?php echo $this->core->template_tags_general(__('Show %_plural% in posts and profiles', 'rs2010-forum')); ?></label>
                        </div>
                        <div class="radio">
                            <label for="<?php echo $this->field_id('show_badges'); ?>"><input type="checkbox" name="<?php echo $this->field_name('show_badges'); ?>" id="<?php echo $this->field_id('show_badges'); ?>" <?php checked($prefs['show_badges'], 1); ?> value="1"> <?php echo $this->core->template_tags_general(__('Show badges in posts and profiles', 'rs2010-forum')); ?></label>
                        </div>
                        <div class="radio">
                            <label for="<?php echo $this->field_id('show_ranks'); ?>"><input type="checkbox" name="<?php echo $this->field_name('show_ranks'); ?>" id="<?php echo $this->field_id('show_ranks'); ?>" <?php checked($prefs['show_ranks'], 1); ?> value="1"> <?php echo $this->core->template_tags_general(__('Show ranks in posts and profiles', 'rs2010-forum')); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <?php
        }

        public function sanitise_preferences($data) {
            if (isset($data['new_topic']['limit']) && isset($data['new_topic']['limit_by'])) {
                $limit = sanitize_text_field($data['new_topic']['limit']);

                if ($limit == '') {
                    $limit = 0;
                }

                $data['new_topic']['limit'] = $limit.'/'.$data['new_topic']['limit_by'];
                unset($data['new_topic']['limit_by']);
            }

            if (isset($data['new_post']['limit']) && isset($data['new_post']['limit_by'])) {
                $limit = sanitize_text_field($data['new_post']['limit']);

                if ($limit == '') {
                    $limit = 0;
                }

                $data['new_post']['limit'] = $limit.'/'.$data['new_post']['limit_by'];
                unset($data['new_post']['limit_by']);
            }

            $data['new_post']['author'] = (isset($data['new_post']['author'])) ? 1 : 0;
            $data['show_points'] = (isset($data['show_points'])) ? 1 : 0;
            $data['show_badges'] = (isset($data['show_badges'])) ? 1 : 0;
            $data['show_ranks'] = (isset($data['show_ranks'])) ? 1 : 0;

            return $data;
        }
    }
}
