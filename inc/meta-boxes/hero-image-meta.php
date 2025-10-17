<?php
/**
 * メインビジュアル（ヒーローイメージ）のメタボックス
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * メインビジュアル設定のメタボックスを追加
 */
function backbone_add_hero_image_meta_box() {
    $post_types = array_keys(backbone_get_hero_supported_post_types());

    foreach ($post_types as $post_type) {
        add_meta_box(
            'backbone_hero_image_meta',
            __('メインビジュアル設定', 'backbone-seo-llmo'),
            'backbone_hero_image_meta_box_callback',
            $post_type,
            'side',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'backbone_add_hero_image_meta_box');

/**
 * メタボックスの表示内容
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function backbone_hero_image_meta_box_callback($post) {
    // Nonce フィールドを追加
    wp_nonce_field('backbone_hero_image_meta_box', 'backbone_hero_image_meta_box_nonce');

    // 現在の設定値を取得
    $display = get_post_meta($post->ID, '_hero_image_display', true);
    $style = get_post_meta($post->ID, '_hero_image_style', true);

    // デフォルト値を設定
    if (empty($display)) {
        $display = 'global';
    }
    if (empty($style)) {
        $style = 'global';
    }
    ?>
    <div class="hero-meta-box">
        <p>
            <label for="hero_image_display"><strong><?php _e('表示設定:', 'backbone-seo-llmo'); ?></strong></label><br>
            <select name="hero_image_display" id="hero_image_display" style="width: 100%;">
                <option value="global" <?php selected($display, 'global'); ?>><?php _e('グローバル設定を使用', 'backbone-seo-llmo'); ?></option>
                <option value="show" <?php selected($display, 'show'); ?>><?php _e('表示', 'backbone-seo-llmo'); ?></option>
                <option value="hide" <?php selected($display, 'hide'); ?>><?php _e('非表示', 'backbone-seo-llmo'); ?></option>
            </select>
        </p>

        <p>
            <label for="hero_image_style"><strong><?php _e('表示スタイル:', 'backbone-seo-llmo'); ?></strong></label><br>
            <select name="hero_image_style" id="hero_image_style" style="width: 100%;">
                <option value="global" <?php selected($style, 'global'); ?>><?php _e('グローバル設定を使用', 'backbone-seo-llmo'); ?></option>
                <?php foreach (backbone_get_hero_style_options() as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($style, $key); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p class="description">
            <?php _e('※ デコレーション設定（枠線、影、角丸など）は「外観」→「カスタマイズ」→「メインビジュアル設定」から行えます。', 'backbone-seo-llmo'); ?>
        </p>
    </div>

    <style>
    .hero-meta-box p {
        margin-bottom: 15px;
    }
    </style>
    <?php
}

/**
 * メタボックスの保存処理
 *
 * @param int $post_id 投稿ID
 */
function backbone_save_hero_image_meta_box($post_id) {
    // Nonce の検証
    if (!isset($_POST['backbone_hero_image_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['backbone_hero_image_meta_box_nonce'], 'backbone_hero_image_meta_box')) {
        return;
    }

    // 自動保存の場合は処理しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 表示設定を保存
    if (isset($_POST['hero_image_display'])) {
        $display = sanitize_text_field($_POST['hero_image_display']);
        update_post_meta($post_id, '_hero_image_display', $display);
    }

    // スタイル設定を保存
    if (isset($_POST['hero_image_style'])) {
        $style = sanitize_text_field($_POST['hero_image_style']);
        update_post_meta($post_id, '_hero_image_style', $style);
    }
}
add_action('save_post', 'backbone_save_hero_image_meta_box');

