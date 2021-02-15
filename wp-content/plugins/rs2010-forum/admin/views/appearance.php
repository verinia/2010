<?php

if (!defined('ABSPATH')) exit;

?>
<div class="wrap" id="rs-options">
    <?php
    $title = __('Appearance', 'rs2010-forum');
    $titleUpdated = __('Appearance updated.', 'rs2010-forum');
    $this->render_admin_header($title, $titleUpdated);
    ?>

    <form method="post">
        <?php wp_nonce_field('rs2010_forum_save_appearance'); ?>

        <div class="settings-box">
            <div class="settings-header">
                <span class="fas fa-paint-brush"></span>
                <?php _e('Appearance', 'rs2010-forum'); ?>
            </div>
            <table>
                <?php
                $themes = $this->rs2010forum->appearance->get_themes();
                if (count($themes) > 1) { ?>
                    <tr>
                        <th><label for="theme"><?php _e('Theme', 'rs2010-forum'); ?>:</label></th>
                        <td>
                            <select name="theme" id="theme">
                                <?php foreach ($themes as $k => $v) {
                                    echo '<option value="'.$k.'" '.selected($k, $this->rs2010forum->appearance->get_current_theme(), false).'>'.$v['name'].'</option>';
                                } ?>
                            </select>
                        </td>
                    </tr>
                <?php
                }
                $themesOption = $this->rs2010forum->appearance->is_default_theme();
                ?>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_font"><?php _e('Font:', 'rs2010-forum'); ?></label></th>
                    <td><input class="regular-text" type="text" name="custom_font" id="custom_font" value="<?php echo esc_html(stripslashes($this->rs2010forum->appearance->options['custom_font'])); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_font_size"><?php _e('Font size:', 'rs2010-forum'); ?></label></th>
                    <td><input class="regular-text" type="text" name="custom_font_size" id="custom_font_size" value="<?php echo esc_html(stripslashes($this->rs2010forum->appearance->options['custom_font_size'])); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_color"><?php _e('Forum color:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_color']); ?>" class="color-picker" name="custom_color" id="custom_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_accent_color"><?php _e('Accent color:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_accent_color']); ?>" class="color-picker" name="custom_accent_color" id="custom_accent_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_accent_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_text_color"><?php _e('Text color:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_text_color']); ?>" class="color-picker" name="custom_text_color" id="custom_text_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_text_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_text_color_light"><?php _e('Text color light:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_text_color_light']); ?>" class="color-picker" name="custom_text_color_light" id="custom_text_color_light" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_text_color_light']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_link_color"><?php _e('Link color:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_link_color']); ?>" class="color-picker" name="custom_link_color" id="custom_link_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_link_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_background_color"><?php _e('Background color (First):', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_background_color']); ?>" class="color-picker" name="custom_background_color" id="custom_background_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_background_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_background_color_alt"><?php _e('Background color (Second):', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_background_color_alt']); ?>" class="color-picker" name="custom_background_color_alt" id="custom_background_color_alt" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_background_color_alt']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_border_color"><?php _e('Border color:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_border_color']); ?>" class="color-picker" name="custom_border_color" id="custom_border_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_border_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_read_indicator_color"><?php _e('Read indicator color:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_read_indicator_color']); ?>" class="color-picker" name="custom_read_indicator_color" id="custom_read_indicator_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_read_indicator_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_unread_indicator_color"><?php _e('Unread indicator color:', 'rs2010-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo stripslashes($this->rs2010forum->appearance->options['custom_unread_indicator_color']); ?>" class="color-picker" name="custom_unread_indicator_color" id="custom_unread_indicator_color" data-default-color="<?php echo $this->rs2010forum->appearance->options_default['custom_unread_indicator_color']; ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_css"><?php _e('Custom CSS:', 'rs2010-forum'); ?></label></th>
                    <td><textarea class="large-text" data-code-editor-mode="css" rows="8" cols="80" type="text" name="custom_css" id="custom_css"><?php echo esc_html(stripslashes($this->rs2010forum->appearance->options['custom_css'])); ?></textarea></td>
                </tr>
            </table>
        </div>

        <input type="submit" name="af_appearance_submit" class="button button-primary" value="<?php _e('Save Appearance', 'rs2010-forum'); ?>">
    </form>
</div>
