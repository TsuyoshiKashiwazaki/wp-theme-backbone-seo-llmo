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
    'default-featured-image.php',         // デフォルトアイキャッチ画像
    'theme-setup.php',          // テーマ基本設定
    'widgets.php',              // ウィジェット関連
    'widget-working-solution.php',  // ウィジェット実用版
    'author-custom-urls.php',   // 著者カスタムURL設定
    'customizer/index.php',     // カスタマイザー設定（utilities.phpの関数を使用）
    'color-file-storage.php',   // ファイルベースカラー保存
    'css-output.php',           // CSS出力関数（utilities.phpの関数を使用）
    'custom-tags-output.php',   // 追加タグ出力（カスタマイザー設定を使用）
    'custom-js-output.php',     // カスタムJS出力（カスタマイザー設定を使用）
    'custom-css-output.php',    // カスタムCSS出力（カスタマイザー設定を使用）
    'seo-robots.php',           // robotsメタタグ出力（カスタマイザー設定を使用）
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
 * タグ・カテゴリーの一覧ページ（/tag/・/category/）の規則を追加
 * （ページ送り /page-N/ の規則は backbone_add_page_n_rewrite_rules が本体の規則から作る）
 */
function backbone_add_custom_pagination_rules() {
    // タグ一覧ページ（/tag/ ルート）- タグクラウド表示
    add_rewrite_rule(
        'tag/?$',
        'index.php?taxonomy_root=post_tag',
        'top'
    );

    // カテゴリ一覧ページ（/category/ ルート）- カテゴリクラウド表示
    add_rewrite_rule(
        'category/?$',
        'index.php?taxonomy_root=category',
        'top'
    );
}
add_action('init', 'backbone_add_custom_pagination_rules');

/**
 * ページ送り /page-N/ の規則を、WordPress 本体（とプラグイン）が作った /page/N/ の規則から作る
 *
 * このテーマのページ送りの形は /page-N/（/page/N/ は実体の無い階層なので使わない）。
 * 以前はアーカイブの種類ごと（カテゴリー・タグ・カスタム投稿タイプ・タクソノミー・日付・作成者…）に規則を手で書き足していたが、
 * 本体の URL の組み立て（前置き・date/・日付の並び・has_archive・with_front・階層…）を写し損ねて、2 ページ目の 404 が何度も再発した。
 * そこで、本体がそのサイトの設定から作った規則の全体（rewrite_rules_array）を見て、
 * 末尾が「<pagination_base>/?([0-9]{1,})/?$」の規則ごとに、末尾を「page-([0-9]{1,})/?$」にした同じクエリの規則を、元の規則の直前に足す。
 * これで /page-N/ は、どのアーカイブでも /page/N/ と同じ順序・同じクエリで解決される（種類の書き漏れが起きない）。
 * 基本パーマリンクでは規則が空なので何もしない。
 *
 * @param string[] $rules 規則の全体（正規表現 => クエリ）
 * @return string[] /page-N/ の規則を足した全体
 */
function backbone_add_page_n_rewrite_rules($rules) {
    global $wp_rewrite;
    if (!is_array($rules) || !($wp_rewrite instanceof WP_Rewrite)) {
        return $rules;
    }
    $result = array();
    foreach ($rules as $regex => $query) {
        $page_n_regex = backbone_page_n_regex_for($regex);
        if ($page_n_regex !== null && !isset($rules[$page_n_regex]) && !isset($result[$page_n_regex])) {
            $result[$page_n_regex] = $query;
        }
        $result[$regex] = $query;
    }
    return $result;
}

/**
 * 規則が、/page-N/ の写しを作る形（末尾が本体のページ送りの形）なら、写しの正規表現を返す。違えば null
 *
 * 本体の形は 2 つ:
 *   pagination_base . '/?([0-9]{1,})/?$'（wp-includes/class-wp-rewrite.php の generate_rewrite_rules。タクソノミー・日付・作成者・検索・トップ・固定ページ等）
 *   pagination_base . '/([0-9]{1,})/?$'  （wp-includes/class-wp-post-type.php の add_rewrite_rules。カスタム投稿タイプのアーカイブ）
 * 末尾の直前が空（サイトの直下）か "/" のときだけ（"foopage/?(...)" のような別の形は対象にしない）。
 * 写しの規則と、旧形式の転送（当たった規則に写しがあるか）の両方で使う
 *
 * @param string $regex 規則の正規表現
 * @return string|null 写しの正規表現
 */
