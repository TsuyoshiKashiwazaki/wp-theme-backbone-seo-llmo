<?php
/**
 * フロントページ（ホームページ）用テンプレートファイル
 *
 * @package Backbone_SEO_LLMO
 */

get_header();

// フロントページモードを取得
$front_page_mode = get_theme_mod('backbone_front_page_mode', 'custom');

if ($front_page_mode === 'custom') {
    // カスタムフロントページを表示
    get_template_part('template-parts/front-page-custom');
} else {
    // 既存のページを使用
    $page_type = get_theme_mod('backbone_front_page_type', 'static_page');

    if ($page_type === 'static_page') {
        // 固定ページを使用
        $selected_id = get_theme_mod('backbone_front_selected_page', 0);
    } else {
        // 投稿を使用
        $selected_id = get_theme_mod('backbone_front_selected_post', 0);
    }

    $post_to_display = ($selected_id > 0) ? get_post($selected_id) : null;

    if ($post_to_display) {
        // 選択されたページまたは投稿を表示
        setup_postdata($GLOBALS['post'] =& $post_to_display);
        ?>

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

        <?php
        wp_reset_postdata();
    } else {
        // ページが選択されていない場合のメッセージ
        ?>
        <section class="no-results not-found">
            <header class="page-header">
                <h1 class="page-title"><?php _e('ページが選択されていません', 'backbone-seo-llmo'); ?></h1>
            </header>

            <div class="page-content">
                <p><?php _e('カスタマイザーの「フロントページ設定」で固定ページまたは投稿ページを選択してください。', 'backbone-seo-llmo'); ?></p>
            </div>
        </section>
        <?php
    }
}

get_footer(); ?>
