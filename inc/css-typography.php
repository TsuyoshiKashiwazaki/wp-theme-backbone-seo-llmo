<?php
/**
 * タイポグラフィ関連の機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}


/**
 * GoogleフォントとタイポグラフィCSSの動的出力
 */
function backbone_dynamic_typography_output() {
    // サブディレクトリ設定がある場合はスキップ
    $subdirectory_settings = backbone_get_current_subdirectory_design_settings();
    if ($subdirectory_settings) {
        return;
    }

    // Googleフォントの読み込み
    $google_fonts_url = backbone_get_google_fonts_url();
    if (!empty($google_fonts_url)) {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link href="' . esc_url($google_fonts_url) . '" rel="stylesheet">' . "\n";
    }

    // 選択されているタイポグラフィパターンのCSS生成
    $current_pattern = get_theme_mod('text_pattern', 'none');

    // カスタムパターンの場合は既存の機能を使用
    if ($current_pattern === 'custom') {
        return;
    }

    // 'none'の場合は何も出力しない（ブラウザデフォルトを使用）
    if ($current_pattern === 'none') {
        return;
    }

    // 動的パターンの場合はJSONから生成
    $css = backbone_generate_typography_css($current_pattern);

    if (!empty($css)) {
        echo '<style type="text/css" id="dynamic-typography-css">' . "\n";
        echo $css;
        echo '</style>' . "\n";
    }
}
add_action('wp_head', 'backbone_dynamic_typography_output', 15);

