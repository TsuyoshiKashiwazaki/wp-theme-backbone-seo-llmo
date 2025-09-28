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

            <?php while (have_posts()) : ?>
                <?php the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>

                        <div class="entry-meta">
                            <span class="post-type">
                                <?php echo get_post_type_object(get_post_type())->labels->singular_name; ?>
                            </span>

                            <?php if (get_post_type() === 'post') : // 動的チェック ?>
                                <span class="posted-on">
                                    <time class="entry-date published" datetime="<?php echo get_the_date('c'); ?>">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                </span>

                                <span class="byline">
                                    <?php echo __('投稿者:', 'backbone-seo-llmo') . ' ' . get_the_author(); ?>
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
