<?php
/**
 * アーカイブページのカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * アーカイブページ設定をカスタマイザーに追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 */
function backbone_add_archive_settings($wp_customize) {
    // アーカイブページ設定のセクションを追加
    $wp_customize->add_section('archive_settings', array(
        'title' => __('アーカイブページ設定', 'backbone-seo-llmo'),
        'priority' => 55,
        'description' => __('カテゴリ一覧ページなどのアーカイブページの表示設定を行います。', 'backbone-seo-llmo'),
    ));

    // 列数設定
    $wp_customize->add_setting('archive_grid_columns', array(
        'default' => '3',
        'sanitize_callback' => 'backbone_sanitize_archive_columns',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_grid_columns', array(
        'label' => __('グリッド列数', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'select',
        'choices' => array(
            '2' => __('2列', 'backbone-seo-llmo'),
            '3' => __('3列', 'backbone-seo-llmo'),
            '4' => __('4列', 'backbone-seo-llmo'),
        ),
        'description' => __('アーカイブページでの投稿一覧の列数を選択します。タブレットは2列、スマートフォンは1列で表示されます。', 'backbone-seo-llmo'),
    ));

    // 並び順設定
    $wp_customize->add_setting('archive_orderby', array(
        'default' => 'date',
        'sanitize_callback' => 'backbone_sanitize_archive_orderby',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_orderby', array(
        'label' => __('並び順', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'select',
        'choices' => array(
            'date' => __('投稿日順（新しい順）', 'backbone-seo-llmo'),
            'modified' => __('更新日順（新しい順）', 'backbone-seo-llmo'),
            'rand' => __('ランダム', 'backbone-seo-llmo'),
        ),
        'description' => __('アーカイブページでの投稿の並び順を選択します。', 'backbone-seo-llmo'),
    ));

    // 表示要素の制御
    $wp_customize->add_setting('archive_show_thumbnail', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_show_thumbnail', array(
        'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('archive_show_date', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_show_date', array(
        'label' => __('投稿日を表示', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('archive_show_modified', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_show_modified', array(
        'label' => __('更新日を表示', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('archive_show_category', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_show_category', array(
        'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('archive_show_excerpt', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_show_excerpt', array(
        'label' => __('抜粋を表示', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'checkbox',
    ));
}

/**
 * 列数のサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_archive_columns($value) {
    $valid_columns = array('2', '3', '4');

    if (in_array($value, $valid_columns, true)) {
        return $value;
    }

    return '3';
}

/**
 * アーカイブ並び順のサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_archive_orderby($value) {
    $valid_orderby = array('date', 'modified', 'rand');

    if (in_array($value, $valid_orderby, true)) {
        return $value;
    }

    return 'date';
}
