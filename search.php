<?php
/**
 * 検索結果ページテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <?php if (have_posts()) : ?>

            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    printf(
                        __('検索結果: %s', 'backbone-seo-llmo'),
                        '<span>' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>
                <p class="search-results-count">
                    <?php
                    global $wp_query;
                    printf(
                        __('%d件の結果が見つかりました', 'backbone-seo-llmo'),
                        $wp_query->found_posts
                    );
                    ?>
                </p>
            </header>

            <?php
            // グリッド列数を取得
            $grid_columns = backbone_get_archive_setting('grid_columns', '3');

            // 表示要素の設定
            $show_thumbnail = backbone_get_archive_setting('show_thumbnail', true);
            $show_date = backbone_get_archive_setting('show_date', true);
            $show_modified = backbone_get_archive_setting('show_modified', false);
            $show_category = backbone_get_archive_setting('show_category', false);
            $show_excerpt = backbone_get_archive_setting('show_excerpt', true);
            ?>

            <div class="archive-grid-container archive-grid-columns-<?php echo esc_attr($grid_columns); ?>">
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>

                        <?php if ($show_date || $show_modified || $show_category) : ?>
                            <div class="entry-meta">
                                <?php if ($show_date) : ?>
                                    <time datetime="<?php echo get_the_date('c'); ?>" class="meta-badge date-badge">
                                        <span class="meta-label-small"><?php echo __('投稿', 'backbone-seo-llmo'); ?>:</span><?php echo get_the_date('Y/m/d'); ?>
                                    </time>
                                <?php endif; ?>

                                <?php if ($show_modified) : ?>
                                    <time datetime="<?php echo get_the_modified_date('c'); ?>" class="meta-badge modified-badge">
                                        <span class="meta-label-small"><?php echo __('更新', 'backbone-seo-llmo'); ?>:</span><?php echo get_the_modified_date('Y/m/d'); ?>
                                    </time>
                                <?php endif; ?>

                                <?php if ($show_category && get_post_type() === 'post') : ?>
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories) {
                                        echo '<span class="post-category meta-badge">' . esc_html($categories[0]->name) . '</span>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </header>

                    <?php if ($show_thumbnail && has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($show_excerpt) : ?>
                        <div class="entry-content">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                </article>

            <?php endwhile; ?>
            </div><!-- .archive-grid-container -->

            <?php
            // ページネーション
            backbone_pagination();
            ?>

        <?php else : ?>

            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php _e('見つかりません', 'backbone-seo-llmo'); ?></h1>
                </header>

                <div class="page-content">
                    <p><?php _e('お探しのキーワードに一致するものが見つかりませんでした。別のキーワードで検索してみてください。', 'backbone-seo-llmo'); ?></p>

                    <div class="search-form-wrapper">
                        <?php get_search_form(); ?>
                    </div>

                    <div class="search-suggestions">
                        <h3><?php _e('検索のヒント', 'backbone-seo-llmo'); ?></h3>
                        <ul>
                            <li><?php _e('キーワードのスペルを確認してください', 'backbone-seo-llmo'); ?></li>
                            <li><?php _e('より一般的なキーワードで検索してください', 'backbone-seo-llmo'); ?></li>
                            <li><?php _e('同義語や関連語を試してください', 'backbone-seo-llmo'); ?></li>
                        </ul>
                    </div>
                </div>
            </section>

        <?php endif; ?>

<?php get_footer(); ?>
