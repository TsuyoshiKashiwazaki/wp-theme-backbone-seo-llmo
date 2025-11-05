<?php
/**
 * メインビジュアル（ヒーローイメージ）関連のヘルパー関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * メインビジュアルを表示するかどうかを判定
 *
 * @param int|null $post_id 投稿ID（nullの場合は現在の投稿）
 * @return bool
 */
function backbone_should_display_hero_image($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (!$post_id || !has_post_thumbnail($post_id)) {
        return false;
    }

    // 個別設定を確認
    $individual_setting = get_post_meta($post_id, '_hero_image_display', true);

    // 個別設定が「非表示」の場合
    if ($individual_setting === 'hide') {
        return false;
    }

    // 個別設定が「表示」の場合
    if ($individual_setting === 'show') {
        return true;
    }

    // 個別設定が「グローバル設定を使用」または未設定の場合、グローバル設定を確認
    // 設定モードを確認
    $setting_mode = get_theme_mod('hero_image_setting_mode', 'common');

    if ($setting_mode === 'common') {
        // 共通設定モード: すべての投稿タイプで同じ設定を使用
        $global_setting = get_theme_mod('hero_image_enable_common', true);
    } else {
        // 個別設定モード: 投稿タイプごとの設定を使用
        $post_type = get_post_type($post_id);
        $global_setting = get_theme_mod('hero_image_enable_' . $post_type, true);
    }

    return (bool) $global_setting;
}

/**
 * メインビジュアルの表示スタイルを取得
 *
 * @param int|null $post_id 投稿ID（nullの場合は現在の投稿）
 * @return string スタイル名（standard, fullwidth, hero, parallax）
 */
function backbone_get_hero_image_style($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // 個別設定を確認
    $individual_style = get_post_meta($post_id, '_hero_image_style', true);

    // 個別設定がある場合
    if ($individual_style && $individual_style !== 'global') {
        return $individual_style;
    }

    // 設定モードを確認
    $setting_mode = get_theme_mod('hero_image_setting_mode', 'common');

    if ($setting_mode === 'common') {
        // 共通設定モード: 共通設定を使用
        return get_theme_mod('hero_image_style_common', 'standard');
    } else {
        // 個別設定モード: 投稿タイプ別設定を確認
        $post_type = get_post_type($post_id);
        $type_style = get_theme_mod('hero_image_style_' . $post_type, null);

        if ($type_style !== null) {
            return $type_style;
        }

        // フォールバック: 共通設定を使用
        return get_theme_mod('hero_image_style_common', 'standard');
    }
}


/**
 * メインビジュアルの配置を取得
 *
 * @param int|null $post_id 投稿ID（nullの場合は現在の投稿）
 * @return string 配置（left, center, right）
 */
function backbone_get_hero_image_alignment($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // 設定モードを確認
    $setting_mode = get_theme_mod('hero_image_setting_mode', 'common');

    if ($setting_mode === 'common') {
        // 共通設定モード: 共通設定を使用
        return get_theme_mod('hero_alignment_common', 'center');
    } else {
        // 個別設定モード: 投稿タイプ別設定を確認
        $post_type = get_post_type($post_id);
        $type_alignment = get_theme_mod('hero_alignment_' . $post_type, null);

        if ($type_alignment !== null) {
            return $type_alignment;
        }

        // フォールバック: 共通設定を使用
        return get_theme_mod('hero_alignment_common', 'center');
    }
}

/**
 * メインビジュアルのCSSクラスを取得
 *
 * @param int|null $post_id 投稿ID（nullの場合は現在の投稿）
 * @return string CSSクラス
 */
function backbone_get_hero_image_classes($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $classes = array('hero-image');

    // スタイルクラスを追加
    $style = backbone_get_hero_image_style($post_id);
    $classes[] = 'hero-' . $style;

    // 配置クラスを追加
    $alignment = backbone_get_hero_image_alignment($post_id);
    $classes[] = 'hero-align-' . $alignment;

    // デコレーション設定を取得してクラスを追加
    $decoration = backbone_get_hero_decoration_settings($post_id);

    // 枠線
    if ($decoration['border'] !== 'none') {
        $classes[] = 'hero-border-' . $decoration['border'];
    }

    // 角丸
    if ($decoration['border_radius'] !== 'none') {
        $classes[] = 'hero-radius-' . $decoration['border_radius'];
    }

    // アニメーション
    if ($decoration['animation'] !== 'none') {
        $classes[] = 'hero-animation-' . $decoration['animation'];
    }

    return implode(' ', $classes);
}

