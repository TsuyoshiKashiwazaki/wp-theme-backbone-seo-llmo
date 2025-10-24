<?php
/**
 * 追加JS設定関連のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 追加JS設定セクションを追加
 */
function backbone_add_custom_js_settings($wp_customize) {
    // 追加JS設定セクション
    $wp_customize->add_section('backbone_custom_js', array(
        'title'       => __('追加JS', 'backbone-seo-llmo'),
        'description' => __('サイト全体または投稿タイプごとにカスタムJavaScriptコードを追加できます。', 'backbone-seo-llmo'),
        'priority'    => 150,
    ));

    // ========================================
    // 全体共通のJS設定
    // ========================================

    $wp_customize->add_setting('custom_js_global_header', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('custom_js_global_header', array(
        'label'       => __('■ 全体共通JS', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_js',
        'type'        => 'hidden',
        'description' => __('サイト全体で読み込まれるJavaScriptコードを設定できます。', 'backbone-seo-llmo'),
    ));

    // 全体共通JS有効化
    $wp_customize->add_setting('custom_js_global_enable', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('custom_js_global_enable', array(
        'label'       => __('全体共通JSを有効化', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_js',
        'type'        => 'checkbox',
        'description' => __('サイト全体でカスタムJSを読み込みます。', 'backbone-seo-llmo'),
    ));

    // 全体共通JS出力場所
    $wp_customize->add_setting('custom_js_global_position', array(
        'default'           => 'header',
        'sanitize_callback' => 'backbone_sanitize_js_position',
    ));

    $wp_customize->add_control('custom_js_global_position', array(
        'label'   => __('全体共通JSの出力場所', 'backbone-seo-llmo'),
        'section' => 'backbone_custom_js',
        'type'    => 'select',
        'choices' => array(
            'header' => __('ヘッダー（<head>内）', 'backbone-seo-llmo'),
            'footer' => __('フッター（</body>の前）', 'backbone-seo-llmo'),
        ),
        'description' => __('JSコードを出力する場所を選択してください。', 'backbone-seo-llmo'),
    ));

    // 全体共通JSコード
    $wp_customize->add_setting('custom_js_global_code', array(
        'default'           => '',
        'sanitize_callback' => 'backbone_sanitize_js_code',
    ));

    $wp_customize->add_control('custom_js_global_code', array(
        'label'       => __('全体共通JSコード', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_js',
        'type'        => 'textarea',
        'description' => __('scriptタグは不要です。JavaScriptコードのみを記述してください。', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'rows'        => 10,
            'placeholder' => "console.log('全体共通JS');\n// ここにJSコードを記述",
            'style'       => 'font-family: monospace; font-size: 12px;',
        ),
    ));

    // ========================================
    // 投稿タイプ別JS設定
    // ========================================

    $wp_customize->add_setting('custom_js_posttype_header', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('custom_js_posttype_header', array(
        'label'       => __('■ 投稿タイプ別JS', 'backbone-seo-llmo'),
        'section'     => 'backbone_custom_js',
        'type'        => 'hidden',
        'description' => __('各投稿タイプごとにカスタムJavaScriptコードを追加できます。コードは該当する投稿タイプの単一ページでのみ読み込まれます。', 'backbone-seo-llmo'),
    ));

    // 公開投稿タイプを取得
    $post_types = get_post_types(array('public' => true), 'objects');

    foreach ($post_types as $post_type) {
        $post_type_name = $post_type->name;
        $post_type_label = $post_type->label;

        // 投稿タイプごとのセパレーター
        $wp_customize->add_setting("custom_js_separator_{$post_type_name}", array(
            'sanitize_callback' => 'wp_kses_post',
        ));

        $wp_customize->add_control("custom_js_separator_{$post_type_name}", array(
            'label'       => sprintf(__('▼ %s', 'backbone-seo-llmo'), $post_type_label),
            'section'     => 'backbone_custom_js',
            'type'        => 'hidden',
            'description' => '',
        ));

        // JS有効化チェックボックス
        $wp_customize->add_setting("custom_js_enable_{$post_type_name}", array(
            'default'           => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));

        $wp_customize->add_control("custom_js_enable_{$post_type_name}", array(
            'label'       => sprintf(__('%s でJSを有効化', 'backbone-seo-llmo'), $post_type_label),
            'section'     => 'backbone_custom_js',
            'type'        => 'checkbox',
            'description' => sprintf(__('%sの単一ページでカスタムJSを読み込みます。', 'backbone-seo-llmo'), $post_type_label),
        ));

        // JS出力場所選択
        $wp_customize->add_setting("custom_js_position_{$post_type_name}", array(
            'default'           => 'header',
            'sanitize_callback' => 'backbone_sanitize_js_position',
        ));

        $wp_customize->add_control("custom_js_position_{$post_type_name}", array(
            'label'   => sprintf(__('%s のJS出力場所', 'backbone-seo-llmo'), $post_type_label),
            'section' => 'backbone_custom_js',
            'type'    => 'select',
            'choices' => array(
                'header' => __('ヘッダー（<head>内）', 'backbone-seo-llmo'),
                'footer' => __('フッター（</body>の前）', 'backbone-seo-llmo'),
            ),
            'description' => __('JSコードを出力する場所を選択してください。', 'backbone-seo-llmo'),
        ));

        // JSコード入力
        $wp_customize->add_setting("custom_js_code_{$post_type_name}", array(
            'default'           => '',
            'sanitize_callback' => 'backbone_sanitize_js_code',
        ));

        $wp_customize->add_control("custom_js_code_{$post_type_name}", array(
            'label'       => sprintf(__('%s のJSコード', 'backbone-seo-llmo'), $post_type_label),
            'section'     => 'backbone_custom_js',
            'type'        => 'textarea',
            'description' => __('scriptタグは不要です。JavaScriptコードのみを記述してください。', 'backbone-seo-llmo'),
            'input_attrs' => array(
                'rows'        => 10,
                'placeholder' => "console.log('Hello from {$post_type_label}');\n// ここにJSコードを記述",
                'style'       => 'font-family: monospace; font-size: 12px;',
            ),
        ));
    }
}

/**
 * JS出力場所のサニタイゼーション
 */
function backbone_sanitize_js_position($input) {
    $valid = array('header', 'footer');
    return in_array($input, $valid, true) ? $input : 'header';
}

/**
 * JSコードのサニタイゼーション
 */
function backbone_sanitize_js_code($input) {
    // 空の場合はそのまま返す
    if (empty($input)) {
        return '';
    }

    // 管理者またはunfiltered_html権限を持つユーザーのみ許可
    if (!current_user_can('unfiltered_html')) {
        return '';
    }

    // scriptタグが含まれている場合は削除（中身は保持）
    $input = preg_replace('/<script[^>]*>|<\/script>/i', '', $input);

    // 基本的なサニタイゼーション（改行とタブは保持）
    $input = trim($input);

    return $input;
}
