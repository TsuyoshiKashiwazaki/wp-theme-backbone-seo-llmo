<?php
/**
 * robotsメタタグの出力制御
 *
 * カスタマイザー「インデックス設定（noindex）」の内容を
 * WordPress本体の wp_robots フィルタへ反映する。
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * タクソノミーアーカイブをnoindexにすべきか判定
 *
 * @param string $type 'tag' または 'category'
 * @return bool noindexにする場合true
 */
function backbone_should_noindex_term($type) {
    $mode = get_theme_mod('noindex_' . $type, 'off');

    if ($mode === 'off') {
        return false;
    }

    if ($mode === 'all') {
        return true;
    }

    // しきい値モード：ぶら下がっている記事数で判定
    $term = get_queried_object();

    if (!($term instanceof WP_Term)) {
        return false;
    }

    $threshold = (int) get_theme_mod('noindex_' . $type . '_threshold', 1);

    return ((int) $term->count <= $threshold);
}

/**
 * カスタマイザー設定にもとづいて noindex を付与
 *
 * follow は常に維持する。noindexにしてもリンクはたどらせたいため。
 *
 * @param array $robots robotsディレクティブの配列
 * @return array 変更後の配列
 */
function backbone_apply_noindex_settings($robots) {
    // 管理画面・フィードは対象外
    if (is_admin() || is_feed()) {
        return $robots;
    }

    $noindex = false;

    // タグ一覧（/tag/）・カテゴリ一覧（/category/）
    // テーマが add_rewrite_rule で生やしている taxonomy_root ページ。
    //
    // 🛑 taxonomy_root は functions.php の query_vars フィルタで公開クエリ変数として
    //    登録されているため、任意の URL に ?taxonomy_root=... を付けるだけで
    //    get_query_var() が値を返してしまう。URL 経由の値を信頼すると
    //    (a) 通常ページに noindex を付けられる (b) 値を変えて noindex を回避できる
    //    の両方が成立するので、rewrite 由来のリクエストだけを対象にする。
    $taxonomy_root = backbone_get_trusted_taxonomy_root();

    if ($taxonomy_root === 'post_tag' && get_theme_mod('noindex_tag_root', false)) {
        $noindex = true;
    }
    if ($taxonomy_root === 'category' && get_theme_mod('noindex_category_root', false)) {
        $noindex = true;
    }

    // 個別タグ・個別カテゴリのアーカイブ
    // taxonomy_root ページとは独立した if にする（else に閉じ込めると
    // 将来 taxonomy_root ページのページネーション等を対象にしたときに漏れる）
    if (is_tag() && backbone_should_noindex_term('tag')) {
        $noindex = true;
    }
    if (is_category() && backbone_should_noindex_term('category')) {
        $noindex = true;
    }
    if (is_author() && get_theme_mod('noindex_author', false)) {
        $noindex = true;
    }
    if (is_date() && get_theme_mod('noindex_date', false)) {
        $noindex = true;
    }

    // 「2ページ目以降」は対象アーカイブを明示的に限定する。
    // is_paged() だけで判定すると、ブログトップやカスタム投稿タイプの
    // アーカイブの 2 ページ目まで巻き込む（設定ラベルの意図と食い違う）。
    if (is_paged() && get_theme_mod('noindex_paged', false)) {
        if (is_tag() || is_category() || is_author() || is_date() || $taxonomy_root) {
            $noindex = true;
        }
    }

    if ($noindex) {
        // index と noindex が同時に立つと意味が壊れるため index は落とす
        unset($robots['index']);
        $robots['noindex'] = true;

        // 🛑 follow は明示的に足さない。
        //    robots の既定動作が follow なので noindex 単独でリンクはたどられる。
        //    ここで follow を書き込むと、自分より後に実行される wp_robots フィルタが
        //    nofollow を立てたときに "noindex, nofollow, follow" という矛盾した出力になる。
        //    先行フィルタだけを見る条件分岐では後続を検出できないため、
        //    そもそも書き込まないことで競合の余地を無くす。
    }

    return $robots;
}
add_filter('wp_robots', 'backbone_apply_noindex_settings');
