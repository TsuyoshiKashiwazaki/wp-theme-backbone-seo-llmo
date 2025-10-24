<?php
/**
 * サブディレクトリ別デザイン設定
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * サブディレクトリデザイン設定用のサニタイズ関数
 */
function backbone_sanitize_subdirectory_select($input) {
    // 入力値をサニタイズ
    $input = sanitize_key($input);

    // 許可される値のリスト
    $allowed_values = array('none');

    // 各種選択肢を取得して許可リストに追加
    if (function_exists('backbone_get_color_theme_choices')) {
        $allowed_values = array_merge($allowed_values, array_keys(backbone_get_color_theme_choices()));
    }
    if (function_exists('backbone_get_design_pattern_choices')) {
        $allowed_values = array_merge($allowed_values, array_keys(backbone_get_design_pattern_choices()));
    }
    if (function_exists('backbone_get_typography_pattern_choices')) {
        $allowed_values = array_merge($allowed_values, array_keys(backbone_get_typography_pattern_choices()));
    }
    if (function_exists('backbone_get_decoration_pattern_choices')) {
        $allowed_values = array_merge($allowed_values, array_keys(backbone_get_decoration_pattern_choices()));
    }

    // 値の重複を削除
    $allowed_values = array_unique($allowed_values);

    // 入力値が許可されているか確認
    return in_array($input, $allowed_values, true) ? $input : 'none';
}

/**
 * サブディレクトリのデザイン設定を追加
 */
function backbone_add_subdirectory_design_settings($wp_customize) {
    // 保存されているサブディレクトリの数を取得（subdirectory-logos.phpと同じデフォルト値に統一）
    $subdirectory_count = get_theme_mod('subdirectory_count', 0);

    // デバッグ用: 実際にサブディレクトリが設定されているか確認
    $has_valid_subdirectory = false;
    for ($i = 1; $i <= min($subdirectory_count, 10); $i++) {
        $path = get_theme_mod("subdirectory_path_{$i}");
        if (!empty($path)) {
            $has_valid_subdirectory = true;
            break;
        }
    }

    // 有効なサブディレクトリが1つもない場合は何もしない
    if (!$has_valid_subdirectory) {
        return;
    }

    // 各サブディレクトリに対してデザイン設定セクションを作成
    for ($i = 1; $i <= min($subdirectory_count, 10); $i++) {
        $subdirectory_path = get_theme_mod("subdirectory_path_{$i}");

        // パスが設定されている場合のみセクションを作成
        if (!empty($subdirectory_path)) {
            $section_id = "backbone_subdirectory_design_{$i}";
            $section_title = sprintf(__('サブディレクトリ %d のデザイン設定', 'backbone-seo-llmo'), $i);

            if (!empty($subdirectory_path)) {
                $section_title .= ' (' . $subdirectory_path . ')';
            }

            // セクション：サブディレクトリのデザイン設定
            $wp_customize->add_section($section_id, array(
                'title'    => $section_title,
                'priority' => 105 + $i,
                'description' => sprintf(__('サブディレクトリ「%s」専用のデザイン設定です。', 'backbone-seo-llmo'), $subdirectory_path),
            ));

            // カラーテーマの選択
            $wp_customize->add_setting("subdirectory_{$i}_color_theme", array(
                'default'           => 'none',
                'sanitize_callback' => 'backbone_sanitize_subdirectory_select',
                'transport'         => 'postMessage',
            ));

            $wp_customize->add_control("subdirectory_{$i}_color_theme", array(
                'label'    => __('カラーテーマ', 'backbone-seo-llmo'),
                'section'  => $section_id,
                'type'     => 'select',
                'choices'  => backbone_get_color_theme_choices(),
                'description' => __('このサブディレクトリ専用の色彩設計を選択できます。', 'backbone-seo-llmo'),
                'priority' => 10,
            ));

            // デザインパターンの選択
            $wp_customize->add_setting("subdirectory_{$i}_design_pattern", array(
                'default'           => 'none',
                'sanitize_callback' => 'backbone_sanitize_subdirectory_select',
                'transport'         => 'postMessage',
            ));

            $wp_customize->add_control("subdirectory_{$i}_design_pattern", array(
                'label'    => __('デザインパターン', 'backbone-seo-llmo'),
                'section'  => $section_id,
                'type'     => 'select',
                'choices'  => backbone_get_design_pattern_choices(),
                'description' => __('このサブディレクトリ専用のレイアウトデザインを選択できます。', 'backbone-seo-llmo'),
                'priority' => 20,
            ));

            // タイポグラフィパターンの選択
            $wp_customize->add_setting("subdirectory_{$i}_text_pattern", array(
                'default'           => 'none',
                'sanitize_callback' => 'backbone_sanitize_subdirectory_select',
                'transport'         => 'postMessage',
            ));

            $wp_customize->add_control("subdirectory_{$i}_text_pattern", array(
                'label'    => __('タイポグラフィパターン', 'backbone-seo-llmo'),
                'section'  => $section_id,
                'type'     => 'select',
                'choices'  => backbone_get_typography_pattern_choices(),
                'description' => __('このサブディレクトリ専用の文字スタイルを選択できます。', 'backbone-seo-llmo'),
                'priority' => 30,
            ));

            // デコレーションパターンの選択
            $wp_customize->add_setting("subdirectory_{$i}_decoration_pattern", array(
                'default'           => 'none',
                'sanitize_callback' => 'backbone_sanitize_subdirectory_select',
                'transport'         => 'postMessage',
            ));

            $wp_customize->add_control("subdirectory_{$i}_decoration_pattern", array(
                'label'    => __('デコレーションパターン', 'backbone-seo-llmo'),
                'section'  => $section_id,
                'type'     => 'select',
                'choices'  => backbone_get_decoration_pattern_choices(),
                'description' => __('このサブディレクトリ専用の見出しやリストの装飾スタイルを選択できます。', 'backbone-seo-llmo'),
                'priority' => 40,
            ));
        }
    }
}

