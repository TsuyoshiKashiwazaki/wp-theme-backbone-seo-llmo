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
                    <?php if (has_post_thumbnail()) : ?>
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

                        <div class="post-meta">
                            <time datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                            <?php
                            $categories = get_the_category();
                            if ($categories) {
                                echo '<span class="post-category">' . esc_html($categories[0]->name) . '</span>';
                            }
                            ?>
                        </div>

                        <div class="post-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
<?php
    wp_reset_postdata();
endif;
?>
