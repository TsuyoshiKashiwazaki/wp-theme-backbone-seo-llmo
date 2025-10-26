<?php
/**
 * Individual Section Template
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

$items = get_query_var('individual_items_data', array());

if (empty($items)) {
    return;
}

// レイアウトをquery_varから取得（なければデフォルト）
$layout = get_query_var('individual_layout', '2col');
?>

<section class="individual-section">
    <div class="individual-list individual-layout-<?php echo esc_attr($layout); ?>">
        <?php foreach ($items as $item) :
            $post_id = isset($item['post_id']) ? intval($item['post_id']) : 0;

            if ($post_id <= 0) {
                continue;
            }

            $post = get_post($post_id);
            if (!$post) {
                continue;
            }

            // 表示要素の設定
            $show_thumbnail = isset($item['show_thumbnail']) ? $item['show_thumbnail'] : true;
            $show_date = isset($item['show_date']) ? $item['show_date'] : true;
            $show_modified = isset($item['show_modified']) ? $item['show_modified'] : false;
            $show_category = isset($item['show_category']) ? $item['show_category'] : true;
            $show_excerpt = isset($item['show_excerpt']) ? $item['show_excerpt'] : true;

            setup_postdata($post);
        ?>
            <article class="individual-item">
                <?php if ($show_thumbnail && has_post_thumbnail($post_id)) : ?>
                    <div class="item-thumbnail">
                        <a href="<?php echo get_permalink($post_id); ?>">
                            <?php echo get_the_post_thumbnail($post_id, 'medium'); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="item-content">
                    <h3 class="item-title">
                        <a href="<?php echo get_permalink($post_id); ?>">
                            <?php echo get_the_title($post_id); ?>
                        </a>
                    </h3>

                    <?php if ($show_date || $show_modified || $show_category) : ?>
                        <div class="item-meta">
                            <?php if ($show_date) : ?>
                                <time datetime="<?php echo get_the_date('c', $post_id); ?>" class="meta-badge date-badge">
                                    <?php echo __('投稿', 'backbone-seo-llmo') . ' ' . get_the_date('', $post_id); ?>
                                </time>
                            <?php endif; ?>

                            <?php if ($show_modified) : ?>
                                <time datetime="<?php echo get_the_modified_date('c', $post_id); ?>" class="meta-badge modified-badge">
                                    <?php echo __('更新', 'backbone-seo-llmo') . ' ' . get_the_modified_date('', $post_id); ?>
                                </time>
                            <?php endif; ?>

                            <?php if ($show_category && get_post_type($post_id) === 'post') : ?>
                                <?php
                                $categories = get_the_category($post_id);
                                if ($categories) {
                                    echo '<span class="post-category meta-badge">' . esc_html($categories[0]->name) . '</span>';
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($show_excerpt) : ?>
                        <div class="item-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt($post_id), 20); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php
            wp_reset_postdata();
        endforeach; ?>
    </div>
</section>
