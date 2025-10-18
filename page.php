<?php
/**
 * 固定ページテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>

            <?php
            // フロントページかつタイトル非表示設定の場合は表示しない
            $show_title = true;
            if (is_front_page()) {
                $show_title = get_theme_mod('front_page_show_title', true);
            }

            $header_class = $show_title ? '' : ' no-title';
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header<?php echo esc_attr($header_class); ?>">
                    <?php if ($show_title) : ?>
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    <?php endif; ?>

                    <?php
                    // メインビジュアルを表示
                    require get_template_directory() . '/inc/template-parts/hero-image.php';
                    ?>
                </header>

                <div class="entry-content">
                    <?php the_content(); ?>

                    <?php
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('ページ:', 'backbone-seo-llmo'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <?php if (get_edit_post_link()) : ?>
                    <footer class="entry-footer">
                        <?php
                        edit_post_link(
                            sprintf(
                                wp_kses(
                                    __('編集 <span class="screen-reader-text">"%s"</span>', 'backbone-seo-llmo'),
                                    array(
                                        'span' => array(
                                            'class' => array(),
                                        ),
                                    )
                                ),
                                get_the_title()
                            ),
                            '<span class="edit-link">',
                            '</span>'
                        );
                        ?>
                    </footer>
                <?php endif; ?>
            </article>

            <?php
            // コメントの表示（固定ページでコメントが有効の場合）
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

        <?php endwhile; ?>

<?php get_footer(); ?>
