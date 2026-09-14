<?php
/**
 * 追加タグ設定関連のカスタマイザー設定
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 追加タグの出力範囲と出力場所の定義
 *
 * 設定IDは custom_tags_{範囲}_{場所}。出力側（custom-tags-output.php）も同じIDで読み出す。
 *
 * @return array
 */
function backbone_get_custom_tags_definitions() {
    return array(
        'scopes' => array(
            'global' => array(
                'label'       => __('■ 全体共通タグ', 'backbone-seo-llmo'),
                'description' => __('サイトのすべてのページに出力されます。', 'backbone-seo-llmo'),
            ),
            'front' => array(
                'label'       => __('■ トップページのみのタグ', 'backbone-seo-llmo'),
                'description' => __('サイトのトップページ（WordPressのホームURL）だけに出力されます。サイト所有権の確認用メタタグなどに使います。', 'backbone-seo-llmo'),
            ),
        ),
        'positions' => array(
            'head' => array(
                'label'       => __('<head>内', 'backbone-seo-llmo'),
                'description' => __('メタタグや計測タグ（script）など、<head>内に置くタグを記述します。', 'backbone-seo-llmo'),
                'placeholder' => '<meta name="example-site-verification" content="xxxxxxxx">',
            ),
            'body_open' => array(
                'label'       => __('<body>の直後', 'backbone-seo-llmo'),
                'description' => __('Googleタグマネージャーの<noscript>など、<body>の直後に置くタグを記述します。', 'backbone-seo-llmo'),
                'placeholder' => '<noscript>...</noscript>',
            ),
            'footer' => array(
                'label'       => __('</body>の直前', 'backbone-seo-llmo'),
                'description' => __('ページの最後に読み込めばよいタグを記述します。', 'backbone-seo-llmo'),
                'placeholder' => '<script src="https://example.com/tag.js" async></script>',
            ),
        ),
    );
}

/**
 * 追加タグ設定セクションを追加
 */
function backbone_add_custom_tags_settings($wp_customize) {
    $definitions = backbone_get_custom_tags_definitions();

    // 追加タグ設定セクション（追加JS: 150 の直前に並べる）
    $wp_customize->add_section('backbone_custom_tags', array(
        'title'       => __('追加タグ', 'backbone-seo-llmo'),
        'description' => esc_html__('メタタグや計測タグなどのHTMLタグを、そのままページに出力します。入力した内容は加工されずに出力されるため、タグの閉じ忘れなどがあるとページの表示が崩れることがあります。出力を止めたいときは内容を空にしてください。', 'backbone-seo-llmo'),
        'priority'    => 145,
    ));

    foreach ($definitions['scopes'] as $scope => $scope_def) {
        // 範囲ごとの見出し
        $wp_customize->add_setting("custom_tags_{$scope}_header", array(
            'capability'        => 'unfiltered_html',
            'sanitize_callback' => 'wp_kses_post',
        ));

        // コントロールの description はエスケープされずに出力されるため、ここで esc_html する
        $wp_customize->add_control("custom_tags_{$scope}_header", array(
            'label'       => $scope_def['label'],
            'section'     => 'backbone_custom_tags',
            'type'        => 'hidden',
            'description' => esc_html($scope_def['description']),
        ));

        foreach ($definitions['positions'] as $position => $position_def) {
            $setting_id = "custom_tags_{$scope}_{$position}";

            // 生のHTMLを保存するため、unfiltered_html 権限を持つユーザーだけが編集できる
            // 出力はカスタマイザーのプレビューで行わないため、プレビューの再読み込みは不要
            $wp_customize->add_setting($setting_id, array(
                'default'           => '',
                'capability'        => 'unfiltered_html',
                'sanitize_callback' => 'backbone_sanitize_custom_tags',
                'transport'         => 'postMessage',
            ));

            $wp_customize->add_control($setting_id, array(
                'label'       => $position_def['label'],
                'section'     => 'backbone_custom_tags',
                'type'        => 'textarea',
                // 説明文中の <head> や <noscript> がタグとして解釈されないようにする
                'description' => esc_html($position_def['description']),
                'input_attrs' => array(
                    'rows'        => 6,
                    'placeholder' => $position_def['placeholder'],
                    'style'       => 'font-family: monospace; font-size: 12px;',
                ),
            ));
        }
    }
}

/**
 * 追加タグのサニタイゼーション
 *
 * タグをそのまま出力する機能のため、HTMLは加工しない。
 */
function backbone_sanitize_custom_tags($input) {
    // 管理者またはunfiltered_html権限を持つユーザーのみ許可
    if (!current_user_can('unfiltered_html')) {
        return '';
    }

    if (!is_string($input)) {
        return '';
    }

    return trim($input);
}
