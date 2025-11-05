<?php
/**
 * メインビジュアル（ヒーローイメージ）のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * メインビジュアル設定をカスタマイザーに追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 */
function backbone_add_hero_image_settings($wp_customize) {
    // メインビジュアル設定のセクションを追加
    $wp_customize->add_section('hero_image_settings', array(
        'title' => __('メインビジュアル設定', 'backbone-seo-llmo'),
        'priority' => 50,
        'description' => __('メインビジュアル（アイキャッチ画像）の表示設定を行います。', 'backbone-seo-llmo'),
    ));

    // 設定モード選択（共通設定 or 個別設定）
    $wp_customize->add_setting('hero_image_setting_mode', array(
        'default' => 'common',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_image_setting_mode', array(
        'label' => __('設定モード', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'type' => 'radio',
        'choices' => array(
            'common' => __('共通設定（全投稿タイプ共通）', 'backbone-seo-llmo'),
            'individual' => __('個別設定（投稿タイプごと）', 'backbone-seo-llmo'),
        ),
        'description' => __('すべての投稿タイプで同じ設定を使う場合は「共通設定」、投稿タイプごとに異なる設定をする場合は「個別設定」を選択してください。', 'backbone-seo-llmo'),
    ));

    // === 共通設定項目 ===

    // 共通: メインビジュアルを表示
    $wp_customize->add_setting('hero_image_enable_common', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_image_enable_common', array(
        'label' => __('メインビジュアルを表示', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'type' => 'checkbox',
        'description' => __('すべての投稿タイプでメインビジュアルをデフォルトで表示します。個別ページで上書き可能です。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('hero_image_setting_mode', 'common') === 'common';
        },
    ));

    // 共通: 表示スタイル設定
    $wp_customize->add_setting('hero_image_style_common', array(
        'default' => 'standard',
        'sanitize_callback' => 'backbone_sanitize_hero_style',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_image_style_common', array(
        'label' => __('表示スタイル', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'type' => 'select',
        'choices' => backbone_get_hero_style_options(),
        'description' => __('メインビジュアルの表示スタイルを選択します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('hero_image_setting_mode', 'common') === 'common' &&
                   get_theme_mod('hero_image_enable_common', true);
        },
    ));

    // 共通: 枠線
    $wp_customize->add_setting('hero_border_common', array(
        'default' => 'none',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_border_common', array(
        'label' => __('枠線', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'type' => 'select',
        'choices' => backbone_get_hero_border_options(),
        'active_callback' => function() {
            return get_theme_mod('hero_image_setting_mode', 'common') === 'common' &&
                   get_theme_mod('hero_image_enable_common', true);
        },
    ));

    // 共通: 枠線の色
    $wp_customize->add_setting('hero_border_color_common', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_border_color_common', array(
        'label' => __('枠線の色', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'active_callback' => function() {
            return get_theme_mod('hero_image_setting_mode', 'common') === 'common' &&
                   get_theme_mod('hero_image_enable_common', true) &&
                   get_theme_mod('hero_border_common', 'none') !== 'none';
        },
    )));

    // 共通: 角丸
    $wp_customize->add_setting('hero_border_radius_common', array(
        'default' => 'none',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_border_radius_common', array(
        'label' => __('角丸', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'type' => 'select',
        'choices' => backbone_get_hero_border_radius_options(),
        'active_callback' => function() {
            return get_theme_mod('hero_image_setting_mode', 'common') === 'common' &&
                   get_theme_mod('hero_image_enable_common', true);
        },
    ));

    // 共通: アニメーション
    $wp_customize->add_setting('hero_animation_common', array(
        'default' => 'none',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_animation_common', array(
        'label' => __('アニメーション', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'type' => 'select',
        'choices' => backbone_get_hero_animation_options(),
        'active_callback' => function() {
            return get_theme_mod('hero_image_setting_mode', 'common') === 'common' &&
                   get_theme_mod('hero_image_enable_common', true);
        },
    ));

    // 共通: 画像配置
    $wp_customize->add_setting('hero_alignment_common', array(
        'default' => 'center',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_alignment_common', array(
        'label' => __('画像配置', 'backbone-seo-llmo'),
        'section' => 'hero_image_settings',
        'type' => 'select',
        'choices' => backbone_get_hero_alignment_options(),
        'description' => __('メインビジュアルの画像配置を選択します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('hero_image_setting_mode', 'common') === 'common' &&
                   get_theme_mod('hero_image_enable_common', true);
        },
    ));

    // 対応する投稿タイプを取得
    $post_types = backbone_get_hero_supported_post_types();

    foreach ($post_types as $post_type => $label) {
        // --- 表示/非表示設定 ---
        $wp_customize->add_setting('hero_image_enable_' . $post_type, array(
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('hero_image_enable_' . $post_type, array(
            'label' => sprintf(__('%s - メインビジュアルを表示', 'backbone-seo-llmo'), $label),
            'section' => 'hero_image_settings',
            'type' => 'checkbox',
            'description' => sprintf(__('%sのメインビジュアルをデフォルトで表示します。個別ページで上書き可能です。', 'backbone-seo-llmo'), $label),
            'active_callback' => function() {
                return get_theme_mod('hero_image_setting_mode', 'common') === 'individual';
            },
        ));

        // --- 表示スタイル設定 ---
        $wp_customize->add_setting('hero_image_style_' . $post_type, array(
            'default' => 'standard',
            'sanitize_callback' => 'backbone_sanitize_hero_style',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('hero_image_style_' . $post_type, array(
            'label' => sprintf(__('%s - 表示スタイル', 'backbone-seo-llmo'), $label),
            'section' => 'hero_image_settings',
            'type' => 'select',
            'choices' => backbone_get_hero_style_options(),
            'description' => __('メインビジュアルの表示スタイルを選択します。', 'backbone-seo-llmo'),
            'active_callback' => function() use ($post_type) {
                return get_theme_mod('hero_image_setting_mode', 'common') === 'individual' &&
                       get_theme_mod('hero_image_enable_' . $post_type, true);
            },
        ));

        // --- デコレーション設定: 枠線 ---
        $wp_customize->add_setting('hero_border_' . $post_type, array(
            'default' => 'none',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('hero_border_' . $post_type, array(
            'label' => sprintf(__('%s - 枠線', 'backbone-seo-llmo'), $label),
            'section' => 'hero_image_settings',
            'type' => 'select',
            'choices' => backbone_get_hero_border_options(),
            'active_callback' => function() use ($post_type) {
                return get_theme_mod('hero_image_setting_mode', 'common') === 'individual' &&
                       get_theme_mod('hero_image_enable_' . $post_type, true);
            },
        ));

        // --- デコレーション設定: 枠線の色 ---
        $wp_customize->add_setting('hero_border_color_' . $post_type, array(
            'default' => '',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_border_color_' . $post_type, array(
            'label' => sprintf(__('%s - 枠線の色', 'backbone-seo-llmo'), $label),
            'section' => 'hero_image_settings',
            'active_callback' => function() use ($post_type) {
                return get_theme_mod('hero_image_setting_mode', 'common') === 'individual' &&
                       get_theme_mod('hero_image_enable_' . $post_type, true) &&
                       get_theme_mod('hero_border_' . $post_type, 'none') !== 'none';
            },
        )));

        // --- デコレーション設定: 角丸 ---
        $wp_customize->add_setting('hero_border_radius_' . $post_type, array(
            'default' => 'none',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('hero_border_radius_' . $post_type, array(
            'label' => sprintf(__('%s - 角丸', 'backbone-seo-llmo'), $label),
            'section' => 'hero_image_settings',
            'type' => 'select',
            'choices' => backbone_get_hero_border_radius_options(),
            'active_callback' => function() use ($post_type) {
                return get_theme_mod('hero_image_setting_mode', 'common') === 'individual' &&
                       get_theme_mod('hero_image_enable_' . $post_type, true);
            },
        ));

        // --- デコレーション設定: アニメーション ---
        $wp_customize->add_setting('hero_animation_' . $post_type, array(
            'default' => 'none',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('hero_animation_' . $post_type, array(
            'label' => sprintf(__('%s - アニメーション', 'backbone-seo-llmo'), $label),
            'section' => 'hero_image_settings',
            'type' => 'select',
            'choices' => backbone_get_hero_animation_options(),
            'active_callback' => function() use ($post_type) {
                return get_theme_mod('hero_image_setting_mode', 'common') === 'individual' &&
                       get_theme_mod('hero_image_enable_' . $post_type, true);
            },
        ));

        // --- 画像配置設定 ---
        $wp_customize->add_setting('hero_alignment_' . $post_type, array(
            'default' => 'center',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('hero_alignment_' . $post_type, array(
            'label' => sprintf(__('%s - 画像配置', 'backbone-seo-llmo'), $label),
            'section' => 'hero_image_settings',
            'type' => 'select',
            'choices' => backbone_get_hero_alignment_options(),
            'description' => __('メインビジュアルの画像配置を選択します。', 'backbone-seo-llmo'),
            'active_callback' => function() use ($post_type) {
                return get_theme_mod('hero_image_setting_mode', 'common') === 'individual' &&
                       get_theme_mod('hero_image_enable_' . $post_type, true);
            },
        ));

        // セパレーター（視覚的な区切り）
        if ($post_type !== array_key_last($post_types)) {
            $wp_customize->add_setting('hero_separator_' . $post_type, array(
                'sanitize_callback' => 'wp_kses_post',
            ));

            $wp_customize->add_control('hero_separator_' . $post_type, array(
                'label' => '',
                'section' => 'hero_image_settings',
                'type' => 'hidden',
                'description' => '<hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">',
                'active_callback' => function() {
                    return get_theme_mod('hero_image_setting_mode', 'common') === 'individual';
                },
            ));
        }
    }
}

/**
 * ヒーロースタイルのサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_hero_style($value) {
    $valid_styles = array_keys(backbone_get_hero_style_options());

    if (in_array($value, $valid_styles, true)) {
        return $value;
    }

    return 'standard';
}
