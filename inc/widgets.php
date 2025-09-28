<?php
/**
 * ウィジェット関連の機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ウィジェットエリアの登録
 */
function backbone_widgets_init() {
    register_sidebar(array(
        'name'          => 'サイドバー1',
        'id'            => 'sidebar-1',
        'description'   => '2カラムと3カラムレイアウト用のサイドバー1です。レイアウト設定で2カラムまたは3カラムを選択すると表示されます。',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
        'class'         => '',
        'show_in_rest'  => true,
    ));

    register_sidebar(array(
        'name'          => 'サイドバー2',
        'id'            => 'sidebar-2',
        'description'   => '3カラムレイアウト専用のサイドバー2です。レイアウト設定で3カラムを選択すると表示されます。',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
        'class'         => '',
        'show_in_rest'  => true,
    ));

    register_sidebar(array(
        'name'          => 'フッターエリア',
        'id'            => 'footer-widgets',
        'description'   => 'フッターのウィジェットエリアです。',
        'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
        'class'         => '',
        'show_in_rest'  => true,
    ));
}
add_action('widgets_init', 'backbone_widgets_init');

/**
 * カスタマイザーでのウィジェット表示を改善
 */
function backbone_customize_widgets($wp_customize) {
    // ウィジェットセクションを常に表示
    $widget_sections = array('sidebar-widgets-sidebar-1', 'sidebar-widgets-sidebar-2', 'sidebar-widgets-footer-widgets');

    foreach ($widget_sections as $section_id) {
        if (isset($wp_customize->sections[$section_id])) {
            $wp_customize->sections[$section_id]->active_callback = '__return_true';
        }
    }
}
add_action('customize_register', 'backbone_customize_widgets', 20);





