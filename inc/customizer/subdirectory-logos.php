<?php
/**
 * サブディレクトリ設定
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * サブディレクトリ設定を追加
 */
function backbone_add_subdirectory_logo_settings($wp_customize) {
    // サブディレクトリ設定セクション
    $wp_customize->add_section('subdirectory_logos', array(
        'title'    => __('サブディレクトリ設定', 'backbone-seo-llmo'),
        'priority' => 25,
        'description' => __('特定のサブディレクトリ以下で異なるロゴやテキストを表示できます。空欄の場合はサイト基本情報の設定が使用されます。', 'backbone-seo-llmo'),
    ));

    // サブディレクトリ設定の数を管理
    $wp_customize->add_setting('subdirectory_count', array(
        'default'           => 0,  // 初期値を0に変更
        'sanitize_callback' => 'absint',
    ));

    // 保存されている設定数を取得
    $saved_count = get_theme_mod('subdirectory_count', 0);

    // 保存された設定がある場合のみ1つ目を表示
    if ($saved_count >= 1) {
        // サブディレクトリ1のパス
        $wp_customize->add_setting("subdirectory_path_1", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_path_1", array(
            'label'       => __('サブディレクトリ 1 のパス', 'backbone-seo-llmo'),
            'section'     => 'subdirectory_logos',
            'type'        => 'text',
            'description' => __('例: /media, /blog, /shop など（スラッシュで始める）', 'backbone-seo-llmo'),
            'priority'    => 10,
        ));

        // サブディレクトリ1のサイトタイトル
        $wp_customize->add_setting("subdirectory_title_1", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_title_1", array(
            'label'       => __('サブディレクトリ 1 のサイトタイトル', 'backbone-seo-llmo'),
            'section'     => 'subdirectory_logos',
            'type'        => 'text',
            'description' => __('空欄の場合はデフォルトのサイトタイトルを使用', 'backbone-seo-llmo'),
            'priority'    => 11,
        ));

        // サブディレクトリ1のロゴ画像
        $wp_customize->add_setting("subdirectory_logo_1", array(
            'default'           => '',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, "subdirectory_logo_1", array(
            'label'       => __('サブディレクトリ 1 のロゴ', 'backbone-seo-llmo'),
            'section'     => 'subdirectory_logos',
            'mime_type'   => 'image',
            'priority'    => 12,
        )));

        // サブディレクトリ1のホームURL（オプション）
        $wp_customize->add_setting("subdirectory_home_1", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control("subdirectory_home_1", array(
            'label'       => __('サブディレクトリ 1 のホームURL', 'backbone-seo-llmo'),
            'section'     => 'subdirectory_logos',
            'type'        => 'url',
            'description' => __('ロゴクリック時のリンク先（空欄の場合は指定サブディレクトリがホームになります）', 'backbone-seo-llmo'),
            'priority'    => 13,
        ));

        // サブディレクトリ1のヘッダーメッセージ
        $wp_customize->add_setting("subdirectory_header_message_1", array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ));

        $wp_customize->add_control("subdirectory_header_message_1", array(
            'label'       => __('サブディレクトリ 1 のヘッダーメッセージ', 'backbone-seo-llmo'),
            'section'     => 'subdirectory_logos',
            'type'        => 'textarea',
            'description' => __('ヘッダーに表示するメッセージ（HTMLタグ使用可）', 'backbone-seo-llmo'),
            'priority'    => 14,
        ));

        // サブディレクトリ1のフッターメッセージ
        $wp_customize->add_setting("subdirectory_footer_message_1", array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ));

        $wp_customize->add_control("subdirectory_footer_message_1", array(
            'label'       => __('サブディレクトリ 1 のフッターメッセージ', 'backbone-seo-llmo'),
            'section'     => 'subdirectory_logos',
            'type'        => 'textarea',
            'description' => __('フッターに表示するメッセージ（HTMLタグ使用可）', 'backbone-seo-llmo'),
            'priority'    => 15,
        ));

        // 削除ボタン
        $wp_customize->add_setting("subdirectory_delete_1", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_delete_1", array(
            'section'     => 'subdirectory_logos',
            'type'        => 'hidden',
            'description' => '<button type="button" class="button button-link-delete" onclick="backboneDeleteSubdirectory(1)" style="color:#d63638;margin-top:10px;">このサブディレクトリ設定を削除</button>',
            'priority'    => 18,
        ));

        // 区切り線
        $wp_customize->add_setting("subdirectory_separator_1", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_separator_1", array(
            'section'     => 'subdirectory_logos',
            'type'        => 'hidden',
            'description' => '<hr style="margin: 20px 0;">',
            'priority'    => 19,
        ));
    }

    // 追加ボタン
    $wp_customize->add_setting('add_subdirectory_button', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('add_subdirectory_button', array(
        'section'     => 'subdirectory_logos',
        'type'        => 'hidden',
        'description' => '<button type="button" class="button button-secondary" onclick="backboneAddSubdirectory()" style="margin-top: 10px;">サブディレクトリ設定を追加</button>',
        'priority'    => 500,
    ));

    // JavaScriptを追加
    add_action('customize_controls_print_footer_scripts', 'backbone_subdirectory_customizer_scripts');
}