function backbone_page_n_regex_for($regex) {
    global $wp_rewrite;
    if (!($wp_rewrite instanceof WP_Rewrite)) {
        return null;
    }
    $regex = (string) $regex;
    $page_tails = array(
        $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$',
        $wp_rewrite->pagination_base . '/([0-9]{1,})/?$',
    );
    foreach ($page_tails as $page_tail) {
        $tail_length = strlen($page_tail);
        if (strlen($regex) < $tail_length || substr($regex, -$tail_length) !== $page_tail) {
            continue;
        }
        $head = substr($regex, 0, -$tail_length);
        if ($head === '' || substr($head, -1) === '/') {
            return $head . 'page-([0-9]{1,})/?$';
        }
        return null;
    }
    return null;
}
// プラグインが同じフィルターで足す /page/N/ の規則も写すため、最後に動かす
add_filter('rewrite_rules_array', 'backbone_add_page_n_rewrite_rules', PHP_INT_MAX);

/**
 * カスタムクエリ変数を登録
 */
function backbone_add_query_vars($vars) {
    $vars[] = 'taxonomy_root';
    return $vars;
}
add_filter('query_vars', 'backbone_add_query_vars');

/**
 * rewrite 規則が生成した taxonomy_root だけを返す
 *
 * taxonomy_root は query_vars フィルタで公開登録されているため、get_query_var() の値は
 * 外部から操作できる。WP::parse_request() は $_POST → $_GET の順に公開 query var を
 * 取り込むので、$_GET だけを弾いても POST での注入は防げない。また「$_GET があれば無視」
 * という実装は、正規の /tag/ に無関係な ?taxonomy_root=x を付けるだけで
 * root 用 noindex を外せてしまう（回避経路になる）。
 *
 * そこで外部入力を一切見ず、rewrite 規則がマッチしたときに WP が組み立てる
 * $wp->matched_query（例 'taxonomy_root=post_tag'）からのみ導出する。
 * matched_query は URL のクエリ文字列ではなく rewrite の置換結果なので、
 * リクエスト側から値を差し込むことはできない。
 *
 * @return string rewrite 由来なら 'post_tag' / 'category'、それ以外は空文字
 */
function backbone_get_trusted_taxonomy_root() {
    global $wp;

    if (!isset($wp) || !is_object($wp) || empty($wp->matched_query)) {
        return '';
    }

    $vars = array();
    parse_str($wp->matched_query, $vars);

    if (empty($vars['taxonomy_root']) || !is_string($vars['taxonomy_root'])) {
        return '';
    }

    // テーマが生やしている taxonomy_root ページは post_tag / category の 2 種類だけ
    $allowed = array('post_tag', 'category');

    return in_array($vars['taxonomy_root'], $allowed, true) ? $vars['taxonomy_root'] : '';
}

/**
 * タクソノミールートページ（/tag/, /category/）のテンプレート読み込み
 */
function backbone_taxonomy_root_template($template) {
    // 外部入力 ($_GET/$_POST) からではなく rewrite の置換結果から導出する
    $taxonomy_root = backbone_get_trusted_taxonomy_root();
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
    $taxonomy_root = backbone_get_trusted_taxonomy_root();
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
    $taxonomy_root = backbone_get_trusted_taxonomy_root();
    if ($taxonomy_root && $wp_query->is_main_query()) {
        $wp_query->is_404 = false;
        $wp_query->is_archive = true;
        status_header(200);
    }
}
add_action('parse_query', 'backbone_taxonomy_root_set_404');

/**
 * 規則が、テーマの作った /page-N/ の写し（末尾が page-([0-9]{1,})/?$）か
 *
 * @param string $regex 規則の正規表現
 * @return bool
 */
