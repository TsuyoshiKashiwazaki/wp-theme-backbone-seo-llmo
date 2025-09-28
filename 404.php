<?php
/**
 * 404エラーページテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php _e('404 - ページが見つかりません', 'backbone-seo-llmo'); ?></h1>
            </header>

            <div class="page-content">
                <div class="error-message">
                    <h2><?php _e('申し訳ございません', 'backbone-seo-llmo'); ?></h2>
                    <p><?php _e('お探しのページは見つかりませんでした。URLが間違っているか、ページが移動または削除された可能性があります。', 'backbone-seo-llmo'); ?></p>
                </div>

                <div class="error-search">
                    <h3><?php _e('検索してみてください', 'backbone-seo-llmo'); ?></h3>
                    <?php get_search_form(); ?>
                </div>

                <div class="error-suggestions">
                    <h3><?php _e('よく見られているページ', 'backbone-seo-llmo'); ?></h3>
                    <div class="popular-posts">
                        <?php
                        $popular_posts = get_posts(array(
                            'numberposts' => 5,
                            'meta_key' => 'views',
                            'orderby' => 'meta_value_num',
                            'order' => 'DESC',
                            'post_status' => 'publish'
                        ));

                        if ($popular_posts) :
                        ?>
                            <ul>
                                <?php foreach ($popular_posts as $post) : setup_postdata($post); ?>
                                    <li>
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        <span class="post-date"><?php echo get_the_date(); ?></span>
                                    </li>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </ul>
                        <?php else : ?>
                            <?php
                            $recent_posts = get_posts(array(
                                'numberposts' => 5,
                                'post_status' => 'publish'
                            ));
                            ?>
                            <ul>
                                <?php foreach ($recent_posts as $post) : setup_postdata($post); ?>
                                    <li>
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        <span class="post-date"><?php echo get_the_date(); ?></span>
                                    </li>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="error-categories">
                    <h3><?php _e('カテゴリー', 'backbone-seo-llmo'); ?></h3>
                    <ul class="category-list">
                        <?php wp_list_categories(array(
                            'title_li' => '',
                            'show_count' => true,
                            'number' => 10,
                        )); ?>
                    </ul>
                </div>

                <div class="error-actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                        <?php _e('ホームページに戻る', 'backbone-seo-llmo'); ?>
                    </a>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <?php _e('前のページに戻る', 'backbone-seo-llmo'); ?>
                    </a>
                </div>
            </div>
        </section>

<?php get_footer(); ?>
