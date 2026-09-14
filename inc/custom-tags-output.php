<?php
/**
 * 追加タグ出力機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 追加タグを<head>内に出力
 *
 * 所有権確認のメタタグや計測タグは<head>の先頭寄りに置く必要があるため、優先度を早くする。
 */
function backbone_output_custom_tags_head() {
    backbone_output_custom_tags('head');
}
add_action('wp_head', 'backbone_output_custom_tags_head', 1);

/**
 * 追加タグを<body>の直後に出力
 */
function backbone_output_custom_tags_body_open() {
    backbone_output_custom_tags('body_open');
}
add_action('wp_body_open', 'backbone_output_custom_tags_body_open', 1);

/**
 * 追加タグを</body>の直前に出力
 */
function backbone_output_custom_tags_footer() {
    backbone_output_custom_tags('footer');
}
add_action('wp_footer', 'backbone_output_custom_tags_footer', 100);

/**
 * 追加タグを出力する共通関数
 *
 * @param string $position 出力場所（'head'、'body_open'、'footer' のいずれか）
 */
function backbone_output_custom_tags($position) {
    // 管理画面またはカスタマイザーでは出力しない
    if (is_admin() || is_customize_preview()) {
        return;
    }

    $scopes = array('global');

    if (is_front_page()) {
        $scopes[] = 'front';
    }

    foreach ($scopes as $scope) {
        $tags = get_theme_mod("custom_tags_{$scope}_{$position}", '');

        if (!is_string($tags) || trim($tags) === '') {
            continue;
        }

        // 権限を持つユーザーが入力したタグをそのまま出力する
        echo "\n<!-- Custom Tags: {$scope} {$position} -->\n";
        echo $tags . "\n";
        echo "<!-- End Custom Tags: {$scope} {$position} -->\n";
    }
}
