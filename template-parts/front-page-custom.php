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

    // セクション表示順序を取得
    $sections_order = get_theme_mod('backbone_front_sections_order', '["list_1","individual_1","list_2","individual_2","list_3","individual_3","list_4","individual_4","list_5","individual_5"]');
    $order_array = json_decode($sections_order, true);

    if (!is_array($order_array)) {
        $order_array = array('list_1', 'individual_1', 'list_2', 'individual_2', 'list_3', 'individual_3', 'list_4', 'individual_4', 'list_5', 'individual_5');
    }

    // 順序に従ってセクションを表示
    foreach ($order_array as $section_key) {
        // 一覧表示セクション
        if (strpos($section_key, 'list_') === 0) {
            $section_num = str_replace('list_', '', $section_key);
            $enable_key = 'backbone_front_list_' . $section_num . '_enable';
            $sections_key = 'backbone_front_list_sections_' . $section_num;

            // 有効化されている場合のみ表示
            if (get_theme_mod($enable_key, false)) {
                $list_sections = get_theme_mod($sections_key, '');
                if (!empty($list_sections)) {
                    $list_sections_data = json_decode($list_sections, true);
                    if (is_array($list_sections_data) && !empty($list_sections_data)) {
                        foreach ($list_sections_data as $section) {
                            set_query_var('section_data', $section);
                            get_template_part('template-parts/sections/list-section');
                        }
                    }
                }
            }
        }
        // 個別記事セクション
        elseif (strpos($section_key, 'individual_') === 0) {
            $section_num = str_replace('individual_', '', $section_key);
            $enable_key = 'backbone_front_individual_' . $section_num . '_enable';
            $layout_key = 'backbone_front_individual_' . $section_num . '_layout';
            $sections_key = 'backbone_front_individual_sections_' . $section_num;

            // 有効化されている場合のみ表示
            if (get_theme_mod($enable_key, false)) {
                $individual_items = get_theme_mod($sections_key, '');
                if (!empty($individual_items)) {
                    $individual_items_data = json_decode($individual_items, true);
                    if (is_array($individual_items_data) && !empty($individual_items_data)) {
                        set_query_var('individual_items_data', $individual_items_data);
                        set_query_var('individual_layout', get_theme_mod($layout_key, '2col'));
                        get_template_part('template-parts/sections/individual-section');
                    }
                }
            }
        }
    }

    // フリーコンテンツエリア
    $free_content = get_theme_mod('backbone_front_free_content', '');
    if ($free_content) {
        get_template_part('template-parts/sections/free-content');
    }
    ?>
</div>
