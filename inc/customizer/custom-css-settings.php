<?php
/**
 * 追加CSS設定関連のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 追加CSS設定セクションを追加
 */
function backbone_add_custom_css_settings($wp_customize) {
    // 追加CSS設定セクション
    $wp_customize->add_section('backbone_custom_css', array(
        'title'       => __('追加CSS', 'backbone-seo-llmo'),
        'description' => __('サイト全体または投稿タイプごとにカスタムCSSを追加できます。', 'backbone-seo-llmo'),
        'priority'    => 155,
    ));

    // ========================================
    // 全体共通のCSS設定
    // ========================================

    $wp_customize->add_setting('custom_css_global_header', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('custom_css_global_header', array(
        'label'       => __('■ 全体共通CSS', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_css',
        'type'        => 'hidden',
        'description' => __('サイト全体で読み込まれるCSSを設定できます。', 'backbone-seo-llmo'),
    ));

    // 全体共通CSS有効化
    $wp_customize->add_setting('custom_css_global_enable', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('custom_css_global_enable', array(
        'label'       => __('全体共通CSSを有効化', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_css',
        'type'        => 'checkbox',
        'description' => __('サイト全体でカスタムCSSを読み込みます。', 'backbone-seo-llmo'),
    ));

    // 全体共通CSS出力場所
    $wp_customize->add_setting('custom_css_global_position', array(
        'default'           => 'header',
        'sanitize_callback' => 'backbone_sanitize_css_position',
    ));

    $wp_customize->add_control('custom_css_global_position', array(
        'label'   => __('全体共通CSSの出力場所', 'backbone-seo-llmo'),
        'section' => 'backbone_custom_css',
        'type'    => 'select',
        'choices' => array(
            'header' => __('ヘッダー（<head>内）', 'backbone-seo-llmo'),
            'footer' => __('フッター（</body>の前）', 'backbone-seo-llmo'),
        ),
        'description' => __('CSSコードを出力する場所を選択してください。', 'backbone-seo-llmo'),
    ));

    // 全体共通CSSコード
    $wp_customize->add_setting('custom_css_global_code', array(
        'default'           => '',
        'sanitize_callback' => 'backbone_sanitize_css_code',
    ));

    $wp_customize->add_control('custom_css_global_code', array(
        'label'       => __('全体共通CSSコード', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_css',
        'type'        => 'textarea',
        'description' => __('styleタグは不要です。CSSコードのみを記述してください。', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'rows'        => 10,
            'placeholder' => "body {\n    /* 全体共通CSS */\n}\n/* ここにCSSコードを記述 */",
            'style'       => 'font-family: monospace; font-size: 12px;',
        ),
    ));

    // ========================================
    // 投稿タイプ別CSS設定
    // ========================================

    $wp_customize->add_setting('custom_css_posttype_header', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('custom_css_posttype_header', array(
        'label'       => __('■ 投稿タイプ別CSS', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_css',
        'type'        => 'hidden',
        'description' => __('各投稿タイプごとにカスタムCSSを追加できます。CSSは該当する投稿タイプの単一ページでのみ読み込まれます。', 'backbone-seo-llmo'),
    ));

    // 公開投稿タイプを取得
    $post_types = get_post_types(array('public' => true), 'objects');

    foreach ($post_types as $post_type) {
        $post_type_name = $post_type->name;
        $post_type_label = $post_type->label;

        // 投稿タイプごとのセパレーター
        $wp_customize->add_setting("custom_css_separator_{$post_type_name}", array(
            'sanitize_callback' => 'wp_kses_post',
        ));

        $wp_customize->add_control("custom_css_separator_{$post_type_name}", array(
            'label'       => sprintf(__('▼ %s', 'backbone-seo-llmo'), $post_type_label),
            'section'     => 'backbone_custom_css',
            'type'        => 'hidden',
            'description' => '',
        ));

        // CSS有効化チェックボックス
        $wp_customize->add_setting("custom_css_enable_{$post_type_name}", array(
            'default'           => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));

        $wp_customize->add_control("custom_css_enable_{$post_type_name}", array(
            'label'       => sprintf(__('%s でCSSを有効化', 'backbone-seo-llmo'), $post_type_label),
            'section'     => 'backbone_custom_css',
            'type'        => 'checkbox',
            'description' => sprintf(__('%sの単一ページでカスタムCSSを読み込みます。', 'backbone-seo-llmo'), $post_type_label),
        ));

        // CSS出力場所選択
        $wp_customize->add_setting("custom_css_position_{$post_type_name}", array(
            'default'           => 'header',
            'sanitize_callback' => 'backbone_sanitize_css_position',
        ));

        $wp_customize->add_control("custom_css_position_{$post_type_name}", array(
            'label'   => sprintf(__('%s のCSS出力場所', 'backbone-seo-llmo'), $post_type_label),
            'section' => 'backbone_custom_css',
            'type'    => 'select',
            'choices' => array(
                'header' => __('ヘッダー（<head>内）', 'backbone-seo-llmo'),
                'footer' => __('フッター（</body>の前）', 'backbone-seo-llmo'),
            ),
            'description' => __('CSSコードを出力する場所を選択してください。', 'backbone-seo-llmo'),
        ));

        // CSSコード入力
        $wp_customize->add_setting("custom_css_code_{$post_type_name}", array(
            'default'           => '',
            'sanitize_callback' => 'backbone_sanitize_css_code',
        ));

        $wp_customize->add_control("custom_css_code_{$post_type_name}", array(
            'label'       => sprintf(__('%s のCSSコード', 'backbone-seo-llmo'), $post_type_label),
            'section'     => 'backbone_custom_css',
            'type'        => 'textarea',
            'description' => __('styleタグは不要です。CSSコードのみを記述してください。', 'backbone-seo-llmo'),
            'input_attrs' => array(
                'rows'        => 10,
                'placeholder' => ".{$post_type_name}-specific {\n    /* {$post_type_label}用CSS */\n}\n/* ここにCSSコードを記述 */",
                'style'       => 'font-family: monospace; font-size: 12px;',
            ),
        ));
    }
}

/**
 * CSS出力場所のサニタイゼーション
 */
function backbone_sanitize_css_position($input) {
    $valid = array('header', 'footer');
    return in_array($input, $valid, true) ? $input : 'header';
}

/**
 * CSSコードのサニタイゼーション
 */
function backbone_sanitize_css_code($input) {
    // 空の場合はそのまま返す
    if (empty($input)) {
        return '';
    }

    // 管理者またはunfiltered_html権限を持つユーザーのみ許可
    if (!current_user_can('unfiltered_html')) {
        return '';
    }

    // styleタグが含まれている場合は削除（中身は保持）
    $input = preg_replace('/<style[^>]*>|<\/style>/i', '', $input);

    // 基本的なサニタイゼーション（改行とタブは保持）
    $input = trim($input);

    return $input;
}
