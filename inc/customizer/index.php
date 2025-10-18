<?php
/**
 * カスタマイザーのメイン登録ファイル
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

// 必要なファイルを読み込み
require_once get_template_directory() . '/inc/customizer/utilities.php';
require_once get_template_directory() . '/inc/customizer/color-utilities.php';
require_once get_template_directory() . '/inc/customizer/design-settings.php';
require_once get_template_directory() . '/inc/customizer/custom-color-theme.php';
// require_once get_template_directory() . '/inc/customizer/color-settings.php'; // カスタムカラー機能削除
// require_once get_template_directory() . '/inc/customizer/typography-settings.php';
require_once get_template_directory() . '/inc/customizer/layout-settings.php';
require_once get_template_directory() . '/inc/customizer/subdirectory-logos.php';
require_once get_template_directory() . '/inc/customizer/subdirectory-design-settings.php';
require_once get_template_directory() . '/inc/customizer/hero-image-settings.php';
require_once get_template_directory() . '/inc/customizer/archive-settings.php';
// require_once get_template_directory() . '/inc/ajax/save-color-theme.php'; // カスタムカラー機能削除

/**
 * カスタマイザーの設定
 */
function backbone_customize_register($wp_customize) {
    // 各設定セクションを追加
    backbone_add_design_settings($wp_customize);
    backbone_add_custom_color_theme_settings($wp_customize);
    // backbone_add_color_settings($wp_customize); // カスタムカラー機能削除
    // backbone_add_typography_settings($wp_customize);
    backbone_add_layout_settings($wp_customize);
    backbone_add_subdirectory_logo_settings($wp_customize);
    backbone_add_subdirectory_design_settings($wp_customize);
    backbone_add_hero_image_settings($wp_customize);
    backbone_add_archive_settings($wp_customize);

    // 既存の「サイト基本情報」セクションにカスタム設定を追加
    // セクションID: 'title_tagline' がWordPressの標準「サイト基本情報」セクション

    // ヘッダーメッセージ
    $wp_customize->add_setting('header_message', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('header_message', array(
        'label'       => __('ヘッダーメッセージ', 'kashiwazaki-searchcraft'),
        'section'     => 'title_tagline',
        'type'        => 'textarea',
        'description' => __('ヘッダーに表示するメッセージを入力してください。HTMLタグも使用できます。', 'kashiwazaki-searchcraft'),
        'input_attrs' => array(
            'rows' => 3,
        ),
    ));

    // フッターメッセージ
    $wp_customize->add_setting('footer_message', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('footer_message', array(
        'label'       => __('フッターメッセージ', 'kashiwazaki-searchcraft'),
        'section'     => 'title_tagline',
        'type'        => 'textarea',
        'description' => __('フッターに表示するメッセージを入力してください。HTMLタグも使用できます。', 'kashiwazaki-searchcraft'),
        'input_attrs' => array(
            'rows' => 3,
        ),
    ));

    // カスタムロゴの説明
    $wp_customize->add_setting('custom_logo_description', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('custom_logo_description', array(
        'label'       => __('ロゴ設定の説明', 'kashiwazaki-searchcraft'),
        'section'     => 'title_tagline',
        'type'        => 'hidden',
        'description' => __('ロゴ画像をアップロードするか、テキストロゴを設定できます。', 'kashiwazaki-searchcraft'),
    ));

    // サイトアイコンの説明
    $wp_customize->add_setting('site_icon_description', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('site_icon_description', array(
        'label'       => __('サイトアイコンの説明', 'kashiwazaki-searchcraft'),
        'section'     => 'title_tagline',
        'type'        => 'hidden',
        'description' => __('ブラウザのタブやブックマークに表示されるアイコンを設定できます。推奨サイズ：512x512px', 'kashiwazaki-searchcraft'),
    ));

    // 開発者向けセクション追加
    $wp_customize->add_section('developer_settings', array(
        'title'    => __('開発者設定', 'kashiwazaki-searchcraft'),
        'priority' => 160,
    ));

    // キャッシュバスティング設定
    $wp_customize->add_setting('enable_cache_busting', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('enable_cache_busting', array(
        'label'       => __('キャッシュバスティング', 'kashiwazaki-searchcraft'),
        'section'     => 'developer_settings',
        'type'        => 'checkbox',
        'description' => __('CSS/JSファイルにタイムスタンプを付けてキャッシュを無効化します。開発時に有効にしてください。', 'kashiwazaki-searchcraft'),
    ));

    // フロントページのタイトル表示設定をホームページ設定セクションに追加
    $wp_customize->add_setting('front_page_show_title', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('front_page_show_title', array(
        'label'       => __('フロントページでタイトルを表示', 'backbone-seo-llmo'),
        'section'     => 'static_front_page',
        'type'        => 'checkbox',
        'description' => __('フロントページ（ホームページ）でページタイトルを表示するかどうかを設定します。', 'backbone-seo-llmo'),
    ));

}

// カスタマイザーに登録
add_action('customize_register', 'backbone_customize_register');


/**
 * カスタマイザーのプレビュー用JavaScript
 */
function backbone_customize_preview_js() {
    wp_enqueue_script(
        'seo-optimus-customizer',
        get_template_directory_uri() . '/js/customizer-controls.js',
        array('customize-preview'),
        '1.0.0',
        true
    );
}
add_action('customize_preview_init', 'backbone_customize_preview_js');

/**
 * カスタマイザーのコントロール用JavaScript
 */
function backbone_customize_controls_js() {
    // キャッシュバスティング用のタイムスタンプを生成
    $cache_bust = current_time('timestamp');
    $cache_bust .= '_' . filemtime(__FILE__); // ファイルの更新時刻も追加
    $cache_bust .= '_' . count(glob(get_template_directory() . '/inc/color-themes/*.json')); // テーマファイル数も追加

    // WordPress Color Pickerのスタイルとスクリプトを読み込み
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    // テーマ詳細表示用のカスタムCSSを追加
    wp_add_inline_style('wp-color-picker', '
        .color-picker-input {
            max-width: 100px;
            font-family: monospace;
            font-size: 11px;
        }
        .color-preview {
            cursor: pointer;
            border: 1px solid var(--border-color);
            display: inline-block;
        }
        .color-preview:hover {
            border-color: var(--accent-color);
        }
        .wp-color-picker-container {
            position: absolute;
            z-index: 1000;
        }
    ');

    wp_enqueue_script(
        'seo-optimus-customizer-controls',
        get_template_directory_uri() . '/js/customizer-controls.js',
        array('customize-controls', 'jquery', 'jquery-core', 'jquery-migrate', 'wp-color-picker'),
        $cache_bust,
        true
    );

    // テーマデータを確実に読み込む
    $theme_data = array();

    // 直接JSONファイルを読み込む（より確実）
    $theme_dir = get_template_directory() . '/inc/color-themes/';
    $json_files = glob($theme_dir . '*.json');

    if (!empty($json_files)) {
        foreach ($json_files as $json_file) {
            $theme_id = basename($json_file, '.json');
            $content = file_get_contents($json_file);

            if ($content !== false) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $theme_data[$theme_id] = $decoded;
                }
            }
        }
    }

    // テーマデータが空の場合のフォールバック
    if (empty($theme_data)) {
        $theme_data = array(
            'tk-theme-andromeda' => array(
                'name' => 'Andromeda Theme',
                'description' => 'Fallback theme for testing',
                'colors' => array(
                    'primary_color' => 'var(--primary-color)',
                    'secondary_color' => 'var(--secondary-color)',
                    'accent_color' => 'var(--accent-color)'
                ),
                'created' => '2025-08-21',
                'updated' => '2025-08-21'
            )
        );
    }


    // テーマデータを wp_localize_script で安全に渡す
    wp_localize_script('seo-optimus-customizer-controls', 'seoOptimusThemeData', array(
        'themes' => $theme_data,
        'themeCount' => count($theme_data),
        'debug' => WP_DEBUG
    ));

    wp_add_inline_script('seo-optimus-customizer-controls', '
        window.seoOptimusJQuery = jQuery;
        window.seoOptimus$ = jQuery;
        
        try {
            var rawThemeData = seoOptimusThemeData.themes || {};
            window.seoOptimusThemes = rawThemeData;
        } catch (e) {
            window.seoOptimusThemes = {};
        }
    ', 'before');
}
add_action('customize_controls_enqueue_scripts', 'backbone_customize_controls_js');


/**
 * カスタマイザーのスタイル
 */
function backbone_customize_styles() {
    $customizer_css_path = get_template_directory() . '/css/customizer.css';
    if (file_exists($customizer_css_path)) {
        wp_enqueue_style(
            'seo-optimus-customizer',
            get_template_directory_uri() . '/css/customizer.css',
            array(),
            '1.0.0'
        );
    }
}
add_action('customize_controls_enqueue_scripts', 'backbone_customize_styles');




