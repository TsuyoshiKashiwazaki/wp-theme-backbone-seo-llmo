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

    // アーカイブページレイアウト設定の説明
    $wp_customize->add_setting('archive_layout_info', array(
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('archive_layout_info', array(
        'label'       => __('アーカイブページレイアウト設定', 'kashiwazaki-searchcraft'),
        'section'     => 'backbone_layout',
        'type'        => 'hidden',
        'description' => __('各種アーカイブページのレイアウトを設定できます。', 'kashiwazaki-searchcraft'),
    ));

    // カテゴリーアーカイブレイアウト
    $wp_customize->add_setting('post_type_layout_category', array(
        'default'           => 'inherit',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('post_type_layout_category', array(
        'label'   => __('カテゴリーアーカイブ レイアウト', 'kashiwazaki-searchcraft'),
        'section' => 'backbone_layout',
        'type'    => 'select',
        'choices' => backbone_get_post_type_layout_choices(),
        'description' => __('カテゴリー一覧ページのレイアウトを設定します。', 'kashiwazaki-searchcraft'),
    ));

    // タグアーカイブレイアウト
    $wp_customize->add_setting('post_type_layout_tag', array(
        'default'           => 'inherit',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('post_type_layout_tag', array(
        'label'   => __('タグアーカイブ レイアウト', 'kashiwazaki-searchcraft'),
        'section' => 'backbone_layout',
        'type'    => 'select',
        'choices' => backbone_get_post_type_layout_choices(),
        'description' => __('タグ一覧ページのレイアウトを設定します。', 'kashiwazaki-searchcraft'),
    ));

    // その他のアーカイブレイアウト（日付、著者など）
    $wp_customize->add_setting('post_type_layout_archive', array(
        'default'           => 'inherit',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('post_type_layout_archive', array(
        'label'   => __('その他のアーカイブ レイアウト', 'kashiwazaki-searchcraft'),
        'section' => 'backbone_layout',
        'type'    => 'select',
        'choices' => backbone_get_post_type_layout_choices(),
        'description' => __('日付アーカイブ、著者アーカイブなど、その他のアーカイブページのレイアウトを設定します。', 'kashiwazaki-searchcraft'),
    ));

    // 検索結果ページレイアウト
    $wp_customize->add_setting('post_type_layout_search', array(
        'default'           => 'inherit',
        'sanitize_callback' => 'backbone_sanitize_select',
    ));

    $wp_customize->add_control('post_type_layout_search', array(
        'label'   => __('検索結果ページ レイアウト', 'kashiwazaki-searchcraft'),
        'section' => 'backbone_layout',
        'type'    => 'select',
        'choices' => backbone_get_post_type_layout_choices(),
        'description' => __('検索結果ページのレイアウトを設定します。', 'kashiwazaki-searchcraft'),
    ));

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

    // 著作権テキスト
    $wp_customize->add_setting('footer_copyright_text', array(
        'default'           => sprintf(__('© %s %s. All rights reserved.', 'backbone-seo-llmo'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('footer_copyright_text', array(
        'label'       => __('著作権テキスト', 'backbone-seo-llmo'),
        'section'     => 'backbone_layout',
        'type'        => 'text',
        'description' => __('フッターに表示する著作権テキストを入力してください。デフォルトでサイト名が含まれます。', 'backbone-seo-llmo'),
    ));

    // 著作権表示のオンオフ
    $wp_customize->add_setting('footer_copyright_show', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('footer_copyright_show', array(
        'label'       => __('著作権表示', 'backbone-seo-llmo'),
        'section'     => 'backbone_layout',
        'type'        => 'checkbox',
        'description' => __('著作権テキストを表示する場合はチェックしてください。', 'backbone-seo-llmo'),
    ));

    // テーマクレジット表示のオンオフ
    $wp_customize->add_setting('footer_credit_show', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('footer_credit_show', array(
        'label'       => __('テーマクレジット表示', 'backbone-seo-llmo'),
        'section'     => 'backbone_layout',
        'type'        => 'checkbox',
        'description' => __('テーマのクレジット（WP Theme: Backbone Theme...）を表示する場合はチェックしてください。', 'backbone-seo-llmo'),
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

    // スティッキーサイドバーの有効化
    $wp_customize->add_setting('enable_sticky_sidebar', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('enable_sticky_sidebar', array(
        'label'       => __('スティッキーサイドバー', 'backbone-seo-llmo'),
        'section'     => 'backbone_layout',
        'type'        => 'checkbox',
        'description' => __('サイドバーがスクロールに追従するようにします。PC（1280px以上）でのみ有効です。', 'backbone-seo-llmo'),
    ));

    // スティッキーヘッダーの有効化
    $wp_customize->add_setting('enable_sticky_header', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('enable_sticky_header', array(
        'label'       => __('スティッキーヘッダー', 'backbone-seo-llmo'),
        'section'     => 'backbone_layout',
        'type'        => 'checkbox',
        'description' => __('ヘッダーを画面上部に固定してスクロールに追従するようにします。', 'backbone-seo-llmo'),
    ));

    // スティッキーヘッダーの透明度
    $wp_customize->add_setting('sticky_header_opacity', array(
        'default'           => 80,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('sticky_header_opacity', array(
        'label'       => __('スクロール時の透明度', 'backbone-seo-llmo'),
        'section'     => 'backbone_layout',
        'type'        => 'range',
        'description' => __('スクロール時のヘッダーの不透明度を設定します。100%で完全不透明、0%で完全透明です。', 'backbone-seo-llmo'),
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 5,
            'data-show-value' => 'true',
        ),
    ));

    // ヘッダー自動非表示
    $wp_customize->add_setting('sticky_header_autohide', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('sticky_header_autohide', array(
        'label'       => __('ヘッダー自動非表示', 'backbone-seo-llmo'),
        'section'     => 'backbone_layout',
        'type'        => 'checkbox',
        'description' => __('スクロールダウン時にヘッダーを隠します。上にスクロールするかタブをクリックすると再表示されます。', 'backbone-seo-llmo'),
    ));
}
