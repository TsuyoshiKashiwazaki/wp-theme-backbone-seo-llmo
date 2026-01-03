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
    'author-custom-urls.php',   // 著者カスタムURL設定
    'customizer/index.php',     // カスタマイザー設定（utilities.phpの関数を使用）
    'color-file-storage.php',   // ファイルベースカラー保存
    'css-output.php',           // CSS出力関数（utilities.phpの関数を使用）
    'custom-js-output.php',     // カスタムJS出力（カスタマイザー設定を使用）
    'custom-css-output.php',    // カスタムCSS出力（カスタマイザー設定を使用）
    'admin-pages.php',          // 管理画面設定
    'rest-api-fix.php',         // REST API JSONエラー修正
    'meta-boxes/hero-image-meta.php', // メインビジュアルのメタボックス
    'meta-boxes/custom-schema-meta.php', // カスタム構造化データのメタボックス
    'meta-boxes/layout-meta.php', // レイアウト設定のメタボックス
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

    wp_enqueue_style(
        'typography-responsive',
        get_template_directory_uri() . '/css/typography-responsive.css',
        array('seo-optimus-style'),
        backbone_get_file_version('/css/typography-responsive.css', $cache_busting_frontend)
    );
}
add_action('wp_enqueue_scripts', 'backbone_enqueue_responsive_typography', 25);

/**
 * フロントページセクション用CSSの追加
 */
function backbone_enqueue_front_page_sections() {
    // フロントエンドキャッシュバスティング設定を取得
    $cache_busting_frontend = get_theme_mod('enable_cache_busting_frontend', false);

    wp_enqueue_style(
        'front-page-sections',
        get_template_directory_uri() . '/css/front-page-sections.css',
        array('seo-optimus-style'),
        backbone_get_file_version('/css/front-page-sections.css', $cache_busting_frontend)
    );
}
add_action('wp_enqueue_scripts', 'backbone_enqueue_front_page_sections', 26);

/**
 * フロントページのヒーローセクションで使用される投稿のブロックコンテンツを
 * グローバルポストに一時的に追加し、has_block() チェックを通過させる
 *
 * これにより、どのプラグインのブロックでも汎用的にアセットが読み込まれる
 */
function backbone_setup_hero_block_context() {
    global $post, $backbone_hero_original_content;

    // フロントページでのみ実行
    if (!is_front_page()) {
        return;
    }

    // カスタムフロントページモードでない場合はスキップ
    if (get_theme_mod('backbone_front_page_mode', 'custom') !== 'custom') {
        return;
    }

    // 説明文のソースがページの場合のみ
    $description_source = get_theme_mod('backbone_front_description_source', 'manual');
    if ($description_source !== 'page') {
        return;
    }

    $source_page_id = get_theme_mod('backbone_front_description_page', 0);
    if (!$source_page_id) {
        return;
    }

    $source_post = get_post($source_page_id);
    if (!$source_post || $source_post->post_status !== 'publish') {
        return;
    }

    // ブロックが含まれている場合のみ処理
    if (!has_blocks($source_post->post_content)) {
        return;
    }

    // グローバルポストのコンテンツを一時的に拡張
    // これにより has_block() がソース投稿のブロックも検出する
    if ($post) {
        $backbone_hero_original_content = $post->post_content;
        $post->post_content .= "\n" . $source_post->post_content;
    } else {
        // グローバルポストがない場合は一時的なポストオブジェクトを作成
        $post = $source_post;
        $backbone_hero_original_content = null;
    }
}
// 他のプラグインより先に実行（優先度1）
add_action('wp_enqueue_scripts', 'backbone_setup_hero_block_context', 1);

/**
 * グローバルポストのコンテンツを元に戻す
 */
function backbone_restore_hero_block_context() {
    global $post, $backbone_hero_original_content;

    if (!is_front_page()) {
        return;
    }

    // 元のコンテンツがある場合は復元
    if ($post && $backbone_hero_original_content !== null) {
        $post->post_content = $backbone_hero_original_content;
        $backbone_hero_original_content = null;
    }
}
// 全てのプラグインのエンキュー後に実行（優先度9999）
add_action('wp_enqueue_scripts', 'backbone_restore_hero_block_context', 9999);

