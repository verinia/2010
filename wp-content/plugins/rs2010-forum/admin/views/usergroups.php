<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap" id="rs-structure">
    <?php
    $title = __('Usergroups', 'rs2010-forum');
    $titleUpdated = __('Usergroups updated.', 'rs2010-forum');
    $this->render_admin_header($title, $titleUpdated);
    ?>

    <div id="editor-container" class="settings-box" style="display: none;">
        <div class="settings-header"></div>
        <div class="editor-instance" id="usergroup-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_save_usergroup'); ?>
                <input type="hidden" name="usergroup_id" value="new">
                <input type="hidden" name="usergroup_category" value="0">

                <table class="form-table">
                    <tr>
                        <th><label for="usergroup_name"><?php _e('Name:', 'rs2010-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="200" name="usergroup_name" id="usergroup_name" value="" required></td>
                    </tr>
                    <tr id="usergroup-color-settings">
                        <th><label for="usergroup_color"><?php _e('Color:', 'rs2010-forum'); ?></label></th>
                        <td><input type="text" value="#444444" class="color-picker" name="usergroup_color" id="usergroup_color" data-default-color="#444444"></td>
                    </tr>
                    <tr>
                        <th>
                            <label for="usergroup_icon"><?php _e('Icon:', 'rs2010-forum'); ?></label>
                            <span class="description"><?php _e('Set an optional icon for the usergroup.', 'rs2010-forum'); ?></span>
                        </th>
                        <td>
                            <input type="text" id="usergroup_icon" name="usergroup_icon" value="" placeholder="fas fa-users">
                            <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">
                                <?php _e('List of available icons.', 'rs2010-forum'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="usergroup_visibility"><?php _e('Hide usergroup:', 'rs2010-forum'); ?></label></th>
                        <td><input type="checkbox" id="usergroup_visibility" name="usergroup_visibility"></td>
                    </tr>
                    <tr>
                        <th><label for="usergroup_auto_add"><?php _e('Add new users automatically:', 'rs2010-forum'); ?></label></th>
                        <td><input type="checkbox" id="usergroup_auto_add" name="usergroup_auto_add"></td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="rs-create-edit-usergroup-submit" value="<?php _e('Save', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="usergroup-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_delete_usergroup'); ?>
                <input type="hidden" name="usergroup-id" value="0">
                <p><?php _e('Are you sure you want to delete this usergroup?', 'rs2010-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="rs2010-forum-delete-usergroup" value="<?php _e('Delete', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance" id="usergroup-category-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_save_usergroup_category'); ?>
                <input type="hidden" name="usergroup_category_id" value="new">

                <table class="form-table">
                    <tr>
                        <th><label for="usergroup_category_name"><?php _e('Name:', 'rs2010-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="200" name="usergroup_category_name" id="usergroup_category_name" value="" required></td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="rs-create-edit-usergroup-category-submit" value="<?php _e('Save', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="usergroup-category-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('rs2010_forum_delete_usergroup_category'); ?>
                <input type="hidden" name="usergroup-category-id" value="0">
                <p><?php _e('Deleting this category will also permanently delete all usergroups inside it. Are you sure you want to delete this category?', 'rs2010-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="rs2010-forum-delete-usergroup-category" value="<?php _e('Delete', 'rs2010-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php _e('Cancel', 'rs2010-forum'); ?></a>
                </p>
            </form>
        </div>
    </div>

    <a href="#" class="usergroup-category-editor-link add-element" data-value-id="new" data-value-editor-title="<?php _e('Add Category', 'rs2010-forum'); ?>">
        <?php
        echo '<span class="fas fa-plus"></span>';
        _e('Add Category', 'rs2010-forum');
        ?>
    </a>

    <?php
    $userGroupsCategories = Rs2010ForumUserGroups::getUserGroupCategories();

    if (!empty($userGroupsCategories)) {
        foreach ($userGroupsCategories as $category) {
            echo '<input type="hidden" id="usergroup_category_'.$category->term_id.'_name" value="'.esc_html(stripslashes($category->name)).'">';

            $usergroups = Rs2010ForumUserGroups::getUserGroupsOfCategory($category->term_id);
            ?>
            <div class="settings-box">
                <div class="settings-header">
                    <span class="fas fa-users"></span>
                    <?php echo stripslashes($category->name); ?>
                    <span class="category-actions">
                        <a href="#" class="usergroup-category-delete-link action-delete" data-value-id="<?php echo $category->term_id; ?>" data-value-editor-title="<?php _e('Delete Category', 'rs2010-forum'); ?>"><?php _e('Delete Category', 'rs2010-forum'); ?></a>
                        &middot;
                        <a href="#" class="usergroup-category-editor-link action-edit" data-value-id="<?php echo $category->term_id; ?>" data-value-editor-title="<?php _e('Edit Category', 'rs2010-forum'); ?>"><?php _e('Edit Category', 'rs2010-forum'); ?></a>
                    </span>
                </div>
                <?php
                if (!empty($usergroups)) {
                    $userGroupsTable = new Rs2010_Forum_Admin_UserGroups_Table($usergroups);
                    $userGroupsTable->prepare_items();
                    $userGroupsTable->display();
                }
                ?>

                <a href="#" class="usergroup-editor-link add-element" data-value-id="new" data-value-category="<?php echo $category->term_id; ?>" data-value-editor-title="<?php _e('Add Usergroup', 'rs2010-forum'); ?>">
                    <span class="fas fa-plus"></span>
                    <?php _e('Add Usergroup', 'rs2010-forum'); ?>
                </a>
            </div>
            <?php
        }

        echo '<a href="#" class="usergroup-category-editor-link add-element" data-value-id="new" data-value-editor-title="'.__('Add Category', 'rs2010-forum').'">';
            echo '<span class="fas fa-plus"></span>';
            _e('Add Category', 'rs2010-forum');
        echo '</a>';
    }
    ?>
</div>