function backbone_is_page_n_rule($regex) {
    $tail = 'page-([0-9]{1,})/?$';
    $regex = (string) $regex;
    if (strlen($regex) < strlen($tail) || substr($regex, -strlen($tail)) !== $tail) {
        return false;
    }
    $head = substr($regex, 0, -strlen($tail));
    return $head === '' || substr($head, -1) === '/';
}

/**
 * 固定ページ・投稿・トップに、ページ送りがあるか
 * 1. 本文の文字だけで見る（本文は実行しない）:
 *    - <!--nextpage--> で分けている → ある（本体は分割ページを /<ページ>/2/、トップでは /page/2/ で表す）
 *    - 登録済みのショートコードも、本体以外（core/ 以外）の動的なブロックも無い → 無い
 *      （本体の一覧のブロックは ?query-N-page= のクエリでページ送りし、URL のパスを使わない）
 * 2. ショートコード・本体以外の動的なブロックがあるときは、それがページ送りを作るかを確かめる:
 *    - パスワード付き・公開でない投稿は本文を処理せず「ありうる」とする（中身を処理しない・出力に混ぜない。404 にもしない）
 *    - 公開の投稿だけ、本文を表示用に処理し（出力は捨てる）、本体のページ送りのリンクの関数（get_pagenum_link・paginate_links）が呼ばれたかを見る。
 *      処理するのは 1 ページ目で誰にでも表示される本文と同じもので、2 ページ目以降の URL のときだけ
 *
 * @param WP_Post|null $post 表示する投稿（トップのカスタム表示など本文を出さないときは null）
 * @return bool ページ送りがある（ありうる）なら true
 */
function backbone_singular_has_paging($post) {
    if (!($post instanceof WP_Post)) {
        return false;
    }
    $content = (string) $post->post_content;
    if (strpos($content, '<!--nextpage-->') !== false) {
        return true;
    }
    $has_shortcode = strpos($content, '[') !== false && preg_match('/' . get_shortcode_regex() . '/', $content);
    $has_dynamic = function_exists('has_blocks') && has_blocks($content) && backbone_blocks_have_third_party_dynamic(parse_blocks($content));
    if (!$has_shortcode && !$has_dynamic) {
        return false;
    }
    // パスワード付き・公開でない投稿は本文を処理しない
    if (post_password_required($post) || '' !== (string) $post->post_password || 'publish' !== get_post_status($post)) {
        return true;
    }
    $GLOBALS['backbone_paging_link_count'] = 0;
    add_filter('get_pagenum_link', 'backbone_count_paging_link', 1);
    add_filter('paginate_links', 'backbone_count_paging_link', 1);
    $saved_post = isset($GLOBALS['post']) ? $GLOBALS['post'] : null;
    $GLOBALS['post'] = $post;
    setup_postdata($post);
    ob_start();
    echo apply_filters('the_content', $content);
    ob_end_clean();
    $GLOBALS['post'] = $saved_post;
    wp_reset_postdata();
    remove_filter('get_pagenum_link', 'backbone_count_paging_link', 1);
    remove_filter('paginate_links', 'backbone_count_paging_link', 1);
    return (int) $GLOBALS['backbone_paging_link_count'] > 0;
}

/**
 * ページ送りのリンクの関数が呼ばれた回数を数える（値は変えない。backbone_singular_has_paging の中だけで付ける）
 *
 * @param mixed $value フィルターの値（そのまま返す）
 * @return mixed
 */
function backbone_count_paging_link($value) {
    $GLOBALS['backbone_paging_link_count'] = (isset($GLOBALS['backbone_paging_link_count']) ? (int) $GLOBALS['backbone_paging_link_count'] : 0) + 1;
    return $value;
}

/**
 * ブロックの木に、中身を確かめる必要のあるブロックがあるか:
 * 本体以外（core/ 以外）の動的なブロック（WP_Block_Type::is_dynamic()）と、中身が別の投稿にある同期パターン（core/block）・パターン・テンプレートパーツ
 *
 * @param array $blocks parse_blocks() の結果
 * @return bool
 */
