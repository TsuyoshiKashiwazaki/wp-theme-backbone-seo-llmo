<?php
/**
 * ナビゲーションメニューのカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ナビゲーション設定をカスタマイザーに追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 */
function backbone_add_navigation_settings($wp_customize) {
    // メニューパネル内に新しいセクションを作成
    $wp_customize->add_section('menu_options', array(
        'title' => __('メニュー表示オプション', 'backbone-seo-llmo'),
        'panel' => 'nav_menus',  // nav_menusパネルの中にセクションを作成
        'priority' => 999,
        'description' => __('メニューの表示方法に関する詳細設定です。', 'backbone-seo-llmo'),
    ));

    // ━━━ 3階層目以降のサブメニューの表示方向設定 ━━━
    // 見出しの代わりに説明テキストを表示
    $wp_customize->add_setting('submenu_third_level_info', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('submenu_third_level_info', array(
        'label' => __('深階層サブメニュー表示設定', 'backbone-seo-llmo'),
        'section' => 'menu_options',  // 新しく作成したセクションに追加
        'priority' => 10,
        'type' => 'hidden',
        'description' => __('3階層目以降（3,4,5,6階層...）のサブメニューの表示方向を設定します。', 'backbone-seo-llmo'),
    ));

    // 3階層目以降のサブメニューの表示方向
    $wp_customize->add_setting('submenu_third_level_direction', array(
        'default' => 'vertical',  // デフォルトを縦表示に変更
        'sanitize_callback' => 'backbone_sanitize_submenu_direction',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('submenu_third_level_direction', array(
        'label' => __('深階層サブメニューの表示方向', 'backbone-seo-llmo'),
        'section' => 'menu_options',  // 新しく作成したセクションに追加
        'type' => 'radio',
        'priority' => 20,
        'choices' => array(
            'vertical' => __('縦に展開（階段状にインデント）', 'backbone-seo-llmo'),
            'horizontal' => __('横に展開（右側に表示）', 'backbone-seo-llmo'),
        ),
        'description' => __('3階層目以降のサブメニューをどのように表示するかを選択してください。「縦に展開」を選択すると、親メニューの直下に階段状にインデントして表示されます。「横に展開」を選択すると、親メニューの右側に表示されます。', 'backbone-seo-llmo'),
    ));
}

/**
 * サブメニュー表示方向のサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_submenu_direction($value) {
    $valid_directions = array('horizontal', 'vertical');

    if (in_array($value, $valid_directions, true)) {
        return $value;
    }

    return 'horizontal';
}

/**
 * 深階層サブメニュー（3階層目以降）の表示方向を取得
 *
 * @return string 'horizontal' または 'vertical'
 */
function backbone_get_submenu_third_level_direction() {
    return get_theme_mod('submenu_third_level_direction', 'horizontal');
}

// モバイル関連の関数は削除（ハンバーガーメニューはプラグインで実装）