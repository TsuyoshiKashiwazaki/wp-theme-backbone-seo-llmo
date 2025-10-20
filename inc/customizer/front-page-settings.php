<?php
/**
 * フロントページ設定のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * フロントページ設定をカスタマイザーに追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 */
function backbone_add_front_page_settings($wp_customize) {
    // WordPressの標準「ホームページ設定」セクションを「フロントページ設定」に変更
    $wp_customize->get_section('static_front_page')->title = __('フロントページ設定', 'backbone-seo-llmo');
    $wp_customize->get_section('static_front_page')->description = __('トップページの表示方法を設定します。カスタムフロントページを使用するか、既存のページを選択できます。', 'backbone-seo-llmo');

    // ============================================
    // A. フロントページモード選択
    // ============================================
    $wp_customize->add_setting('backbone_front_page_mode', array(
        'default' => 'custom',
        'sanitize_callback' => 'backbone_sanitize_front_page_mode',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_page_mode', array(
        'label' => __('フロントページの表示方法', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'type' => 'radio',
        'priority' => 10,
        'choices' => array(
            'custom' => __('カスタムフロントページを使用', 'backbone-seo-llmo'),
            'page' => __('既存のページを使用', 'backbone-seo-llmo'),
        ),
        'description' => __('トップページの表示方法を選択してください。', 'backbone-seo-llmo'),
    ));

    // ============================================
    // B. カスタムフロントページ設定
    // ============================================

    // --- ヒーローイメージ ---
    $wp_customize->add_setting('backbone_front_hero_image', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'backbone_front_hero_image', array(
        'label' => __('ヒーローイメージ', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 20,
        'mime_type' => 'image',
        'description' => __('トップページに表示するメインビジュアル画像を選択してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    )));

    // --- ヒーローイメージの高さ ---
    $wp_customize->add_setting('backbone_front_hero_height', array(
        'default' => '400',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_hero_height', array(
        'label' => __('ヒーローイメージの高さ (px)', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 30,
        'type' => 'number',
        'input_attrs' => array(
            'min' => 200,
            'max' => 800,
            'step' => 50,
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   !empty(get_theme_mod('backbone_front_hero_image'));
        },
    ));

    // --- ヒーローイメージのオーバーレイ ---
    $wp_customize->add_setting('backbone_front_hero_overlay', array(
        'default' => '0.3',
        'sanitize_callback' => 'backbone_sanitize_overlay_opacity',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_hero_overlay', array(
        'label' => __('オーバーレイの透明度', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 40,
        'type' => 'select',
        'choices' => array(
            '0' => __('なし', 'backbone-seo-llmo'),
            '0.1' => '10%',
            '0.2' => '20%',
            '0.3' => '30%',
            '0.4' => '40%',
            '0.5' => '50%',
            '0.6' => '60%',
            '0.7' => '70%',
        ),
        'description' => __('画像の上に表示する暗いオーバーレイの透明度を設定します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   !empty(get_theme_mod('backbone_front_hero_image'));
        },
    ));

    // --- タイトル ---
    $wp_customize->add_setting('backbone_front_title', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_title', array(
        'label' => __('タイトル', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 50,
        'type' => 'text',
        'description' => __('フロントページのメインタイトルを入力してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- キャッチコピー ---
    $wp_customize->add_setting('backbone_front_catchphrase', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_catchphrase', array(
        'label' => __('キャッチコピー', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 60,
        'type' => 'text',
        'description' => __('タイトルの下に表示するキャッチコピー（サブタイトル）を入力してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- 説明文 ---
    $wp_customize->add_setting('backbone_front_description', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_wysiwyg_content',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_WYSIWYG_Control($wp_customize, 'backbone_front_description', array(
        'label' => __('説明文', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 70,
        'description' => __('サイトの説明文を入力してください。改行・太字・リンクなどが使用できます。', 'backbone-seo-llmo'),
        'placeholder' => "<p>当サイトは<strong>SEO対策</strong>と<strong>LLMO最適化</strong>に特化したWordPressテーマです。</p>\n<p>検索エンジンとAIの両方に最適化されたサイト構築をサポートします。</p>",
        'editor_settings' => array(
            'textarea_rows' => 6,
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    )));

    // セクション区切り用の見出し
    $wp_customize->add_setting('backbone_front_free_section_divider', array(
        'sanitize_callback' => '__return_false',
    ));

    $wp_customize->add_control('backbone_front_free_section_divider', array(
        'label' => __('📝 フリーコンテンツエリア', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'type' => 'hidden',
        'priority' => 74,
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- フリーコンテンツエリア ---
    $wp_customize->add_setting('backbone_front_free_content', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_wysiwyg_content',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_WYSIWYG_Control($wp_customize, 'backbone_front_free_content', array(
        'label' => __('フリーコンテンツエリア', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 80,
        'description' => __('自由にコンテンツを追加できます。改行・太字・リンク・リストなどが使用できます。', 'backbone-seo-llmo'),
        'placeholder' => "<p><strong>お知らせ</strong></p>\n<ul>\n  <li>新機能をリリースしました</li>\n  <li>セミナー開催のご案内</li>\n  <li><a href=\"/contact/\">お問い合わせ</a>はこちら</li>\n</ul>",
        'editor_settings' => array(
            'textarea_rows' => 10,
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    )));

    // ============================================
    // D. 記事一覧セクション
    // ============================================

    // セクション区切り用の見出し
    $wp_customize->add_setting('backbone_front_posts_section_divider', array(
        'sanitize_callback' => '__return_false',
    ));

    $wp_customize->add_control('backbone_front_posts_section_divider', array(
        'label' => __('📰 記事一覧セクション', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'type' => 'hidden',
        'priority' => 197,
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- 記事一覧セクション表示 ---
    $wp_customize->add_setting('backbone_front_posts_enable', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_posts_enable', array(
        'label' => __('記事一覧セクションを表示', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 200,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- 記事一覧セクションタイトル ---
    $wp_customize->add_setting('backbone_front_posts_title', array(
        'default' => __('最新記事', 'backbone-seo-llmo'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_posts_title', array(
        'label' => __('記事一覧セクションタイトル', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 210,
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_posts_enable', true);
        },
    ));

    // --- 記事表示件数 ---
    $wp_customize->add_setting('backbone_front_posts_count', array(
        'default' => '6',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_posts_count', array(
        'label' => __('表示件数', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 220,
        'type' => 'select',
        'choices' => array(
            '3' => '3件',
            '6' => '6件',
            '9' => '9件',
            '12' => '12件',
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_posts_enable', true);
        },
    ));

    // --- カテゴリー選択 ---
    $wp_customize->add_setting('backbone_front_posts_category', array(
        'default' => '0',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_posts_category', array(
        'label' => __('カテゴリー', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 230,
        'type' => 'select',
        'choices' => backbone_get_categories_for_dropdown(),
        'description' => __('特定のカテゴリーの記事のみ表示する場合は選択してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_posts_enable', true);
        },
    ));

    // --- レイアウト選択 ---
    $wp_customize->add_setting('backbone_front_posts_layout', array(
        'default' => '3col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_posts_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 240,
        'type' => 'select',
        'choices' => array(
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_posts_enable', true);
        },
    ));

    // --- 並び順選択 ---
    $wp_customize->add_setting('backbone_front_posts_orderby', array(
        'default' => 'date',
        'sanitize_callback' => 'backbone_sanitize_posts_orderby',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_posts_orderby', array(
        'label' => __('並び順', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 250,
        'type' => 'select',
        'choices' => array(
            'date' => __('最新順', 'backbone-seo-llmo'),
            'comment_count' => __('コメント数順', 'backbone-seo-llmo'),
            'rand' => __('ランダム', 'backbone-seo-llmo'),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_posts_enable', true);
        },
    ));

    // ============================================
    // E. 特集・ピックアップセクション
    // ============================================

    // セクション区切り用の見出し
    $wp_customize->add_setting('backbone_front_pickup_section_divider', array(
        'sanitize_callback' => '__return_false',
    ));

    $wp_customize->add_control('backbone_front_pickup_section_divider', array(
        'label' => __('⭐ 特集・ピックアップセクション', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'type' => 'hidden',
        'priority' => 298,
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- ピックアップセクション表示 ---
    $wp_customize->add_setting('backbone_front_pickup_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_pickup_enable', array(
        'label' => __('特集・ピックアップセクションを表示', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 300,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- ピックアップセクションタイトル ---
    $wp_customize->add_setting('backbone_front_pickup_title', array(
        'default' => __('特集記事', 'backbone-seo-llmo'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_pickup_title', array(
        'label' => __('ピックアップセクションタイトル', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 310,
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_pickup_enable', false);
        },
    ));

    // --- ピックアップレイアウト ---
    $wp_customize->add_setting('backbone_front_pickup_layout', array(
        'default' => '3col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_pickup_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 311,
        'type' => 'select',
        'choices' => array(
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_pickup_enable', false);
        },
    ));

    // --- ピックアップ記事（リピーター） ---
    $wp_customize->add_setting('backbone_front_pickup_items', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_repeater_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_pickup_items', array(
        'label' => __('ピックアップ記事', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 312,
        'description' => __('表示する記事を追加してください。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'post_id' => array(
                'type' => 'select',
                'label' => __('記事を選択', 'backbone-seo-llmo'),
                'choices' => backbone_get_all_posts_for_dropdown(),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_pickup_enable', false);
        },
    )));

    // ============================================
    // F. サービス・機能紹介セクション
    // ============================================

    // セクション区切り用の見出し
    $wp_customize->add_setting('backbone_front_services_section_divider', array(
        'sanitize_callback' => '__return_false',
    ));

    $wp_customize->add_control('backbone_front_services_section_divider', array(
        'label' => __('🔧 サービス・機能紹介セクション', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'type' => 'hidden',
        'priority' => 398,
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- サービスセクション表示 ---
    $wp_customize->add_setting('backbone_front_services_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_services_enable', array(
        'label' => __('サービス・機能紹介セクションを表示', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 400,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- サービスセクションタイトル ---
    $wp_customize->add_setting('backbone_front_services_title', array(
        'default' => __('サービス紹介', 'backbone-seo-llmo'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_services_title', array(
        'label' => __('サービスセクションタイトル', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 410,
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_services_enable', false);
        },
    ));

    // --- サービスレイアウト ---
    $wp_customize->add_setting('backbone_front_services_layout', array(
        'default' => '3col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_services_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 411,
        'type' => 'select',
        'choices' => array(
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_services_enable', false);
        },
    ));

    // --- サービスカード（リピーター） ---
    $wp_customize->add_setting('backbone_front_service_items', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_repeater_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_service_items', array(
        'label' => __('サービスカード', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 412,
        'description' => __('サービスを追加してください。タイトル・説明文・リンクを設定できます。', 'backbone-seo-llmo'),
        'add_button_label' => __('サービスを追加', 'backbone-seo-llmo'),
        'fields' => array(
            'title' => array(
                'type' => 'text',
                'label' => __('タイトル', 'backbone-seo-llmo'),
            ),
            'desc' => array(
                'type' => 'textarea',
                'label' => __('説明文（HTMLタグ使用可）', 'backbone-seo-llmo'),
            ),
            'url' => array(
                'type' => 'url',
                'label' => __('リンクURL', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_services_enable', false);
        },
    )));

    // ============================================
    // C. 既存ページ選択
    // ============================================

    // --- ページタイプ選択 ---
    $wp_customize->add_setting('backbone_front_page_type', array(
        'default' => 'static_page',
        'sanitize_callback' => 'backbone_sanitize_page_type',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_page_type', array(
        'label' => __('ページタイプ', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 85,
        'type' => 'radio',
        'choices' => array(
            'static_page' => __('固定ページを使用', 'backbone-seo-llmo'),
            'post' => __('投稿を使用', 'backbone-seo-llmo'),
        ),
        'description' => __('固定ページと投稿のどちらを使用するか選択してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'page';
        },
    ));

    // --- 固定ページ選択 ---
    $wp_customize->add_setting('backbone_front_selected_page', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_selected_page', array(
        'label' => __('固定ページ', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 90,
        'type' => 'dropdown-pages',
        'description' => __('トップページに表示する固定ページを選択してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'page' &&
                   get_theme_mod('backbone_front_page_type', 'static_page') === 'static_page';
        },
    ));

    // --- 投稿ページ選択 ---
    $wp_customize->add_setting('backbone_front_selected_post', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_selected_post', array(
        'label' => __('投稿', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 100,
        'type' => 'select',
        'choices' => backbone_get_posts_for_dropdown(),
        'description' => __('トップページに表示する投稿を選択してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'page' &&
                   get_theme_mod('backbone_front_page_type', 'static_page') === 'post';
        },
    ));

    // ============================================
    // WordPressの標準設定を非表示化
    // ============================================
    $wp_customize->remove_control('show_on_front');
    $wp_customize->remove_control('page_on_front');
    $wp_customize->remove_control('page_for_posts');
}

/**
 * フロントページモードのサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_front_page_mode($value) {
    $valid_modes = array('custom', 'page');

    if (in_array($value, $valid_modes, true)) {
        return $value;
    }

    return 'custom';
}

/**
 * オーバーレイ透明度のサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_overlay_opacity($value) {
    $valid_opacities = array('0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7');

    if (in_array($value, $valid_opacities, true)) {
        return $value;
    }

    return '0.3';
}

/**
 * ページタイプのサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_page_type($value) {
    $valid_types = array('static_page', 'post');

    if (in_array($value, $valid_types, true)) {
        return $value;
    }

    return 'static_page';
}

/**
 * 投稿一覧を取得してドロップダウン用の配列を返す
 *
 * @return array 投稿の配列（ID => タイトル）
 */
function backbone_get_posts_for_dropdown() {
    $posts = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    $options = array(0 => __('— 選択してください —', 'backbone-seo-llmo'));

    foreach ($posts as $post) {
        $options[$post->ID] = $post->post_title;
    }

    return $options;
}

/**
 * カテゴリー一覧を取得してドロップダウン用の配列を返す
 *
 * @return array カテゴリーの配列（ID => 名前）
 */
function backbone_get_categories_for_dropdown() {
    $categories = get_categories(array(
        'hide_empty' => false,
    ));

    $options = array(0 => __('全カテゴリー', 'backbone-seo-llmo'));

    foreach ($categories as $category) {
        $options[$category->term_id] = $category->name;
    }

    return $options;
}

/**
 * 投稿と固定ページを取得してドロップダウン用の配列を返す
 *
 * @return array 投稿・固定ページの配列（ID => タイトル）
 */
function backbone_get_all_posts_for_dropdown() {
    $posts = get_posts(array(
        'post_type' => array('post', 'page'),
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    $options = array(0 => __('— 選択してください —', 'backbone-seo-llmo'));

    foreach ($posts as $post) {
        $post_type_label = get_post_type_object($post->post_type)->labels->singular_name;
        $options[$post->ID] = '[' . $post_type_label . '] ' . $post->post_title;
    }

    return $options;
}

/**
 * WYSIWYGコンテンツのサニタイズ関数
 * TinyMCEのブックマークタグを除去
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_wysiwyg_content($value) {
    // TinyMCEのブックマークタグを除去
    $value = preg_replace('/<span[^>]*mce_SELRES_start[^>]*>[\x{FEFF}\x{200B}]*<\/span>/iu', '', $value);

    // wp_kses_postでHTMLをサニタイズ
    return wp_kses_post($value);
}

/**
 * リピーターのJSONデータをサニタイズ
 *
 * @param string $value JSON文字列
 * @return string サニタイズ済みのJSON文字列
 */
function backbone_sanitize_repeater_json($value) {
    if (empty($value)) {
        return '';
    }

    // JSON形式かチェック
    $items = json_decode($value, true);
    if (!is_array($items)) {
        return '';
    }

    // 各アイテムをサニタイズ
    $sanitized_items = array();
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $sanitized_item = array();
        foreach ($item as $key => $val) {
            // キーをサニタイズ
            $clean_key = sanitize_key($key);

            // 値の型に応じてサニタイズ
            if ($clean_key === 'post_id') {
                $sanitized_item[$clean_key] = absint($val);
            } elseif ($clean_key === 'url') {
                $sanitized_item[$clean_key] = esc_url_raw($val);
            } elseif ($clean_key === 'desc') {
                // 説明文はHTMLタグを許可
                $sanitized_item[$clean_key] = wp_kses_post($val);
            } else {
                $sanitized_item[$clean_key] = sanitize_text_field($val);
            }
        }

        if (!empty($sanitized_item)) {
            $sanitized_items[] = $sanitized_item;
        }
    }

    return wp_json_encode($sanitized_items);
}

/**
 * 統一レイアウトのサニタイズ関数
 * 全セクション共通で使用
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_layout_unified($value) {
    $valid_layouts = array('2col', '3col', '4col', 'list');

    if (in_array($value, $valid_layouts, true)) {
        return $value;
    }

    return '3col';
}

/**
 * 記事一覧レイアウトのサニタイズ関数（後方互換性のため残す）
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_posts_layout($value) {
    // 旧形式を新形式に変換
    $conversion_map = array(
        'grid-3col' => '3col',
        'grid-2col' => '2col',
        'grid-4col' => '4col',
    );

    if (isset($conversion_map[$value])) {
        $value = $conversion_map[$value];
    }

    return backbone_sanitize_layout_unified($value);
}

/**
 * 記事一覧並び順のサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_posts_orderby($value) {
    $valid_orderby = array('date', 'comment_count', 'rand');

    if (in_array($value, $valid_orderby, true)) {
        return $value;
    }

    return 'date';
}

/**
 * レイアウトカラム数のサニタイズ関数（後方互換性のため残す）
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_layout_columns($value) {
    return backbone_sanitize_layout_unified($value);
}
