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

    // 統一設定モード
    $wp_customize->add_setting('archive_use_unified_settings', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('archive_use_unified_settings', array(
        'label' => __('すべて共通設定を使用', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'type' => 'checkbox',
        'description' => __('チェックを入れると、すべてのアーカイブページに同じ設定を適用します。チェックを外すと、各アーカイブタイプごとに個別設定が可能になります。', 'backbone-seo-llmo'),
        'priority' => 1,
    ));

    // ━━━ すべて共通設定 ━━━
    $wp_customize->add_setting('archive_common_heading', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'archive_common_heading', array(
        'label' => __('すべて共通設定', 'backbone-seo-llmo'),
        'section' => 'archive_settings',
        'priority' => 10,
        'description' => __('全アーカイブページに適用される共通設定です。', 'backbone-seo-llmo'),
        'active_callback' => 'backbone_is_unified_archive_settings_enabled',
    )));

    // 共通設定の項目
    backbone_add_archive_setting_controls($wp_customize, '', 'archive_settings', 11, true);

    // 個別設定
    backbone_add_individual_archive_controls(
        $wp_customize,
        'category',
        __('カテゴリアーカイブ個別設定', 'backbone-seo-llmo'),
        __('カテゴリアーカイブページの表示設定。「すべて共通設定を使用」がオフの時に有効。', 'backbone-seo-llmo'),
        100,
        true
    );

    backbone_add_individual_archive_controls(
        $wp_customize,
        'tag',
        __('タグアーカイブ個別設定', 'backbone-seo-llmo'),
        __('タグアーカイブページの表示設定。「すべて共通設定を使用」がオフの時に有効。', 'backbone-seo-llmo'),
        200,
        true
    );

    backbone_add_individual_archive_controls(
        $wp_customize,
        'author',
        __('著者アーカイブ個別設定', 'backbone-seo-llmo'),
        __('著者アーカイブページの表示設定。「すべて共通設定を使用」がオフの時に有効。', 'backbone-seo-llmo'),
        300,
        true
    );

    backbone_add_individual_archive_controls(
        $wp_customize,
        'date',
        __('日付アーカイブ個別設定', 'backbone-seo-llmo'),
        __('日付アーカイブページの表示設定。「すべて共通設定を使用」がオフの時に有効。', 'backbone-seo-llmo'),
        400,
        true
    );

    backbone_add_individual_archive_controls(
        $wp_customize,
        'search',
        __('検索結果個別設定', 'backbone-seo-llmo'),
        __('検索結果ページの表示設定。「すべて共通設定を使用」がオフの時に有効。', 'backbone-seo-llmo'),
        500,
        false // 検索結果は並び順設定を表示しない
    );

    // カスタム投稿タイプの個別設定を追加
    backbone_add_cpt_archive_controls($wp_customize);
}

/**
 * アーカイブ設定コントロールを追加（共通設定または個別設定）
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 * @param string $type アーカイブタイプ（空文字の場合は共通設定）
 * @param string $section セクションID
 * @param int $priority_start 開始priority
 * @param bool $show_orderby 並び順設定を表示するか
 */
