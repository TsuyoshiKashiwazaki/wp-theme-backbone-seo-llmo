<?php
/**
 * 記事一覧セクション
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

$section_title = get_theme_mod('backbone_front_posts_title', __('最新記事', 'backbone-seo-llmo'));
$posts_count = get_theme_mod('backbone_front_posts_count', '6');
$category_id = get_theme_mod('backbone_front_posts_category', '0');
$layout = get_theme_mod('backbone_front_posts_layout', '3col');
$orderby = get_theme_mod('backbone_front_posts_orderby', 'date');

// 表示要素の設定
$show_thumbnail = get_theme_mod('backbone_front_posts_show_thumbnail', true);
$show_date = get_theme_mod('backbone_front_posts_show_date', true);
$show_modified = get_theme_mod('backbone_front_posts_show_modified', false);
$show_category = get_theme_mod('backbone_front_posts_show_category', true);
$show_excerpt = get_theme_mod('backbone_front_posts_show_excerpt', true);

// クエリ引数
$args = array(
    'post_type' => 'post',
    'posts_per_page' => intval($posts_count),
    'orderby' => $orderby,
    'order' => 'DESC',
);

if ($category_id > 0) {
    $args['cat'] = $category_id;
}

$posts_query = new WP_Query($args);

if ($posts_query->have_posts()) :
?>
    <section class="posts-list-section">
        <?php if ($section_title) : ?>
            <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <div class="posts-list posts-layout-<?php echo esc_attr($layout); ?>">
            <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                <article <?php post_class('post-item'); ?>>
                    <?php if ($show_thumbnail && has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="post-content">
                        <h3 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>

                        <?php if ($show_date || $show_modified || $show_category) : ?>
                            <div class="post-meta">
                                <?php
                                // 並び順に応じてバッジの表示順序を決定
                                if ($orderby === 'modified') {
                                    // 更新日順の場合は更新日を先に表示
                                    if ($show_modified) : ?>
                                        <time datetime="<?php echo get_the_modified_date('c'); ?>" class="meta-badge modified-badge">
                                            <span class="meta-label-small"><?php echo __('更新', 'backbone-seo-llmo'); ?>:</span><?php echo get_the_modified_date('Y/m/d'); ?>
                                        </time>
                                    <?php endif;
                                    if ($show_date) : ?>
                                        <time datetime="<?php echo get_the_date('c'); ?>" class="meta-badge date-badge">
                                            <span class="meta-label-small"><?php echo __('投稿', 'backbone-seo-llmo'); ?>:</span><?php echo get_the_date('Y/m/d'); ?>
                                        </time>
                                    <?php endif;
                                } else {
                                    // 投稿日順またはその他の場合は投稿日を先に表示
                                    if ($show_date) : ?>
                                        <time datetime="<?php echo get_the_date('c'); ?>" class="meta-badge date-badge">
                                            <span class="meta-label-small"><?php echo __('投稿', 'backbone-seo-llmo'); ?>:</span><?php echo get_the_date('Y/m/d'); ?>
                                        </time>
                                    <?php endif;
                                    if ($show_modified) : ?>
                                        <time datetime="<?php echo get_the_modified_date('c'); ?>" class="meta-badge modified-badge">
                                            <span class="meta-label-small"><?php echo __('更新', 'backbone-seo-llmo'); ?>:</span><?php echo get_the_modified_date('Y/m/d'); ?>
                                        </time>
                                    <?php endif;
                                }
                                ?>

                                <?php if ($show_category) : ?>
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories) {
                                        echo '<span class="post-category">' . esc_html($categories[0]->name) . '</span>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_excerpt) : ?>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
<?php
    wp_reset_postdata();
endif;
?>
