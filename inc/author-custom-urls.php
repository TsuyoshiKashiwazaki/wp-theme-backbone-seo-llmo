<?php
/**
 * Author Custom URLs
 *
 * Allows customizing the author archive URL (/author/{username}/).
 * This affects ALL plugins and themes that use get_author_posts_url().
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom author URL field to user profile (below username field)
 */
function backbone_add_author_url_field($user) {
    $custom_url = get_user_meta($user->ID, 'custom_author_url', true);
    ?>
    <tr>
        <th><label for="custom_author_url"><?php _e('カスタム著者URL', 'backbone-seo-llmo'); ?></label></th>
        <td>
            <input type="text" name="custom_author_url" id="custom_author_url"
                   value="<?php echo esc_attr($custom_url); ?>"
                   class="regular-text"
                   placeholder="例: profile, https://example.com/profile/" />
            <p class="description">
                <?php _e('著者ページのURLをカスタマイズできます（すべてのプラグイン・テーマで使用されます）', 'backbone-seo-llmo'); ?><br>
                <?php _e('相対パス（profile）または完全URL（https://example.com/profile/）が設定可能', 'backbone-seo-llmo'); ?><br>
                <?php _e('未設定の場合は /author/{ユーザー名}/ が使用されます', 'backbone-seo-llmo'); ?>
            </p>
            <?php if ($custom_url): ?>
                <p class="description">
                    <strong><?php _e('現在のURL:', 'backbone-seo-llmo'); ?></strong>
                    <?php
                    // 完全URLかどうかチェック
                    $display_url = (strpos($custom_url, 'http://') === 0 || strpos($custom_url, 'https://') === 0)
                        ? $custom_url
                        : home_url('/' . trim($custom_url, '/') . '/');
                    ?>
                    <a href="<?php echo esc_url($display_url); ?>" target="_blank">
                        <?php echo esc_url($display_url); ?>
                    </a>
                </p>
            <?php endif; ?>
        </td>
    </tr>
    <?php
}
add_action('personal_options', 'backbone_add_author_url_field');

/**
 * Save custom author URL
 */
function backbone_save_author_url_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    $custom_url = isset($_POST['custom_author_url']) ? sanitize_text_field($_POST['custom_author_url']) : '';

    // Only trim slashes for relative paths, not full URLs
    if (!empty($custom_url) && strpos($custom_url, 'http://') !== 0 && strpos($custom_url, 'https://') !== 0) {
        $custom_url = trim($custom_url, '/');
    }

    // Save or delete
    if (!empty($custom_url)) {
        update_user_meta($user_id, 'custom_author_url', $custom_url);
    } else {
        delete_user_meta($user_id, 'custom_author_url');
    }
}
add_action('personal_options_update', 'backbone_save_author_url_field');
add_action('edit_user_profile_update', 'backbone_save_author_url_field');

/**
 * Filter author link to use custom URL
 * This affects ALL plugins and themes using get_author_posts_url()
 * Note: Does NOT add rewrite rules - the URL should point to an existing page
 */
function backbone_custom_author_link($link, $author_id, $author_nicename) {
    $custom_url = get_user_meta($author_id, 'custom_author_url', true);

    if (!empty($custom_url)) {
        // Check if it's a full URL (starts with http:// or https://)
        if (strpos($custom_url, 'http://') === 0 || strpos($custom_url, 'https://') === 0) {
            return $custom_url; // Return full URL as-is
        }

        // Relative path: combine with home_url
        $custom_url = trim($custom_url, '/');
        return user_trailingslashit(home_url('/' . $custom_url), 'author');
    }

    return $link;
}
add_filter('author_link', 'backbone_custom_author_link', 10, 3);