function backbone_add_archive_setting_controls($wp_customize, $type, $section, $priority_start, $show_orderby = true) {
    $prefix = empty($type) ? 'archive_' : 'archive_' . $type . '_';
    $priority = $priority_start;

    // 個別設定の場合はactive_callbackを設定
    $is_individual = !empty($type);

    // 列数設定
    $wp_customize->add_setting($prefix . 'grid_columns', array(
        'default' => '3',
        'sanitize_callback' => 'backbone_sanitize_archive_columns',
        'transport' => 'refresh',
    ));

    $control_args = array(
        'label' => __('グリッド列数', 'backbone-seo-llmo'),
        'section' => $section,
        'type' => 'select',
        'choices' => array(
            '2' => __('2列', 'backbone-seo-llmo'),
            '3' => __('3列', 'backbone-seo-llmo'),
            '4' => __('4列', 'backbone-seo-llmo'),
        ),
        'priority' => $priority++,
    );
    if ($is_individual) {
        $control_args['active_callback'] = 'backbone_is_individual_archive_settings_enabled';
    } else {
        $control_args['active_callback'] = 'backbone_is_unified_archive_settings_enabled';
    }
    $wp_customize->add_control($prefix . 'grid_columns', $control_args);

    // 並び順設定
    if ($show_orderby) {
        $wp_customize->add_setting($prefix . 'orderby', array(
            'default' => 'date',
            'sanitize_callback' => 'backbone_sanitize_archive_orderby',
            'transport' => 'refresh',
        ));

        $control_args = array(
            'label' => __('並び順', 'backbone-seo-llmo'),
            'section' => $section,
            'type' => 'select',
            'choices' => array(
                'date' => __('投稿日順（新しい順）', 'backbone-seo-llmo'),
                'modified' => __('更新日順（新しい順）', 'backbone-seo-llmo'),
                'rand' => __('ランダム', 'backbone-seo-llmo'),
            ),
            'priority' => $priority++,
        );
        if ($is_individual) {
            $control_args['active_callback'] = 'backbone_is_individual_archive_settings_enabled';
        } else {
            $control_args['active_callback'] = 'backbone_is_unified_archive_settings_enabled';
        }
        $wp_customize->add_control($prefix . 'orderby', $control_args);
    }

    // アイキャッチ画像表示
    $wp_customize->add_setting($prefix . 'show_thumbnail', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $control_args = array(
        'label' => __('アイキャッチ画像を表示', 'backbone-seo-llmo'),
        'section' => $section,
        'type' => 'checkbox',
        'priority' => $priority++,
    );
    if ($is_individual) {
        $control_args['active_callback'] = 'backbone_is_individual_archive_settings_enabled';
    } else {
        $control_args['active_callback'] = 'backbone_is_unified_archive_settings_enabled';
    }
    $wp_customize->add_control($prefix . 'show_thumbnail', $control_args);

    // アイキャッチ画像サイズ
    $wp_customize->add_setting($prefix . 'thumbnail_size', array(
        'default' => 'full',
        'sanitize_callback' => 'backbone_sanitize_thumbnail_size',
        'transport' => 'refresh',
    ));

    $control_args = array(
        'label' => __('アイキャッチ画像サイズ', 'backbone-seo-llmo'),
        'section' => $section,
        'type' => 'select',
        'choices' => array(
            'full' => __('フルサイズ', 'backbone-seo-llmo'),
            'large' => __('大サイズ', 'backbone-seo-llmo'),
            'medium_large' => __('中大サイズ', 'backbone-seo-llmo'),
            'medium' => __('中サイズ', 'backbone-seo-llmo'),
            'thumbnail' => __('サムネイル', 'backbone-seo-llmo'),
        ),
        'priority' => $priority++,
    );
    if ($is_individual) {
        $control_args['active_callback'] = function() use ($prefix) {
            return backbone_is_individual_archive_settings_enabled() && get_theme_mod($prefix . 'show_thumbnail', true);
        };
    } else {
        $control_args['active_callback'] = function() use ($prefix) {
            return backbone_is_unified_archive_settings_enabled() && get_theme_mod($prefix . 'show_thumbnail', true);
        };
    }
    $wp_customize->add_control($prefix . 'thumbnail_size', $control_args);

    // 投稿日表示
    $wp_customize->add_setting($prefix . 'show_date', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $control_args = array(
        'label' => __('投稿日を表示', 'backbone-seo-llmo'),
        'section' => $section,
        'type' => 'checkbox',
        'priority' => $priority++,
    );
    if ($is_individual) {
        $control_args['active_callback'] = 'backbone_is_individual_archive_settings_enabled';
    } else {
        $control_args['active_callback'] = 'backbone_is_unified_archive_settings_enabled';
    }
    $wp_customize->add_control($prefix . 'show_date', $control_args);

    // 更新日表示
    $wp_customize->add_setting($prefix . 'show_modified', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $control_args = array(
        'label' => __('更新日を表示', 'backbone-seo-llmo'),
        'section' => $section,
        'type' => 'checkbox',
        'priority' => $priority++,
    );
    if ($is_individual) {
        $control_args['active_callback'] = 'backbone_is_individual_archive_settings_enabled';
    } else {
        $control_args['active_callback'] = 'backbone_is_unified_archive_settings_enabled';
    }
    $wp_customize->add_control($prefix . 'show_modified', $control_args);

    // カテゴリ表示
    $wp_customize->add_setting($prefix . 'show_category', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $control_args = array(
        'label' => __('カテゴリを表示', 'backbone-seo-llmo'),
        'section' => $section,
        'type' => 'checkbox',
        'priority' => $priority++,
    );
    if ($is_individual) {
        $control_args['active_callback'] = 'backbone_is_individual_archive_settings_enabled';
    } else {
        $control_args['active_callback'] = 'backbone_is_unified_archive_settings_enabled';
    }
    $wp_customize->add_control($prefix . 'show_category', $control_args);

    // 抜粋表示
    $wp_customize->add_setting($prefix . 'show_excerpt', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $control_args = array(
        'label' => __('抜粋を表示', 'backbone-seo-llmo'),
        'section' => $section,
        'type' => 'checkbox',
        'priority' => $priority++,
    );
    if ($is_individual) {
        $control_args['active_callback'] = 'backbone_is_individual_archive_settings_enabled';
    } else {
        $control_args['active_callback'] = 'backbone_is_unified_archive_settings_enabled';
    }
    $wp_customize->add_control($prefix . 'show_excerpt', $control_args);
}

/**
 * 個別アーカイブ設定コントロールを追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 * @param string $type アーカイブタイプ
 * @param string $label 見出しラベル
 * @param string $description 見出しの説明
 * @param int $priority_start 開始priority
 * @param bool $show_orderby 並び順設定を表示するか
 */
function backbone_add_individual_archive_controls($wp_customize, $type, $label, $description, $priority_start, $show_orderby = true) {
    // 見出し
    $wp_customize->add_setting('archive_' . $type . '_heading', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control(new Backbone_Customize_Heading_Control($wp_customize, 'archive_' . $type . '_heading', array(
        'label' => $label,
        'section' => 'archive_settings',
        'priority' => $priority_start,
        'description' => $description,
        'active_callback' => 'backbone_is_individual_archive_settings_enabled',
    )));

    // 設定コントロールを追加
    backbone_add_archive_setting_controls($wp_customize, $type, 'archive_settings', $priority_start + 1, $show_orderby);
}

/**
 * カスタム投稿タイプの個別設定を追加
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザーオブジェクト
 */
function backbone_add_cpt_archive_controls($wp_customize) {
    // 公開されているカスタム投稿タイプを取得（ビルトインを除外）
    $post_types = get_post_types(array(
        'public' => true,
        '_builtin' => false
    ), 'objects');

    $priority = 600;
    foreach ($post_types as $post_type) {
        if ($post_type->has_archive) {
            backbone_add_individual_archive_controls(
                $wp_customize,
                'cpt_' . $post_type->name,
                sprintf(__('%s アーカイブ個別設定', 'backbone-seo-llmo'), $post_type->label),
                sprintf(__('%s のアーカイブページの表示設定。「すべて共通設定を使用」がオフの時に有効。', 'backbone-seo-llmo'), $post_type->label),
                $priority,
                true
            );
            $priority += 100;
        }
    }
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

/**
 * アイキャッチ画像サイズのサニタイズ関数
 *
 * @param string $value 入力値
 * @return string サニタイズ済みの値
 */
function backbone_sanitize_thumbnail_size($value) {
    $valid_sizes = array('full', 'large', 'medium_large', 'medium', 'thumbnail');

    if (in_array($value, $valid_sizes, true)) {
        return $value;
    }

    return 'full';
}

/**
 * 個別アーカイブ設定が有効かどうかを判定
 *
 * @return bool 個別設定が有効な場合true
 */
function backbone_is_individual_archive_settings_enabled() {
    return !get_theme_mod('archive_use_unified_settings', true);
}

/**
 * 統一アーカイブ設定が有効かどうかを判定
 *
 * @return bool 統一設定が有効な場合true
 */
function backbone_is_unified_archive_settings_enabled() {
    return get_theme_mod('archive_use_unified_settings', true);
}
