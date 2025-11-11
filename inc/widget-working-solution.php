<?php
/**
 * ウィジェット機能 - 実用版
 * シンプルで確実に動作する実装
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * エラー出力の抑制（ウィジェット画面のみ）
 */
if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'widgets.php') !== false) {
    // doing_it_wrongエラーを無効化
    add_filter('doing_it_wrong_trigger_error', '__return_false', 1);
}

/**
 * ウィジェット管理画面の改善
 */
function backbone_improve_widget_screen() {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'widgets') {
        return;
    }

    // 管理画面キャッシュバスティング設定を取得
    $cache_busting_admin = get_theme_mod('enable_cache_busting_admin', false);

    // ウィジェット拡張スクリプト
    wp_enqueue_script(
        'backbone-widget-improvements',
        get_template_directory_uri() . '/js/widget-block-editor.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-data'),
        backbone_get_file_version('/js/widget-block-editor.js', $cache_busting_admin),
        true
    );

    // データを渡す
    wp_localize_script('backbone-widget-improvements', 'backboneWidgetEditor', array(
        'isWidgetsScreen' => true,
        'customizerUrl' => admin_url('customize.php?autofocus[panel]=widgets'),
    ));

    // 必要なCSSのみ追加
    wp_add_inline_style('wp-edit-widgets', '
        /* ウィジェットエリアを確実に表示 */
        .components-panel__body {
            display: block !important;
        }

        .components-panel__body-toggle {
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        /* エラーメッセージのみ非表示 */
        body > div:not([id]):not([class]) {
            display: none !important;
        }
    ');

    // エラーメッセージを非表示にする最小限のJS
    wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            // wp-editorエラーのみを削除
            $("body").contents().filter(function() {
                return this.nodeType === 3 && this.textContent.includes("wp-editor");
            }).remove();
        });
    ');
}
add_action('admin_enqueue_scripts', 'backbone_improve_widget_screen');

/**
 * ウィジェット管理画面への通知
 */
function backbone_widget_screen_notice() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'widgets') {
        ?>
        <div class="notice notice-info">
            <p>
                <strong>💡 ヒント:</strong>
                リアルタイムプレビューを見ながら編集したい場合は、
                <a href="<?php echo admin_url('customize.php?autofocus[panel]=widgets'); ?>">カスタマイザーのウィジェット設定</a>
                でレガシーウィジェットを使用できます。
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'backbone_widget_screen_notice');

/**
 * カスタマイザーでの通知
 */
function backbone_customizer_notice() {
    ?>
    <script>
    jQuery(function($) {
        if (wp.customize) {
            wp.customize.bind('ready', function() {
                wp.customize.panel('widgets', function(panel) {
                    var notice = '<div style="margin: 10px; padding: 10px; background: #f0f0f1; border-left: 4px solid #2271b1;">' +
                        '<strong>ウィジェット編集オプション</strong><br>' +
                        'ブロックエディタは <a href="<?php echo admin_url('widgets.php'); ?>" target="_blank">外観 > ウィジェット</a> で利用できます。' +
                        '</div>';

                    if (!panel.container.find('.backbone-customizer-notice').length) {
                        panel.container.find('.accordion-section-content').first()
                            .prepend('<div class="backbone-customizer-notice">' + notice + '</div>');
                    }
                });
            });
        }
    });
    </script>
    <?php
}
add_action('customize_controls_print_footer_scripts', 'backbone_customizer_notice');