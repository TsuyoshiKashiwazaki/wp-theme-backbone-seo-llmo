<?php
/**
 * 検索フォームテンプレート
 *
 * @package Backbone_SEO_LLMO
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="search-field-<?php echo uniqid(); ?>" class="screen-reader-text">
        <?php _e('検索:', 'backbone-seo-llmo'); ?>
    </label>
    <div class="search-form-wrapper">
        <input
            type="search"
            id="search-field-<?php echo uniqid(); ?>"
            class="search-field"
            placeholder="<?php echo esc_attr_x('キーワードを入力...', 'placeholder', 'backbone-seo-llmo'); ?>"
            value="<?php echo get_search_query(); ?>"
            name="s"
            title="<?php echo esc_attr_x('検索:', 'label', 'backbone-seo-llmo'); ?>"
            required
        />
        <button type="submit" class="search-submit">
            <span class="screen-reader-text"><?php echo _x('検索', 'submit button', 'backbone-seo-llmo'); ?></span>
            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true">
                <path d="M15.707 14.293l-4.822-4.822A6.019 6.019 0 0012 6a6 6 0 10-6 6 6.019 6.019 0 003.471-1.115l4.822 4.822a1 1 0 001.414-1.414zM6 10a4 4 0 114-4 4.005 4.005 0 01-4 4z"/>
            </svg>
        </button>
    </div>
</form>
