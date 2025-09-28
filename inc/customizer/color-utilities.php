<?php
/**
 * カラーユーティリティ関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 色選択肢の配列を生成
 */
function backbone_get_color_theme_choices() {
    $themes = backbone_get_color_themes();
    $choices = array('none' => __('設定なし', 'kashiwazaki-searchcraft'));

    foreach ($themes as $key => $theme) {
        $choices[$key] = $theme['name'];
    }

    return $choices;
}

/**
 * 色テーマデータを取得
 */
function backbone_get_color_themes() {
    static $themes = null;

    if ($themes === null) {
        $themes = array();

        // テーマファイルの読み込み
        $theme_files = glob(get_template_directory() . '/inc/color-themes/*.json');

        foreach ($theme_files as $file) {
            $filename = basename($file, '.json');
            $theme_data = json_decode(file_get_contents($file), true);

            if ($theme_data && isset($theme_data['name'])) {
                $themes[$filename] = $theme_data;
            }
        }

    }

    return $themes;
}

// backbone_sanitize_select() は utilities.php で定義済み
