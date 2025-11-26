<?php
/**
 * レイアウト設定のメタボックス
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * レイアウト設定のメタボックスを追加
 */
function backbone_add_layout_meta_box() {
    // 投稿、固定ページ、およびすべてのパブリックなカスタム投稿タイプを対象にする
    $post_types = get_post_types(array('public' => true), 'names');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'backbone_layout_meta',
            __('レイアウト設定', 'backbone-seo-llmo'),
            'backbone_layout_meta_box_callback',
            $post_type,
            'side',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'backbone_add_layout_meta_box');

/**
 * メタボックスの表示内容
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function backbone_layout_meta_box_callback($post) {
    // Nonce フィールドを追加
    wp_nonce_field('backbone_layout_meta_box', 'backbone_layout_meta_box_nonce');

    // 現在の設定値を取得
    // 互換性のために古いキーもチェックするが、新しいキーを優先
    $layout = get_post_meta($post->ID, '_backbone_layout_settings', true);
    if (empty($layout)) {
        $layout = get_post_meta($post->ID, '_backbone_page_layout', true);
    }

    // デフォルト値を設定
    if (empty($layout)) {
        $layout = 'default';
    }

    // 選択肢の定義
    $options = array(
        'default' => __('カスタマイザー設定に従う', 'backbone-seo-llmo'),
        'single-column' => __('1カラム', 'backbone-seo-llmo'),
        'two-columns' => __('2カラム', 'backbone-seo-llmo'),
        'three-columns' => __('3カラム', 'backbone-seo-llmo'),
        'full-width' => __('全幅レイアウト（フルワイド）', 'backbone-seo-llmo'),
    );
    ?>
    <div class="layout-meta-box">
        <p>
            <label for="backbone_layout_settings"><strong><?php _e('この投稿のレイアウト:', 'backbone-seo-llmo'); ?></strong></label><br>
            <select name="backbone_layout_settings" id="backbone_layout_settings" style="width: 100%;">
                <?php foreach ($options as $value => $label) : ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected($layout, $value); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p class="description">
            <?php _e('「カスタマイザー設定に従う」を選択すると、カスタマイザーの「レイアウト設定」での設定が適用されます。その他のオプションを選択すると、この投稿のみそのレイアウトが適用されます。', 'backbone-seo-llmo'); ?>
        </p>
    </div>
    <?php
}

/**
 * メタボックスの保存処理
 *
 * @param int $post_id 投稿ID
 */
function backbone_save_layout_meta_box($post_id) {
    // Nonce の検証
    if (!isset($_POST['backbone_layout_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['backbone_layout_meta_box_nonce'], 'backbone_layout_meta_box')) {
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

    // レイアウト設定を保存
    if (isset($_POST['backbone_layout_settings'])) {
        $layout = sanitize_text_field($_POST['backbone_layout_settings']);
        update_post_meta($post_id, '_backbone_layout_settings', $layout);
        
        // 古いキーのデータを削除（移行用）
        delete_post_meta($post_id, '_backbone_page_layout');
    }
}
add_action('save_post', 'backbone_save_layout_meta_box');