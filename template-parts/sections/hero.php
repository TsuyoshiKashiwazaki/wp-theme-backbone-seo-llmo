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

// 説明文の取得（手動入力 or ページから取得）
$description_source = get_theme_mod('backbone_front_description_source', 'manual');
$description = '';
$description_title = '';

if ($description_source === 'page') {
    // ページからコンテンツを取得
    $source_page_id = get_theme_mod('backbone_front_description_page', 0);
    $show_title = get_theme_mod('backbone_front_description_show_title', false);
    $use_source_thumbnail = get_theme_mod('backbone_front_use_source_thumbnail', false);

    if ($source_page_id) {
        $source_post = get_post($source_page_id);
        if ($source_post && $source_post->post_status === 'publish') {
            // タイトルを取得（オプション）
            if ($show_title) {
                $description_title = $source_post->post_title;
            }

            // アイキャッチ画像をメインビジュアルに使用（オプション）
            if ($use_source_thumbnail && has_post_thumbnail($source_page_id)) {
                $hero_image_id = get_post_thumbnail_id($source_page_id);
            }

            // 投稿タイプに応じてコンテンツを取得
            if ($source_post->post_type === 'poll') {
                // poll投稿タイプの場合はカスタムフィールドから取得
                $poll_description = get_post_meta($source_page_id, '_kashiwazaki_poll_description', true);
                if ($poll_description) {
                    $description = wpautop($poll_description);
                }
            } else {
                // 通常の投稿・固定ページの場合
                // ブロックエディタのコンテンツを正しくレンダリングするため
                // グローバルポストを一時的に切り替える
                global $post;
                $original_post = $post;
                $post = $source_post;
                setup_postdata($post);

                // the_content フィルターでブロックをレンダリング
                $description = apply_filters('the_content', $source_post->post_content);

                // グローバルポストを元に戻す
                $post = $original_post;
                if ($original_post) {
                    setup_postdata($original_post);
                } else {
                    wp_reset_postdata();
                }
            }
        }
    }
} else {
    // 手動入力の説明文
    $description = get_theme_mod('backbone_front_description', '');
}

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

        <?php if ($description || $description_title) : ?>
            <div class="hero-description">
                <?php if ($description_title) : ?>
                    <h2 class="hero-description-title"><?php echo esc_html($description_title); ?></h2>
                <?php endif; ?>
                <?php
                // コンテンツは the_content フィルターを通過済みで、
                // 管理者が選択した投稿からのものなので直接出力
                // （通常の the_content() と同じ動作）
                echo $description;
                ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