/**
 * カスタマイザー用のJavaScript
 */
function backbone_subdirectory_customizer_scripts() {
    // 保存されているカウントを取得
    $saved_count = get_theme_mod('subdirectory_count', 0);
    ?>
    <script>
    var backboneSubdirectoryCount = <?php echo $saved_count; ?>; // PHPから現在のカウントを取得
    var backboneMaxSubdirectories = 10;

    function backboneAddSubdirectory() {
        if (backboneSubdirectoryCount >= backboneMaxSubdirectories) {
            alert('最大' + backboneMaxSubdirectories + '個までのサブディレクトリを設定できます。');
            return;
        }

        backboneSubdirectoryCount++;

        // 通知を表示
        var notice = jQuery('<div class="notice notice-info" style="position:fixed;top:46px;left:50%;transform:translateX(-50%);z-index:100000;padding:12px 20px;background:#2196F3;color:white;border-radius:4px;box-shadow:0 2px 5px rgba(0,0,0,0.3);"><p>サブディレクトリ設定を追加中...</p></div>');
        jQuery('body').append(notice);

        // AJAXでサーバー側に設定を保存
        jQuery.post(ajaxurl, {
            action: 'backbone_add_subdirectory',
            count: backboneSubdirectoryCount,
            _wpnonce: wp.customize.settings.nonce.save
        }, function(response) {
            if (response.success) {
                // 成功したらリロード（この時点で既に保存済みなので警告は出ない）
                window.location.reload();
            } else {
                alert('エラーが発生しました。');
                notice.remove();
            }
        });
    }

    function backboneDeleteSubdirectory(index) {
        if (!confirm('サブディレクトリ ' + index + ' の設定を削除しますか？\nこの操作は元に戻せません。')) {
            return;
        }

        // 通知を表示
        var notice = jQuery('<div class="notice notice-info" style="position:fixed;top:46px;left:50%;transform:translateX(-50%);z-index:100000;padding:12px 20px;background:#2196F3;color:white;border-radius:4px;box-shadow:0 2px 5px rgba(0,0,0,0.3);"><p>サブディレクトリ設定を削除中...</p></div>');
        jQuery('body').append(notice);

        // AJAXでサーバー側で削除処理
        jQuery.post(ajaxurl, {
            action: 'backbone_delete_subdirectory',
            index: index,
            _wpnonce: wp.customize.settings.nonce.save
        }, function(response) {
            if (response.success) {
                // 成功したらリロード
                window.location.reload();
            } else {
                alert('エラーが発生しました。');
                notice.remove();
            }
        });
    }

    // 初期化時に既存の設定数を確認（修正版）
    jQuery(document).ready(function($) {
        // PHPから取得した値を使用
        var savedCount = <?php echo $saved_count; ?>;

        // カウントが0の場合、最初の追加で1になるように設定
        if (savedCount === 0) {
            backboneSubdirectoryCount = 0;
        } else {
            // 既存の設定がある場合は、その数を使用
            backboneSubdirectoryCount = savedCount;
        }
    });
    </script>
    <?php
}

