<?php
/**
 * 投稿ページ（ブログ一覧）用テンプレートファイル
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <?php
        // 投稿ページの内容を表示（設定されている場合）
        $posts_page_id = get_option('page_for_posts');
        if ($posts_page_id) {
            $posts_page = get_post($posts_page_id);
            if ($posts_page) : ?>
                <article id="posts-page-<?php echo $posts_page_id; ?>" class="posts-page-content">
                    <header class="page-header">
                        <h1 class="page-title"><?php echo get_the_title($posts_page); ?></h1>
                    </header>

                    <?php if (has_post_thumbnail($posts_page_id)) : ?>
                        <div class="page-thumbnail">
                            <?php echo get_the_post_thumbnail($posts_page_id, 'large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="page-content">
                        <?php echo apply_filters('the_content', $posts_page->post_content); ?>
                    </div>
                </article>
            <?php endif;
        }
        ?>

        <!-- 投稿一覧の表示 -->
        <?php if (have_posts()) : ?>
            <div class="posts-section">
                <header class="posts-section-header">
                    <h2 class="posts-section-title"><?php _e('最新の投稿', 'backbone-seo-llmo'); ?></h2>
                </header>

                <div class="posts-grid">
                    <?php while (have_posts()) : ?>
                        <?php the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                            <header class="entry-header">
                                <h3 class="entry-title">
                                    <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h3>

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
                            </header>

                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="entry-content">
                                <?php the_excerpt(); ?>
                                <p class="read-more">
                                    <a href="<?php the_permalink(); ?>" class="more-link">
                                        <?php _e('続きを読む', 'backbone-seo-llmo'); ?>
                                    </a>
                                </p>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php
                // ページネーション
                backbone_pagination();
                ?>
            </div>
        <?php else : ?>
            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php _e('投稿が見つかりません', 'backbone-seo-llmo'); ?></h1>
                </header>

                <div class="page-content">
                    <?php if (current_user_can('publish_posts')) : ?>
                        <p><?php printf(__('投稿を開始するには<a href="%1$s">こちらをクリック</a>してください。', 'backbone-seo-llmo'), esc_url(admin_url('post-new.php'))); ?></p>
                    <?php else : ?>
                        <p><?php _e('まだ投稿がありません。', 'backbone-seo-llmo'); ?></p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

<?php get_footer(); ?>