/**
 * 現在のサブディレクトリ用のデザイン設定を取得
 */
function backbone_get_current_subdirectory_design_settings() {
    // REQUEST_URIから現在のパスを取得
    $current_url = $_SERVER['REQUEST_URI'];
    $current_path = $current_url;

    // wp-json, wp-admin, wp-content などのWordPressディレクトリを検出して、その前の部分を除去
    if (preg_match('#^(.*?)(/wp-json/|/wp-admin/|/wp-content/|/wp-includes/)#', $current_url, $matches)) {
        $wp_base = $matches[1]; // /campany など
        if (!empty($wp_base)) {
            // WordPressのベースディレクトリを除去
            $current_path = substr($current_url, strlen($wp_base));
        }
    } else {
        // 通常のページの場合、最初のディレクトリがWordPressのインストールディレクトリの可能性を考慮
        // /campany/seo-note/... のような構造の場合
        if (preg_match('#^/[^/]+(/.*)?$#', $current_url, $matches)) {
            // 最初のディレクトリを一時的に除去してテスト
            $test_path = isset($matches[1]) ? $matches[1] : '/';

            // 保存されているサブディレクトリと照合してみる
            $subdirectory_count = get_theme_mod('subdirectory_count', 1);
            for ($i = 1; $i <= min($subdirectory_count, 10); $i++) {
                $subdirectory_path = get_theme_mod("subdirectory_path_{$i}");
                if (!empty($subdirectory_path)) {
                    $normalized = '/' . trim($subdirectory_path, '/');
                    // テストパスがサブディレクトリ設定にマッチするか確認
                    if (strpos($test_path, $normalized) === 0) {
                        // マッチした場合、このパスを使用
                        $current_path = $test_path;
                        break;
                    }
                }
            }
        }
    }

    // クエリパラメータを除去
    $current_path = parse_url($current_path, PHP_URL_PATH);
    if (empty($current_path) || $current_path === null || $current_path === false) {
        $current_path = '/';
    }

    // 保存されているサブディレクトリの数を取得
    $subdirectory_count = get_theme_mod('subdirectory_count', 0);

    // マッチするサブディレクトリを探す
    for ($i = 1; $i <= min($subdirectory_count, 10); $i++) {
        $subdirectory_path = get_theme_mod("subdirectory_path_{$i}");

        if (!empty($subdirectory_path)) {
            // スラッシュの正規化
            $subdirectory_path = '/' . trim($subdirectory_path, '/');

            // パスが一致するかチェック（前方一致）
            // /seo-note は /seo-note, /seo-note/, /seo-note/article01/ などにマッチ
            if ($current_path !== null && strpos($current_path, $subdirectory_path) === 0) {
                // このサブディレクトリの設定を返す
                return array(
                    'index' => $i,
                    'path' => $subdirectory_path,
                    'color_theme' => get_theme_mod("subdirectory_{$i}_color_theme", 'none'),
                    'design_pattern' => get_theme_mod("subdirectory_{$i}_design_pattern", 'none'),
                    'text_pattern' => get_theme_mod("subdirectory_{$i}_text_pattern", 'none'),
                    'decoration_pattern' => get_theme_mod("subdirectory_{$i}_decoration_pattern", 'none'),
                );
            }
        }
    }

    return false;
}

/**
 * サブディレクトリ用のデザインCSSを生成
 */