/**
 * AJAXでサブディレクトリ数を更新
 */
add_action('wp_ajax_backbone_add_subdirectory', 'backbone_handle_add_subdirectory');
function backbone_handle_add_subdirectory() {
    // nonceチェック
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'save-customize_' . get_stylesheet())) {
        wp_send_json_error('Invalid nonce');
        return;
    }

    // カスタマイザー権限チェック
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Permission denied');
        return;
    }

    // カウントを取得して保存
    $count = isset($_POST['count']) ? intval($_POST['count']) : 1;
    $count = min($count, 10); // 最大10個まで

    // theme_modを直接更新
    set_theme_mod('subdirectory_count', $count);

    wp_send_json_success(array('count' => $count));
}

/**
 * AJAXでサブディレクトリを削除
 */
add_action('wp_ajax_backbone_delete_subdirectory', 'backbone_handle_delete_subdirectory');
function backbone_handle_delete_subdirectory() {
    // nonceチェック
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'save-customize_' . get_stylesheet())) {
        wp_send_json_error('Invalid nonce');
        return;
    }

    // カスタマイザー権限チェック
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Permission denied');
        return;
    }

    $index = isset($_POST['index']) ? intval($_POST['index']) : 0;
    if ($index < 1 || $index > 10) {
        wp_send_json_error('Invalid index');
        return;
    }

    // 現在のカウントを取得
    $current_count = get_theme_mod('subdirectory_count', 0);

    // 削除する設定をクリア
    remove_theme_mod("subdirectory_path_{$index}");
    remove_theme_mod("subdirectory_title_{$index}");
    remove_theme_mod("subdirectory_logo_{$index}");
    remove_theme_mod("subdirectory_home_{$index}");
    remove_theme_mod("subdirectory_header_message_{$index}");
    remove_theme_mod("subdirectory_footer_message_{$index}");

    // デザイン設定もクリア
    remove_theme_mod("subdirectory_{$index}_color_theme");
    remove_theme_mod("subdirectory_{$index}_design_pattern");
    remove_theme_mod("subdirectory_{$index}_text_pattern");
    remove_theme_mod("subdirectory_{$index}_decoration_pattern");

    // 後ろの設定を前に詰める
    for ($i = $index; $i < $current_count; $i++) {
        $next = $i + 1;

        // 次の設定を現在の位置にコピー
        set_theme_mod("subdirectory_path_{$i}", get_theme_mod("subdirectory_path_{$next}", ''));
        set_theme_mod("subdirectory_title_{$i}", get_theme_mod("subdirectory_title_{$next}", ''));
        set_theme_mod("subdirectory_logo_{$i}", get_theme_mod("subdirectory_logo_{$next}", ''));
        set_theme_mod("subdirectory_home_{$i}", get_theme_mod("subdirectory_home_{$next}", ''));
        set_theme_mod("subdirectory_header_message_{$i}", get_theme_mod("subdirectory_header_message_{$next}", ''));
        set_theme_mod("subdirectory_footer_message_{$i}", get_theme_mod("subdirectory_footer_message_{$next}", ''));

        // デザイン設定もコピー
        set_theme_mod("subdirectory_{$i}_color_theme", get_theme_mod("subdirectory_{$next}_color_theme", 'none'));
        set_theme_mod("subdirectory_{$i}_design_pattern", get_theme_mod("subdirectory_{$next}_design_pattern", 'none'));
        set_theme_mod("subdirectory_{$i}_text_pattern", get_theme_mod("subdirectory_{$next}_text_pattern", 'none'));
        set_theme_mod("subdirectory_{$i}_decoration_pattern", get_theme_mod("subdirectory_{$next}_decoration_pattern", 'none'));
    }

    // 最後の設定を削除
    if ($current_count > 0) {
        remove_theme_mod("subdirectory_path_{$current_count}");
        remove_theme_mod("subdirectory_title_{$current_count}");
        remove_theme_mod("subdirectory_logo_{$current_count}");
        remove_theme_mod("subdirectory_home_{$current_count}");
        remove_theme_mod("subdirectory_header_message_{$current_count}");
        remove_theme_mod("subdirectory_footer_message_{$current_count}");

        // デザイン設定も削除
        remove_theme_mod("subdirectory_{$current_count}_color_theme");
        remove_theme_mod("subdirectory_{$current_count}_design_pattern");
        remove_theme_mod("subdirectory_{$current_count}_text_pattern");
        remove_theme_mod("subdirectory_{$current_count}_decoration_pattern");
    }

    // カウントを更新
    $new_count = max(0, $current_count - 1);
    set_theme_mod('subdirectory_count', $new_count);

    wp_send_json_success(array('count' => $new_count));
}

