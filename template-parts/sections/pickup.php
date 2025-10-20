<?php
/**
 * 特集・ピックアップセクション
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

$section_title = get_theme_mod('backbone_front_pickup_title', __('特集記事', 'backbone-seo-llmo'));
$layout = get_theme_mod('backbone_front_pickup_layout', '3col');

// ピックアップ記事のIDを収集（リピーターから）
$pickup_ids = array();
$pickup_items_json = get_theme_mod('backbone_front_pickup_items', '');

if (!empty($pickup_items_json)) {
    $pickup_items = json_decode($pickup_items_json, true);
    if (is_array($pickup_items)) {
        foreach ($pickup_items as $item) {
            if (isset($item['post_id']) && $item['post_id'] > 0) {
                $pickup_ids[] = intval($item['post_id']);
            }
        }
    }
}

if (!empty($pickup_ids)) :
?>
    <section class="pickup-section">
        <?php if ($section_title) : ?>
            <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <div class="pickup-list pickup-layout-<?php echo esc_attr($layout); ?>">
            <?php foreach ($pickup_ids as $post_id) :
                $post = get_post($post_id);
                if ($post) :
                    setup_postdata($post);
            ?>
                    <article class="pickup-item">
                        <?php if (has_post_thumbnail($post_id)) : ?>
                            <div class="pickup-thumbnail">
                                <a href="<?php echo get_permalink($post_id); ?>">
                                    <?php echo get_the_post_thumbnail($post_id, 'medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="pickup-content">
                            <h3 class="pickup-title">
                                <a href="<?php echo get_permalink($post_id); ?>">
                                    <?php echo get_the_title($post_id); ?>
                                </a>
                            </h3>

                            <div class="pickup-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt($post_id), 20); ?>
                            </div>
                        </div>
                    </article>
                <?php
                    wp_reset_postdata();
                endif;
            endforeach; ?>
        </div>
    </section>
<?php endif; ?>
