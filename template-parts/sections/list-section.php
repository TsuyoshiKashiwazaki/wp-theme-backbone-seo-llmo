<?php
/**
 * List Section Template
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

$section = get_query_var('section_data', array());

if (empty($section)) {
    return;
}

$section_title = isset($section['title']) ? $section['title'] : '';
$posts_count = isset($section['count']) ? intval($section['count']) : 6;
$display_type = isset($section['display_type']) ? $section['display_type'] : 'category';
$layout = isset($section['layout']) ? $section['layout'] : '3col';
$orderby = isset($section['orderby']) ? $section['orderby'] : 'date';

$show_thumbnail = isset($section['show_thumbnail']) ? $section['show_thumbnail'] : true;
$show_date = isset($section['show_date']) ? $section['show_date'] : true;
$show_modified = isset($section['show_modified']) ? $section['show_modified'] : false;
$show_category = isset($section['show_category']) ? $section['show_category'] : true;
$show_excerpt = isset($section['show_excerpt']) ? $section['show_excerpt'] : true;

// デフォルトのクエリ引数
$args = array(
    'posts_per_page' => $posts_count,
    'orderby' => $orderby,
    'order' => 'DESC',
);

// 表示対象に応じてクエリを変更
switch ($display_type) {
    case 'category':
        $args['post_type'] = 'post';
        $category_id = isset($section['category']) ? intval($section['category']) : 0;
        if ($category_id > 0) {
            $args['cat'] = $category_id;
        }
        break;

    case 'tag':
        $args['post_type'] = array('post', 'page'); // タグは投稿と固定ページ両方をサポート
        $tag_id = isset($section['tag']) ? intval($section['tag']) : 0;
        if ($tag_id > 0) {
            $args['tag_id'] = $tag_id;
        }
        break;

    case 'post_type':
        $post_type = isset($section['post_type_filter']) ? $section['post_type_filter'] : 'post';
        $args['post_type'] = $post_type;
        break;

    case 'author':
        $args['post_type'] = 'post';
        $author_id = isset($section['author']) ? intval($section['author']) : 0;
        if ($author_id > 0) {
            $args['author'] = $author_id;
        }
        break;

    case 'date':
        $args['post_type'] = 'post';
        $date_range = isset($section['date_range']) ? $section['date_range'] : 'current_month';
        switch ($date_range) {
            case 'current_month':
                $args['year'] = current_time('Y');
                $args['monthnum'] = current_time('m');
                break;
            case 'last_month':
                $args['year'] = date('Y', strtotime('-1 month'));
                $args['monthnum'] = date('m', strtotime('-1 month'));
                break;
            case 'current_year':
                $args['year'] = current_time('Y');
                break;
            case 'last_year':
                $args['year'] = current_time('Y') - 1;
                break;
        }
        break;
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
                                if ($orderby === 'modified') {
                                    if ($show_modified) : ?>
                                        <time datetime="<?php echo get_the_modified_date('c'); ?>" class="meta-badge modified-badge">
                                            <?php echo __('更新', 'backbone-seo-llmo') . ' ' . get_the_modified_date(); ?>
                                        </time>
                                    <?php endif;
                                    if ($show_date) : ?>
                                        <time datetime="<?php echo get_the_date('c'); ?>" class="meta-badge date-badge">
                                            <?php echo __('投稿', 'backbone-seo-llmo') . ' ' . get_the_date(); ?>
                                        </time>
                                    <?php endif;
                                } else {
                                    if ($show_date) : ?>
                                        <time datetime="<?php echo get_the_date('c'); ?>" class="meta-badge date-badge">
                                            <?php echo __('投稿', 'backbone-seo-llmo') . ' ' . get_the_date(); ?>
                                        </time>
                                    <?php endif;
                                    if ($show_modified) : ?>
                                        <time datetime="<?php echo get_the_modified_date('c'); ?>" class="meta-badge modified-badge">
                                            <?php echo __('更新', 'backbone-seo-llmo') . ' ' . get_the_modified_date(); ?>
                                        </time>
                                    <?php endif;
                                }

                                if ($show_category) :
                                    $categories = get_the_category();
                                    if ($categories) {
                                        echo '<span class="post-category">' . esc_html($categories[0]->name) . '</span>';
                                    }
                                endif;
                                ?>
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

        <?php
        // アーカイブリンクを表示
        $show_archive_link = isset($section['show_archive_link']) ? $section['show_archive_link'] : false;
        if ($show_archive_link) {
            $archive_url = '';
            $archive_text = __('一覧を表示', 'backbone-seo-llmo');

            switch ($display_type) {
                case 'category':
                    if ($category_id > 0) {
                        $archive_url = get_category_link($category_id);
                    }
                    break;
                case 'tag':
                    if ($tag_id > 0) {
                        $archive_url = get_tag_link($tag_id);
                    }
                    break;
                case 'post_type':
                    if ($post_type && $post_type !== 'post') {
                        $archive_url = get_post_type_archive_link($post_type);
                    }
                    break;
                case 'author':
                    if ($author_id > 0) {
                        $archive_url = get_author_posts_url($author_id);
                    }
                    break;
                case 'date':
                    if (isset($args['year'])) {
                        if (isset($args['monthnum'])) {
                            $archive_url = get_month_link($args['year'], $args['monthnum']);
                        } else {
                            $archive_url = get_year_link($args['year']);
                        }
                    }
                    break;
            }

            if ($archive_url) : ?>
                <div class="section-archive-link">
                    <a href="<?php echo esc_url($archive_url); ?>" class="archive-link-button">
                        <?php echo esc_html($archive_text); ?> →
                    </a>
                </div>
            <?php endif;
        }
        ?>
    </section>
<?php
    wp_reset_postdata();
endif;
?>
