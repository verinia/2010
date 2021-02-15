<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap" id="rs-structure">
    <?php
    $title = __('Ads', 'rs2010-forum');
    $titleUpdated = __('Ads updated.', 'rs2010-forum');
    $this->render_admin_header($title, $titleUpdated);
    ?>

    <div id="editor-container" class="settings-box" style="display: none;">
        <div class="settings-header"></div>
        <div class="editor-instance" id="ad-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_save_ad'); ?>
                <input type="hidden" name="ad_id" value="new">

                <table class="form-table">
                    <tr>
                        <th><label for="ad_name"><?php _e('Name:', 'rs2010-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="200" name="ad_name" id="ad_name" value="" required></td>
                    </tr>
                    <tr>
                        <th><label for="ad_code"><?php _e('Code:', 'rs2010-forum'); ?></label></th>
                        <td><textarea class="large-text" data-code-editor-mode="htmlmixed" rows="8" cols="80" type="text" name="ad_code" id="ad_code"></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="ad_active"><?php _e('Active:', 'rs2010-forum'); ?></label></th>
                        <td><input type="checkbox" id="ad_active" name="ad_active"></td>
                    </tr>
                    <tr id="locations-editor">
                        <th><label><?php _e('Location:', 'rs2010-forum'); ?></label></th>
                        <td>
                            <?php
                            foreach ($this->rs2010forum->ads->locations as $key => $value) {
                                echo '<label><input type="checkbox" name="ad_locations[]" value="'.$key.'">'.$value.'</label>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="rs-create-edit-ad-submit" value="<?php _e('Save', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="ad-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_delete_ad'); ?>
                <input type="hidden" name="ad_id" value="0">
                <p><?php _e('Are you sure you want to delete this ad?', 'rs2010-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="rs2010-forum-delete-ad" value="<?php _e('Delete', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>
    </div>

    <div class="settings-box">
        <div class="settings-header">
            <span class="fas fa-ad"></span>
            <?php _e('Ads', 'rs2010-forum'); ?>
        </div>
        <?php
        $ads = $this->rs2010forum->ads->get_ads();

        if (!empty($ads)) {
            $ads_table = new Rs2010ForumAdminTableAds($ads);
            $ads_table->prepare_items();
            $ads_table->display();
        }

        echo '<a href="#" class="ad-editor-link add-element" data-value-id="new" data-value-editor-title="'.__('New Ad', 'rs2010-forum').'">';
            echo '<span class="fas fa-plus"></span>';
            echo __('New Ad', 'rs2010-forum');
        echo '</a>';

        ?>
    </div>
</div>
