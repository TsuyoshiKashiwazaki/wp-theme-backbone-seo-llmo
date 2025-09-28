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
                    <?php
                    the_archive_description();
                    ?>
				<?php kspb_display_breadcrumbs(); ?>
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
                                        <span class="byline">
                                            <?php echo __('投稿者:', 'backbone-seo-llmo') . ' ' . get_the_author(); ?>
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
                    the_posts_pagination(array(
                        'prev_text' => __('前のページ', 'backbone-seo-llmo'),
                        'next_text' => __('次のページ', 'backbone-seo-llmo'),
                    ));
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