/**
 * タイトルの区切り文字を変更（&#8211; → |）
 */
function backbone_change_title_separator($sep) {
    return ' | ';
}
add_filter('document_title_separator', 'backbone_change_title_separator');
add_filter('wp_title_separator', 'backbone_change_title_separator');

/**
 * フロントページでソースページのタイトルを使用
 */
function backbone_front_page_source_title($title_parts) {
    // フロントページでのみ実行
    if (!is_front_page()) {
        return $title_parts;
    }

    // カスタムフロントページモードでない場合はスキップ
    if (get_theme_mod('backbone_front_page_mode', 'custom') !== 'custom') {
        return $title_parts;
    }

    // 説明文のソースがページでない場合はスキップ
    if (get_theme_mod('backbone_front_description_source', 'manual') !== 'page') {
        return $title_parts;
    }

    // オプションが無効な場合はスキップ
    if (!get_theme_mod('backbone_front_use_source_title', false)) {
        return $title_parts;
    }

    // ソースページを取得
    $source_page_id = get_theme_mod('backbone_front_description_page', 0);
    if (!$source_page_id) {
        return $title_parts;
    }

    $source_post = get_post($source_page_id);
    if (!$source_post || $source_post->post_status !== 'publish') {
        return $title_parts;
    }

    // タイトルをソースページのタイトルに変更
    $title_parts['title'] = $source_post->post_title;

    // tagline（キャッチフレーズ）をサイト名に置き換え
    if (isset($title_parts['tagline'])) {
        unset($title_parts['tagline']);
    }
    $title_parts['site'] = get_bloginfo('name');

    return $title_parts;
}
add_filter('document_title_parts', 'backbone_front_page_source_title', 5);

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
 * タクソノミーアーカイブに、全ての公開投稿タイプを含める
 * プラグインの設定不備でタクソノミーに正しく登録されていない投稿タイプも表示
 */
function backbone_include_all_post_types_in_taxonomy_archives($query) {
    if (!$query->is_main_query() || is_admin()) {
        return;
    }

    // タグアーカイブまたはカテゴリアーカイブ
    if ($query->is_tag() || $query->is_category()) {
        // 全ての公開投稿タイプを取得
        $post_types = get_post_types(array('public' => true), 'names');
        // attachment は除外
        unset($post_types['attachment']);
        $query->set('post_type', array_values($post_types));
    }
}
add_filter('pre_get_posts', 'backbone_include_all_post_types_in_taxonomy_archives');

/**
 * カスタマイザーのJavaScriptモジュールを読み込み
 * 注意: この関数は inc/customizer/index.php の backbone_customize_controls_js() で
 *       既に処理されているため、削除しました。
 *       重複登録を防ぐため、カスタマイザー関連のスクリプト読み込みは
 *       inc/customizer/index.php で一元管理します。
 */