function backbone_blocks_have_third_party_dynamic($blocks) {
    if (!class_exists('WP_Block_Type_Registry')) {
        return false;
    }
    $registry = WP_Block_Type_Registry::get_instance();
    // 中身が別の投稿・ファイルにあり、本文の文字にも parse_blocks の木にも現れないブロック（同期パターン・パターン・テンプレートパーツ）。
    // 本体は表示のときに参照先を展開する（wp-includes/blocks/block.php の get_post( $attributes['ref'] ) など）ので、中身を確かめる側に入れる
    $reference_blocks = array('core/block', 'core/pattern', 'core/template-part');
    foreach ((array) $blocks as $block) {
        $name = isset($block['blockName']) ? (string) $block['blockName'] : '';
        if (in_array($name, $reference_blocks, true) || !empty($block['attrs']['ref'])) {
            return true;
        }
        if ($name !== '' && strpos($name, 'core/') !== 0) {
            $type = $registry->get_registered($name);
            if ($type && $type->is_dynamic()) {
                return true;
            }
        }
        if (!empty($block['innerBlocks']) && backbone_blocks_have_third_party_dynamic($block['innerBlocks'])) {
            return true;
        }
    }
    return false;
}

/**
 * /page-N/ のページで、本体のページ送りのリンク（get_pagenum_link・paginate_links）を /page-M/ の形に直す
 * 本体の get_pagenum_link() は /page/N/ しか外さないので、/x/page-2/ では続きが /x/page-2/page/3/、先頭が /x/page-2/ になる。
 * 今のリクエストが /page-N/ の写しの規則で解決されたとき（その一覧には /page-N/ の規則がある）だけ、
 * 今のページ番号の /page-N/ を外し、末尾の /page/M/ を /page-M/（M=1 ならページ送りを外した URL）にする
 *
 * @param string $url リンクの URL
 * @return string
 */
function backbone_page_n_link($url) {
    global $wp, $wp_rewrite;
    if (!is_string($url) || !($wp instanceof WP) || !($wp_rewrite instanceof WP_Rewrite) || !$wp_rewrite->using_permalinks()) {
        return $url;
    }
    if (!backbone_is_page_n_rule($wp->matched_rule)) {
        return $url;
    }
    $paged = (int) get_query_var('paged');
    $parts = explode('?', $url, 2);
    $path = $parts[0];
    $base = preg_quote($wp_rewrite->pagination_base, '#');
    if ($paged > 1) {
        // 今のページの /page-N/ の後ろが、空か本体のページ送り（/page/M/）のときだけ外す
        $path = preg_replace('#/page-' . $paged . '(?=/?$|/' . $base . '/\d+/?$)#', '', $path, 1);
    }
    if (preg_match('#/' . $base . '/(\d+)(/?)$#', $path, $m)) {
        $path = preg_replace('#/' . $base . '/\d+/?$#', ((int) $m[1] > 1) ? '/page-' . (int) $m[1] . '/' : '/', $path);
    }
    $path = preg_replace('#(?<!:)/{2,}#', '/', $path);
    return $path . (isset($parts[1]) ? '?' . $parts[1] : '');
}
add_filter('get_pagenum_link', 'backbone_page_n_link');
add_filter('paginate_links', 'backbone_page_n_link');

/**
 * トップページで表示する投稿（front-page.php と同じ選び方）。カスタム表示（本文を出さない）なら null
 *
 * @return WP_Post|null
 */
function backbone_front_page_display_post() {
    if (get_theme_mod('backbone_front_page_mode', 'custom') === 'custom') {
        return null;
    }
    $page_type = get_theme_mod('backbone_front_page_type', 'static_page');
    $selected_id = ($page_type === 'static_page')
        ? get_theme_mod('backbone_front_selected_page', 0)
        : get_theme_mod('backbone_front_selected_post', 0);
    $post = ($selected_id > 0) ? get_post($selected_id) : null;
    return ($post instanceof WP_Post) ? $post : null;
}

/**
 * 今のリクエスト（固定ページ・投稿・トップ）を表示するテンプレートのパス（template_include を通す前のもの）
 * 本体の wp-includes/template-loader.php と同じ順で、同じ関数（get_front_page_template など）でテンプレートを探す。
 * 本体はこの後 template_include フィルターで最終のテンプレートを決めるので、呼び出し側（backbone_redirect_old_pagination）は
 * この結果に template_include を通してから見る（プラグインの切り替えを反映する）
 *
 * @return string 見つからなければ空文字
 */
