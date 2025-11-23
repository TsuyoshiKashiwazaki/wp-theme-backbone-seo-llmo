<?php
/**
 * カスタムJS出力機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 投稿タイプ別のカスタムJSをヘッダーに出力
 */
function backbone_output_custom_js_header() {
    backbone_output_custom_js('header');
}
add_action('wp_head', 'backbone_output_custom_js_header', 100);

/**
 * 投稿タイプ別のカスタムJSをフッターに出力
 */
function backbone_output_custom_js_footer() {
    backbone_output_custom_js('footer');
}
add_action('wp_footer', 'backbone_output_custom_js_footer', 100);

/**
 * カスタムJSを出力する共通関数
 *
 * @param string $position 出力場所（'header' または 'footer'）
 */
function backbone_output_custom_js($position) {
    // 管理画面またはカスタマイザーでは出力しない
    if (is_admin() || is_customize_preview()) {
        return;
    }

    // ========================================
    // 1. 全体共通JSを出力
    // ========================================
    $global_js_enabled = get_theme_mod('custom_js_global_enable', false);

    if ($global_js_enabled) {
        $global_js_position = get_theme_mod('custom_js_global_position', 'header');

        if ($global_js_position === $position) {
            $global_js_code = get_theme_mod('custom_js_global_code', '');

            if (!empty(trim($global_js_code))) {
                echo "\n<!-- Global Custom JS -->\n";
                echo '<script type="text/javascript">' . "\n";
                echo $global_js_code . "\n";
                echo '</script>' . "\n";
                echo "<!-- End Global Custom JS -->\n\n";
            }
        }
    }

    // ========================================
    // 2. 投稿タイプ別JSを出力
    // ========================================

    // 現在の投稿タイプを取得
    $post_type = get_post_type();

    // 投稿タイプが取得できない場合は全体共通のみ出力して終了
    if (!$post_type) {
        return;
    }

    // この投稿タイプでJSが有効かチェック
    $js_enabled = get_theme_mod("custom_js_enable_{$post_type}", false);

    if (!$js_enabled) {
        return;
    }

    // 出力場所が一致するかチェック
    $js_position = get_theme_mod("custom_js_position_{$post_type}", 'header');

    if ($js_position !== $position) {
        return;
    }

    // JSコードを取得
    $js_code = get_theme_mod("custom_js_code_{$post_type}", '');

    // コードが空の場合は終了
    if (empty(trim($js_code))) {
        return;
    }

    // JSコードを出力
    echo "\n<!-- Custom JS for post type: {$post_type} -->\n";
    echo '<script type="text/javascript">' . "\n";
    echo $js_code . "\n";
    echo '</script>' . "\n";
    echo "<!-- End Custom JS for post type: {$post_type} -->\n\n";
}

/**
 * カスタマイザーでのリアルタイムプレビュー用のJavaScript
 */
function backbone_custom_js_customize_preview() {
    // スクリプト本体を構築
    $post_types = get_post_types(array('public' => true), 'objects');

    $script = "(function($) {
        'use strict';

        // 全体共通JSのJS設定変更を監視
        wp.customize('custom_js_global_enable', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        wp.customize('custom_js_global_position', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        wp.customize('custom_js_global_code', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });";

    // 投稿タイプ別のJS設定変更を監視
    foreach ($post_types as $post_type) {
        $post_type_name = $post_type->name;
        $script .= "

        // {$post_type_name} のJS有効化設定
        wp.customize('custom_js_enable_{$post_type_name}', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        // {$post_type_name} のJS出力場所設定
        wp.customize('custom_js_position_{$post_type_name}', function(value) {
            value.bind(function(newval) {
                wp.customize.preview.send('refresh');
            });
        });

        // {$post_type_name} のJSコード
        wp.customize('custom_js_code_{$post_type_name}', function(value) {
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
add_action('customize_preview_init', 'backbone_custom_js_customize_preview');