/**
 * 動的にサブディレクトリ設定を追加（AJAX経由）
 */
add_action('customize_register', function($wp_customize) {
    // 保存されている設定数を取得
    $subdirectory_count = get_theme_mod('subdirectory_count', 0);

    // 2つ目以降の設定を動的に追加
    for ($i = 2; $i <= min($subdirectory_count, 10); $i++) {
        // サブディレクトリパス
        $wp_customize->add_setting("subdirectory_path_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_path_{$i}", array(
            'label'       => sprintf(__('サブディレクトリ %d のパス', 'backbone-seo-llmo'), $i),
            'section'     => 'subdirectory_logos',
            'type'        => 'text',
            'description' => __('例: /media, /blog, /shop など（スラッシュで始める）', 'backbone-seo-llmo'),
            'priority'    => ($i * 10),
        ));

        // サイトタイトル
        $wp_customize->add_setting("subdirectory_title_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_title_{$i}", array(
            'label'       => sprintf(__('サブディレクトリ %d のサイトタイトル', 'backbone-seo-llmo'), $i),
            'section'     => 'subdirectory_logos',
            'type'        => 'text',
            'description' => __('空欄の場合はデフォルトのサイトタイトルを使用', 'backbone-seo-llmo'),
            'priority'    => ($i * 10 + 1),
        ));

        // ロゴ画像
        $wp_customize->add_setting("subdirectory_logo_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, "subdirectory_logo_{$i}", array(
            'label'       => sprintf(__('サブディレクトリ %d のロゴ', 'backbone-seo-llmo'), $i),
            'section'     => 'subdirectory_logos',
            'mime_type'   => 'image',
            'priority'    => ($i * 10 + 2),
        )));

        // ホームURL（オプション）
        $wp_customize->add_setting("subdirectory_home_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control("subdirectory_home_{$i}", array(
            'label'       => sprintf(__('サブディレクトリ %d のホームURL', 'backbone-seo-llmo'), $i),
            'section'     => 'subdirectory_logos',
            'type'        => 'url',
            'description' => __('ロゴクリック時のリンク先（空欄の場合は指定サブディレクトリがホームになります）', 'backbone-seo-llmo'),
            'priority'    => ($i * 10 + 3),
        ));

        // ヘッダーメッセージ
        $wp_customize->add_setting("subdirectory_header_message_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ));

        $wp_customize->add_control("subdirectory_header_message_{$i}", array(
            'label'       => sprintf(__('サブディレクトリ %d のヘッダーメッセージ', 'backbone-seo-llmo'), $i),
            'section'     => 'subdirectory_logos',
            'type'        => 'textarea',
            'description' => __('ヘッダーに表示するメッセージ（HTMLタグ使用可）', 'backbone-seo-llmo'),
            'priority'    => ($i * 10 + 4),
        ));

        // フッターメッセージ
        $wp_customize->add_setting("subdirectory_footer_message_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ));

        $wp_customize->add_control("subdirectory_footer_message_{$i}", array(
            'label'       => sprintf(__('サブディレクトリ %d のフッターメッセージ', 'backbone-seo-llmo'), $i),
            'section'     => 'subdirectory_logos',
            'type'        => 'textarea',
            'description' => __('フッターに表示するメッセージ（HTMLタグ使用可）', 'backbone-seo-llmo'),
            'priority'    => ($i * 10 + 5),
        ));

        // 削除ボタン
        $wp_customize->add_setting("subdirectory_delete_{$i}", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_delete_{$i}", array(
            'section'     => 'subdirectory_logos',
            'type'        => 'hidden',
            'description' => '<button type="button" class="button button-link-delete" onclick="backboneDeleteSubdirectory(' . $i . ')" style="color:#d63638;margin-top:10px;">このサブディレクトリ設定を削除</button>',
            'priority'    => ($i * 10 + 8),
        ));

        // 区切り線
        $wp_customize->add_setting("subdirectory_separator_{$i}", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("subdirectory_separator_{$i}", array(
            'section'     => 'subdirectory_logos',
            'type'        => 'hidden',
            'description' => '<hr style="margin: 20px 0;">',
            'priority'    => ($i * 10 + 9),
        ));
    }
}, 20);