function backbone_generate_subdirectory_design_css() {
    $settings = backbone_get_current_subdirectory_design_settings();

    if (!$settings) {
        return;
    }

    $css = '';

    // デバッグ情報をコメントで出力
    $css .= sprintf("/* Subdirectory Settings Found: path=%s, index=%d */\n", $settings['path'], $settings['index']);

    // デバッグ：現在のURLを出力
    $css .= sprintf("/* Current URL: %s */\n", $_SERVER['REQUEST_URI']);

    // カラーテーマのCSS
    if (!empty($settings['color_theme']) && $settings['color_theme'] !== 'none') {
        // テーマデータを取得してCSSを生成
        $theme_data = backbone_get_theme_data_by_id($settings['color_theme']);
        if ($theme_data) {
            $css .= "/* Color Theme: {$settings['color_theme']} */\n";
            $css .= backbone_generate_css_from_theme_data($theme_data);
        }
    }

    // デザインパターンのCSS
    if (!empty($settings['design_pattern']) && $settings['design_pattern'] !== 'none') {
        $css .= "/* Design Pattern: {$settings['design_pattern']} */\n";
        $css .= backbone_generate_design_css($settings['design_pattern']);
    }

    // タイポグラフィパターンのCSS
    if (!empty($settings['text_pattern']) && $settings['text_pattern'] !== 'none') {
        $css .= "/* Typography Pattern: {$settings['text_pattern']} */\n";
        $css .= backbone_generate_typography_css($settings['text_pattern']);
    }

    // デコレーションパターンのCSS
    if (!empty($settings['decoration_pattern']) && $settings['decoration_pattern'] !== 'none') {
        $css .= "/* Decoration Pattern: {$settings['decoration_pattern']} */\n";
        $css .= backbone_generate_decoration_css($settings['decoration_pattern']);
    }

    if (!empty($css)) {
        echo sprintf(
            '<style id="subdirectory-design-css">%s</style>',
            $css
        );
    }
}

// フックに追加（メインのデザインCSSより後に出力して上書き）
add_action('wp_head', 'backbone_generate_subdirectory_design_css', 1000);

/**
 * カスタマイザープレビュー用スクリプトの登録
 */
function backbone_subdirectory_customizer_preview_scripts() {
    wp_enqueue_script(
        'subdirectory-customizer',
        get_template_directory_uri() . '/js/subdirectory-customizer.js',
        array('customize-controls'),
        '1.0.0',
        true
    );

    // サブディレクトリ設定保存後の自動リロード機能
    wp_enqueue_script(
        'subdirectory-auto-reload',
        get_template_directory_uri() . '/js/subdirectory-auto-reload.js',
        array('customize-controls', 'jquery'),
        '1.0.0',
        true
    );

    // 動的セクション管理用スクリプトはコメントアウト（公開されていない設定でセクションが表示される問題を回避）
    // wp_enqueue_script(
    //     'subdirectory-dynamic-sections',
    //     get_template_directory_uri() . '/js/subdirectory-dynamic-sections.js',
    //     array('customize-controls', 'jquery'),
    //     '1.0.0',
    //     true
    // );

    // // 選択肢のデータをJavaScriptに渡す
    // wp_localize_script('subdirectory-dynamic-sections', 'backboneCustomizerData', array(
    //     'colorThemeChoices' => backbone_get_color_theme_choices(),
    //     'designPatternChoices' => backbone_get_design_pattern_choices(),
    //     'typographyPatternChoices' => backbone_get_typography_pattern_choices(),
    //     'decorationPatternChoices' => backbone_get_decoration_pattern_choices(),
    // ));
}
add_action('customize_controls_enqueue_scripts', 'backbone_subdirectory_customizer_preview_scripts');

/**
 * カスタマイザープレビューフレーム用スクリプトの登録
 */
function backbone_subdirectory_customizer_preview_frame_scripts() {
    wp_enqueue_script(
        'subdirectory-customizer-preview',
        get_template_directory_uri() . '/js/subdirectory-customizer-preview.js',
        array('customize-preview'),
        '1.0.0',
        true
    );
}
add_action('customize_preview_init', 'backbone_subdirectory_customizer_preview_frame_scripts');

/**
 * サブディレクトリ設定が有効な場合、メインのデザイン設定を無効化
 */
function backbone_override_main_design_settings() {
    $settings = backbone_get_current_subdirectory_design_settings();

    if ($settings) {
        // メインのデザイン設定を上書き
        add_filter('theme_mod_color_theme', function($value) use ($settings) {
            return $settings['color_theme'];
        });

        add_filter('theme_mod_design_pattern', function($value) use ($settings) {
            return $settings['design_pattern'];
        });

        add_filter('theme_mod_text_pattern', function($value) use ($settings) {
            return $settings['text_pattern'];
        });

        add_filter('theme_mod_decoration_pattern', function($value) use ($settings) {
            return $settings['decoration_pattern'];
        });
    }
}
add_action('init', 'backbone_override_main_design_settings');