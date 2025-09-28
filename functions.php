<?php
/**
 * Backbone Theme for SEO + LLMO Functions
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 機能別ファイルの読み込み
 * 注意：読み込み順序が重要です（依存関係があるため）
 */
$inc_files = array(
    'utilities/core-utilities.php',     // コアヘルパー関数・ユーティリティ（最初に読み込む）
    'utilities/layout-utilities.php',   // レイアウト関連の関数
    'utilities/typography-utilities.php', // タイポグラフィ関連の関数
    'utilities/decoration-utilities.php', // デコレーション関連の関数
    'utilities/design-utilities.php',     // デザインパターン関連の関数
    'utilities/color-utilities.php',      // カラーテーマ関連の関数
    'theme-setup.php',          // テーマ基本設定
    'widgets.php',              // ウィジェット関連
    'customizer/index.php',     // カスタマイザー設定（utilities.phpの関数を使用）
    'color-file-storage.php',   // ファイルベースカラー保存
    'css-output.php',           // CSS出力関数（utilities.phpの関数を使用）
    'admin-pages.php',          // 管理画面設定
    'rest-api-fix.php',         // REST API JSONエラー修正
);

foreach ($inc_files as $file) {
    $file_path = get_template_directory() . '/inc/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

/**
 * WordPressの標準的な背景色設定を無効化
 */
function backbone_remove_default_background_support() {
    remove_theme_support('custom-background');
}
add_action('after_setup_theme', 'backbone_remove_default_background_support', 20);

/**
 * レスポンシブタイポグラフィCSSの追加
 */
function backbone_enqueue_responsive_typography() {
    wp_enqueue_style(
        'typography-responsive',
        get_template_directory_uri() . '/css/typography-responsive.css',
        array('style'),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'backbone_enqueue_responsive_typography', 25);

/**
 * タイトルの区切り文字を変更（&#8211; → |）
 */
function backbone_change_title_separator($sep) {
    return ' | ';
}
add_filter('document_title_separator', 'backbone_change_title_separator');
add_filter('wp_title_separator', 'backbone_change_title_separator');

/**
 * 固定ページにタグを有効化
 */
function backbone_add_tags_to_pages() {
    register_taxonomy_for_object_type('post_tag', 'page');
}
add_action('init', 'backbone_add_tags_to_pages');

/**
 * 固定ページに抜粋欄を追加（meta descriptionとして使用可能）
 */
function backbone_add_excerpt_to_pages() {
    add_post_type_support('page', 'excerpt');
}
add_action('init', 'backbone_add_excerpt_to_pages');

/**
 * 固定ページの抜粋を強制的に有効化（別の方法）
 */
function backbone_page_excerpt_metabox() {
    add_meta_box(
        'postexcerpt',
        __('抜粋'),
        'post_excerpt_meta_box',
        'page',
        'normal',
        'core'
    );
}
add_action('add_meta_boxes', 'backbone_page_excerpt_metabox');

/**
 * ブロックエディタでも固定ページの抜粋を有効化
 */
function backbone_rest_api_page_excerpt() {
    register_rest_field('page', 'excerpt', array(
        'get_callback' => function($post) {
            return get_the_excerpt($post['id']);
        },
        'update_callback' => function($value, $post) {
            return wp_update_post(array(
                'ID' => $post->ID,
                'post_excerpt' => $value
            ));
        },
        'schema' => array(
            'type' => 'string',
            'context' => array('view', 'edit')
        )
    ));
}
add_action('rest_api_init', 'backbone_rest_api_page_excerpt');

/**
 * 固定ページをタグアーカイブに含める
 */
function backbone_include_pages_in_tag_archives($query) {
    if ($query->is_tag() && $query->is_main_query()) {
        $query->set('post_type', array('post', 'page'));
    }
}
add_filter('pre_get_posts', 'backbone_include_pages_in_tag_archives');

/**
 * カスタマイザーのJavaScriptモジュールを読み込み
 */
function backbone_enqueue_customizer_modules() {
    if (!is_customize_preview()) {
        return;
    }

    // モジュールを順番に読み込み（依存関係を考慮）
    wp_enqueue_script(
        'customizer-utils',
        get_template_directory_uri() . '/js/customizer-utils.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'customizer-storage',
        get_template_directory_uri() . '/js/customizer-storage.js',
        array('jquery', 'customizer-utils'),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'customizer-preview',
        get_template_directory_uri() . '/js/customizer-preview.js',
        array('jquery', 'customize-preview', 'customizer-utils'),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'customizer-themes',
        get_template_directory_uri() . '/js/customizer-themes.js',
        array('jquery', 'customize-controls', 'customizer-utils'),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'customizer-ui',
        get_template_directory_uri() . '/js/customizer-ui.js',
        array('jquery', 'customize-controls', 'customizer-utils', 'customizer-storage', 'customizer-preview', 'customizer-themes'),
        '1.0.0',
        true
    );

    // メインコントロールファイル（最後に読み込み）
    wp_enqueue_script(
        'customizer-controls-main',
        get_template_directory_uri() . '/js/customizer-controls.js',
        array('jquery', 'customize-controls', 'customizer-utils', 'customizer-storage', 'customizer-preview', 'customizer-themes', 'customizer-ui'),
        '1.0.0',
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'backbone_enqueue_customizer_modules');
