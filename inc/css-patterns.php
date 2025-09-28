<?php
/**
 * デザインパターン・デコレーションパターン関連の機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * デコレーションパターンCSSの動的出力
 */
function backbone_dynamic_decoration_output() {
    // サブディレクトリ設定がある場合はスキップ
    $subdirectory_settings = backbone_get_current_subdirectory_design_settings();
    if ($subdirectory_settings) {
        return;
    }

    // 選択されているデコレーションパターンのCSS生成
    $current_decoration = get_theme_mod('decoration_pattern', 'none');

    // 'none'の場合は何も出力しない
    if ($current_decoration === 'none') {
        return;
    }

    // 動的パターンの場合はJSONから生成
    $css = backbone_generate_decoration_css($current_decoration);

    if (!empty($css)) {
        echo '<style type="text/css" id="dynamic-decoration-css">' . "\n";
        echo $css;
        echo '</style>' . "\n";


    }
}
add_action('wp_head', 'backbone_dynamic_decoration_output', 20);

/**
 * デザインパターンCSSの動的出力
 */
function backbone_dynamic_design_output() {
    // サブディレクトリ設定がある場合はスキップ
    $subdirectory_settings = backbone_get_current_subdirectory_design_settings();
    if ($subdirectory_settings) {
        return;
    }

    // 選択されているデザインパターンのCSS生成
    $current_design = get_theme_mod('design_pattern', 'none');

    // 'none'の場合は何も出力しない
    if ($current_design === 'none') {
        return;
    }

    // 動的パターンの場合はJSONから生成
    $css = backbone_generate_design_css($current_design);
    
    // フルワイドレイアウト用の追加CSSを削除（他のレイアウトと同じ処理を使用）

    if (!empty($css)) {
        echo '<style type="text/css" id="dynamic-design-css">' . "\n";
        echo $css;
        echo '</style>' . "\n";

        // 強制CSSはdesign-utilities.phpで生成されたメインCSSに含まれるため削除

        // 安全なJavaScript（左飛び防止）- ヘッダーコンテナは除外
        echo '<script type="text/javascript">' . "\n";
        echo "(function() {\n";
        echo "    function safelyFixContainers() {\n";
        echo "        // ヘッダー以外のコンテナのみ選択\n";
        echo "        const containers = document.querySelectorAll('.design-{$current_design} main .container');\n";
        echo "        containers.forEach(function(el) {\n";
        echo "            el.style.setProperty('width', '100%', 'important');\n";
        echo "            el.style.setProperty('max-width', '100%', 'important');\n";
        echo "            // すべてのレイアウト時でパディングを0に\n";
        echo "            el.style.setProperty('padding', '0', 'important');\n";
        echo "            el.style.setProperty('margin', '0 auto', 'important');\n";
        echo "            el.style.setProperty('box-sizing', 'border-box', 'important');\n";
        echo "            // 左飛び防止\n";
        echo "            el.style.removeProperty('left');\n";
        echo "            el.style.removeProperty('margin-left');\n";
        echo "            el.style.removeProperty('margin-right');\n";
        echo "        });\n";
        echo "    }\n";
        echo "    if (document.readyState === 'loading') {\n";
        echo "        document.addEventListener('DOMContentLoaded', safelyFixContainers);\n";
        echo "    } else {\n";
        echo "        safelyFixContainers();\n";
        echo "    }\n";
        echo "})();\n";
        echo '</script>' . "\n";
    }
}
add_action('wp_head', 'backbone_dynamic_design_output', 999);

/**
 * ヘッダーコンテナの統一スタイル（最終オーバーライド）
 * デザインパターンの後に適用して全レイアウトで統一を保証
 */
function backbone_unified_header_override() {
    $current_design = get_theme_mod('design_pattern', 'none');
    
    // デザインパターンが設定されている場合は、そのデザインパターンのスタイルを優先
    if ($current_design !== 'none') {
        return; // デザインパターンに任せる
    }
    
    ?>
    <style type="text/css" id="unified-header-override">
    /* デザインパターンなしの場合のみヘッダーコンテナスタイル */
    body .site-header {
        width: 100% !important;
        position: relative !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0 !important;
    }
    
    body .site-header .container {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding-left: 2.5rem !important;
        padding-right: 2.5rem !important;
        box-sizing: border-box !important;
        position: relative !important;
        left: 0 !important;
        right: 0 !important;
    }
    
    /* タブレットサイズ：PCと同じ仕様 */
    @media (min-width: 768px) and (max-width: 1279px) {
        body .site-header .container {
            padding-left: 2.5rem !important;
            padding-right: 2.5rem !important;
        }
    }
    
    @media (max-width: 767px) {
        body .site-header .container {
            padding-left: 0.9375rem !important;
            padding-right: 0.9375rem !important;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'backbone_unified_header_override', 1001);