// function backbone_enqueue_customizer_modules() {
//     // is_customize_preview() チェックは不要
//     // このフック自体がカスタマイザーコントロール内でのみ実行される
//
//     // 管理画面キャッシュバスティング設定を取得
//     $cache_busting_admin = get_theme_mod('enable_cache_busting_admin', false);
//     $version_admin = $cache_busting_admin ? current_time('YmdHis') : '1.0.0';
//
//     // モジュールを順番に読み込み（依存関係を考慮）
//     wp_enqueue_script(
//         'customizer-utils',
//         get_template_directory_uri() . '/js/customizer-utils.js',
//         array('jquery'),
//         $version_admin,
//         true
//     );
//
//     wp_enqueue_script(
//         'customizer-storage',
//         get_template_directory_uri() . '/js/customizer-storage.js',
//         array('jquery', 'customizer-utils'),
//         $version_admin,
//         true
//     );
//
//     wp_enqueue_script(
//         'customizer-preview',
//         get_template_directory_uri() . '/js/customizer-preview.js',
//         array('jquery', 'customize-preview', 'customizer-utils'),
//         $version_admin,
//         true
//     );
//
//     wp_enqueue_script(
//         'customizer-themes',
//         get_template_directory_uri() . '/js/customizer-themes.js',
//         array('jquery', 'customize-controls', 'customizer-utils'),
//         $version_admin,
//         true
//     );
//
//     wp_enqueue_script(
//         'customizer-ui',
//         get_template_directory_uri() . '/js/customizer-ui.js',
//         array('jquery', 'customize-controls', 'customizer-utils', 'customizer-storage', 'customizer-preview', 'customizer-themes'),
//         $version_admin,
//         true
//     );
//
//     // メインコントロールファイル（最後に読み込み）
//     wp_enqueue_script(
//         'customizer-controls-main',
//         get_template_directory_uri() . '/js/customizer-controls.js',
//         array('jquery', 'customize-controls', 'customizer-utils', 'customizer-storage', 'customizer-preview', 'customizer-themes', 'customizer-ui'),
//         $version_admin,
//         true
//     );
// }
// add_action('customize_controls_enqueue_scripts', 'backbone_enqueue_customizer_modules');

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
            // 一時的にコメントアウト - デバッグ用
            // add_rewrite_rule(
            //     $slug . '/(?!page-)([^/]+)/page-([0-9]{1,})/?$',
            //     'index.php?post_type=$matches[1]&paged=$matches[2]',
            //     'top'
            // );
        }
    }

    // タグ一覧ページ（/tag/ ルート）- タグクラウド表示
    add_rewrite_rule(
        'tag/?$',
        'index.php?taxonomy_root=post_tag',
        'top'
    );

    // タグアーカイブのページネーション
    add_rewrite_rule(
        'tag/([^/]+)/page-([0-9]{1,})/?$',
        'index.php?tag=$matches[1]&paged=$matches[2]',
        'top'
    );

    // カテゴリ一覧ページ（/category/ ルート）- カテゴリクラウド表示
    add_rewrite_rule(
        'category/?$',
        'index.php?taxonomy_root=category',
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
    // ※汎用的なルールなので最後に配置し、'bottom' 優先度に設定
    //   これによりカスタム投稿タイプのルールが優先される
    add_rewrite_rule(
        '([^/]+)/page-([0-9]{1,})/?$',
        'index.php?category_name=$matches[1]&paged=$matches[2]',
        'bottom'
    );
}
add_action('init', 'backbone_add_custom_pagination_rules');

/**
 * カスタムクエリ変数を登録
 */
function backbone_add_query_vars($vars) {
    $vars[] = 'old_pagination';
    $vars[] = 'taxonomy_root';
    return $vars;
}
add_filter('query_vars', 'backbone_add_query_vars');

/**
 * タクソノミールートページ（/tag/, /category/）のテンプレート読み込み
 */
function backbone_taxonomy_root_template($template) {
    $taxonomy_root = get_query_var('taxonomy_root');
    if ($taxonomy_root) {
        // taxonomy-root.php があれば使用、なければ archive.php
        $new_template = locate_template('taxonomy-root.php');
        if ($new_template) {
            return $new_template;
        }
        return locate_template('archive.php');
    }
    return $template;
}
add_filter('template_include', 'backbone_taxonomy_root_template');

/**
 * タクソノミールートページのドキュメントタイトルを設定
 */
function backbone_taxonomy_root_document_title($title) {
    $taxonomy_root = get_query_var('taxonomy_root');
    if ($taxonomy_root) {
        $taxonomy_obj = get_taxonomy($taxonomy_root);
        $page_title = $taxonomy_obj ? $taxonomy_obj->labels->name : __('タクソノミー', 'backbone-seo-llmo');
        $title['title'] = $page_title;
    }
    return $title;
}
add_filter('document_title_parts', 'backbone_taxonomy_root_document_title');

/**
 * タクソノミールートページでis_404をfalseに設定
 */
