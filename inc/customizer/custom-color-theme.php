<?php
/**
 * 独自カラーテーマ設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタムリセットボタンコントロール
 */
if (class_exists('WP_Customize_Control')) {
    class Custom_Color_Reset_Control extends WP_Customize_Control {
        public $type = 'custom_reset_button';

        public function render_content() {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <?php if ($this->description) : ?>
                    <span class="description customize-control-description"><?php echo $this->description; ?></span>
                <?php endif; ?>
                <button type="button" class="button button-secondary custom-color-reset-button">
                    ベーステーマの色にリセット
                </button>
            </label>
            <?php
        }
    }
}

/**
 * 独自カラーテーマ設定を追加
 */
function backbone_add_custom_color_theme_settings($wp_customize) {
    // セクション：独自カラーテーマ設定
    // 常にセクションを追加し、active_callbackで表示制御を行う
    $wp_customize->add_section('backbone_custom_color_theme', array(
        'title'    => __('独自カラーテーマの設定', 'kashiwazaki-searchcraft'),
        'priority' => 35,
        'active_callback' => 'backbone_is_custom_color_theme_active'
    ));

    // ベーステーマ選択
    $wp_customize->add_setting('custom_color_base_theme', array(
        'default'           => 'none',
        'sanitize_callback' => 'backbone_sanitize_select',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('custom_color_base_theme', array(
        'label'    => __('ベースカラーテーマ', 'kashiwazaki-searchcraft'),
        'section'  => 'backbone_custom_color_theme',
        'type'     => 'select',
        'choices'  => backbone_get_base_theme_choices(),
        'description' => __('カスタマイズのベースとなるカラーテーマを選択してください。', 'kashiwazaki-searchcraft'),
    ));

    // カラー設定項目の定義
    $color_settings = array(
        'primary_color' => array(
            'label' => __('プライマリカラー', 'kashiwazaki-searchcraft'),
            'description' => __('ヘッダー、フッター、主要な要素の背景色', 'kashiwazaki-searchcraft'),
        ),
        'secondary_color' => array(
            'label' => __('セカンダリカラー', 'kashiwazaki-searchcraft'),
            'description' => __('サブ要素やアクセントに使用', 'kashiwazaki-searchcraft'),
        ),
        'accent_color' => array(
            'label' => __('アクセントカラー', 'kashiwazaki-searchcraft'),
            'description' => __('ボタンやリンクなど強調したい部分', 'kashiwazaki-searchcraft'),
        ),
        'background_color' => array(
            'label' => __('背景色', 'kashiwazaki-searchcraft'),
            'description' => __('メインコンテンツエリアの背景色', 'kashiwazaki-searchcraft'),
        ),
        'background_secondary' => array(
            'label' => __('セカンダリ背景色', 'kashiwazaki-searchcraft'),
            'description' => __('サイドバーや補助的なエリアの背景色', 'kashiwazaki-searchcraft'),
        ),
        'text_primary' => array(
            'label' => __('メインテキスト色', 'kashiwazaki-searchcraft'),
            'description' => __('本文の文字色', 'kashiwazaki-searchcraft'),
        ),
        'text_secondary' => array(
            'label' => __('セカンダリテキスト色', 'kashiwazaki-searchcraft'),
            'description' => __('見出しや重要な文字の色', 'kashiwazaki-searchcraft'),
        ),
        'text_light' => array(
            'label' => __('薄いテキスト色', 'kashiwazaki-searchcraft'),
            'description' => __('補助的な情報の文字色', 'kashiwazaki-searchcraft'),
        ),
        'link_color' => array(
            'label' => __('リンク色', 'kashiwazaki-searchcraft'),
            'description' => __('通常のリンクの色', 'kashiwazaki-searchcraft'),
        ),
        'link_hover_color' => array(
            'label' => __('リンクホバー色', 'kashiwazaki-searchcraft'),
            'description' => __('リンクにマウスを重ねた時の色', 'kashiwazaki-searchcraft'),
        ),
        'header_link_color' => array(
            'label' => __('ヘッダーリンク色', 'kashiwazaki-searchcraft'),
            'description' => __('ヘッダー内のリンクの色', 'kashiwazaki-searchcraft'),
        ),
        'header_link_hover_color' => array(
            'label' => __('ヘッダーリンクホバー色', 'kashiwazaki-searchcraft'),
            'description' => __('ヘッダー内のリンクにマウスを重ねた時の色', 'kashiwazaki-searchcraft'),
        ),
        'footer_link_color' => array(
            'label' => __('フッターリンク色', 'kashiwazaki-searchcraft'),
            'description' => __('フッター内のリンクの色', 'kashiwazaki-searchcraft'),
        ),
        'footer_link_hover_color' => array(
            'label' => __('フッターリンクホバー色', 'kashiwazaki-searchcraft'),
            'description' => __('フッター内のリンクにマウスを重ねた時の色', 'kashiwazaki-searchcraft'),
        ),
        'border_color' => array(
            'label' => __('ボーダー色', 'kashiwazaki-searchcraft'),
            'description' => __('区切り線や枠線の色', 'kashiwazaki-searchcraft'),
        ),
        'button_background_color' => array(
            'label' => __('ボタン背景色', 'kashiwazaki-searchcraft'),
            'description' => __('ボタンの背景色', 'kashiwazaki-searchcraft'),
        ),
        'button_text_color' => array(
            'label' => __('ボタンテキスト色', 'kashiwazaki-searchcraft'),
            'description' => __('ボタン内の文字色', 'kashiwazaki-searchcraft'),
        ),
        'button_hover_background_color' => array(
            'label' => __('ボタンホバー背景色', 'kashiwazaki-searchcraft'),
            'description' => __('ボタンにマウスを重ねた時の背景色', 'kashiwazaki-searchcraft'),
        ),
        'form_background_color' => array(
            'label' => __('フォーム背景色', 'kashiwazaki-searchcraft'),
            'description' => __('入力フォームの背景色', 'kashiwazaki-searchcraft'),
        ),
        'form_focus_color' => array(
            'label' => __('フォームフォーカス色', 'kashiwazaki-searchcraft'),
            'description' => __('入力フォームがフォーカスされた時の枠線色', 'kashiwazaki-searchcraft'),
        ),
        'search_button_color' => array(
            'label' => __('検索ボタン色', 'kashiwazaki-searchcraft'),
            'description' => __('検索ボタンの色', 'kashiwazaki-searchcraft'),
        ),
    );

    // ベーステーマが選択されている場合はその色を初期値にする
    $base_theme = get_theme_mod('custom_color_base_theme', 'none');
    $base_colors = array();
    
    if ($base_theme !== 'none') {
        $themes = backbone_get_color_themes();
        if (isset($themes[$base_theme]) && isset($themes[$base_theme]['colors'])) {
            $base_colors = $themes[$base_theme]['colors'];
        }
    }
    
    // 各カラー設定を追加
    foreach ($color_settings as $key => $setting) {
        $setting_id = 'custom_color_' . $key;
        $default_color = isset($base_colors[$key]) ? $base_colors[$key] : '';
        
        $wp_customize->add_setting($setting_id, array(
            'default'           => $default_color,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $setting_id, array(
            'label'       => $setting['label'],
            'section'     => 'backbone_custom_color_theme',
            'description' => $setting['description'],
        )));
    }

    // リセットボタン（カスタムコントロール）
    $wp_customize->add_setting('custom_color_reset', array(
        'sanitize_callback' => 'absint',
    ));

    if (class_exists('Custom_Color_Reset_Control')) {
        $wp_customize->add_control(new Custom_Color_Reset_Control($wp_customize, 'custom_color_reset', array(
            'label'       => __('色をリセット', 'kashiwazaki-searchcraft'),
            'section'     => 'backbone_custom_color_theme',
            'description' => __('選択したベーステーマの色にリセットします。', 'kashiwazaki-searchcraft'),
        )));
    } else {
        // フォールバック: 標準のボタン（テキストなし）
        $wp_customize->add_control('custom_color_reset', array(
            'label'       => __('色をリセット', 'kashiwazaki-searchcraft'),
            'section'     => 'backbone_custom_color_theme',
            'type'        => 'button',
            'description' => __('選択したベーステーマの色にリセットします。このボタンをクリックしてください。', 'kashiwazaki-searchcraft'),
            'input_attrs' => array(
                'class' => 'button button-secondary custom-color-reset-button',
            ),
        ));
    }
}

/**
 * カスタムカラーテーマがアクティブかチェック
 */
function backbone_is_custom_color_theme_active() {
    return get_theme_mod('color_theme', 'none') === 'none';
}

/**
 * ベーステーマの選択肢を取得
 */
function backbone_get_base_theme_choices() {
    $choices = array(
        'none' => __('未選択', 'kashiwazaki-searchcraft')
    );
    $color_themes = backbone_get_color_themes();
    
    if ($color_themes) {
        foreach ($color_themes as $theme_id => $theme) {
            $choices[$theme_id] = $theme['name'];
        }
    }
    
    return $choices;
}

/**
 * カスタマイザー用のJavaScriptを追加
 */
function backbone_customize_preview_custom_colors() {
    // 管理画面キャッシュバスティング設定を取得
    $cache_busting_admin = get_theme_mod('enable_cache_busting_admin', false);
    $version_admin = $cache_busting_admin ? current_time('YmdHis') : wp_get_theme()->get('Version');

    wp_enqueue_script(
        'seo-optimus-customize-preview-custom-colors',
        get_template_directory_uri() . '/js/customize-preview-custom-colors.js',
        array('customize-preview', 'jquery'),
        $version_admin,
        true
    );

    // カラーテーマデータをJavaScriptに渡す
    $color_themes = backbone_get_color_themes();
    wp_localize_script('seo-optimus-customize-preview-custom-colors', 'seoOptimusColorThemes', $color_themes);
}
add_action('customize_preview_init', 'backbone_customize_preview_custom_colors');

/**
 * カスタマイザーコントロール用のJavaScriptを追加
 */
function backbone_customize_controls_custom_colors() {
    // 管理画面キャッシュバスティング設定を取得
    $cache_busting_admin = get_theme_mod('enable_cache_busting_admin', false);
    $version_admin = $cache_busting_admin ? current_time('YmdHis') : wp_get_theme()->get('Version');

    wp_enqueue_script(
        'seo-optimus-customize-controls-custom-colors',
        get_template_directory_uri() . '/js/customize-controls-custom-colors.js',
        array('customize-controls', 'jquery'),
        $version_admin,
        true
    );

    // カラーテーマデータをJavaScriptに渡す
    $color_themes = backbone_get_color_themes();
    wp_localize_script('seo-optimus-customize-controls-custom-colors', 'seoOptimusColorThemes', $color_themes);
}
add_action('customize_controls_enqueue_scripts', 'backbone_customize_controls_custom_colors');

/**
 * カスタムカラーをCSSに適用
 */
function backbone_apply_custom_colors() {
    if (get_theme_mod('color_theme', 'none') !== 'none') {
        return;
    }

    // カスタムカラーが設定されているかチェック
    $has_custom_colors = false;
    $custom_css = ':root {' . "\n";
    
    $color_keys = array(
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

    foreach ($color_keys as $key => $css_var) {
        $value = get_theme_mod('custom_color_' . $key);
        if ($value) {
            $custom_css .= '    ' . $css_var . ': ' . $value . ';' . "\n";
            $has_custom_colors = true;
        }
    }

    $custom_css .= '}' . "\n";

    // カスタムカラーが設定されている場合のみ出力
    if ($has_custom_colors) {
        echo '<style type="text/css" id="custom-color-theme-css">' . "\n";
        echo $custom_css;
        echo '</style>' . "\n";
    }
}
add_action('wp_head', 'backbone_apply_custom_colors', 100); // 優先度を100に変更して最後に実行