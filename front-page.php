<?php
/**
 * フロントページ（ホームページ）用テンプレートファイル
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('front-page-content'); ?>>
                    <header class="entry-header">
                        <?php if (get_theme_mod('front_page_show_title', true)) : ?>
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        <?php endif; ?>
                    </header>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <?php if (comments_open() || get_comments_number()) : ?>
                        <footer class="entry-footer">
                            <?php comments_template(); ?>
                        </footer>
                    <?php endif; ?>
                </article>

            <?php endwhile; ?>
        <?php else : ?>
            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php _e('コンテンツが見つかりません', 'backbone-seo-llmo'); ?></h1>
                </header>

                <div class="page-content">
                    <p><?php _e('フロントページに設定された固定ページのコンテンツが表示されません。', 'backbone-seo-llmo'); ?></p>
                </div>
            </section>
        <?php endif; ?>

<?php get_footer(); ?>
