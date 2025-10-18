<?php
/**
 * アーカイブページテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

get_header(); ?>
        <?php if (have_posts()) : ?>

            <article class="page type-page status-publish hentry archive-page">
                <header class="entry-header">
                    <?php
                    // 動的なアーカイブタイトルを直接生成（ハードコード回避）
                    $archive_title = '';

                    // 確実にカスタム投稿タイプを検出
                    $post_type = get_query_var('post_type');

                    // 複数の方法でカスタム投稿タイプを検出
                    if (empty($post_type)) {
                        // 方法1: URLパス解析
                        if (!empty($_SERVER['REQUEST_URI'])) {
                            $path_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                            $path_parts = array_filter($path_parts); // 空要素削除
                            if (count($path_parts) >= 2) {
                                // 最後の部分をチェック
                                $potential_type = end($path_parts);
                                $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
                                if (in_array($potential_type, $custom_post_types)) {
                                    $post_type = $potential_type;
                                }
                            }
                        }
                    }

                    // 方法2: is_post_type_archive()とget_queried_object()
                    if (empty($post_type) && is_post_type_archive()) {
                        $queried_obj = get_queried_object();
                        if ($queried_obj && isset($queried_obj->name)) {
                            $post_type = $queried_obj->name;
                        }
                    }

                    // カスタム投稿タイプのラベルを取得
                    if (!empty($post_type)) {
                        $post_type_obj = get_post_type_object($post_type);
                        if ($post_type_obj && !empty($post_type_obj->labels->name)) {
                            $archive_title = $post_type_obj->labels->name;
                        }
                    }

                    // フォールバック
                    if (empty($archive_title)) {
                        $archive_title = get_the_archive_title();
                        // 「アーカイブ:」を削除
                        $archive_title = preg_replace('/^.*:\s*/', '', $archive_title);
                    }

                    echo '<h1 class="entry-title">' . esc_html($archive_title) . '</h1>';
                    ?>
                </header>

                <div class="entry-content">
				<?php if (function_exists('kspb_display_breadcrumbs')) : kspb_display_breadcrumbs(); endif; ?>

                    <?php
                    // グリッド列数を取得
                    $grid_columns = get_theme_mod('archive_grid_columns', '3');
                    ?>
                    <div class="archive-grid-container archive-grid-columns-<?php echo esc_attr($grid_columns); ?>">
                    <?php while (have_posts()) : ?>
                        <?php the_post(); ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <header class="entry-header">
                                <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>

                                <?php
                                $current_post_type_obj = get_post_type_object(get_post_type());
                                if ($current_post_type_obj && $current_post_type_obj->public) :
                                ?>
                                    <div class="entry-meta">
                                        <span class="posted-on">
                                            <time class="entry-date published" datetime="<?php echo get_the_date('c'); ?>">
                                                <?php echo get_the_date(); ?>
                                            </time>
                                        </span>
                                    </div>
                                <?php endif; ?>
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
                            </div>
                        </article>

                    <?php endwhile; ?>
                    </div><!-- .archive-grid-container -->

                    <?php
                    // ページネーション（カスタム /page-2/ 形式）
                    global $wp_query;
                    if ($wp_query->max_num_pages > 1) {
                        $current_page = max(1, get_query_var('paged'));

                        // 現在のアーカイブURLを取得（ページネーション部分を除去）
                        $current_url = get_pagenum_link(1);
                        $current_url = preg_replace('#/page/\d+/?#', '', $current_url);
                        $current_url = preg_replace('#/page-\d+/?#', '', $current_url);
                        $current_url = trailingslashit($current_url);

                        echo '<nav class="navigation pagination" aria-label="Posts pagination">';
                        echo '<h2 class="screen-reader-text">Posts pagination</h2>';
                        echo '<div class="nav-links">';

                        $pagination_args = array(
                            'base' => $current_url . '%_%',
                            'format' => 'page-%#%/',
                            'current' => $current_page,
                            'total' => $wp_query->max_num_pages,
                            'prev_text' => __('前のページ', 'backbone-seo-llmo'),
                            'next_text' => __('次のページ', 'backbone-seo-llmo'),
                            'mid_size' => 2,
                            'end_size' => 1,
                            'add_args' => false,
                        );

                        echo paginate_links($pagination_args);
                        echo '</div></nav>';
                    }
                    ?>
                </div>
            </article>

        <?php else : ?>

            <article class="page no-results">
                <header class="entry-header">
                    <h1 class="entry-title"><?php _e('見つかりません', 'backbone-seo-llmo'); ?></h1>
                </header>

                <div class="entry-content">
                    <p><?php _e('このアーカイブには投稿がありません。', 'backbone-seo-llmo'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </article>

        <?php endif; ?>

<?php get_footer(); ?>