/**
 * 利用可能なヒーロースタイルのリストを取得
 *
 * @return array スタイルの配列（キー => ラベル）
 */
function backbone_get_hero_style_options() {
    return array(
        'standard' => __('標準', 'backbone-seo-llmo'),
        'fullwidth' => __('フルワイド', 'backbone-seo-llmo'),
        'circle' => __('サークル（円形）', 'backbone-seo-llmo'),
        'card' => __('カード型', 'backbone-seo-llmo'),
    );
}

/**
 * ヒーロー画像に対応している投稿タイプのリストを取得
 *
 * @return array 投稿タイプの配列（キー => ラベル）
 */
function backbone_get_hero_supported_post_types() {
    $post_types = get_post_types(array('public' => true), 'objects');
    $supported = array();

    // attachment（メディア）を除外
    foreach ($post_types as $post_type) {
        if ($post_type->name !== 'attachment' && post_type_supports($post_type->name, 'thumbnail')) {
            $supported[$post_type->name] = $post_type->label;
        }
    }

    return $supported;
}

/**
 * メインビジュアルのデコレーション設定を取得
 * グローバル設定（カスタマイザー）から取得
 *
 * @param int|null $post_id 投稿ID（nullの場合は現在の投稿）
 * @return array デコレーション設定の配列
 */
function backbone_get_hero_decoration_settings($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // 設定モードを確認
    $setting_mode = get_theme_mod('hero_image_setting_mode', 'common');

    if ($setting_mode === 'common') {
        // 共通設定モード: 共通設定を使用
        $border = get_theme_mod('hero_border_common', 'none');
        $border_color = get_theme_mod('hero_border_color_common', '');
        $border_radius = get_theme_mod('hero_border_radius_common', 'none');
        $animation = get_theme_mod('hero_animation_common', 'none');
    } else {
        // 個別設定モード: 投稿タイプ別設定を確認、なければ共通設定を使用
        $post_type = get_post_type($post_id);

        $border = get_theme_mod('hero_border_' . $post_type, null);
        if ($border === null) {
            $border = get_theme_mod('hero_border_common', 'none');
        }

        $border_color = get_theme_mod('hero_border_color_' . $post_type, null);
        if ($border_color === null) {
            $border_color = get_theme_mod('hero_border_color_common', '');
        }

        $border_radius = get_theme_mod('hero_border_radius_' . $post_type, null);
        if ($border_radius === null) {
            $border_radius = get_theme_mod('hero_border_radius_common', 'none');
        }

        $animation = get_theme_mod('hero_animation_' . $post_type, null);
        if ($animation === null) {
            $animation = get_theme_mod('hero_animation_common', 'none');
        }
    }

    return array(
        'border' => $border,
        'border_color' => $border_color,
        'border_radius' => $border_radius,
        'animation' => $animation,
    );
}

/**
 * ボーダーオプション
 */
function backbone_get_hero_border_options() {
    return array(
        'none' => __('なし', 'backbone-seo-llmo'),
        'thin' => __('細線', 'backbone-seo-llmo'),
        'thick' => __('太線', 'backbone-seo-llmo'),
    );
}

/**
 * 角丸オプション
 */
function backbone_get_hero_border_radius_options() {
    return array(
        'none' => __('なし', 'backbone-seo-llmo'),
        'small' => __('小', 'backbone-seo-llmo'),
        'medium' => __('中', 'backbone-seo-llmo'),
        'large' => __('大', 'backbone-seo-llmo'),
        'circle' => __('円形', 'backbone-seo-llmo'),
    );
}

/**
 * アニメーションオプション
 */
function backbone_get_hero_animation_options() {
    return array(
        'none' => __('なし', 'backbone-seo-llmo'),
        'fade-in' => __('フェードイン', 'backbone-seo-llmo'),
        'slide-up' => __('スライドアップ', 'backbone-seo-llmo'),
        'zoom-in' => __('ズームイン', 'backbone-seo-llmo'),
    );
}

/**
 * 画像配置オプション
 */
function backbone_get_hero_alignment_options() {
    return array(
        'left' => __('左寄せ', 'backbone-seo-llmo'),
        'center' => __('中央', 'backbone-seo-llmo'),
        'right' => __('右寄せ', 'backbone-seo-llmo'),
    );
}

