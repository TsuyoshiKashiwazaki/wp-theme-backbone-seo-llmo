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
 * スクリプトとスタイルの連結を無効化
 * ERR_INCOMPLETE_CHUNKED_ENCODING エラー対策
 * load-styles.php と load-scripts.php による大量ファイルの結合時に
 * チャンク転送エラーが発生する問題を回避します
 */
if (!defined('CONCATENATE_SCRIPTS')) {
    define('CONCATENATE_SCRIPTS', false);
}
if (!defined('CONCATENATE_STYLES')) {
    define('CONCATENATE_STYLES', false);
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
    'utilities/hero-image-utilities.php', // メインビジュアル関連の関数
    'theme-setup.php',          // テーマ基本設定
    'widgets.php',              // ウィジェット関連
    'widget-working-solution.php',  // ウィジェット実用版
    'customizer/index.php',     // カスタマイザー設定（utilities.phpの関数を使用）
    'color-file-storage.php',   // ファイルベースカラー保存
    'css-output.php',           // CSS出力関数（utilities.phpの関数を使用）
    'custom-js-output.php',     // カスタムJS出力（カスタマイザー設定を使用）
    'custom-css-output.php',    // カスタムCSS出力（カスタマイザー設定を使用）
    'admin-pages.php',          // 管理画面設定
    'rest-api-fix.php',         // REST API JSONエラー修正
    'meta-boxes/hero-image-meta.php', // メインビジュアルのメタボックス
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
    // フロントエンドキャッシュバスティング設定を取得
    $cache_busting_frontend = get_theme_mod('enable_cache_busting_frontend', false);
    $version = $cache_busting_frontend ? current_time('YmdHis') : '1.0.0';

    wp_enqueue_style(
        'typography-responsive',
        get_template_directory_uri() . '/css/typography-responsive.css',
        array('style'),
        $version
    );
}
add_action('wp_enqueue_scripts', 'backbone_enqueue_responsive_typography', 25);

/**
 * フロントページセクション用CSSの追加
 */
function backbone_enqueue_front_page_sections() {
    // フロントエンドキャッシュバスティング設定を取得
    $cache_busting_frontend = get_theme_mod('enable_cache_busting_frontend', false);
    $version = $cache_busting_frontend ? current_time('YmdHis') : '1.0.1';

    wp_enqueue_style(
        'front-page-sections',
        get_template_directory_uri() . '/css/front-page-sections.css',
        array('seo-optimus-style'),
        $version
    );
}
add_action('wp_enqueue_scripts', 'backbone_enqueue_front_page_sections', 26);

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
 * customize_controls_enqueue_scripts フック内で実行されるため、
 * コントロールパネル側でのみ動作する
 */
