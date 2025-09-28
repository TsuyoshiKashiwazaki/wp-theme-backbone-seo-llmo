<?php
/**
 * カスタマイザー用ユーティリティ関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * レイアウト設定の定数定義（ハードコード排除）
 */
if (!function_exists('backbone_get_layout_choices')) {
    function backbone_get_layout_choices() {
        return array(
            'single-column'  => __('1カラム', 'kashiwazaki-searchcraft'),
            'two-columns'    => __('2カラム', 'kashiwazaki-searchcraft'),
            'three-columns'  => __('3カラム', 'kashiwazaki-searchcraft'),
            'full-width'     => __('全幅レイアウト（フルワイド）', 'kashiwazaki-searchcraft'),
        );
    }
}

if (!function_exists('backbone_get_post_type_layout_choices')) {
    function backbone_get_post_type_layout_choices() {
        return array(
            'inherit'        => __('フロントページ設定に従う', 'kashiwazaki-searchcraft'),
            'single-column'  => __('1カラム', 'kashiwazaki-searchcraft'),
            'two-columns'    => __('2カラム', 'kashiwazaki-searchcraft'),
            'three-columns'  => __('3カラム', 'kashiwazaki-searchcraft'),
            'full-width'     => __('全幅レイアウト（フルワイド）', 'kashiwazaki-searchcraft'),
        );
    }
}

if (!function_exists('backbone_get_default_layout')) {
    function backbone_get_default_layout() {
        return 'two-columns';
    }
}



/**
 * デザインパターンの選択肢を取得
 */
function backbone_get_design_pattern_choices() {
    $choices = array('none' => __('設定なし', 'kashiwazaki-searchcraft'));

    $design_patterns = backbone_get_design_patterns();
    if ($design_patterns) {
        foreach ($design_patterns as $pattern_id => $pattern) {
            $choices[$pattern_id] = $pattern['name'];
        }
    }

    return $choices;
}

/**
 * タイポグラフィパターンの選択肢を取得
 */
function backbone_get_typography_pattern_choices() {
    $choices = array('none' => __('設定なし', 'kashiwazaki-searchcraft'));

    $typography_patterns = backbone_get_typography_patterns();
    if ($typography_patterns) {
        foreach ($typography_patterns as $pattern_id => $pattern) {
            $choices[$pattern_id] = $pattern['name'];
        }
    }

    return $choices;
}

/**
 * デコレーションパターンの選択肢を取得
 */
function backbone_get_decoration_pattern_choices() {
    $choices = array('none' => __('設定なし', 'kashiwazaki-searchcraft'));

    $decoration_patterns = backbone_get_decoration_patterns();
    if ($decoration_patterns) {
        foreach ($decoration_patterns as $pattern_id => $pattern) {
            $choices[$pattern_id] = $pattern['name'];
        }
    }

    return $choices;
}

/**
 * セレクトボックスのサニタイズ
 */
function backbone_sanitize_select($input, $setting) {
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}

/**
 * 数値のサニタイズ
 */
function backbone_sanitize_number($input) {
    if (is_numeric($input)) {
        $number = intval($input);
        return max(0, $number);
    }
    return 300; // デフォルト値を返す
}

/**
 * 数値範囲のサニタイズ
 */
function backbone_sanitize_number_range($input, $setting) {
    $input = absint($input);
    $min = $setting->manager->get_control($setting->id)->input_attrs['min'];
    $max = $setting->manager->get_control($setting->id)->input_attrs['max'];
    return ($input >= $min && $input <= $max) ? $input : $setting->default;
}
