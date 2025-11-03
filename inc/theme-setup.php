<?php
/**
 * テーマセットアップ・基本設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * テーマのセットアップ
 */
function backbone_setup() {
    // 翻訳ファイルの読み込み
    load_theme_textdomain('backbone-seo-llmo', get_template_directory() . '/languages');

    // フィードリンクを自動追加
    add_theme_support('automatic-feed-links');

    // title タグの自動出力
    add_theme_support('title-tag');

    // 投稿サムネイルのサポート
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(1200, 800, true);

    // カスタムロゴのサポート
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // HTML5のサポート
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // カスタムメニューの登録
    register_nav_menus(array(
        'primary' => __('メインメニュー', 'backbone-seo-llmo'),
        'footer'  => __('フッターメニュー', 'backbone-seo-llmo'),
    ));

    // エディタースタイルの有効化
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');

    // ワイドアライメントのサポート
    add_theme_support('align-wide');

    // レスポンシブ埋め込みのサポート
    add_theme_support('responsive-embeds');

    // ウィジェットブロックエディタのサポート
    add_theme_support('widgets-block-editor');

    // ブロックスタイルのサポート
    add_theme_support('wp-block-styles');

    // 外観ツールのサポート（ブロックエディタの詳細機能）
    add_theme_support('appearance-tools');
}
add_action('after_setup_theme', 'backbone_setup');

/**
 * WordPress標準の「色」セクションを削除
 */
function backbone_remove_colors_section($wp_customize) {
    $wp_customize->remove_section('colors');
}
add_action('customize_register', 'backbone_remove_colors_section', 15);

/**
 * スタイルとスクリプトの読み込み
 */
function backbone_scripts() {
    // フロントエンドキャッシュバスティング設定を取得
    $cache_busting_frontend = get_theme_mod('enable_cache_busting_frontend', false);

    // バージョン文字列を決定（キャッシュバスティングが有効なら現在時刻、無効なら固定バージョン）
    $version = $cache_busting_frontend ? current_time('YmdHis') : '1.0.0';


    // メインスタイルシート（キャッシュバスティング対応）
    wp_enqueue_style('seo-optimus-style', get_stylesheet_uri(), array(), $version);

    // 分割されたレイアウトCSSファイルの読み込み（キャッシュバスティング対応）
    wp_enqueue_style('seo-optimus-layout-base', get_template_directory_uri() . '/css/layout-base.css', array('seo-optimus-style'), $version);
    wp_enqueue_style('seo-optimus-layout-single-column', get_template_directory_uri() . '/css/layout-single-column.css', array('seo-optimus-layout-base'), $version);
    wp_enqueue_style('seo-optimus-layout-full-width', get_template_directory_uri() . '/css/layout-full-width.css', array('seo-optimus-layout-base'), $version);
    wp_enqueue_style('seo-optimus-layout-two-columns', get_template_directory_uri() . '/css/layout-two-columns.css', array('seo-optimus-layout-base'), $version);
    wp_enqueue_style('seo-optimus-layout-three-columns', get_template_directory_uri() . '/css/layout-three-columns.css', array('seo-optimus-layout-base'), $version);
    wp_enqueue_style('seo-optimus-layout-admin', get_template_directory_uri() . '/css/layout-admin.css', array('seo-optimus-layout-base'), $version);
    
    // 検索ポップアップのCSS（検索ボタンが有効の場合のみ）
    if (get_theme_mod('search_button_enabled', true)) {
        wp_enqueue_style('seo-optimus-search-popup', get_template_directory_uri() . '/css/search-popup.css', array('seo-optimus-style'), $version);
    }

    // メインビジュアル（ヒーローイメージ）のCSS
    wp_enqueue_style('hero-image-styles', get_template_directory_uri() . '/css/hero-image-styles.css', array('seo-optimus-style'), $version);

    // アーカイブページグリッドレイアウトのCSS
    wp_enqueue_style('archive-grid-styles', get_template_directory_uri() . '/css/archive-grid.css', array('seo-optimus-style'), $version);

    // WordPress コア jQuery を明示的に読み込み
    wp_enqueue_script('jquery-core');
    wp_enqueue_script('jquery-migrate');

    // 追加の jQuery ライブラリ
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-widget');
    wp_enqueue_script('jquery-ui-mouse');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script('jquery-ui-tabs');

    // JavaScriptファイル（キャッシュバスティング対応）
    wp_enqueue_script('seo-optimus-script', get_template_directory_uri() . '/js/theme.js', array('jquery', 'jquery-core', 'jquery-migrate'), $version, true);
    
    // 検索ポップアップのJavaScript（検索ボタンが有効の場合のみ）
    if (get_theme_mod('search_button_enabled', true)) {
        wp_enqueue_script('seo-optimus-search-popup', get_template_directory_uri() . '/js/search-popup-simple.js', array('jquery'), $version, true);
    }

    // jQueryの競合回避を削除（WordPress コア jQuery を使用）
    wp_add_inline_script('seo-optimus-script', '
        // WordPress コア jQuery を確実に使用
        window.seoOptimusJQuery = jQuery;
        window.seoOptimus$ = jQuery;
    ', 'after');

    // コメント返信スクリプト
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'backbone_scripts');

/**
 * フォールバックメニュー
 */
function backbone_fallback_menu() {
    echo '<ul id="primary-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('ホーム', 'seo-optimus-general') . '</a></li>';

    $pages = get_pages();
    foreach ($pages as $page) {
        echo '<li><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a></li>';
    }

    // 検索ボタンが有効の場合、メニューの最後に追加（メインレベルのみ）
    if (get_theme_mod('search_button_enabled', true)) {
        echo '<li class="menu-item menu-item-search menu-item-depth-0">
            <button class="search-toggle" aria-label="検索を開く" aria-expanded="false">
                <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </button>
        </li>';
    }

    echo '</ul>';
}

/**
 * テーマアクティベート時にパーマリンク構造を設定
 */
function backbone_set_default_permalink_structure() {
    // パーマリンク構造を /%category%/%postname%/ に設定
    update_option('permalink_structure', '/%category%/%postname%/');
    
    // リライトルールをフラッシュ
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'backbone_set_default_permalink_structure');

