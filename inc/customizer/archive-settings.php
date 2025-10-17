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
        'priority' => 35,
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
