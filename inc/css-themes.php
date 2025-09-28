<?php
/**
 * カラーテーマ関連の機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カラーテーマCSSの動的出力
 */
function backbone_dynamic_color_theme_output() {
    // サブディレクトリ設定がある場合はスキップ
    $subdirectory_settings = backbone_get_current_subdirectory_design_settings();
    if ($subdirectory_settings) {
        return;
    }

    // 選択されているカラーテーマのCSS生成
    $current_theme = get_theme_mod('color_theme', 'none');

    if ($current_theme === 'none') {
        // テーマが選択されていない場合は、独自カラーテーマの処理に任せる
        // カスタムカラーが設定されていない場合のみデフォルトを出力
        $has_custom_colors = false;
        $color_keys = array(
            'primary_color', 'secondary_color', 'accent_color',
            'background_color', 'background_secondary',
            'text_primary', 'text_secondary', 'text_light',
            'link_color', 'link_hover_color',
            'header_link_color', 'header_link_hover_color',
            'footer_link_color', 'footer_link_hover_color',
            'border_color',
            'button_background_color', 'button_text_color', 'button_hover_background_color',
            'form_background_color', 'form_focus_color', 'search_button_color'
        );
        
        foreach ($color_keys as $key) {
            if (get_theme_mod('custom_color_' . $key)) {
                $has_custom_colors = true;
                break;
            }
        }
        
        if (!$has_custom_colors) {
            backbone_output_default_css_variables();
        }
        return;
    }

    // JSONファイルからテーマデータを読み込み
    $theme_data = backbone_get_theme_data_by_id($current_theme);
    
    if ($theme_data && isset($theme_data['colors'])) {
        $css = backbone_generate_css_from_theme_data($theme_data);
        echo '<style type="text/css" id="dynamic-color-theme-css">' . "\n";
        echo $css;
        echo '</style>' . "\n";
    } else {
        // テーマデータが見つからない場合はデフォルトを出力
        backbone_output_default_css_variables();
    }
}
add_action('wp_head', 'backbone_dynamic_color_theme_output');

/**
 * テーマIDからテーマデータを取得
 */
function backbone_get_theme_data_by_id($theme_id) {
    $theme_file = get_template_directory() . '/inc/color-themes/' . $theme_id . '.json';
    
    if (file_exists($theme_file)) {
        $json_content = file_get_contents($theme_file);
        return json_decode($json_content, true);
    }
    
    return null;
}

/**
 * テーマデータからCSSを生成
 */
function backbone_generate_css_from_theme_data($theme_data) {
    if (!isset($theme_data['colors'])) {
        return '';
    }
    
    $colors = $theme_data['colors'];
    $css = ':root {' . "\n";
    
    // カラー変数のマッピング（JSONキーとCSS変数名が同じになったためシンプル化）
    $color_mappings = array(
        'primary_color' => '--primary-color',
        'secondary_color' => '--secondary-color', 
        'accent_color' => '--accent-color',
        'background_color' => '--background-color',
        'background_secondary' => '--background-secondary',
        'text_primary' => '--text-primary',
        'text_secondary' => '--text-secondary',
        'text_light' => '--text-light',
        'link_color' => '--link-color',
        'link_hover_color' => '--link-hover-color',
        'header_link_color' => '--header-link-color',
        'header_link_hover_color' => '--header-link-hover-color',
        'footer_link_color' => '--footer-link-color',
        'footer_link_hover_color' => '--footer-link-hover-color',
        'border_color' => '--border-color',
        'button_background_color' => '--button-background-color',
        'button_text_color' => '--button-text-color',
        'button_hover_background_color' => '--button-hover-background-color',
        'form_background_color' => '--form-background-color',
        'form_focus_color' => '--form-focus-color',
        'search_button_color' => '--search-button-color'
    );
    
    // CSS変数を生成
    foreach ($colors as $key => $value) {
        if (isset($color_mappings[$key])) {
            $css .= '    ' . $color_mappings[$key] . ': ' . $value . ';' . "\n";
        }
    }
    
    $css .= '}' . "\n";
    
    return $css;
}