function backbone_request_hierarchy_template() {
    $getters = array(
        'is_front_page'     => 'get_front_page_template',
        'is_home'           => 'get_home_template',
        'is_privacy_policy' => 'get_privacy_policy_template',
        'is_attachment'     => 'get_attachment_template',
        'is_single'         => 'get_single_template',
        'is_page'           => 'get_page_template',
        'is_singular'       => 'get_singular_template',
    );
    $template = '';
    foreach ($getters as $tag => $getter) {
        if (function_exists($tag) && function_exists($getter) && call_user_func($tag)) {
            $template = (string) call_user_func($getter);
            if ($template !== '') {
                break;
            }
        }
    }
    if ($template === '') {
        $template = (string) get_index_template();
    }
    return $template === '' ? '' : wp_normalize_path($template);
}

/**
 * テンプレートのファイルが、このテーマ（親テーマ）自身のファイルか
 * 子テーマ・プラグインのテンプレートは本文の外で独自の一覧とページ送り（new WP_Query と paginate_links など）を出しうるので偽。
 * このテーマ自身の固定ページ・投稿・トップのテンプレートは、本文の外でページ送りを出さない
 *
 * @param string $template テンプレートのパス
 * @return bool
 */
function backbone_template_is_parent_theme($template) {
    $template = (string) $template;
    if ($template === '') {
        return false;
    }
    $template = wp_normalize_path($template);
    $parent = trailingslashit(wp_normalize_path(get_template_directory()));
    $child = trailingslashit(wp_normalize_path(get_stylesheet_directory()));
    if ($child !== $parent && strpos($template, $child) === 0) {
        return false;
    }
    return strpos($template, $parent) === 0;
}

/**
 * ページ送りの URL の整理（このテーマのページ送りの正しい形は /page-N/。/page/N/ は実体の無い階層）
 * 判断は URL の形ではなく、WordPress がその URL をどう解釈したかで行う:
 *   本体が照合したパス（$wp->request。サブディレクトリ設置の前置きは外れている）の末尾が /page/N/ か /page-N/ で、
 *   かつ本体がそれをページ送り（その番号）として読んだとき（クエリ変数 paged。トップに固定ページを表示する設定では本体が page に移す）だけ扱う。
 *   スラッグが page-2024 の投稿や、検索語が page-2024 の検索は、ページ送りとして読まれないので対象外。
 * - 固定ページ・投稿・トップの 2 ページ目以降は、ページ送りが無ければ（backbone_singular_has_paging）中身の無い URL として 404 にする
 * - 旧形式 /page/N/ は、当たった規則に /page-N/ の写しがあるときだけ /page-N/ へ 301 転送する（/page/1/ はページ送りを外した URL へ。クエリはそのまま）
 * 基本パーマリンクでは何もしない（/page-N/ の規則が使われないため）
 *
 * 以前は旧形式の目印（クエリ変数 old_pagination）を立てる規則が無く、転送が一度も動いていなかった
 */