/**
 * 現在のURLに基づいて適切なサブディレクトリ設定を取得
 */
function backbone_get_subdirectory_settings() {
    static $processing = false;

    // 無限ループを防ぐ
    if ($processing) {
        return array(
            'logo_id' => get_theme_mod('custom_logo'),
            'home_url' => home_url('/'),
            'site_title' => get_option('blogname'),
            'header_message' => get_theme_mod('header_message', ''),
            'footer_message' => get_theme_mod('footer_message', ''),
            'is_subdirectory' => false
        );
    }

    $processing = true;

    $current_url = $_SERVER['REQUEST_URI'];

    // WordPressのインストールディレクトリを考慮（デザイン設定と同じロジックを使用）
    // wp-json, wp-admin, wp-content などのWordPressディレクトリを検出して、その前の部分を除去
    $current_path = $current_url;
    if (preg_match('#^(.*?)(/wp-json/|/wp-admin/|/wp-content/|/wp-includes/)#', $current_url, $matches)) {
        $wp_base = $matches[1]; // /campany など
        if (!empty($wp_base)) {
            // WordPressのベースディレクトリを除去
            $current_path = substr($current_url, strlen($wp_base));
        }
    } else {
        // 通常のページの場合、最初のディレクトリがWordPressのインストールディレクトリの可能性を考慮
        // /campany/seo-note/... のような構造の場合
        if (preg_match('#^/[^/]+(/.*)?$#', $current_url, $matches)) {
            // 最初のディレクトリを一時的に除去してテスト
            $test_path = isset($matches[1]) ? $matches[1] : '/';

            // 保存されているサブディレクトリと照合してみる
            $subdirectory_count_test = get_theme_mod('subdirectory_count', 1);
            for ($j = 1; $j <= min($subdirectory_count_test, 10); $j++) {
                $subdirectory_path_test = get_theme_mod("subdirectory_path_{$j}");
                if (!empty($subdirectory_path_test)) {
                    $normalized = '/' . trim($subdirectory_path_test, '/');
                    // テストパスがサブディレクトリ設定にマッチするか確認
                    if (strpos($test_path, $normalized) === 0) {
                        // マッチした場合、このパスを使用
                        $current_path = $test_path;
                        break;
                    }
                }
            }
        }
    }

    $current_path = parse_url($current_path, PHP_URL_PATH);

    // デフォルト設定（get_option を使用して無限ループを回避）
    $default_settings = array(
        'logo_id' => get_theme_mod('custom_logo'),
        'home_url' => home_url('/'),
        'site_title' => get_option('blogname'),
        'header_message' => get_theme_mod('header_message', ''),
        'footer_message' => get_theme_mod('footer_message', ''),
        'is_subdirectory' => false
    );

    // 保存されている設定数を取得
    $subdirectory_count = get_theme_mod('subdirectory_count', 0);

    // サブディレクトリ設定をチェック
    for ($i = 1; $i <= min($subdirectory_count, 10); $i++) {
        $subdirectory_path = get_theme_mod("subdirectory_path_{$i}");

        if ($subdirectory_path) {
            // スラッシュの正規化
            $subdirectory_path = '/' . trim($subdirectory_path, '/');

            // パスが一致するかチェック（前方一致）
            if ($current_path !== null && strpos($current_path, $subdirectory_path) === 0) {
                // ロゴ設定
                $subdirectory_logo = get_theme_mod("subdirectory_logo_{$i}");
                $logo_id = $subdirectory_logo ? $subdirectory_logo : $default_settings['logo_id'];

                // ホームURL設定
                $home_url = get_theme_mod("subdirectory_home_{$i}");
                if (empty($home_url)) {
                    $home_url = home_url($subdirectory_path);
                }

                // サイトタイトル設定
                $site_title = get_theme_mod("subdirectory_title_{$i}");
                if (empty($site_title)) {
                    $site_title = $default_settings['site_title'];
                }

                // ヘッダーメッセージ設定
                $header_message = get_theme_mod("subdirectory_header_message_{$i}");
                if (empty($header_message)) {
                    $header_message = $default_settings['header_message'];
                }

                // フッターメッセージ設定
                $footer_message = get_theme_mod("subdirectory_footer_message_{$i}");
                if (empty($footer_message)) {
                    $footer_message = $default_settings['footer_message'];
                }

                $processing = false;
                return array(
                    'logo_id' => $logo_id,
                    'home_url' => $home_url,
                    'site_title' => $site_title,
                    'header_message' => $header_message,
                    'footer_message' => $footer_message,
                    'is_subdirectory' => true,
                    'directory_path' => $subdirectory_path
                );
            }
        }
    }

    $processing = false;
    return $default_settings;
}

