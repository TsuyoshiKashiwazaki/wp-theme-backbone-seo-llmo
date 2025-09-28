<?php
/**
 * サイドバーテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

if (!is_active_sidebar('sidebar-main')) {
    return;
}
?>

<?php if (is_active_sidebar('sidebar-main')) : ?>
    <?php dynamic_sidebar('sidebar-main'); ?>
<?php else : ?>

    <!-- デフォルトウィジェット：検索 -->
    <div class="widget widget_search">
        <h3 class="widget-title"><?php _e('検索', 'backbone-seo-llmo'); ?></h3>
        <?php get_search_form(); ?>
    </div>

    <!-- デフォルトウィジェット：最新の投稿 -->
    <div class="widget widget_recent_entries">
        <h3 class="widget-title"><?php _e('最新の投稿', 'backbone-seo-llmo'); ?></h3>
        <ul>
            <?php
            $recent_posts = wp_get_recent_posts(array(
                'numberposts' => 5,
                'post_status' => 'publish'
            ));
            foreach ($recent_posts as $recent) :
            ?>
                <li>
                    <a href="<?php echo get_permalink($recent['ID']); ?>">
                        <?php echo $recent['post_title']; ?>
                    </a>
                    <span class="post-date"><?php echo get_the_date('', $recent['ID']); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- デフォルトウィジェット：カテゴリー -->
    <div class="widget widget_categories">
        <h3 class="widget-title"><?php _e('カテゴリー', 'backbone-seo-llmo'); ?></h3>
        <ul>
            <?php wp_list_categories(array(
                'title_li' => '',
                'show_count' => true,
            )); ?>
        </ul>
    </div>

    <!-- デフォルトウィジェット：アーカイブ -->
    <div class="widget widget_archive">
        <h3 class="widget-title"><?php _e('アーカイブ', 'backbone-seo-llmo'); ?></h3>
        <ul>
            <?php wp_get_archives(array(
                'type' => 'monthly',
                'show_post_count' => true,
            )); ?>
        </ul>
    </div>

    <!-- デフォルトウィジェット：タグクラウド -->
    <?php if (has_tag()) : ?>
        <div class="widget widget_tag_cloud">
            <h3 class="widget-title"><?php _e('タグ', 'backbone-seo-llmo'); ?></h3>
            <?php wp_tag_cloud(array(
                'smallest' => 0.8,
                'largest' => 1.2,
                'unit' => 'rem',
                'number' => 20,
            )); ?>
        </div>
    <?php endif; ?>

    <!-- デフォルトウィジェット：メタ情報 -->
    <div class="widget widget_meta">
        <h3 class="widget-title"><?php _e('メタ情報', 'backbone-seo-llmo'); ?></h3>
        <ul>
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <li><a href="<?php echo esc_url(get_bloginfo('rss2_url')); ?>"><?php _e('投稿の RSS', 'backbone-seo-llmo'); ?></a></li>
            <li><a href="<?php echo esc_url(get_bloginfo('comments_rss2_url')); ?>"><?php _e('コメントの RSS', 'backbone-seo-llmo'); ?></a></li>
            <li><a href="https://wordpress.org/"><?php _e('WordPress.org', 'backbone-seo-llmo'); ?></a></li>
        </ul>
    </div>

<?php endif; ?>
