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
                    // メタ情報の表示設定を取得（統一設定または個別設定）
                    $show_date = backbone_get_post_meta_setting('show_date', true);
                    $show_modified = backbone_get_post_meta_setting('show_modified', true);
                    $show_author = backbone_get_post_meta_setting('show_author', false);
                    $show_category = backbone_get_post_meta_setting('show_category', false);
                    $show_tags = backbone_get_post_meta_setting('show_tags', false);

                    // いずれかの表示設定がオンの場合のみメタ情報を表示
                    if ($show_date || $show_modified || $show_author || $show_category || $show_tags) :
                    ?>
                        <div class="entry-meta">
                            <?php if ($show_date) : ?>
                                <span class="meta-label"><?php echo __('投稿', 'backbone-seo-llmo'); ?>:</span>
                                <time datetime="<?php echo get_the_date('c'); ?>" class="meta-badge date-badge">
                                    <?php echo get_the_date(); ?>
                                </time>
                            <?php endif; ?>

                            <?php if ($show_modified && get_the_modified_date('c') !== get_the_date('c')) : ?>
                                <span class="meta-label"><?php echo __('更新', 'backbone-seo-llmo'); ?>:</span>
                                <time datetime="<?php echo get_the_modified_date('c'); ?>" class="meta-badge modified-badge">
                                    <?php echo get_the_modified_date(); ?>
                                </time>
                            <?php endif; ?>

                            <?php if ($show_author) : ?>
                                <span class="meta-label"><?php echo __('著者', 'backbone-seo-llmo'); ?>:</span>
                                <span class="meta-badge author-badge">
                                    <?php echo get_the_author(); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($show_category && has_category()) : ?>
                                <span class="meta-label"><?php echo __('カテゴリ', 'backbone-seo-llmo'); ?>:</span>
                                <?php
                                $categories = get_the_category();
                                foreach ($categories as $category) :
                                ?>
                                    <span class="meta-badge category-badge">
                                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"><?php echo esc_html($category->name); ?></a>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if ($show_tags && has_tag()) : ?>
                                <span class="meta-label"><?php echo __('タグ', 'backbone-seo-llmo'); ?>:</span>
                                <?php
                                $tags = get_the_tags();
                                $tags_limit = backbone_get_post_meta_setting('tags_limit', 5);
                                $tag_count = 0;
                                foreach ($tags as $tag) :
                                    $is_hidden = ($tag_count >= $tags_limit);
                                    $hidden_class = $is_hidden ? ' tags-hidden' : '';
                                ?>
                                    <span class="meta-badge tags-badge<?php echo $hidden_class; ?>" <?php echo $is_hidden ? 'style="display:none;"' : ''; ?>>
                                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>"><?php echo esc_html($tag->name); ?></a>
                                    </span>
                                <?php
                                    $tag_count++;
                                endforeach;

                                // 残りのタグがある場合は展開ボタンを表示
                                $remaining = count($tags) - $tags_limit;
                                if ($remaining > 0) :
                                ?>
                                    <button class="meta-badge tags-more-btn" onclick="Array.from(document.querySelectorAll('.tags-hidden')).forEach(el => el.style.display='inline-block'); this.style.display='none';">
                                        +<?php echo $remaining; ?>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    // メインビジュアルを表示
                    require get_template_directory() . '/template-parts/hero-image.php';
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
