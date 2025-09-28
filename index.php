<?php
/**
 * メインテンプレートファイル
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <?php if (have_posts()) : ?>

            <?php if (is_home() && !is_front_page()) : ?>
                <header class="page-header">
                    <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                </header>
            <?php endif; ?>

            <?php while (have_posts()) : ?>
                <?php the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php
                        if (is_singular()) :
                            the_title('<h1 class="entry-title">', '</h1>');
                        else :
                            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                        endif;
                        ?>

                        <?php if (get_post_type() === 'post') : // 動的チェック ?>
                            <div class="entry-meta">
                                <span class="posted-on">
                                    <time class="entry-date published" datetime="<?php echo get_the_date('c'); ?>">
                                        <?php echo get_the_date(); ?>
                                    </time>
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

                    <?php if (has_post_thumbnail() && !is_singular()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('large'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php
                        if (is_singular()) :
                            the_content();
                        else :
                            the_excerpt();
                        endif;
                        ?>

                        <?php if (!is_singular()) : ?>
                            <p class="read-more">
                                <a href="<?php the_permalink(); ?>" class="more-link">
                                    <?php _e('続きを読む', 'backbone-seo-llmo'); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if (is_singular()) : ?>
                        <footer class="entry-footer">
                            <?php
                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . __('ページ:', 'backbone-seo-llmo'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </footer>
                    <?php endif; ?>
                </article>

                <?php
                // 投稿詳細ページでコメントを表示
                if (is_singular() && (comments_open() || get_comments_number())) :
                    comments_template();
                endif;
                ?>

            <?php endwhile; ?>

            <?php
            // ページネーション
            if (!is_singular()) :
                backbone_pagination();
            endif;
            ?>

        <?php else : ?>

            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php _e('見つかりません', 'backbone-seo-llmo'); ?></h1>
                </header>

                <div class="page-content">
                    <?php if (is_home() && current_user_can('publish_posts')) : ?>
                        <p><?php printf(__('投稿を開始するには<a href="%1$s">こちらをクリック</a>してください。', 'backbone-seo-llmo'), esc_url(admin_url('post-new.php'))); ?></p>
                    <?php elseif (is_search()) : ?>
                        <p><?php _e('お探しのキーワードに一致するものが見つかりませんでした。別のキーワードで検索してみてください。', 'backbone-seo-llmo'); ?></p>
                        <?php get_search_form(); ?>
                    <?php else : ?>
                        <p><?php _e('お探しのページは見つかりませんでした。検索をお試しください。', 'backbone-seo-llmo'); ?></p>
                        <?php get_search_form(); ?>
                    <?php endif; ?>
                </div>
            </section>

        <?php endif; ?>

<?php get_footer(); ?>
