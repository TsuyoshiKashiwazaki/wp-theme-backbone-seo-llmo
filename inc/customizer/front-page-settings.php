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

    // 現在のモードに応じて説明文を変更
    $current_mode = get_theme_mod('backbone_front_page_mode', 'custom');
    if ($current_mode === 'custom') {
        $wp_customize->get_section('static_front_page')->description = __('⚠️ 現在、テーマ独自のカスタムフロントページ機能を使用しています。WordPress標準の「ホームページの表示」設定は無効化されています。', 'backbone-seo-llmo');
    } else {
        $wp_customize->get_section('static_front_page')->description = __('トップページの表示方法を設定します。カスタムフロントページを使用するか、既存のページを選択できます。', 'backbone-seo-llmo');
    }

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

    // --- 説明文ソース選択 ---
    $wp_customize->add_setting('backbone_front_description_source', array(
        'default' => 'manual',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_description_source', array(
        'label' => __('説明文のソース', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 68,
        'type' => 'radio',
        'choices' => array(
            'manual' => __('手動入力', 'backbone-seo-llmo'),
            'page' => __('ページから取得', 'backbone-seo-llmo'),
        ),
        'description' => __('説明文を直接入力するか、既存のページから取得するかを選択してください。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- 説明文ソースページ選択（検索可能） ---
    $wp_customize->add_setting('backbone_front_description_page', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Searchable_Select_Control($wp_customize, 'backbone_front_description_page', array(
        'label' => __('ソースページ', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 69,
        'choices' => backbone_get_all_posts_for_dropdown(),
        'description' => __('コンテンツを取得するページ・投稿・カスタム投稿を選択してください。検索できます。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_description_source', 'manual') === 'page';
        },
    )));

    // --- ソースページのタイトル表示 ---
    $wp_customize->add_setting('backbone_front_description_show_title', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_description_show_title', array(
        'label' => __('タイトル（h1）を表示する', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 69,
        'type' => 'checkbox',
        'description' => __('ソースページのタイトルも説明文の前に表示します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_description_source', 'manual') === 'page';
        },
    ));

    // --- ソースページのタイトルをページタイトルに使用 ---
    $wp_customize->add_setting('backbone_front_use_source_title', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_use_source_title', array(
        'label' => __('TITLEを「投稿タイトル | サイト名」にする', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 69,
        'type' => 'checkbox',
        'description' => __('ブラウザのタイトルタグにソースページのタイトルを使用します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_description_source', 'manual') === 'page';
        },
    ));

    // --- ソースページのアイキャッチ画像をメインビジュアルに使用 ---
    $wp_customize->add_setting('backbone_front_use_source_thumbnail', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_use_source_thumbnail', array(
        'label' => __('アイキャッチ画像をメインビジュアルに使用', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 69,
        'type' => 'checkbox',
        'description' => __('ソースページのアイキャッチ画像をヒーローイメージとして表示します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_description_source', 'manual') === 'page';
        },
    ));

    // --- 説明文（手動入力） ---
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
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_description_source', 'manual') === 'manual';
        },
    )));

    // ============================================
    // セクション表示順序の設定
    // ============================================
    $wp_customize->add_setting('backbone_front_sections_order', array(
        'default' => '["list_1","individual_1","list_2","individual_2","list_3","individual_3","list_4","individual_4","list_5","individual_5"]',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Section_Order_Control($wp_customize, 'backbone_front_sections_order', array(
        'label' => __('🔀 セクション表示順序', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 73,
        'description' => __('ドラッグ&ドロップでセクションの表示順序を変更できます。', 'backbone-seo-llmo'),
        'sections' => array(
            'list_1' => array('label' => '一覧表示セクション 1', 'type' => '📰'),
            'list_2' => array('label' => '一覧表示セクション 2', 'type' => '📰'),
            'list_3' => array('label' => '一覧表示セクション 3', 'type' => '📰'),
            'list_4' => array('label' => '一覧表示セクション 4', 'type' => '📰'),
            'list_5' => array('label' => '一覧表示セクション 5', 'type' => '📰'),
            'individual_1' => array('label' => '個別記事セクション 1', 'type' => '⭐'),
            'individual_2' => array('label' => '個別記事セクション 2', 'type' => '⭐'),
            'individual_3' => array('label' => '個別記事セクション 3', 'type' => '⭐'),
            'individual_4' => array('label' => '個別記事セクション 4', 'type' => '⭐'),
            'individual_5' => array('label' => '個別記事セクション 5', 'type' => '⭐'),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    )));

    // セクション区切り用の見出し（descriptionを使用）
    $wp_customize->add_setting('backbone_front_free_section_divider', array(
        'sanitize_callback' => '__return_false',
    ));

    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'backbone_front_free_section_divider', array(
        'label' => __('📝 フリーコンテンツエリア', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 74,
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    )));

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
    // D. 一覧表示セクション（リピーター）
    // ============================================

    // セクション区切り用の見出し
    $wp_customize->add_setting('backbone_front_list_sections_divider', array(
        'sanitize_callback' => '__return_false',
    ));

    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'backbone_front_list_sections_divider', array(
        'label' => __('📰 一覧表示セクション（最大5つ）', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 197,
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    )));

    // --- 一覧表示セクション 1 ---
    // 有効/無効
    $wp_customize->add_setting('backbone_front_list_1_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_list_1_enable', array(
        'label' => __('一覧表示セクション 1 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 198,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // セクション1のリピーター
    $wp_customize->add_setting('backbone_front_list_sections_1', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_list_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_list_sections_1', array(
        'label' => __('一覧表示セクション 1', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 199,
        'description' => __('記事一覧を表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'title' => array(
                'type' => 'text',
                'label' => __('セクションタイトル', 'backbone-seo-llmo'),
            ),
            'count' => array(
                'type' => 'number',
                'label' => __('表示件数', 'backbone-seo-llmo'),
            ),
            'display_type' => array(
                'type' => 'select',
                'label' => __('表示対象', 'backbone-seo-llmo'),
                'choices' => array(
                    'category' => __('カテゴリー', 'backbone-seo-llmo'),
                    'tag' => __('タグ', 'backbone-seo-llmo'),
                    'post_type' => __('投稿タイプ', 'backbone-seo-llmo'),
                    'author' => __('作成者', 'backbone-seo-llmo'),
                    'date' => __('期間', 'backbone-seo-llmo'),
                ),
            ),
            'category' => array(
                'type' => 'select',
                'label' => __('カテゴリー', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全カテゴリー', 'backbone-seo-llmo')),
            ),
            'tag' => array(
                'type' => 'select',
                'label' => __('タグ', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全タグ', 'backbone-seo-llmo')),
            ),
            'post_type_filter' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo')),
            ),
            'author' => array(
                'type' => 'select',
                'label' => __('作成者', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全作成者', 'backbone-seo-llmo')),
            ),
            'date_range' => array(
                'type' => 'select',
                'label' => __('期間', 'backbone-seo-llmo'),
                'choices' => array(
                    'current_month' => __('今月', 'backbone-seo-llmo'),
                    'last_month' => __('先月', 'backbone-seo-llmo'),
                    'current_year' => __('今年', 'backbone-seo-llmo'),
                    'last_year' => __('昨年', 'backbone-seo-llmo'),
                ),
            ),
            'layout' => array(
                'type' => 'select',
                'label' => __('レイアウト', 'backbone-seo-llmo'),
                'choices' => array(
                    '1col' => __('1カラム', 'backbone-seo-llmo'),
                    '2col' => __('2カラム', 'backbone-seo-llmo'),
                    '3col' => __('3カラム', 'backbone-seo-llmo'),
                    '4col' => __('4カラム', 'backbone-seo-llmo'),
                    'list' => __('リスト', 'backbone-seo-llmo'),
                ),
            ),
            'orderby' => array(
                'type' => 'select',
                'label' => __('並び順', 'backbone-seo-llmo'),
                'choices' => array(
                    'date' => __('投稿日順（新しい順）', 'backbone-seo-llmo'),
                    'modified' => __('更新日順（新しい順）', 'backbone-seo-llmo'),
                    'rand' => __('ランダム', 'backbone-seo-llmo'),
                ),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'thumbnail_size' => array(
                'type' => 'select',
                'label' => __('アイキャッチ画像サイズ', 'backbone-seo-llmo'),
                'choices' => array(
                    'full' => __('フルサイズ', 'backbone-seo-llmo'),
                    'large' => __('大サイズ', 'backbone-seo-llmo'),
                    'medium_large' => __('中大サイズ', 'backbone-seo-llmo'),
                    'medium' => __('中サイズ', 'backbone-seo-llmo'),
                    'thumbnail' => __('サムネイル', 'backbone-seo-llmo'),
                ),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
            'show_archive_link' => array(
                'type' => 'checkbox',
                'label' => __('一覧表示リンクを表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_list_1_enable', false);
        },
    )));

    // --- 一覧表示セクション 2 ---
    $wp_customize->add_setting('backbone_front_list_2_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_list_2_enable', array(
        'label' => __('一覧表示セクション 2 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 201,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_list_sections_2', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_list_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_list_sections_2', array(
        'label' => __('一覧表示セクション 2', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 202,
        'description' => __('記事一覧を表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'title' => array(
                'type' => 'text',
                'label' => __('セクションタイトル', 'backbone-seo-llmo'),
            ),
            'count' => array(
                'type' => 'number',
                'label' => __('表示件数', 'backbone-seo-llmo'),
            ),
            'display_type' => array(
                'type' => 'select',
                'label' => __('表示対象', 'backbone-seo-llmo'),
                'choices' => array(
                    'category' => __('カテゴリー', 'backbone-seo-llmo'),
                    'tag' => __('タグ', 'backbone-seo-llmo'),
                    'post_type' => __('投稿タイプ', 'backbone-seo-llmo'),
                    'author' => __('作成者', 'backbone-seo-llmo'),
                    'date' => __('期間', 'backbone-seo-llmo'),
                ),
            ),
            'category' => array(
                'type' => 'select',
                'label' => __('カテゴリー', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全カテゴリー', 'backbone-seo-llmo')),
            ),
            'tag' => array(
                'type' => 'select',
                'label' => __('タグ', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全タグ', 'backbone-seo-llmo')),
            ),
            'post_type_filter' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo')),
            ),
            'author' => array(
                'type' => 'select',
                'label' => __('作成者', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全作成者', 'backbone-seo-llmo')),
            ),
            'date_range' => array(
                'type' => 'select',
                'label' => __('期間', 'backbone-seo-llmo'),
                'choices' => array(
                    'current_month' => __('今月', 'backbone-seo-llmo'),
                    'last_month' => __('先月', 'backbone-seo-llmo'),
                    'current_year' => __('今年', 'backbone-seo-llmo'),
                    'last_year' => __('昨年', 'backbone-seo-llmo'),
                ),
            ),
            'layout' => array(
                'type' => 'select',
                'label' => __('レイアウト', 'backbone-seo-llmo'),
                'choices' => array(
                    '1col' => __('1カラム', 'backbone-seo-llmo'),
                    '2col' => __('2カラム', 'backbone-seo-llmo'),
                    '3col' => __('3カラム', 'backbone-seo-llmo'),
                    '4col' => __('4カラム', 'backbone-seo-llmo'),
                    'list' => __('リスト', 'backbone-seo-llmo'),
                ),
            ),
            'orderby' => array(
                'type' => 'select',
                'label' => __('並び順', 'backbone-seo-llmo'),
                'choices' => array(
                    'date' => __('投稿日順（新しい順）', 'backbone-seo-llmo'),
                    'modified' => __('更新日順（新しい順）', 'backbone-seo-llmo'),
                    'rand' => __('ランダム', 'backbone-seo-llmo'),
                ),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'thumbnail_size' => array(
                'type' => 'select',
                'label' => __('アイキャッチ画像サイズ', 'backbone-seo-llmo'),
                'choices' => array(
                    'full' => __('フルサイズ', 'backbone-seo-llmo'),
                    'large' => __('大サイズ', 'backbone-seo-llmo'),
                    'medium_large' => __('中大サイズ', 'backbone-seo-llmo'),
                    'medium' => __('中サイズ', 'backbone-seo-llmo'),
                    'thumbnail' => __('サムネイル', 'backbone-seo-llmo'),
                ),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
            'show_archive_link' => array(
                'type' => 'checkbox',
                'label' => __('一覧表示リンクを表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_list_2_enable', false);
        },
    )));

    // --- 一覧表示セクション 3 ---
    $wp_customize->add_setting('backbone_front_list_3_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_list_3_enable', array(
        'label' => __('一覧表示セクション 3 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 203,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_list_sections_3', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_list_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_list_sections_3', array(
        'label' => __('一覧表示セクション 3', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 204,
        'description' => __('記事一覧を表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'title' => array(
                'type' => 'text',
                'label' => __('セクションタイトル', 'backbone-seo-llmo'),
            ),
            'count' => array(
                'type' => 'number',
                'label' => __('表示件数', 'backbone-seo-llmo'),
            ),
            'display_type' => array(
                'type' => 'select',
                'label' => __('表示対象', 'backbone-seo-llmo'),
                'choices' => array(
                    'category' => __('カテゴリー', 'backbone-seo-llmo'),
                    'tag' => __('タグ', 'backbone-seo-llmo'),
                    'post_type' => __('投稿タイプ', 'backbone-seo-llmo'),
                    'author' => __('作成者', 'backbone-seo-llmo'),
                    'date' => __('期間', 'backbone-seo-llmo'),
                ),
            ),
            'category' => array(
                'type' => 'select',
                'label' => __('カテゴリー', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全カテゴリー', 'backbone-seo-llmo')),
            ),
            'tag' => array(
                'type' => 'select',
                'label' => __('タグ', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全タグ', 'backbone-seo-llmo')),
            ),
            'post_type_filter' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo')),
            ),
            'author' => array(
                'type' => 'select',
                'label' => __('作成者', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全作成者', 'backbone-seo-llmo')),
            ),
            'date_range' => array(
                'type' => 'select',
                'label' => __('期間', 'backbone-seo-llmo'),
                'choices' => array(
                    'current_month' => __('今月', 'backbone-seo-llmo'),
                    'last_month' => __('先月', 'backbone-seo-llmo'),
                    'current_year' => __('今年', 'backbone-seo-llmo'),
                    'last_year' => __('昨年', 'backbone-seo-llmo'),
                ),
            ),
            'layout' => array(
                'type' => 'select',
                'label' => __('レイアウト', 'backbone-seo-llmo'),
                'choices' => array(
                    '1col' => __('1カラム', 'backbone-seo-llmo'),
                    '2col' => __('2カラム', 'backbone-seo-llmo'),
                    '3col' => __('3カラム', 'backbone-seo-llmo'),
                    '4col' => __('4カラム', 'backbone-seo-llmo'),
                    'list' => __('リスト', 'backbone-seo-llmo'),
                ),
            ),
            'orderby' => array(
                'type' => 'select',
                'label' => __('並び順', 'backbone-seo-llmo'),
                'choices' => array(
                    'date' => __('投稿日順（新しい順）', 'backbone-seo-llmo'),
                    'modified' => __('更新日順（新しい順）', 'backbone-seo-llmo'),
                    'rand' => __('ランダム', 'backbone-seo-llmo'),
                ),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'thumbnail_size' => array(
                'type' => 'select',
                'label' => __('アイキャッチ画像サイズ', 'backbone-seo-llmo'),
                'choices' => array(
                    'full' => __('フルサイズ', 'backbone-seo-llmo'),
                    'large' => __('大サイズ', 'backbone-seo-llmo'),
                    'medium_large' => __('中大サイズ', 'backbone-seo-llmo'),
                    'medium' => __('中サイズ', 'backbone-seo-llmo'),
                    'thumbnail' => __('サムネイル', 'backbone-seo-llmo'),
                ),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
            'show_archive_link' => array(
                'type' => 'checkbox',
                'label' => __('一覧表示リンクを表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_list_3_enable', false);
        },
    )));

    // --- 一覧表示セクション 4 ---
    $wp_customize->add_setting('backbone_front_list_4_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_list_4_enable', array(
        'label' => __('一覧表示セクション 4 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 205,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_list_sections_4', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_list_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_list_sections_4', array(
        'label' => __('一覧表示セクション 4', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 206,
        'description' => __('記事一覧を表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'title' => array(
                'type' => 'text',
                'label' => __('セクションタイトル', 'backbone-seo-llmo'),
            ),
            'count' => array(
                'type' => 'number',
                'label' => __('表示件数', 'backbone-seo-llmo'),
            ),
            'display_type' => array(
                'type' => 'select',
                'label' => __('表示対象', 'backbone-seo-llmo'),
                'choices' => array(
                    'category' => __('カテゴリー', 'backbone-seo-llmo'),
                    'tag' => __('タグ', 'backbone-seo-llmo'),
                    'post_type' => __('投稿タイプ', 'backbone-seo-llmo'),
                    'author' => __('作成者', 'backbone-seo-llmo'),
                    'date' => __('期間', 'backbone-seo-llmo'),
                ),
            ),
            'category' => array(
                'type' => 'select',
                'label' => __('カテゴリー', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全カテゴリー', 'backbone-seo-llmo')),
            ),
            'tag' => array(
                'type' => 'select',
                'label' => __('タグ', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全タグ', 'backbone-seo-llmo')),
            ),
            'post_type_filter' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo')),
            ),
            'author' => array(
                'type' => 'select',
                'label' => __('作成者', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全作成者', 'backbone-seo-llmo')),
            ),
            'date_range' => array(
                'type' => 'select',
                'label' => __('期間', 'backbone-seo-llmo'),
                'choices' => array(
                    'current_month' => __('今月', 'backbone-seo-llmo'),
                    'last_month' => __('先月', 'backbone-seo-llmo'),
                    'current_year' => __('今年', 'backbone-seo-llmo'),
                    'last_year' => __('昨年', 'backbone-seo-llmo'),
                ),
            ),
            'layout' => array(
                'type' => 'select',
                'label' => __('レイアウト', 'backbone-seo-llmo'),
                'choices' => array(
                    '1col' => __('1カラム', 'backbone-seo-llmo'),
                    '2col' => __('2カラム', 'backbone-seo-llmo'),
                    '3col' => __('3カラム', 'backbone-seo-llmo'),
                    '4col' => __('4カラム', 'backbone-seo-llmo'),
                    'list' => __('リスト', 'backbone-seo-llmo'),
                ),
            ),
            'orderby' => array(
                'type' => 'select',
                'label' => __('並び順', 'backbone-seo-llmo'),
                'choices' => array(
                    'date' => __('投稿日順（新しい順）', 'backbone-seo-llmo'),
                    'modified' => __('更新日順（新しい順）', 'backbone-seo-llmo'),
                    'rand' => __('ランダム', 'backbone-seo-llmo'),
                ),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'thumbnail_size' => array(
                'type' => 'select',
                'label' => __('アイキャッチ画像サイズ', 'backbone-seo-llmo'),
                'choices' => array(
                    'full' => __('フルサイズ', 'backbone-seo-llmo'),
                    'large' => __('大サイズ', 'backbone-seo-llmo'),
                    'medium_large' => __('中大サイズ', 'backbone-seo-llmo'),
                    'medium' => __('中サイズ', 'backbone-seo-llmo'),
                    'thumbnail' => __('サムネイル', 'backbone-seo-llmo'),
                ),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
            'show_archive_link' => array(
                'type' => 'checkbox',
                'label' => __('一覧表示リンクを表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_list_4_enable', false);
        },
    )));

    // --- 一覧表示セクション 5 ---
    $wp_customize->add_setting('backbone_front_list_5_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_list_5_enable', array(
        'label' => __('一覧表示セクション 5 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 207,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_list_sections_5', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_list_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_list_sections_5', array(
        'label' => __('一覧表示セクション 5', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 208,
        'description' => __('記事一覧を表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'title' => array(
                'type' => 'text',
                'label' => __('セクションタイトル', 'backbone-seo-llmo'),
            ),
            'count' => array(
                'type' => 'number',
                'label' => __('表示件数', 'backbone-seo-llmo'),
            ),
            'display_type' => array(
                'type' => 'select',
                'label' => __('表示対象', 'backbone-seo-llmo'),
                'choices' => array(
                    'category' => __('カテゴリー', 'backbone-seo-llmo'),
                    'tag' => __('タグ', 'backbone-seo-llmo'),
                    'post_type' => __('投稿タイプ', 'backbone-seo-llmo'),
                    'author' => __('作成者', 'backbone-seo-llmo'),
                    'date' => __('期間', 'backbone-seo-llmo'),
                ),
            ),
            'category' => array(
                'type' => 'select',
                'label' => __('カテゴリー', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全カテゴリー', 'backbone-seo-llmo')),
            ),
            'tag' => array(
                'type' => 'select',
                'label' => __('タグ', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全タグ', 'backbone-seo-llmo')),
            ),
            'post_type_filter' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo')),
            ),
            'author' => array(
                'type' => 'select',
                'label' => __('作成者', 'backbone-seo-llmo'),
                'choices' => array(0 => __('全作成者', 'backbone-seo-llmo')),
            ),
            'date_range' => array(
                'type' => 'select',
                'label' => __('期間', 'backbone-seo-llmo'),
                'choices' => array(
                    'current_month' => __('今月', 'backbone-seo-llmo'),
                    'last_month' => __('先月', 'backbone-seo-llmo'),
                    'current_year' => __('今年', 'backbone-seo-llmo'),
                    'last_year' => __('昨年', 'backbone-seo-llmo'),
                ),
            ),
            'layout' => array(
                'type' => 'select',
                'label' => __('レイアウト', 'backbone-seo-llmo'),
                'choices' => array(
                    '1col' => __('1カラム', 'backbone-seo-llmo'),
                    '2col' => __('2カラム', 'backbone-seo-llmo'),
                    '3col' => __('3カラム', 'backbone-seo-llmo'),
                    '4col' => __('4カラム', 'backbone-seo-llmo'),
                    'list' => __('リスト', 'backbone-seo-llmo'),
                ),
            ),
            'orderby' => array(
                'type' => 'select',
                'label' => __('並び順', 'backbone-seo-llmo'),
                'choices' => array(
                    'date' => __('投稿日順（新しい順）', 'backbone-seo-llmo'),
                    'modified' => __('更新日順（新しい順）', 'backbone-seo-llmo'),
                    'rand' => __('ランダム', 'backbone-seo-llmo'),
                ),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'thumbnail_size' => array(
                'type' => 'select',
                'label' => __('アイキャッチ画像サイズ', 'backbone-seo-llmo'),
                'choices' => array(
                    'full' => __('フルサイズ', 'backbone-seo-llmo'),
                    'large' => __('大サイズ', 'backbone-seo-llmo'),
                    'medium_large' => __('中大サイズ', 'backbone-seo-llmo'),
                    'medium' => __('中サイズ', 'backbone-seo-llmo'),
                    'thumbnail' => __('サムネイル', 'backbone-seo-llmo'),
                ),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
            'show_archive_link' => array(
                'type' => 'checkbox',
                'label' => __('一覧表示リンクを表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_list_5_enable', false);
        },
    )));

    // ============================================
    // E. 個別記事セクション（最大3つ）
    // ============================================

    // セクション区切り用の見出し
    $wp_customize->add_setting('backbone_front_individual_sections_divider', array(
        'sanitize_callback' => '__return_false',
    ));

    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'backbone_front_individual_sections_divider', array(
        'label' => __('⭐ 個別記事セクション（最大5つ）', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 298,
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    )));

    // --- 個別記事セクション 1 ---
    $wp_customize->add_setting('backbone_front_individual_1_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_1_enable', array(
        'label' => __('個別記事セクション 1 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 299,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    // --- 個別記事セクション 1 のレイアウト ---
    $wp_customize->add_setting('backbone_front_individual_1_layout', array(
        'default' => '2col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_1_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 300,
        'type' => 'select',
        'choices' => array(
            '1col' => __('1カラム', 'backbone-seo-llmo'),
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'description' => __('個別記事セクション 1 のレイアウトを選択します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_1_enable', false);
        },
    ));

    // --- 個別記事セクション 1 ---
    $wp_customize->add_setting('backbone_front_individual_sections_1', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_individual_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_individual_sections_1', array(
        'label' => __('個別記事セクション 1', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 301,
        'description' => __('特定の記事を選択して表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'post_type' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo'), 'page' => __('固定ページ', 'backbone-seo-llmo')),  // デフォルト値を設定
            ),
            'post_id' => array(
                'type' => 'select',
                'label' => __('記事を選択', 'backbone-seo-llmo'),
                'choices' => array(0 => __('— 選択してください —', 'backbone-seo-llmo')),  // デフォルト値を設定
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_1_enable', false);
        },
    )));

    // --- 個別記事セクション 2 ---
    $wp_customize->add_setting('backbone_front_individual_2_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_2_enable', array(
        'label' => __('個別記事セクション 2 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 302,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_2_layout', array(
        'default' => '2col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_2_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 303,
        'type' => 'select',
        'choices' => array(
            '1col' => __('1カラム', 'backbone-seo-llmo'),
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'description' => __('個別記事セクション 2 のレイアウトを選択します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_2_enable', false);
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_sections_2', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_individual_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_individual_sections_2', array(
        'label' => __('個別記事セクション 2', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 304,
        'description' => __('特定の記事を選択して表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'post_type' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo'), 'page' => __('固定ページ', 'backbone-seo-llmo')),
            ),
            'post_id' => array(
                'type' => 'select',
                'label' => __('記事を選択', 'backbone-seo-llmo'),
                'choices' => array(0 => __('— 選択してください —', 'backbone-seo-llmo')),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_2_enable', false);
        },
    )));

    // --- 個別記事セクション 3 ---
    $wp_customize->add_setting('backbone_front_individual_3_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_3_enable', array(
        'label' => __('個別記事セクション 3 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 305,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_3_layout', array(
        'default' => '2col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_3_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 306,
        'type' => 'select',
        'choices' => array(
            '1col' => __('1カラム', 'backbone-seo-llmo'),
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'description' => __('個別記事セクション 3 のレイアウトを選択します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_3_enable', false);
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_sections_3', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_individual_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_individual_sections_3', array(
        'label' => __('個別記事セクション 3', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 307,
        'description' => __('特定の記事を選択して表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'post_type' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo'), 'page' => __('固定ページ', 'backbone-seo-llmo')),
            ),
            'post_id' => array(
                'type' => 'select',
                'label' => __('記事を選択', 'backbone-seo-llmo'),
                'choices' => array(0 => __('— 選択してください —', 'backbone-seo-llmo')),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_3_enable', false);
        },
    )));

    // --- 個別記事セクション 4 ---
    $wp_customize->add_setting('backbone_front_individual_4_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_4_enable', array(
        'label' => __('個別記事セクション 4 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 308,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_4_layout', array(
        'default' => '2col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_4_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 309,
        'type' => 'select',
        'choices' => array(
            '1col' => __('1カラム', 'backbone-seo-llmo'),
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'description' => __('個別記事セクション 4 のレイアウトを選択します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_4_enable', false);
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_sections_4', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_individual_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_individual_sections_4', array(
        'label' => __('個別記事セクション 4', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 310,
        'description' => __('特定の記事を選択して表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'post_type' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo'), 'page' => __('固定ページ', 'backbone-seo-llmo')),
            ),
            'post_id' => array(
                'type' => 'select',
                'label' => __('記事を選択', 'backbone-seo-llmo'),
                'choices' => array(0 => __('— 選択してください —', 'backbone-seo-llmo')),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_4_enable', false);
        },
    )));

    // --- 個別記事セクション 5 ---
    $wp_customize->add_setting('backbone_front_individual_5_enable', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_5_enable', array(
        'label' => __('個別記事セクション 5 を有効化', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 311,
        'type' => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom';
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_5_layout', array(
        'default' => '2col',
        'sanitize_callback' => 'backbone_sanitize_layout_unified',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('backbone_front_individual_5_layout', array(
        'label' => __('レイアウト', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 312,
        'type' => 'select',
        'choices' => array(
            '1col' => __('1カラム', 'backbone-seo-llmo'),
            '2col' => __('2カラム', 'backbone-seo-llmo'),
            '3col' => __('3カラム', 'backbone-seo-llmo'),
            '4col' => __('4カラム', 'backbone-seo-llmo'),
            'list' => __('リスト', 'backbone-seo-llmo'),
        ),
        'description' => __('個別記事セクション 5 のレイアウトを選択します。', 'backbone-seo-llmo'),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_5_enable', false);
        },
    ));

    $wp_customize->add_setting('backbone_front_individual_sections_5', array(
        'default' => '',
        'sanitize_callback' => 'backbone_sanitize_individual_sections_json',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new Backbone_Customize_Repeater_Control($wp_customize, 'backbone_front_individual_sections_5', array(
        'label' => __('個別記事セクション 5', 'backbone-seo-llmo'),
        'section' => 'static_front_page',
        'priority' => 313,
        'description' => __('特定の記事を選択して表示します。', 'backbone-seo-llmo'),
        'add_button_label' => __('記事を追加', 'backbone-seo-llmo'),
        'fields' => array(
            'post_type' => array(
                'type' => 'select',
                'label' => __('投稿タイプ', 'backbone-seo-llmo'),
                'choices' => array('post' => __('投稿', 'backbone-seo-llmo'), 'page' => __('固定ページ', 'backbone-seo-llmo')),
            ),
            'post_id' => array(
                'type' => 'select',
                'label' => __('記事を選択', 'backbone-seo-llmo'),
                'choices' => array(0 => __('— 選択してください —', 'backbone-seo-llmo')),
            ),
            'show_thumbnail' => array(
                'type' => 'checkbox',
                'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __('投稿日を表示', 'backbone-seo-llmo'),
            ),
            'show_modified' => array(
                'type' => 'checkbox',
                'label' => __('更新日を表示', 'backbone-seo-llmo'),
            ),
            'show_category' => array(
                'type' => 'checkbox',
                'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
            ),
            'show_excerpt' => array(
                'type' => 'checkbox',
                'label' => __('抜粋を表示', 'backbone-seo-llmo'),
            ),
        ),
        'active_callback' => function() {
            return get_theme_mod('backbone_front_page_mode', 'custom') === 'custom' &&
                   get_theme_mod('backbone_front_individual_5_enable', false);
        },
    )));

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

    $options = array(0 => __('— 未選択 —', 'backbone-seo-llmo'));

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
    // すべての公開投稿タイプを取得
    $post_types = get_post_types(array('public' => true), 'names');

    // attachmentを除外
    unset($post_types['attachment']);

    $posts = get_posts(array(
        'post_type' => $post_types,
        'posts_per_page' => -1,
        'post_status' => array('publish', 'draft', 'private'),
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    $options = array(0 => __('— 未選択 —', 'backbone-seo-llmo'));

    foreach ($posts as $post) {
        $post_type_label = get_post_type_object($post->post_type)->labels->singular_name;
        $status_label = '';
        if ($post->post_status === 'draft') {
            $status_label = __('【下書き】', 'backbone-seo-llmo');
        } elseif ($post->post_status === 'private') {
            $status_label = __('【非公開】', 'backbone-seo-llmo');
        }
        $options[$post->ID] = '[' . $post_type_label . '] ' . $status_label . $post->post_title;
    }

    return $options;
}

/**
 * 投稿タイプのドロップダウン用の配列を返す
 *
 * @return array 投稿タイプの配列（slug => ラベル）
 */
function backbone_get_post_types_for_dropdown() {
    $post_types = get_post_types(array('public' => true), 'objects');

    $options = array();

    foreach ($post_types as $post_type) {
        $options[$post_type->name] = $post_type->label;
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
    $valid_layouts = array('1col', '2col', '3col', '4col', 'list');

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
    $valid_orderby = array('date', 'modified', 'rand');

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

/**
 * 一覧表示セクションのJSONデータをサニタイズ
 *
 * @param string $value JSON文字列
 * @return string サニタイズ済みのJSON文字列
 */
function backbone_sanitize_list_sections_json($value) {
    if (empty($value)) {
        return '';
    }

    $sections = json_decode($value, true);
    if (!is_array($sections)) {
        return '';
    }

    $sanitized_sections = array();
    foreach ($sections as $section) {
        if (!is_array($section)) {
            continue;
        }

        $sanitized_section = array();
        $sanitized_section['title'] = isset($section['title']) ? sanitize_text_field($section['title']) : '';
        $sanitized_section['count'] = isset($section['count']) ? absint($section['count']) : 6;
        $sanitized_section['display_type'] = isset($section['display_type']) ? sanitize_key($section['display_type']) : 'category';
        $sanitized_section['category'] = isset($section['category']) ? absint($section['category']) : 0;
        $sanitized_section['tag'] = isset($section['tag']) ? absint($section['tag']) : 0;
        $sanitized_section['post_type_filter'] = isset($section['post_type_filter']) ? sanitize_key($section['post_type_filter']) : 'post';
        $sanitized_section['author'] = isset($section['author']) ? absint($section['author']) : 0;
        $sanitized_section['date_range'] = isset($section['date_range']) ? sanitize_key($section['date_range']) : 'current_month';
        $sanitized_section['layout'] = isset($section['layout']) ? backbone_sanitize_layout_unified($section['layout']) : '3col';
        $sanitized_section['orderby'] = isset($section['orderby']) ? backbone_sanitize_posts_orderby($section['orderby']) : 'date';
        $sanitized_section['show_thumbnail'] = isset($section['show_thumbnail']) ? rest_sanitize_boolean($section['show_thumbnail']) : true;
        $sanitized_section['thumbnail_size'] = isset($section['thumbnail_size']) ? backbone_sanitize_thumbnail_size($section['thumbnail_size']) : 'full';
        $sanitized_section['show_date'] = isset($section['show_date']) ? rest_sanitize_boolean($section['show_date']) : true;
        $sanitized_section['show_modified'] = isset($section['show_modified']) ? rest_sanitize_boolean($section['show_modified']) : false;
        $sanitized_section['show_category'] = isset($section['show_category']) ? rest_sanitize_boolean($section['show_category']) : true;
        $sanitized_section['show_excerpt'] = isset($section['show_excerpt']) ? rest_sanitize_boolean($section['show_excerpt']) : true;
        $sanitized_section['show_archive_link'] = isset($section['show_archive_link']) ? rest_sanitize_boolean($section['show_archive_link']) : false;

        $sanitized_sections[] = $sanitized_section;
    }

    return wp_json_encode($sanitized_sections);
}

/**
 * 個別記事セクションのJSONデータをサニタイズ
 *
 * @param string $value JSON文字列
 * @return string サニタイズ済みのJSON文字列
 */
function backbone_sanitize_individual_sections_json($value) {
    if (empty($value)) {
        return '';
    }

    $items = json_decode($value, true);
    if (!is_array($items)) {
        return '';
    }

    $sanitized_items = array();
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $sanitized_item = array();
        $sanitized_item['post_type'] = isset($item['post_type']) ? sanitize_key($item['post_type']) : 'post';
        $sanitized_item['post_id'] = isset($item['post_id']) ? absint($item['post_id']) : 0;
        $sanitized_item['show_thumbnail'] = isset($item['show_thumbnail']) ? rest_sanitize_boolean($item['show_thumbnail']) : true;
        $sanitized_item['show_date'] = isset($item['show_date']) ? rest_sanitize_boolean($item['show_date']) : true;
        $sanitized_item['show_modified'] = isset($item['show_modified']) ? rest_sanitize_boolean($item['show_modified']) : false;
        $sanitized_item['show_category'] = isset($item['show_category']) ? rest_sanitize_boolean($item['show_category']) : true;
        $sanitized_item['show_excerpt'] = isset($item['show_excerpt']) ? rest_sanitize_boolean($item['show_excerpt']) : true;

        if ($sanitized_item['post_id'] > 0) {
            $sanitized_items[] = $sanitized_item;
        }
    }

    return wp_json_encode($sanitized_items);
}
