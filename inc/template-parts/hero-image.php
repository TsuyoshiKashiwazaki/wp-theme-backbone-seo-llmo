<?php
/**
 * メインビジュアル（ヒーローイメージ）表示用テンプレートパーツ
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

// メインビジュアルを表示するかどうかを判定
if (!backbone_should_display_hero_image()) {
    return;
}

$post_id = get_the_ID();
$hero_classes = backbone_get_hero_image_classes($post_id);
$hero_style = backbone_get_hero_image_style($post_id);
$decoration = backbone_get_hero_decoration_settings($post_id);
$thumbnail_size = ($hero_style === 'fullwidth') ? 'full' : 'large';

// インラインスタイルの構築
$inline_styles = array();

// 枠線の色
if ($decoration['border'] !== 'none' && !empty($decoration['border_color'])) {
    $inline_styles[] = 'border-color: ' . esc_attr($decoration['border_color']) . ';';
}

$style_attr = !empty($inline_styles) ? ' style="' . implode(' ', $inline_styles) . '"' : '';
?>
<div class="<?php echo esc_attr($hero_classes); ?>"<?php echo $style_attr; ?>>
    <div class="hero-image-container">
        <?php the_post_thumbnail($thumbnail_size, array('class' => 'hero-image-img')); ?>
    </div>
</div>
