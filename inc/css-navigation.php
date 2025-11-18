<?php
/**
 * ナビゲーション設定関連のCSS出力
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ナビゲーション設定CSSの動的出力
 */
function backbone_dynamic_navigation_output() {
    $css = '';

    // モバイルメニュー非表示ブレークポイント設定を取得
    $breakpoint = backbone_get_mobile_menu_hide_breakpoint();

    // ブレークポイントに応じてメニューを非表示にするCSS
    if ($breakpoint === 'mobile') {
        // スマホのみ非表示（767px以下）
        $css .= "/* モバイルメニュー非表示（スマホのみ） */\n";
        $css .= "@media (max-width: 767px) {\n";
        $css .= "    .main-navigation ul {\n";
        $css .= "        display: none !important;\n";
        $css .= "    }\n";
        $css .= "    .main-navigation ul.active {\n";
        $css .= "        display: flex !important;\n";
        $css .= "        flex-direction: column;\n";
        $css .= "    }\n";
        $css .= "}\n\n";
    } elseif ($breakpoint === 'tablet') {
        // タブレット以下非表示（1279px以下）
        $css .= "/* モバイルメニュー非表示（タブレット以下） */\n";
        $css .= "@media (max-width: 1279px) {\n";
        $css .= "    .main-navigation ul {\n";
        $css .= "        display: none !important;\n";
        $css .= "    }\n";
        $css .= "    .main-navigation ul.active {\n";
        $css .= "        display: flex !important;\n";
        $css .= "        flex-direction: column;\n";
        $css .= "    }\n";
        $css .= "}\n\n";
    }
    // 'none' の場合はCSSを出力しない（常に表示）

    return $css;
}

/**
 * ナビゲーションCSSを <head> に出力
 */
function backbone_output_navigation_css() {
    $css = backbone_dynamic_navigation_output();

    if (!empty($css)) {
        echo "<style id=\"backbone-navigation-css\">\n";
        echo $css;
        echo "</style>\n";
    }
}
add_action('wp_head', 'backbone_output_navigation_css', 100);
