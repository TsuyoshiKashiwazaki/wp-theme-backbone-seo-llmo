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
                </header>

                <?php
                // メインビジュアルを表示
                require get_template_directory() . '/template-parts/hero-image.php';
                ?>

                <div class="entry-content">
                    <?php the_content(); ?>

                    <?php
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('ページ:', 'backbone-seo-llmo'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
            </article>

            <?php
            // コメントの表示
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

        <?php endwhile; ?>

<?php get_footer(); ?>