/**
 * 現在のURLに基づいて適切なロゴ設定を取得（後方互換性のために残す）
 */
function backbone_get_subdirectory_logo_settings() {
    $settings = backbone_get_subdirectory_settings();
    // すべての設定を返す
    return $settings;
}

/**
 * カスタムロゴを表示（サブディレクトリ対応版）
 */
function backbone_display_custom_logo() {
    $settings = backbone_get_subdirectory_logo_settings();
    $logo_id = $settings['logo_id'];
    $home_url = $settings['home_url'];

    if ($logo_id) {
        $logo = wp_get_attachment_image($logo_id, 'full', false, array(
            'class' => 'custom-logo',
            'itemprop' => 'logo',
        ));

        if ($logo) {
            $html = sprintf(
                '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
                esc_url($home_url),
                $logo
            );

            // フィルターフックを追加
            echo apply_filters('backbone_subdirectory_logo_html', $html, $logo_id, $home_url, $settings);
            return true;
        }
    }

    return false;
}

/**
 * サブディレクトリ設定に基づいてサイトタイトルを取得
 */
function backbone_get_site_title() {
    $settings = backbone_get_subdirectory_logo_settings();
    return $settings['site_title'];
}

/**
 * サブディレクトリ設定に基づいてヘッダーメッセージを取得
 */
function backbone_get_header_message() {
    $settings = backbone_get_subdirectory_logo_settings();
    return $settings['header_message'];
}

/**
 * サブディレクトリ設定に基づいてフッターメッセージを取得
 */
function backbone_get_footer_message() {
    $settings = backbone_get_subdirectory_logo_settings();
    return $settings['footer_message'];
}

/**
 * サブディレクトリ設定に基づいてタグラインを取得
 */
function backbone_get_tagline() {
    $settings = backbone_get_subdirectory_logo_settings();

    // サブディレクトリ設定にtaglineがあればそれを返す
    if (isset($settings['tagline']) && !empty($settings['tagline'])) {
        return $settings['tagline'];
    }

    // なければWordPressのデフォルトキャッチフレーズを返す
    return get_bloginfo('description');
}

