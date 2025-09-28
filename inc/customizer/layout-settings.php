<?php
/**
 * レイアウト設定関連のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * レイアウト設定セクションを追加
 */
function backbone_add_layout_settings($wp_customize) {
    // レイアウト設定セクション
    $wp_customize->add_section('backbone_layout', array(
        'title'    => __('レイアウト設定', 'kashiwazaki-searchcraft'),
        'priority' => 40,
    ));

    // サイトレイアウト
    $wp_customize->add_setting('site_layout', array(
        'default'           => 'two-columns',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('site_layout', array(
        'label'   => __('フロントページレイアウト', 'kashiwazaki-searchcraft'),
        'section' => 'backbone_layout',
        'type'    => 'select',
        'choices' => backbone_get_layout_choices(),
    ));

    // サイドバー位置
    $wp_customize->add_setting('sidebar_position', array(
        'default'           => 'right',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('sidebar_position', array(
        'label'   => __('サイドバー位置', 'kashiwazaki-searchcraft'),
        'section' => 'backbone_layout',
        'type'    => 'select',
        'choices' => array(
            'left'  => __('左', 'kashiwazaki-searchcraft'),
            'right' => __('右', 'kashiwazaki-searchcraft'),
        ),
    ));

    // 投稿タイプ別レイアウト設定の説明
    $wp_customize->add_setting('post_type_layout_info', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('post_type_layout_info', array(
        'label'       => __('投稿タイプ別レイアウト設定', 'kashiwazaki-searchcraft'),
        'section'     => 'backbone_layout',
        'type'        => 'hidden',
        'description' => __('各投稿タイプごとに個別のレイアウトを設定できます。', 'kashiwazaki-searchcraft'),
    ));

    // 投稿タイプ別レイアウト設定の詳細
    $post_types = get_post_types(array('public' => true), 'objects');
    foreach ($post_types as $post_type) {
        $post_type_name = $post_type->name;
        $post_type_label = $post_type->label;

        // 投稿タイプ別レイアウト設定
        $wp_customize->add_setting("post_type_layout_{$post_type_name}", array(
            'default'           => 'inherit',
            'sanitize_callback' => 'backbone_sanitize_select',
        ));

        $wp_customize->add_control("post_type_layout_{$post_type_name}", array(
            'label'   => sprintf(__('%s レイアウト', 'kashiwazaki-searchcraft'), $post_type_label),
            'section' => 'backbone_layout',
            'type'    => 'select',
            'choices' => backbone_get_post_type_layout_choices(),
        ));
    }

    // ヘッダーメッセージ
    $wp_customize->add_setting('header_message', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('header_message', array(
        'label'       => __('ヘッダーメッセージ', 'kashiwazaki-searchcraft'),
        'section'     => 'backbone_layout',
        'type'        => 'textarea',
        'description' => __('ヘッダーに表示するメッセージを入力してください。HTMLタグも使用できます。', 'kashiwazaki-searchcraft'),
        'input_attrs' => array(
            'rows' => 3,
        ),
    ));

    // フッターメッセージ
    $wp_customize->add_setting('footer_message', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('footer_message', array(
        'label'       => __('フッターメッセージ', 'kashiwazaki-searchcraft'),
        'section'     => 'backbone_layout',
        'type'        => 'textarea',
        'description' => __('フッターに表示するメッセージを入力してください。HTMLタグも使用できます。', 'kashiwazaki-searchcraft'),
        'input_attrs' => array(
            'rows' => 3,
        ),
    ));





    // 検索ボタン設定
    $wp_customize->add_setting('search_button_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('search_button_enabled', array(
        'label'       => __('検索ボタン設定', 'kashiwazaki-searchcraft'),
        'section'     => 'backbone_layout',
        'type'        => 'checkbox',
        'description' => __('ヘッダーに検索ボタンを表示します。クリックすると検索ポップアップが開きます。', 'kashiwazaki-searchcraft'),
    ));

    // サイドバーの幅
    $wp_customize->add_setting('sidebar_width', array(
        'default'           => '300',
        'sanitize_callback' => 'backbone_sanitize_number',
    ));

    $wp_customize->add_control('sidebar_width', array(
        'label'       => __('サイドバーの幅', 'kashiwazaki-searchcraft'),
        'section'     => 'backbone_layout',
        'type'        => 'number',
        'description' => __('サイドバーの幅を設定します（px）。コンテンツエリアの幅は自動的に調整されます。', 'kashiwazaki-searchcraft'),
        'input_attrs' => array(
            'min'  => 200,
            'max'  => 500,
            'step' => 25,
        ),
    ));
}
