<?php
/**
 * カスタムCSS出力機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 投稿タイプ別のカスタムCSSをヘッダーに出力
 */
function backbone_output_custom_css_header() {
    backbone_output_custom_css('header');
}
add_action('wp_head', 'backbone_output_custom_css_header', 100);

/**
 * 投稿タイプ別のカスタムCSSをフッターに出力
 */
function backbone_output_custom_css_footer() {
    backbone_output_custom_css('footer');
}
add_action('wp_footer', 'backbone_output_custom_css_footer', 100);

/**
 * カスタムCSSを出力する共通関数
 *
 * @param string $position 出力場所（'header' または 'footer'）
 */
function backbone_output_custom_css($position) {
    // 管理画面またはカスタマイザーでは出力しない
    if (is_admin() || is_customize_preview()) {
        return;
    }

    // ========================================
    // 1. 全体共通CSSを出力
    // ========================================
    $global_css_enabled = get_theme_mod('custom_css_global_enable', false);

    if ($global_css_enabled) {
        $global_css_position = get_theme_mod('custom_css_global_position', 'header');

        if ($global_css_position === $position) {
            $global_css_code = get_theme_mod('custom_css_global_code', '');

            if (!empty(trim($global_css_code))) {
                echo "\n<!-- Global Custom CSS -->\n";
                echo '<style type="text/css">' . "\n";
                echo $global_css_code . "\n";
                echo '</style>' . "\n";
                echo "<!-- End Global Custom CSS -->\n\n";
            }
        }
    }

    // ========================================
    // 2. 投稿タイプ別CSSを出力
    // ========================================

    // 現在の投稿タイプを取得
    $post_type = get_post_type();

    // 投稿タイプが取得できない場合は全体共通のみ出力して終了
    if (!$post_type) {
        return;
    }

    // この投稿タイプでCSSが有効かチェック
    $css_enabled = get_theme_mod("custom_css_enable_{$post_type}", false);

    if (!$css_enabled) {
        return;
    }

    // 出力場所が一致するかチェック
    $css_position = get_theme_mod("custom_css_position_{$post_type}", 'header');

    if ($css_position !== $position) {
        return;
    }

    // CSSコードを取得
    $css_code = get_theme_mod("custom_css_code_{$post_type}", '');

    // コードが空の場合は終了
    if (empty(trim($css_code))) {
        return;
    }

    // CSSコードを出力
    echo "\n<!-- Custom CSS for post type: {$post_type} -->\n";
    echo '<style type="text/css">' . "\n";
    echo $css_code . "\n";
    echo '</style>' . "\n";
    echo "<!-- End Custom CSS for post type: {$post_type} -->\n\n";
}

/**
 * カスタマイザーでのリアルタイムプレビュー用のJavaScript
 */
function backbone_custom_css_customize_preview() {
    // スクリプト本体を構築
    $post_types = get_post_types(array('public' => true), 'objects');

    $script = "(function($) {
        'use strict';

        // 全体共通CSSのCSS設定変更を監視
        wp.customize('custom_css_global_enable', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        wp.customize('custom_css_global_position', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        wp.customize('custom_css_global_code', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });";

    // 投稿タイプ別のCSS設定変更を監視
    foreach ($post_types as $post_type) {
        $post_type_name = $post_type->name;
        $script .= "

        // {$post_type_name} のCSS有効化設定
        wp.customize('custom_css_enable_{$post_type_name}', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        // {$post_type_name} のCSS出力場所設定
        wp.customize('custom_css_position_{$post_type_name}', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        // {$post_type_name} のCSSコード
        wp.customize('custom_css_code_{$post_type_name}', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });";
    }

    $script .= "
    })(jQuery);";

    // customize-preview スクリプトに依存関係付きで追加（jQueryが確実に読み込まれた後に実行）
    wp_add_inline_script('customize-preview', $script);
}
add_action('customize_preview_init', 'backbone_custom_css_customize_preview');
