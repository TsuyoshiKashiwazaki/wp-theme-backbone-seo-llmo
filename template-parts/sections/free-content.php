<?php
/**
 * フリーコンテンツエリア
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

$free_content = get_theme_mod('backbone_front_free_content', '');

if ($free_content) :
?>
    <section class="free-content-section">
        <div class="free-content">
            <?php echo wp_kses_post($free_content); ?>
        </div>
    </section>
<?php endif; ?>
