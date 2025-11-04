<?php
/**
 * 404エラーページテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

status_header(404);
nocache_headers();

get_header(); ?>
        <article class="error-404 not-found">
            <header class="entry-header">
                <h1 class="entry-title"><?php _e('404 - ページが見つかりません', 'backbone-seo-llmo'); ?></h1>
            </header>

            <div class="entry-content">
                <p><?php _e('お探しのページは見つかりませんでした。別のキーワードで検索してみてください。', 'backbone-seo-llmo'); ?></p>
                <?php get_search_form(); ?>
            </div>
        </article>

<?php get_footer(); ?>
