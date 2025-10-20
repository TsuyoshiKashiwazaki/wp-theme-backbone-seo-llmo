<?php
/**
 * サービス・機能紹介セクション
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

$section_title = get_theme_mod('backbone_front_services_title', __('サービス紹介', 'backbone-seo-llmo'));
$layout = get_theme_mod('backbone_front_services_layout', '3col');

// サービスカードを収集（リピーターから）
$services = array();
$service_items_json = get_theme_mod('backbone_front_service_items', '');

if (!empty($service_items_json)) {
    $service_items = json_decode($service_items_json, true);
    if (is_array($service_items)) {
        foreach ($service_items as $item) {
            if (isset($item['title']) && !empty($item['title'])) {
                $services[] = array(
                    'title' => $item['title'],
                    'desc' => isset($item['desc']) ? $item['desc'] : '',
                    'url' => isset($item['url']) ? $item['url'] : '',
                );
            }
        }
    }
}

if (!empty($services)) :
?>
    <section class="services-section">
        <?php if ($section_title) : ?>
            <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <div class="services-list services-layout-<?php echo esc_attr($layout); ?>">
            <?php foreach ($services as $service) : ?>
                <div class="service-item">
                    <?php if ($service['url']) : ?>
                        <a href="<?php echo esc_url($service['url']); ?>" class="service-link">
                    <?php endif; ?>

                    <div class="service-content">
                        <h3 class="service-title"><?php echo esc_html($service['title']); ?></h3>
                        <?php if ($service['desc']) : ?>
                            <div class="service-desc"><?php echo wp_kses_post($service['desc']); ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($service['url']) : ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