function backbone_redirect_old_pagination() {
    global $wp_rewrite, $wp_query, $wp;
    if (!($wp_rewrite instanceof WP_Rewrite) || !$wp_rewrite->using_permalinks() || !($wp instanceof WP)) {
        return;
    }
    $request = trim((string) $wp->request, '/');
    $base = preg_quote($wp_rewrite->pagination_base, '#');
    $is_old = (bool) preg_match('#(?:^|/)' . $base . '/(\d+)$#', $request, $old_match);
    $is_new = (bool) preg_match('#(?:^|/)page-(\d+)$#', $request, $new_match);
    if (!$is_old && !$is_new) {
        return;
    }
    $page = (int) ($is_old ? $old_match[1] : $new_match[1]);
    // 当たった規則がページ送りの規則（新形式なら写しの /page-N/、旧形式なら本体の /page/N/）のときだけ扱う。
    // URL の番号がクエリ（?paged=N）と偶然一致しただけの検索語・スラッグは、ページ送りの規則に当たらないので対象外
    $rule_is_paging = $is_new ? backbone_is_page_n_rule($wp->matched_rule) : (backbone_page_n_regex_for($wp->matched_rule) !== null);
    if (!$rule_is_paging) {
        return;
    }
    $paged = (int) get_query_var('paged');
    $split_page = (int) get_query_var('page');
    if ($paged !== $page && !(is_front_page() && $split_page === $page)) {
        // 本体はこの URL をページ送りとして読んでいない
        return;
    }

    // 固定ページ・投稿・トップの 2 ページ目以降で、ページ送りが無ければ 404。
    // 表示に使うテンプレートが子テーマ・プラグインのもの（本文の外で独自の一覧とページ送りを出しうる）なら 404 にしない。
    // 表示に使うテンプレートは、本体と同じ順で探したもの（backbone_request_hierarchy_template）に、本体がテンプレートを読む直前に通す
    // template_include フィルターを通した結果で見る（プラグインが template_include で一覧のテンプレートに切り替える構成を 404 にしない）。
    // 404 の判定自体はここ（template_redirect の優先度 1）で行う。カスタムの 404 ページ・404 の記録などのプラグインは
    // template_redirect で is_404() を見るので、判定を template_include まで遅らせるとそれらが動かなくなるため。
    // それ以外で独自のページ送りを付けたときは、フィルター backbone_paged_url_has_no_pages で false を返せば 404 にしない
    if ($page > 1 && (is_singular() || is_front_page())) {
        $display_post = is_front_page() ? backbone_front_page_display_post() : get_queried_object();
        $hierarchy_template = backbone_request_hierarchy_template();
        $final_template = $hierarchy_template !== '' ? (string) apply_filters('template_include', $hierarchy_template) : '';
        $has_no_pages = backbone_template_is_parent_theme($final_template)
            && !backbone_singular_has_paging($display_post instanceof WP_Post ? $display_post : null);
        if (apply_filters('backbone_paged_url_has_no_pages', $has_no_pages)) {
            $wp_query->set_404();
            status_header(404);
            nocache_headers();
            return;
        }
    }

    // 旧形式 /page/N/ → /page-N/。当たった規則の写しが規則の配列に同じクエリであるときだけ
    // （写しの無い形の規則・同じ URL を別の意味で受ける規則がある場合は、転送先が同じ一覧にならないので触れない）
    $page_n_regex = $is_old ? backbone_page_n_regex_for($wp->matched_rule) : null;
    $rules = $page_n_regex !== null ? $wp_rewrite->wp_rewrite_rules() : array();
    $same_query = $page_n_regex !== null && is_array($rules)
        && isset($rules[$page_n_regex], $rules[$wp->matched_rule])
        && $rules[$page_n_regex] === $rules[$wp->matched_rule];
    if ($is_old && !is_404() && $same_query) {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        $request_parts = explode('?', $request_uri, 2);
        $old_pattern = '#/' . $base . '/' . $page . '/?$#';
        if (!preg_match($old_pattern, $request_parts[0])) {
            return;
        }
        $new_path = preg_replace($old_pattern, $page > 1 ? '/page-' . $page . '/' : '/', $request_parts[0]);
        $new_uri = $new_path . (isset($request_parts[1]) && $request_parts[1] !== '' ? '?' . $request_parts[1] : '');
        wp_safe_redirect($new_uri, 301);
        exit;
    }
}
// redirect_canonical（優先度 10）より先に動かす
add_action('template_redirect', 'backbone_redirect_old_pagination', 1);

/**
 * 新しく付けるスラッグを、ページ送りの形（page-数字）にしない。既存の投稿のスラッグは変えない
 * 本体は日付アーカイブと紛らわしい数字だけのスラッグを、新しいスラッグのときだけ避ける（wp-includes/post.php の
 * `( ! $post || $post->post_name !== $slug )`）。このテーマはページ送りを /page-N/ にするので、page-N も同じ条件で避ける
 *
 * @param string $slug        本体が決めたスラッグ
 * @param int    $post_id     投稿 ID（新規は 0）
 * @param string $post_status 状態
 * @param string $post_type   投稿タイプ
 * @param int    $post_parent 親
 * @return string
 */
