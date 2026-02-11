<?php
/**
 * タクソノミールートページテンプレート
 * /tag/ や /category/ のルートページをタグクラウドで表示
 *
 * @package Backbone_SEO_LLMO
 */

get_header();

$taxonomy_root = get_query_var('taxonomy_root');
$taxonomy_obj = get_taxonomy($taxonomy_root);

// 全タームを取得（投稿があるもののみ）
$terms = get_terms(array(
    'taxonomy' => $taxonomy_root,
    'hide_empty' => true,
    'orderby' => 'name',
    'order' => 'ASC',
));

// タイトル
$page_title = $taxonomy_obj ? $taxonomy_obj->labels->name : __('タクソノミー', 'backbone-seo-llmo');

// 実際の公開記事数を一括取得（$term->countはDBキャッシュで不正確な場合がある）
$actual_counts = array();
if (!empty($terms) && !is_wp_error($terms)) {
    global $wpdb;
    $post_types = get_post_types(array('public' => true), 'names');
    unset($post_types['attachment']);
    $post_type_in = "'" . implode("','", esc_sql(array_values($post_types))) . "'";

    $tt_ids = wp_list_pluck($terms, 'term_taxonomy_id');
    $tt_ids_in = implode(',', array_map('intval', $tt_ids));

    $rows = $wpdb->get_results(
        "SELECT tr.term_taxonomy_id, COUNT(DISTINCT p.ID) as cnt
         FROM {$wpdb->term_relationships} tr
         JOIN {$wpdb->posts} p ON p.ID = tr.object_id
         WHERE tr.term_taxonomy_id IN ({$tt_ids_in})
           AND p.post_status = 'publish'
           AND p.post_type IN ({$post_type_in})
         GROUP BY tr.term_taxonomy_id"
    );
    foreach ($rows as $row) {
        $actual_counts[$row->term_taxonomy_id] = (int) $row->cnt;
    }
}

// 最大・最小投稿数を取得（フォントサイズ計算用）
$max_count = 0;
$min_count = PHP_INT_MAX;
if (!empty($terms) && !is_wp_error($terms)) {
    foreach ($terms as $term) {
        $count = isset($actual_counts[$term->term_taxonomy_id]) ? $actual_counts[$term->term_taxonomy_id] : 0;
        if ($count > $max_count) $max_count = $count;
        if ($count < $min_count) $min_count = $count;
    }
}
?>

<?php if (!empty($terms) && !is_wp_error($terms)) : ?>

    <article class="page type-page status-publish hentry">
        <header class="entry-header">
            <h1 class="entry-title"><?php echo esc_html($page_title); ?></h1>
        </header>

        <div class="entry-content">
            <?php if (function_exists('kspb_display_breadcrumbs')) : kspb_display_breadcrumbs(); endif; ?>

            <div class="tag-cloud-wrapper">
                <?php
                foreach ($terms as $term) :
                    $count = isset($actual_counts[$term->term_taxonomy_id]) ? $actual_counts[$term->term_taxonomy_id] : 0;
                    if ($count === 0) continue;
                    // フォントサイズを計算（0.85em〜2em）
                    if ($max_count === $min_count) {
                        $font_size = 1.2;
                    } else {
                        $font_size = 0.85 + (($count - $min_count) / ($max_count - $min_count)) * 1.15;
                    }
                    ?>
                    <a href="<?php echo esc_url(get_term_link($term)); ?>"
                       class="tag-cloud-link"
                       style="font-size: <?php echo esc_attr(round($font_size, 2)); ?>em;"
                       title="<?php echo esc_attr($term->name . ' (' . $count . '件)'); ?>">
                        <?php echo esc_html($term->name); ?><span class="tag-count">(<?php echo esc_html($count); ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>

            <p class="tag-cloud-total">
                <?php
                printf(
                    __('全%d件の%s', 'backbone-seo-llmo'),
                    count($terms),
                    esc_html($page_title)
                );
                ?>
            </p>
        </div>
    </article>

<?php else : ?>

    <article class="page no-results">
        <header class="entry-header">
            <h1 class="entry-title"><?php _e('見つかりません', 'backbone-seo-llmo'); ?></h1>
        </header>

        <div class="entry-content">
            <p><?php _e('該当する項目がありません。', 'backbone-seo-llmo'); ?></p>
            <?php get_search_form(); ?>
        </div>
    </article>

<?php endif; ?>

<style>
.tag-cloud-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5em 1em;
    align-items: baseline;
    line-height: 2;
    padding: 1em 0;
}

.tag-cloud-link {
    display: inline-block;
    text-decoration: none;
    color: var(--text-primary);
    transition: color 0.2s ease, transform 0.2s ease;
    white-space: nowrap;
}

.tag-cloud-link:hover {
    color: var(--accent-color);
    transform: scale(1.05);
}

.tag-cloud-link .tag-count {
    font-size: 0.7em;
    color: var(--text-secondary, #666);
    margin-left: 0.2em;
}

.tag-cloud-total {
    margin-top: 1.5em;
    padding-top: 1em;
    border-top: 1px solid var(--border-color, #ddd);
    color: var(--text-secondary, #666);
    font-size: 0.9em;
}
</style>

<?php get_footer(); ?>
