<?php
/**
 * ヒーローセクション
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

$hero_image_id = get_theme_mod('backbone_front_hero_image', '');
$hero_height = get_theme_mod('backbone_front_hero_height', '400');
$hero_overlay = get_theme_mod('backbone_front_hero_overlay', '0.3');
$title = get_theme_mod('backbone_front_title', '');
$catchphrase = get_theme_mod('backbone_front_catchphrase', '');
$description = get_theme_mod('backbone_front_description', '');

// ヒーローイメージまたはタイトルがある場合のみ表示
if ($hero_image_id || $title) :
?>
    <section class="hero-section">
        <?php if ($hero_image_id) :
            $hero_image_url = wp_get_attachment_image_url($hero_image_id, 'full');
        ?>
            <div class="hero-image" style="background-image: url('<?php echo esc_url($hero_image_url); ?>'); height: <?php echo esc_attr($hero_height); ?>px;">
                <?php if ($hero_overlay > 0) : ?>
                    <div class="hero-overlay" style="background-color: rgba(0, 0, 0, <?php echo esc_attr($hero_overlay); ?>);"></div>
                <?php endif; ?>

                <div class="hero-content">
                    <?php if ($title) : ?>
                        <h1 class="hero-title"><?php echo esc_html($title); ?></h1>
                    <?php endif; ?>

                    <?php if ($catchphrase) : ?>
                        <p class="hero-catchphrase"><?php echo esc_html($catchphrase); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$hero_image_id && $title) : ?>
            <div class="hero-text-only">
                <h1 class="hero-title"><?php echo esc_html($title); ?></h1>
                <?php if ($catchphrase) : ?>
                    <p class="hero-catchphrase"><?php echo esc_html($catchphrase); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($description) : ?>
            <div class="hero-description">
                <?php echo wp_kses_post($description); ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
