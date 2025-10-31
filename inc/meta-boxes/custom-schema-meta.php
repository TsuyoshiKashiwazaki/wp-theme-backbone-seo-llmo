<?php
/**
 * カスタム構造化データ（JSON-LD）のメタボックス
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタム構造化データメタボックスを追加
 */
function backbone_add_custom_schema_meta_box() {
    $post_types = array('post', 'page');

    // カスタム投稿タイプも追加
    $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
    $post_types = array_merge($post_types, $custom_post_types);

    foreach ($post_types as $post_type) {
        add_meta_box(
            'backbone_custom_schema_meta',
            __('カスタムヘッダーコード', 'backbone-seo-llmo'),
            'backbone_custom_schema_meta_box_callback',
            $post_type,
            'normal',
            'low'
        );
    }
}
add_action('add_meta_boxes', 'backbone_add_custom_schema_meta_box');

/**
 * メタボックスの表示内容
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function backbone_custom_schema_meta_box_callback($post) {
    // Nonce フィールドを追加
    wp_nonce_field('backbone_custom_schema_meta_box', 'backbone_custom_schema_meta_box_nonce');

    // 現在の設定値を取得
    $custom_schema = get_post_meta($post->ID, '_custom_json_ld', true);

    ?>
    <div class="custom-schema-meta-box">
        <p class="description">
            <?php _e('このページ専用の &lt;head&gt; タグ内に追加したいコードを入力してください。構造化データ、メタタグ、スクリプトなど何でも入力できます。', 'backbone-seo-llmo'); ?>
        </p>

        <p>
            <textarea
                name="custom_json_ld"
                id="custom_json_ld"
                rows="25"
                style="width: 100%; font-family: monospace; font-size: 12px;"
                placeholder='<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  ...
}
</script>'
            ><?php echo esc_textarea($custom_schema); ?></textarea>
        </p>

        <p class="description">
            <?php _e('※ 入力した内容はそのまま &lt;head&gt; 内に出力されます。', 'backbone-seo-llmo'); ?>
        </p>
    </div>

    <style>
    .custom-schema-meta-box textarea {
        tab-size: 2;
    }
    </style>
    <?php
}

/**
 * メタボックスの保存処理
 *
 * @param int $post_id 投稿ID
 */
function backbone_save_custom_schema_meta_box($post_id) {
    // Nonce の検証
    if (!isset($_POST['backbone_custom_schema_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['backbone_custom_schema_meta_box_nonce'], 'backbone_custom_schema_meta_box')) {
        return;
    }

    // 自動保存の場合は処理しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限チェック
    if (isset($_POST['post_type']) && 'page' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // カスタムヘッダーコードの保存
    if (isset($_POST['custom_json_ld'])) {
        $custom_code = wp_unslash($_POST['custom_json_ld']);
        update_post_meta($post_id, '_custom_json_ld', $custom_code);
    } else {
        delete_post_meta($post_id, '_custom_json_ld');
    }
}
add_action('save_post', 'backbone_save_custom_schema_meta_box');

/**
 * カスタムヘッダーコードをheadに出力
 */
function backbone_output_custom_schema() {
    if (!is_singular()) {
        return;
    }

    $custom_code = get_post_meta(get_the_ID(), '_custom_json_ld', true);

    if (empty($custom_code)) {
        return;
    }

    echo "\n" . $custom_code . "\n";
}
add_action('wp_head', 'backbone_output_custom_schema', 99);
