<?php
/**
 * デザイン設定関連のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * デザイン設定セクションを追加
 */
function backbone_add_design_settings($wp_customize) {
    // セクション：デザイン設定
    $wp_customize->add_section('backbone_design', array(
        'title'    => __('デザイン設定', 'kashiwazaki-searchcraft'),
        'priority' => 30,
    ));

    // カラーテーマの選択
    $wp_customize->add_setting('color_theme', array(
        'default'           => 'none',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('color_theme', array(
        'label'    => __('カラーテーマ', 'kashiwazaki-searchcraft'),
        'section'  => 'backbone_design',
        'type'     => 'select',
        'choices'  => backbone_get_color_theme_choices(),
        'description' => __('サイト全体の色彩設計を選択できます。「設定なし」を選択すると詳細なカラー設定が表示されます。', 'kashiwazaki-searchcraft'),
    ));

    // デザインパターンの選択
    $wp_customize->add_setting('design_pattern', array(
        'default'           => 'none',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('design_pattern', array(
        'label'    => __('デザインパターン', 'kashiwazaki-searchcraft'),
        'section'  => 'backbone_design',
        'type'     => 'select',
        'choices'  => backbone_get_design_pattern_choices(),
        'description' => __('サイト全体のレイアウトデザインを選択できます。', 'kashiwazaki-searchcraft'),
    ));

    // タイポグラフィパターンの選択
    $wp_customize->add_setting('text_pattern', array(
        'default'           => 'none',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('text_pattern', array(
        'label'    => __('タイポグラフィパターン', 'kashiwazaki-searchcraft'),
        'section'  => 'backbone_design',
        'type'     => 'select',
        'choices'  => backbone_get_typography_pattern_choices(),
        'description' => __('サイト全体の文字スタイルを選択できます。「設定なし」を選択すると詳細なタイポグラフィ設定が表示されます。', 'kashiwazaki-searchcraft'),
    ));

    // デコレーションパターンの選択
    $wp_customize->add_setting('decoration_pattern', array(
        'default'           => 'none',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('decoration_pattern', array(
        'label'    => __('デコレーションパターン', 'kashiwazaki-searchcraft'),
        'section'  => 'backbone_design',
        'type'     => 'select',
        'choices'  => backbone_get_decoration_pattern_choices(),
        'description' => __('見出しやリストの装飾スタイルを選択できます。', 'kashiwazaki-searchcraft'),
    ));
}