/**
 * HTMLのtitleタグにサブディレクトリ設定を反映
 */
add_filter('document_title_parts', function($title_parts) {
    $settings = backbone_get_subdirectory_settings();

    // タイトル部分が空の場合、適切なタイトルを設定
    if (!isset($title_parts['title']) || empty($title_parts['title'])) {
        if (is_page()) {
            // 固定ページの場合
            $title_parts['title'] = get_the_title();
        } elseif (is_single()) {
            // 投稿の場合
            $title_parts['title'] = get_the_title();
        } elseif (is_category()) {
            // カテゴリーアーカイブ
            $title_parts['title'] = single_cat_title('', false);
        } elseif (is_tag()) {
            // タグアーカイブ
            $title_parts['title'] = single_tag_title('', false);
        } elseif (is_author()) {
            // 著者アーカイブ
            $title_parts['title'] = get_the_author();
        } elseif (is_year()) {
            // 年別アーカイブ
            $title_parts['title'] = get_the_date('Y年');
        } elseif (is_month()) {
            // 月別アーカイブ
            $title_parts['title'] = get_the_date('Y年n月');
        } elseif (is_day()) {
            // 日別アーカイブ
            $title_parts['title'] = get_the_date('Y年n月j日');
        } elseif (is_post_type_archive()) {
            // カスタム投稿タイプアーカイブ
            $title_parts['title'] = post_type_archive_title('', false);
        } elseif (is_search()) {
            // 検索結果
            $title_parts['title'] = '「' . get_search_query() . '」の検索結果';
        } elseif (is_404()) {
            // 404ページ
            $title_parts['title'] = 'ページが見つかりません';
        } elseif (is_archive()) {
            // その他のアーカイブページ
            // get_the_archive_title()から適切にタイトルを取得
            $archive_title = get_the_archive_title();
            // WordPressが追加する "アーカイブ:" などのプレフィックスを削除
            // 日本語と英語のプレフィックスに対応
            $archive_title = preg_replace('/^(アーカイブ|Archive|カテゴリー|Category|タグ|Tag):\s*/iu', '', $archive_title);
            $title_parts['title'] = $archive_title ?: __('Archive', 'backbone-seo-llmo');
        }

        // それでもタイトルが空の場合、H1から取得を試みる
        if (empty($title_parts['title'])) {
            // クエリから最初の投稿のタイトルを取得
            if (have_posts()) {
                $title_parts['title'] = 'ページ';
            }
        }
    }

    // サブディレクトリの設定がある場合はサイト名部分を置き換える
    if ($settings['is_subdirectory'] && !empty($settings['site_title'])) {
        // サイト名部分のみを置き換え（個別ページタイトルは維持）
        if (isset($title_parts['site'])) {
            $title_parts['site'] = $settings['site_title'];
        }
    }

    return $title_parts;
}, 10);

/**
 * bloginfo('name')の出力をフィルター
 */
add_filter('bloginfo', function($output, $show) {
    // nameパラメータの場合のみ処理
    if ($show === 'name') {
        // 現在のURLを直接チェック
        $current_url = $_SERVER['REQUEST_URI'];
        $current_path = parse_url($current_url, PHP_URL_PATH);

        // current_pathがnullの場合は早期リターン
        if ($current_path === null || $current_path === false) {
            return $output;
        }

        // 保存されている設定数を取得
        $subdirectory_count = get_theme_mod('subdirectory_count', 1);

        // サブディレクトリ設定をチェック
        for ($i = 1; $i <= min($subdirectory_count, 10); $i++) {
            $subdirectory_path = get_theme_mod("subdirectory_path_{$i}");
            $subdirectory_title = get_theme_mod("subdirectory_title_{$i}");

            if ($subdirectory_path && $subdirectory_title) {
                // パスが一致するかチェック（前方一致）
                if (strpos($current_path, $subdirectory_path) === 0) {
                    return $subdirectory_title;
                }
            }
        }
    }

    return $output;
}, 10, 2);