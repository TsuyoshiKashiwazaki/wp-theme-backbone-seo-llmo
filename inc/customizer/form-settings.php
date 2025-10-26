<?php
/**
 * Form Settings for Customizer
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

function backbone_add_form_settings($wp_customize) {
    $wp_customize->add_section('form_settings', array(
        'title' => __('フォーム表示設定', 'backbone-seo-llmo'),
        'priority' => 56,
        'description' => __('入力フォームの表示設定を行います。Contact Form 7や自作フォームなど、すべてのフォームに適用されます。', 'backbone-seo-llmo'),
    ));

    $wp_customize->add_setting('form_input_padding_vertical', array(
        'default' => 12,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_input_padding_vertical', array(
        'label' => __('入力欄のパディング（上下）', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('入力欄の上下の余白（px）', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 0,
            'max' => 30,
            'step' => 1,
        ),
    ));

    $wp_customize->add_setting('form_input_padding_horizontal', array(
        'default' => 15,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_input_padding_horizontal', array(
        'label' => __('入力欄のパディング（左右）', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('入力欄の左右の余白（px）', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 0,
            'max' => 50,
            'step' => 1,
        ),
    ));

    $wp_customize->add_setting('form_input_font_size', array(
        'default' => 16,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_input_font_size', array(
        'label' => __('フォントサイズ', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('入力欄のフォントサイズ（px）', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 12,
            'max' => 24,
            'step' => 1,
        ),
    ));

    $wp_customize->add_setting('form_input_border_width', array(
        'default' => 2,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_input_border_width', array(
        'label' => __('ボーダーの太さ', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('入力欄のボーダーの太さ（px）', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 0,
            'max' => 5,
            'step' => 1,
        ),
    ));

    $wp_customize->add_setting('form_input_margin_bottom', array(
        'default' => 15,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_input_margin_bottom', array(
        'label' => __('入力欄同士の間隔', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('各入力欄の下の間隔（px）', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 0,
            'max' => 50,
            'step' => 1,
        ),
    ));

    $wp_customize->add_setting('form_textarea_min_height', array(
        'default' => 150,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_textarea_min_height', array(
        'label' => __('テキストエリアの最小高さ', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('テキストエリア（メッセージ欄など）の最小高さ（px）', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 50,
            'max' => 500,
            'step' => 10,
        ),
    ));

    $wp_customize->add_setting('form_input_line_height', array(
        'default' => 1.5,
        'sanitize_callback' => 'backbone_sanitize_float',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_input_line_height', array(
        'label' => __('行の高さ', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('テキストの行の高さ（1.0〜2.0）', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 1.0,
            'max' => 2.0,
            'step' => 0.1,
        ),
    ));

    // 最大幅
    $wp_customize->add_setting('form_input_max_width', array(
        'default' => 600,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('form_input_max_width', array(
        'label' => __('入力欄の最大幅', 'backbone-seo-llmo'),
        'section' => 'form_settings',
        'type' => 'number',
        'description' => __('入力欄の最大幅（px）。0で無制限（100%）。広い画面で入力欄が広がりすぎるのを防ぎます。', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min' => 0,
            'max' => 1200,
            'step' => 10,
        ),
    ));
}
