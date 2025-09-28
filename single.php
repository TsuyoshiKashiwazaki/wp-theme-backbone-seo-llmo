<?php
/**
 * 投稿詳細ページテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

                    <?php if (get_post_type() === 'post') : // 動的チェック ?>
                        <div class="entry-meta">
                            <span class="posted-on">
                                <time class="entry-date published" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                                <?php if (get_the_time('U') !== get_the_modified_time('U')) : ?>
                                    <time class="updated" datetime="<?php echo get_the_modified_date('c'); ?>">
                                        <?php printf(__('更新日: %s', 'backbone-seo-llmo'), get_the_modified_date()); ?>
                                    </time>
                                <?php endif; ?>
                            </span>

                            <span class="byline">
                                <?php echo __('投稿者:', 'backbone-seo-llmo') . ' ' . get_the_author(); ?>
                            </span>

                            <?php if (has_category()) : ?>
                                <span class="cat-links">
                                    <?php echo get_the_category_list(', '); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (has_tag()) : ?>
                                <span class="tags-links">
                                    <?php echo get_the_tag_list('', ', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>

                    <?php
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('ページ:', 'backbone-seo-llmo'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="entry-footer">
                    <?php
                    // 前後の投稿へのナビゲーション
                    the_post_navigation(array(
                        'prev_text' => '<span class="nav-subtitle">' . __('前の投稿', 'backbone-seo-llmo') . '</span> <span class="nav-title">%title</span>',
                        'next_text' => '<span class="nav-subtitle">' . __('次の投稿', 'backbone-seo-llmo') . '</span> <span class="nav-title">%title</span>',
                    ));
                    ?>
                </footer>
            </article>

            <?php
            // コメントの表示
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

        <?php endwhile; ?>

<?php get_footer(); ?>
