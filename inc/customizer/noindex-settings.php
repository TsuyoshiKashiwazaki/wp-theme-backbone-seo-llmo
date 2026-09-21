<?php
/**
 * インデックス制御（noindex）のカスタマイザー設定
 *
 * アーカイブ系ページを検索エンジンのインデックス対象から外すための設定。
 * 既定値はすべて「noindexにしない」＝導入しても挙動は変わらない。
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * インデックス制御設定をカスタマイザーに追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 */
function backbone_add_noindex_settings($wp_customize) {
    $wp_customize->add_section('noindex_settings', array(
        'title' => __('インデックス設定（noindex）', 'backbone-seo-llmo'),
        'priority' => 56,
        'description' => __('アーカイブページを検索エンジンのインデックス対象から外します。すべて「noindexにしない」が初期値です。follow は常に維持されるため、リンクのたどり直しは妨げません。なお検索結果ページはWordPress本体が既にnoindexにしているため、ここには項目がありません。', 'backbone-seo-llmo'),
    ));

    $mode_choices = array(
        'off'  => __('noindexにしない', 'backbone-seo-llmo'),
        'thin' => __('記事数がしきい値以下のものだけ', 'backbone-seo-llmo'),
        'all'  => __('すべてnoindexにする', 'backbone-seo-llmo'),
    );

    $priority = 10;

    // ━━━ タグ ━━━
    $wp_customize->add_setting('noindex_tag_heading', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'noindex_tag_heading', array(
        'label' => __('タグ', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'priority' => $priority++,
    )));

    $wp_customize->add_setting('noindex_tag', array(
        'default' => 'off',
        'sanitize_callback' => 'backbone_sanitize_noindex_mode',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('noindex_tag', array(
        'label' => __('個別タグアーカイブ（/tag/○○/）', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'type' => 'select',
        'choices' => $mode_choices,
        'priority' => $priority++,
    ));

    $wp_customize->add_setting('noindex_tag_threshold', array(
        'default' => 1,
        'sanitize_callback' => 'backbone_sanitize_noindex_threshold',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('noindex_tag_threshold', array(
        'label' => __('しきい値（記事数がこの数以下）', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'type' => 'number',
        'input_attrs' => array('min' => 0, 'max' => 100, 'step' => 1),
        'description' => __('例：1 にすると、記事が1本しかぶら下がっていないタグだけをnoindexにします。記事が増えれば自動的にindexへ戻ります。', 'backbone-seo-llmo'),
        'priority' => $priority++,
        'active_callback' => 'backbone_is_noindex_tag_thin_mode',
    ));

    $wp_customize->add_setting('noindex_tag_root', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('noindex_tag_root', array(
        'label' => __('タグ一覧ページ（/tag/）もnoindexにする', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'type' => 'checkbox',
        'description' => __('全タグへのリンクを持つハブページです。noindexにするとクロール経路が細くなるため、通常はオフのままを推奨します。', 'backbone-seo-llmo'),
        'priority' => $priority++,
    ));

    // ━━━ カテゴリ ━━━
    $priority = 100;
    $wp_customize->add_setting('noindex_category_heading', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'noindex_category_heading', array(
        'label' => __('カテゴリ', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'priority' => $priority++,
    )));

    $wp_customize->add_setting('noindex_category', array(
        'default' => 'off',
        'sanitize_callback' => 'backbone_sanitize_noindex_mode',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('noindex_category', array(
        'label' => __('個別カテゴリアーカイブ', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'type' => 'select',
        'choices' => $mode_choices,
        'priority' => $priority++,
    ));

    $wp_customize->add_setting('noindex_category_threshold', array(
        'default' => 1,
        'sanitize_callback' => 'backbone_sanitize_noindex_threshold',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('noindex_category_threshold', array(
        'label' => __('しきい値（記事数がこの数以下）', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'type' => 'number',
        'input_attrs' => array('min' => 0, 'max' => 100, 'step' => 1),
        'priority' => $priority++,
        'active_callback' => 'backbone_is_noindex_category_thin_mode',
    ));

    $wp_customize->add_setting('noindex_category_root', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('noindex_category_root', array(
        'label' => __('カテゴリ一覧ページ（/category/）もnoindexにする', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'type' => 'checkbox',
        'priority' => $priority++,
    ));

    // ━━━ その他のアーカイブ ━━━
    $priority = 200;
    $wp_customize->add_setting('noindex_other_heading', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'noindex_other_heading', array(
        'label' => __('その他のアーカイブ', 'backbone-seo-llmo'),
        'section' => 'noindex_settings',
        'priority' => $priority++,
    )));

    $other = array(
        'noindex_author' => __('著者アーカイブをnoindexにする', 'backbone-seo-llmo'),
        'noindex_date'   => __('日付アーカイブをnoindexにする', 'backbone-seo-llmo'),
        'noindex_paged'  => __('2ページ目以降（ページネーション）をnoindexにする', 'backbone-seo-llmo'),
    );

    foreach ($other as $key => $label) {
        $wp_customize->add_setting($key, array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control($key, array(
            'label' => $label,
            'section' => 'noindex_settings',
            'type' => 'checkbox',
            'priority' => $priority++,
        ));
    }
}

/**
 * noindexモードのサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_noindex_mode($value) {
    $valid_modes = array('off', 'thin', 'all');

    if (in_array($value, $valid_modes, true)) {
        return $value;
    }

    return 'off';
}

/**
 * しきい値のサニタイズ関数
 *
 * @param mixed $value 入力値
 * @return int サニタイズ済みの値（0〜100）
 */
function backbone_sanitize_noindex_threshold($value) {
    $value = absint($value);

    if ($value > 100) {
        return 100;
    }

    return $value;
}

/**
 * タグが「しきい値モード」かどうかを判定
 *
 * @return bool しきい値モードの場合true
 */
function backbone_is_noindex_tag_thin_mode() {
    return get_theme_mod('noindex_tag', 'off') === 'thin';
}

/**
 * カテゴリが「しきい値モード」かどうかを判定
 *
 * @return bool しきい値モードの場合true
 */
function backbone_is_noindex_category_thin_mode() {
    return get_theme_mod('noindex_category', 'off') === 'thin';
}

/**
 * カスタマイザー用スクリプトの読み込み
 *
 * モード選択としきい値欄の表示を即時連動させる。
 */
function backbone_noindex_customizer_scripts() {
    $cache_busting_admin = get_theme_mod('enable_cache_busting_admin', false);

    wp_enqueue_script(
        'backbone-customizer-noindex',
        get_template_directory_uri() . '/js/customizer-noindex.js',
        array('customize-controls'),
        backbone_get_file_version('/js/customizer-noindex.js', $cache_busting_admin),
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'backbone_noindex_customizer_scripts');