function backbone_taxonomy_root_set_404($wp_query) {
    $taxonomy_root = get_query_var('taxonomy_root');
    if ($taxonomy_root && $wp_query->is_main_query()) {
        $wp_query->is_404 = false;
        $wp_query->is_archive = true;
        status_header(200);
    }
}
add_action('parse_query', 'backbone_taxonomy_root_set_404');

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
    $flushed = get_option('backbone_rewrite_flushed_v22');
    if (!$flushed) {
        flush_rewrite_rules(false);
        update_option('backbone_rewrite_flushed_v22', true);
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
        // backbone_get_archive_setting を使って個別設定にも対応
        $orderby = backbone_get_archive_setting('orderby', 'date');

        if ($orderby && in_array($orderby, array('date', 'modified', 'rand'))) {
            // randの場合はorder指定不要、それ以外はDESC
            if ($orderby !== 'rand') {
                // セカンダリーソートキーとしてIDを追加（安定したソートのため）
                $query->set('orderby', array($orderby => 'DESC', 'ID' => 'DESC'));
            } else {
                $query->set('orderby', $orderby);
            }
        }
    }
}
// プラグインのpre_get_posts (priority 999) の後に実行するため、より高い優先度を設定
add_action('pre_get_posts', 'backbone_modify_archive_query', 9999);

/**
 * FIX: テンプレート表示直前に投稿順序を強制修正
 *
 * 何らかの理由でメインクエリの投稿順序が template_redirect 中に変更されてしまう問題に対する修正。
 * このフックで正しい順序の投稿を再取得し、メインクエリを上書きする。
 */
function backbone_force_correct_post_order() {
    global $wp_query;

    // カスタム投稿タイプのアーカイブページのみ対象
    if (!is_admin() && is_post_type_archive() && $wp_query->is_main_query()) {
        // 現在の投稿タイプを取得
        $post_type = get_query_var('post_type');
        if (empty($post_type) && isset($wp_query->query_vars['post_type'])) {
            $post_type = $wp_query->query_vars['post_type'];
        }

        if (empty($post_type)) {
            return;
        }

        // backbone_get_archive_setting を使って個別設定にも対応
        $orderby = backbone_get_archive_setting('orderby', 'date');

        if ($orderby && in_array($orderby, array('date', 'modified', 'rand'))) {
            $paged = max(1, get_query_var('paged'));
            $posts_per_page = get_query_var('posts_per_page');

            // orderby設定を準備
            $query_orderby = ($orderby === 'rand')
                ? 'rand'
                : array($orderby => 'DESC', 'ID' => 'DESC');

            // 正しい順序で投稿を再取得
            $fix_query = new WP_Query(array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => $posts_per_page,
                'paged' => $paged,
                'orderby' => $query_orderby,
                'no_found_rows' => false,
            ));

            // メインクエリの投稿を置き換え
            $wp_query->posts = $fix_query->posts;
            $wp_query->post_count = $fix_query->post_count;
        }
    }
}
add_action('template_redirect', 'backbone_force_correct_post_order', 99999);

/**
 * カスタマイザーコントロール用CSSとJSを読み込み
 * 注意: この関数は inc/customizer/index.php の backbone_customize_controls_js() と
 *       backbone_customize_styles() で既に処理されているため、削除しました。
 *       重複登録を防ぐため、カスタマイザー関連のアセット読み込みは
 *       inc/customizer/index.php で一元管理します。
 */
// function backbone_enqueue_customizer_controls_assets($wp_customize) {
//     // CSS
//     wp_enqueue_style(
//         "backbone-customizer-controls",
//         get_template_directory_uri() . "/css/customizer-controls.css",
//         array(),
//         "1.0.0"
//     );
//
//     // JavaScript
//     wp_enqueue_script(
//         "backbone-customizer-controls",
//         get_template_directory_uri() . "/js/customizer-controls.js",
//         array("jquery", "customize-controls"),
//         "1.0.0",
//         true
//     );
// }
// add_action("customize_controls_enqueue_scripts", "backbone_enqueue_customizer_controls_assets");