function backbone_avoid_new_page_n_slug($slug, $post_id, $post_status, $post_type, $post_parent) {
    if (!is_string($slug) || !preg_match('/^page-\d+$/', $slug)) {
        return $slug;
    }
    $post = $post_id ? get_post($post_id) : null;
    if ($post instanceof WP_Post && $post->post_name === $slug) {
        return $slug;
    }
    return wp_unique_post_slug($slug . '-2', $post_id, $post_status, $post_type, $post_parent);
}
add_filter('wp_unique_post_slug', 'backbone_avoid_new_page_n_slug', 10, 5);

/**
 * /page-N/ の写しの規則に当たった URL が、既存の投稿・固定ページのパーマリンクそのもの（スラッグが page-9 など）か
 * 例: パーマリンク /%postname%/ でスラッグ page-9 の投稿の /page-9/ は、トップのページ送りの写し "page-([0-9]{1,})/?$" に先に当たる。
 *     カスタム投稿タイプの /book/page-9/ は、アーカイブの写し "book/page-([0-9]{1,})/?$" に先に当たる。
 * ここ（request フィルター）では本体のクエリを変えず、その投稿を候補として覚えるだけにする。どちらを表示するかは、
 * 本体が一覧のクエリを実行した後に backbone_page_n_fallback_to_existing_post で決める（一覧にそのページがあれば一覧）。
 * どの投稿の URL かは、写しの規則を除いた規則の全体で本体の url_to_postid() に引かせる（パーマリンクの構造・投稿タイプの前置きによらない）。
 * 既存のスラッグは変えない（既存の URL を保つ。新しいスラッグは backbone_avoid_new_page_n_slug で page-N にしない）
 *
 * @param array $query_vars 本体が規則から作ったクエリ変数
 * @return array 変えずに返す
 */
function backbone_page_n_prefer_existing_post($query_vars) {
    global $wp;
    $GLOBALS['backbone_page_n_existing_post'] = 0;
    if (!($wp instanceof WP) || !backbone_is_page_n_rule($wp->matched_rule)) {
        return $query_vars;
    }
    $path = trim((string) $wp->request, '/');
    if ($path === '') {
        return $query_vars;
    }
    // 本体の WP_Rewrite::wp_rewrite_rules() は読んだ規則を $wp_rewrite->rules に残すので、引いた後で元に戻す
    // （元が配列でなければ、フィルターを外した後の保存済みの規則にする。写しを除いた規則を残さない）
    global $wp_rewrite;
    $saved_rules = ($wp_rewrite instanceof WP_Rewrite) ? $wp_rewrite->rules : null;
    add_filter('option_rewrite_rules', 'backbone_without_page_n_rules', PHP_INT_MAX);
    $post_id = url_to_postid(home_url(user_trailingslashit($path)));
    remove_filter('option_rewrite_rules', 'backbone_without_page_n_rules', PHP_INT_MAX);
    if ($wp_rewrite instanceof WP_Rewrite) {
        $wp_rewrite->rules = is_array($saved_rules) ? $saved_rules : get_option('rewrite_rules');
    }
    $post = $post_id ? get_post($post_id) : null;
    if (!($post instanceof WP_Post) || 'publish' !== $post->post_status) {
        return $query_vars;
    }
    // パーマリンクがこの URL そのものであるときだけ（別の階層の同じスラッグは取らない）
    if (untrailingslashit((string) get_permalink($post)) !== untrailingslashit(home_url('/' . $path))) {
        return $query_vars;
    }
    $GLOBALS['backbone_page_n_existing_post'] = (int) $post->ID;
    return $query_vars;
}
add_filter('request', 'backbone_page_n_prefer_existing_post');