function backbone_enqueue_customizer_modules() {
    // is_customize_preview() チェックは不要
    // このフック自体がカスタマイザーコントロール内でのみ実行される

    // 管理画面キャッシュバスティング設定を取得
    $cache_busting_admin = get_theme_mod('enable_cache_busting_admin', false);
    $version_admin = $cache_busting_admin ? current_time('YmdHis') : '1.0.0';

    // モジュールを順番に読み込み（依存関係を考慮）
    wp_enqueue_script(
        'customizer-utils',
        get_template_directory_uri() . '/js/customizer-utils.js',
        array('jquery'),
        $version_admin,
        true
    );

    wp_enqueue_script(
        'customizer-storage',
        get_template_directory_uri() . '/js/customizer-storage.js',
        array('jquery', 'customizer-utils'),
        $version_admin,
        true
    );

    wp_enqueue_script(
        'customizer-preview',
        get_template_directory_uri() . '/js/customizer-preview.js',
        array('jquery', 'customize-preview', 'customizer-utils'),
        $version_admin,
        true
    );

    wp_enqueue_script(
        'customizer-themes',
        get_template_directory_uri() . '/js/customizer-themes.js',
        array('jquery', 'customize-controls', 'customizer-utils'),
        $version_admin,
        true
    );

    wp_enqueue_script(
        'customizer-ui',
        get_template_directory_uri() . '/js/customizer-ui.js',
        array('jquery', 'customize-controls', 'customizer-utils', 'customizer-storage', 'customizer-preview', 'customizer-themes'),
        $version_admin,
        true
    );

    // メインコントロールファイル（最後に読み込み）
    wp_enqueue_script(
        'customizer-controls-main',
        get_template_directory_uri() . '/js/customizer-controls.js',
        array('jquery', 'customize-controls', 'customizer-utils', 'customizer-storage', 'customizer-preview', 'customizer-themes', 'customizer-ui'),
        $version_admin,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'backbone_enqueue_customizer_modules');

/**
 * 誤ったリダイレクトのみを防ぐ（正常なリダイレクトは許可）
 */
function backbone_fix_archive_pagination_redirect($redirect_url, $requested_url) {
    // 新しいページネーション形式 /page-2/ が使われている場合、リダイレクトをブロック
    if (strpos($requested_url, '/page-') !== false) {
        // /page-2/ から /page-2/page/2/ へのリダイレクトをブロック
        if ($redirect_url && preg_match('#/page-\d+/page/\d+/#', $redirect_url)) {
            return false;
        }
        // /page-2/ 形式のURLはそのまま許可（リダイレクトしない）
        return false;
    }

    // ページネーションURLからページネーションURLへのリダイレクトの場合、
    // リクエストURLとリダイレクト先URLが大きく異なる場合のみブロック
    if (strpos($requested_url, '/page/') !== false && $redirect_url) {
        // リクエストURLのベースパスを取得
        $requested_base = preg_replace('#/page/\d+/?#', '', $requested_url);
        $redirect_base = preg_replace('#/page/\d+/?#', '', $redirect_url);

        // ベースパスが異なる場合はブロック（異なるアーカイブへのリダイレクト）
        if ($requested_base !== $redirect_base) {
            return false;
        }
    }

    return $redirect_url;
}
add_filter('redirect_canonical', 'backbone_fix_archive_pagination_redirect', 10, 2);

/**
 * カスタムページネーションルールを追加
 */
function backbone_add_custom_pagination_rules() {
    // すべての登録済みカスタム投稿タイプを取得
    $post_types = get_post_types(array('_builtin' => false, 'public' => true), 'objects');

    foreach ($post_types as $post_type) {
        if (!empty($post_type->rewrite) && isset($post_type->rewrite['slug'])) {
            $slug = $post_type->rewrite['slug'];

            // カスタム投稿タイプアーカイブのページネーション（単一階層）
            // 例: /seo-note/page-2/
            add_rewrite_rule(
                $slug . '/page-([0-9]{1,})/?$',
                'index.php?post_type=' . $post_type->name . '&paged=$matches[1]',
                'top'
            );

            // カスタム投稿タイプアーカイブのページネーション（2階層）
            // 例: /seo-note/report/page-2/ (reportは子の投稿タイプ)
            add_rewrite_rule(
                $slug . '/([^/]+)/page-([0-9]{1,})/?$',
                'index.php?post_type=$matches[1]&paged=$matches[2]',
                'top'
            );
        }
    }

    // タグアーカイブのページネーション
    add_rewrite_rule(
        'tag/([^/]+)/page-([0-9]{1,})/?$',
        'index.php?tag=$matches[1]&paged=$matches[2]',
        'top'
    );

    // カテゴリーアーカイブのページネーション（カテゴリーベースあり）
    add_rewrite_rule(
        'category/(.+?)/page-([0-9]{1,})/?$',
        'index.php?category_name=$matches[1]&paged=$matches[2]',
        'top'
    );

    // 日付アーカイブのページネーション
    add_rewrite_rule(
        '([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page-([0-9]{1,})/?$',
        'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]',
        'top'
    );

    add_rewrite_rule(
        '([0-9]{4})/([0-9]{1,2})/page-([0-9]{1,})/?$',
        'index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]',
        'top'
    );

    add_rewrite_rule(
        '([0-9]{4})/page-([0-9]{1,})/?$',
        'index.php?year=$matches[1]&paged=$matches[2]',
        'top'
    );

    // 作者アーカイブのページネーション
    add_rewrite_rule(
        'author/([^/]+)/page-([0-9]{1,})/?$',
        'index.php?author_name=$matches[1]&paged=$matches[2]',
        'top'
    );

    // カテゴリーアーカイブのページネーション（カテゴリーベースなし）
    // ※汎用的なルールなので最後に配置
    add_rewrite_rule(
        '([^/]+)/page-([0-9]{1,})/?$',
        'index.php?category_name=$matches[1]&paged=$matches[2]',
        'top'
    );
}
add_action('init', 'backbone_add_custom_pagination_rules');

/**
 * カスタムクエリ変数を登録
 */
function backbone_add_query_vars($vars) {
    $vars[] = 'old_pagination';
    return $vars;
}
add_filter('query_vars', 'backbone_add_query_vars');

/**
 * 旧ページネーション形式から新形式へリダイレクト
 */
function backbone_redirect_old_pagination() {
    if (get_query_var('old_pagination') == '1' && get_query_var('paged')) {
        $paged = get_query_var('paged');
        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $new_url = preg_replace('#/page/(\d+)/?#', '/page-$1/', $current_url);

        if ($new_url !== $current_url) {
            wp_redirect($new_url, 301);
            exit;
        }
    }
}
add_action('template_redirect', 'backbone_redirect_old_pagination');

/**
 * Rewriteルールを一度だけフラッシュ（初回のみ実行）
 */
function backbone_flush_rewrite_rules_once() {
    $flushed = get_option('backbone_rewrite_flushed_v18');
    if (!$flushed) {
        flush_rewrite_rules(false);
        update_option('backbone_rewrite_flushed_v18', true);
    }
}
add_action('init', 'backbone_flush_rewrite_rules_once', 20);

/**
 * アーカイブページのメインクエリに並び順設定を適用
 */
function backbone_modify_archive_query($query) {
    // 管理画面またはメインクエリでない場合は何もしない
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // アーカイブページのみ対象
    if ($query->is_archive() || $query->is_home()) {
        $orderby = get_theme_mod('archive_orderby', 'date');

        if ($orderby && in_array($orderby, array('date', 'modified', 'rand'))) {
            $query->set('orderby', $orderby);

            // randの場合はorder指定不要、それ以外はDESC
            if ($orderby !== 'rand') {
                $query->set('order', 'DESC');
            }
        }
    }
}
add_action('pre_get_posts', 'backbone_modify_archive_query');

/**
 * フロントページのコンテンツ最大幅を出力
 */
function backbone_output_front_page_content_width() {
    $max_width = get_theme_mod('backbone_front_content_max_width', 1200);

    // 明示的に0かどうかチェック
    if ($max_width === 0 || $max_width === '0' || (is_numeric($max_width) && intval($max_width) === 0)) {
        $custom_css = "
        .hero-description,
        .posts-list-section,
        .pickup-section,
        .services-section,
        .free-content-section {
            max-width: none !important;
        }
        ";
        wp_add_inline_style('seo-optimus-style', $custom_css);
    }
    // 1200以外の値の場合は指定幅を適用
    elseif ($max_width && $max_width != 1200) {
        $custom_css = "
        .hero-description,
        .posts-list-section,
        .pickup-section,
        .services-section,
        .free-content-section {
            max-width: {$max_width}px !important;
        }
        ";
        wp_add_inline_style('seo-optimus-style', $custom_css);
    }
}
add_action('wp_enqueue_scripts', 'backbone_output_front_page_content_width');
