<?php
/**
 * カスタムフロントページのテンプレートパーツ
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="custom-front-page">
    <?php
    // ヒーローセクション
    get_template_part('template-parts/sections/hero');

    // 記事一覧セクション
    if (get_theme_mod('backbone_front_posts_enable', true)) {
        get_template_part('template-parts/sections/posts-list');
    }

    // 特集・ピックアップセクション
    if (get_theme_mod('backbone_front_pickup_enable', false)) {
        get_template_part('template-parts/sections/pickup');
    }

    // サービス・機能紹介セクション
    if (get_theme_mod('backbone_front_services_enable', false)) {
        get_template_part('template-parts/sections/services');
    }

    // フリーコンテンツエリア
    $free_content = get_theme_mod('backbone_front_free_content', '');
    if ($free_content) {
        get_template_part('template-parts/sections/free-content');
    }
    ?>
</div>