/**
 * 本体が一覧のクエリを実行した後（WP::main の query_posts の後、handle_404 の最初）に、候補の投稿を表示するかを決める
 * - 一覧にそのページの投稿がある → 一覧のまま（ページ送りのリンク・旧形式の転送が指すのは一覧なので、続きに進めなくしない）
 * - 一覧が空（そのページが無い）か、1 件の固定ページ・投稿を表すクエリ（トップに固定ページを表示する設定）→ 候補の投稿を表示する
 * 一覧の件数は本体のメインクエリの実際の結果で見る（プラグインやテーマの pre_get_posts による 1 ページの件数の変更も反映される）
 *
 * @param bool     $preempt  本体の 404 の処理を飛ばすか
 * @param WP_Query $wp_query メインクエリ
 * @return bool 変えずに返す
 */
function backbone_page_n_fallback_to_existing_post($preempt, $wp_query) {
    global $wp;
    $post_id = isset($GLOBALS['backbone_page_n_existing_post']) ? (int) $GLOBALS['backbone_page_n_existing_post'] : 0;
    $GLOBALS['backbone_page_n_existing_post'] = 0;
    if (false !== $preempt || $post_id <= 0 || !($wp_query instanceof WP_Query) || !$wp_query->is_main_query()) {
        return $preempt;
    }
    if (!$wp_query->is_singular && $wp_query->post_count > 0) {
        return $preempt;
    }
    $post = get_post($post_id);
    if (!($post instanceof WP_Post) || 'publish' !== $post->post_status) {
        return $preempt;
    }
    $vars = ('page' === $post->post_type)
        ? array('page_id' => $post->ID)
        : array('p' => $post->ID, 'post_type' => $post->post_type);
    $wp_query->query($vars);
    if ($wp instanceof WP) {
        $wp->query_vars = $vars;
    }
    return $preempt;
}
add_filter('pre_handle_404', 'backbone_page_n_fallback_to_existing_post', 10, 2);

/**
 * 規則の全体から、テーマの /page-N/ の写しの規則を除く（url_to_postid() で投稿の URL を引くときだけ使う）
 *
 * @param mixed $rules 保存済みの規則（option rewrite_rules）
 * @return mixed
 */
function backbone_without_page_n_rules($rules) {
    if (!is_array($rules)) {
        return $rules;
    }
    $result = array();
    foreach ($rules as $regex => $query) {
        if (!backbone_is_page_n_rule($regex)) {
            $result[$regex] = $query;
        }
    }
    return $result;
}

/**
 * Rewriteルールを一度だけフラッシュ（初回のみ実行）
 */
function backbone_flush_rewrite_rules_once() {
    // v26: ページ送り /page-N/ の規則を、手書きから本体の /page/N/ の規則の写し（backbone_add_page_n_rewrite_rules）に変えたので、もう一度だけ作り直す
    // （v23〜v25: アーカイブの種類ごとに手書きの規則を足していた）
    $flushed = get_option('backbone_rewrite_flushed_v26');
    if (!$flushed) {
        flush_rewrite_rules(false);
        update_option('backbone_rewrite_flushed_v26', true);
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
    // 検索結果は関連度などの並びを保つため対象外（is_search と is_post_type_archive は同時に立つことがある）
    if (!is_admin() && is_post_type_archive() && !is_search() && $wp_query->is_main_query()) {
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
            // orderby設定を準備
            $query_orderby = ($orderby === 'rand')
                ? 'rand'
                : array($orderby => 'DESC', 'ID' => 'DESC');

            // 正しい順序で投稿を再取得
            // メインクエリの条件（カテゴリ・著者・日付などの絞り込みや他フックで追加された条件）を引き継ぎ、並び順だけを差し替える
            $fix_args = $wp_query->query_vars;
            $fix_args['orderby'] = $query_orderby;
            $fix_args['no_found_rows'] = false;
            $fix_query = new WP_Query($fix_args);

            // メインクエリの投稿と件数を置き換え（件数を更新しないと、ページ送りや「N件」の表示が実際の一覧と食い違う）
            $wp_query->posts = $fix_query->posts;
            $wp_query->post_count = $fix_query->post_count;
            $wp_query->found_posts = $fix_query->found_posts;
            $wp_query->max_num_pages = $fix_query->max_num_pages;
            $wp_query->current_post = -1;
            $wp_query->post = !empty($fix_query->posts) ? $fix_query->posts[0] : null;
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
