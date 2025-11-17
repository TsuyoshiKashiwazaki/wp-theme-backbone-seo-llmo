<?php
/**
 * 記事メタ情報のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 記事メタ情報設定をカスタマイザーに追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 */
function backbone_add_single_post_settings($wp_customize) {
    // 記事メタ情報設定のセクションを追加
    $wp_customize->add_section('post_meta_settings', array(
        'title' => __('記事メタ情報設定', 'backbone-seo-llmo'),
        'priority' => 56,
        'description' => __('投稿・カスタム投稿タイプの個別記事ページで表示する日付・著者・カテゴリ・タグなどのメタ情報を設定します。', 'backbone-seo-llmo'),
    ));

    // 統一設定モード
    $wp_customize->add_setting('post_meta_use_unified', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('post_meta_use_unified', array(
        'label' => __('すべて共通設定を使用', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'description' => __('チェックを入れると、すべての投稿タイプに同じ設定を適用します。チェックを外すと、投稿タイプごとに個別設定が可能になります。', 'backbone-seo-llmo'),
        'priority' => 1,
    ));

    // ━━━ すべて共通設定 ━━━
    $wp_customize->add_setting('post_meta_common_heading', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'post_meta_common_heading', array(
        'label' => __('すべて共通設定', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'priority' => 5,
        'description' => __('全投稿タイプに適用される共通設定です。', 'backbone-seo-llmo'),
        'active_callback' => 'backbone_is_unified_post_meta_settings_enabled',
    )));

    // 投稿日表示
    $wp_customize->add_setting('single_show_date', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('single_show_date', array(
        'label' => __('投稿日を表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'description' => __('記事の公開日を表示します。SEO・ユーザビリティ向上のため推奨。', 'backbone-seo-llmo'),
        'priority' => 10,
        'active_callback' => 'backbone_is_unified_post_meta_settings_enabled',
    ));

    // 更新日表示
    $wp_customize->add_setting('single_show_modified', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('single_show_modified', array(
        'label' => __('更新日を表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'description' => __('記事の最終更新日を表示します。公開日と異なる場合のみ表示されます。コンテンツの鮮度を示すため推奨。', 'backbone-seo-llmo'),
        'priority' => 20,
        'active_callback' => 'backbone_is_unified_post_meta_settings_enabled',
    ));

    // 著者情報表示
    $wp_customize->add_setting('single_show_author', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('single_show_author', array(
        'label' => __('著者名を表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'description' => __('記事の著者名を表示します。複数著者サイトの場合に有用。', 'backbone-seo-llmo'),
        'priority' => 30,
        'active_callback' => 'backbone_is_unified_post_meta_settings_enabled',
    ));

    // カテゴリ表示
    $wp_customize->add_setting('single_show_category', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('single_show_category', array(
        'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'description' => __('記事のカテゴリを表示します。必要に応じて有効化してください。', 'backbone-seo-llmo'),
        'priority' => 40,
        'active_callback' => 'backbone_is_unified_post_meta_settings_enabled',
    ));

    // タグ表示
    $wp_customize->add_setting('single_show_tags', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('single_show_tags', array(
        'label' => __('タグを表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'description' => __('記事のタグをヘッダーに表示します。タグが多い場合は表示が煩雑になるため、通常はオフ推奨。', 'backbone-seo-llmo'),
        'priority' => 50,
        'active_callback' => 'backbone_is_unified_post_meta_settings_enabled',
    ));

    // タグ表示数の上限
    $wp_customize->add_setting('single_tags_limit', array(
        'default' => 5,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('single_tags_limit', array(
        'label' => __('タグ表示数の上限', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'number',
        'description' => __('ヘッダーに表示するタグの最大数。多すぎるとレイアウトが崩れるため、5〜10個を推奨。', 'backbone-seo-llmo'),
        'priority' => 55,
        'active_callback' => 'backbone_is_unified_post_meta_settings_enabled',
        'input_attrs' => array(
            'min' => 1,
            'max' => 50,
            'step' => 1,
        ),
    ));

    // 個別投稿タイプ設定
    backbone_add_post_type_meta_controls($wp_customize, 'post', __('通常投稿の個別設定', 'backbone-seo-llmo'), 100);
    backbone_add_post_type_meta_controls($wp_customize, 'page', __('固定ページの個別設定', 'backbone-seo-llmo'), 150);

    // カスタム投稿タイプの個別設定を追加
    backbone_add_custom_post_type_meta_controls($wp_customize);
}

/**
 * 投稿タイプごとのメタ情報設定を追加
 */
function backbone_add_post_type_meta_controls($wp_customize, $post_type, $label, $priority) {
    $prefix = 'post_meta_' . $post_type . '_';

    // 見出し
    $wp_customize->add_setting($prefix . 'heading', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, $prefix . 'heading', array(
        'label' => $label,
        'section' => 'post_meta_settings',
        'priority' => $priority,
        'description' => sprintf(__('%s の個別設定。「すべて共通設定を使用」がオフの時に有効。', 'backbone-seo-llmo'), $label),
        'active_callback' => 'backbone_is_individual_post_meta_settings_enabled',
    )));

    // 投稿日
    $wp_customize->add_setting($prefix . 'show_date', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control($prefix . 'show_date', array(
        'label' => __('投稿日を表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'priority' => $priority + 1,
        'active_callback' => 'backbone_is_individual_post_meta_settings_enabled',
    ));

    // 更新日
    $wp_customize->add_setting($prefix . 'show_modified', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control($prefix . 'show_modified', array(
        'label' => __('更新日を表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'priority' => $priority + 2,
        'active_callback' => 'backbone_is_individual_post_meta_settings_enabled',
    ));

    // 著者
    $wp_customize->add_setting($prefix . 'show_author', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control($prefix . 'show_author', array(
        'label' => __('著者を表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'priority' => $priority + 3,
        'active_callback' => 'backbone_is_individual_post_meta_settings_enabled',
    ));

    // カテゴリ
    $wp_customize->add_setting($prefix . 'show_category', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control($prefix . 'show_category', array(
        'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'priority' => $priority + 4,
        'active_callback' => 'backbone_is_individual_post_meta_settings_enabled',
    ));

    // タグ
    $wp_customize->add_setting($prefix . 'show_tags', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control($prefix . 'show_tags', array(
        'label' => __('タグを表示', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'checkbox',
        'priority' => $priority + 5,
        'active_callback' => 'backbone_is_individual_post_meta_settings_enabled',
    ));

    // タグ表示数
    $wp_customize->add_setting($prefix . 'tags_limit', array(
        'default' => 5,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control($prefix . 'tags_limit', array(
        'label' => __('タグ表示数上限', 'backbone-seo-llmo'),
        'section' => 'post_meta_settings',
        'type' => 'number',
        'priority' => $priority + 6,
        'active_callback' => 'backbone_is_individual_post_meta_settings_enabled',
        'input_attrs' => array('min' => 1, 'max' => 50),
    ));
}

/**
 * カスタム投稿タイプのメタ情報設定を追加
 */
function backbone_add_custom_post_type_meta_controls($wp_customize) {
    $post_types = get_post_types(array(
        'public' => true,
        '_builtin' => false
    ), 'objects');

    $priority = 200;
    foreach ($post_types as $post_type) {
        backbone_add_post_type_meta_controls(
            $wp_customize,
            $post_type->name,
            sprintf(__('%s の個別設定', 'backbone-seo-llmo'), $post_type->label),
            $priority
        );
        $priority += 100;
    }
}

/**
 * メタ情報設定を取得（統一設定または個別設定）
 */
function backbone_get_post_meta_setting($key, $default = false) {
    $use_unified = get_theme_mod('post_meta_use_unified', true);

    if ($use_unified) {
        // 共通設定を使用
        return get_theme_mod('single_' . $key, $default);
    } else {
        // 投稿タイプごとの個別設定を使用
        $post_type = get_post_type();
        $individual_key = 'post_meta_' . $post_type . '_' . $key;
        return get_theme_mod($individual_key, get_theme_mod('single_' . $key, $default));
    }
}

/**
 * 個別メタ情報設定が有効かどうかを判定
 *
 * @return bool 個別設定が有効な場合true
 */
function backbone_is_individual_post_meta_settings_enabled() {
    return !get_theme_mod('post_meta_use_unified', true);
}

/**
 * 統一メタ情報設定が有効かどうかを判定
 *
 * @return bool 統一設定が有効な場合true
 */
function backbone_is_unified_post_meta_settings_enabled() {
    return get_theme_mod('post_meta_use_unified', true);
}
