<?php
/**
 * デコレーション関連のユーティリティ関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * デコレーションパターンの動的読み込み機能
 */

/**
 * デコレーションJSONファイルを読み込む
 */
function backbone_get_decoration_patterns() {
    $patterns = array();

    // 親テーマのパターンを読み込み
    $parent_dir = get_template_directory() . '/inc/decorations-themes/';
    if (is_dir($parent_dir)) {
        $patterns = backbone_load_decoration_from_dir($parent_dir, $patterns);
    }

    // 子テーマのパターンを読み込み（上書き可能）
    if (get_template_directory() !== get_stylesheet_directory()) {
        $child_dir = get_stylesheet_directory() . '/inc/decorations-themes/';
        if (is_dir($child_dir)) {
            $patterns = backbone_load_decoration_from_dir($child_dir, $patterns);
        }
    }

    return $patterns;
}

/**
 * 指定ディレクトリからデコレーションパターンを読み込む
 */
function backbone_load_decoration_from_dir($dir, $patterns) {
    $files = glob($dir . '*.json');
    if ($files) {
        foreach ($files as $file) {
            $json_content = file_get_contents($file);
            $pattern_data = json_decode($json_content, true);
            if ($pattern_data && isset($pattern_data['id']) && isset($pattern_data['name'])) {
                $patterns[$pattern_data['id']] = $pattern_data;
            }
        }
    }
    return $patterns;
}

/**
 * カスタマイザー用のデコレーション選択肢配列を生成
 */
function backbone_get_decoration_choices() {
    $patterns = backbone_get_decoration_patterns();
    $choices = array('none' => '設定なし');

    foreach ($patterns as $id => $pattern) {
        $choices[$id] = $pattern['name'];
    }

    return $choices;
}

/**
 * 特定のデコレーションパターン情報を取得
 */
function backbone_get_decoration_pattern($pattern_id) {
    $patterns = backbone_get_decoration_patterns();
    return isset($patterns[$pattern_id]) ? $patterns[$pattern_id] : null;
}

/**
 * CSSプロパティを適切な形式に変換
 */
function backbone_convert_css_property($property, $value) {
    // 配列の場合はスキップ（疑似要素など）
    if (is_array($value)) {
        return '';
    }

    // アンダースコアをハイフンに変換
    $css_property = str_replace('_', '-', $property);

    // Webkitプレフィックス対応
    if (strpos($property, 'webkit_') === 0) {
        $css_property = '-webkit-' . substr($css_property, 8);
    }

    // content プロパティの特別処理
    if ($css_property === 'content') {
        $value = backbone_quote_content_value($value);
    }

    return "{$css_property}: {$value};";
}

/**
 * content値を適切にクォートする
 */
function backbone_quote_content_value($value) {
    // 既にクォートされている場合はそのまま
    if (preg_match('/^["\'].*["\']$/', $value)) {
        return $value;
    }

    // counter()関数の場合はクォート不要
    if (preg_match('/^counter\(/', $value)) {
        return $value;
    }

    // その他の場合はダブルクォートで囲む
    return '"' . $value . '"';
}

/**
 * 疑似要素CSSを生成
 */
function backbone_generate_pseudo_element_css($selector, $pseudo, $styles) {
    if (!is_array($styles)) {
        return '';
    }

    $css = "{$selector}::{$pseudo} {\n";

    foreach ($styles as $property => $value) {
        $css .= "    " . backbone_convert_css_property($property, $value) . "\n";
    }

    $css .= "}\n";

    return $css;
}

/**
 * デコレーションパターンのCSSを生成
 */
function backbone_generate_decoration_css($pattern_id) {
    $pattern = backbone_get_decoration_pattern($pattern_id);

    if (!$pattern) {
        return '';
    }

    $css = '';

    // CSS変数定義（badge padding など）
    if (isset($pattern['badge_padding']) || isset($pattern['badge_padding_mobile'])) {
        $css .= "body.decoration-{$pattern_id} {\n";
        if (isset($pattern['badge_padding'])) {
            $css .= "    --badge-padding: {$pattern['badge_padding']};\n";
        }
        if (isset($pattern['badge_padding_mobile'])) {
            $css .= "    --badge-padding-mobile: {$pattern['badge_padding_mobile']};\n";
        }
        $css .= "}\n";
    }

    // 見出しスタイル
    if (isset($pattern['headings'])) {
        foreach ($pattern['headings'] as $heading => $styles) {
            // より包括的なセレクターで、WordPressブロックエディター要素にも適用
            $selectors = array(
                "body.decoration-{$pattern_id} {$heading}",
                "body.decoration-{$pattern_id} .entry-title {$heading}",
                "body.decoration-{$pattern_id} .wp-block-heading {$heading}",
                "body.decoration-{$pattern_id} .wp-block-post-title {$heading}",
                "body.decoration-{$pattern_id} .page-title {$heading}",
                "body.decoration-{$pattern_id} .archive-title {$heading}",
                "body.decoration-{$pattern_id} .search-title {$heading}",
                "body.decoration-{$pattern_id} .comment-title {$heading}",
                "body.decoration-{$pattern_id} .entry-header {$heading}",
                "body.decoration-{$pattern_id} .entry-content {$heading}",
                "body.decoration-{$pattern_id} .sidebar {$heading}",
                "body.decoration-{$pattern_id} .footer-widgets {$heading}",
                "body.decoration-{$pattern_id} .site-header {$heading}",
                "body.decoration-{$pattern_id} .site-footer {$heading}",
                "body.decoration-{$pattern_id} .main-navigation {$heading}",
                "body.decoration-{$pattern_id} .main-content {$heading}",
                "body.decoration-{$pattern_id} .content-area {$heading}",
                "body.decoration-{$pattern_id} .container {$heading}",
                "body.decoration-{$pattern_id} .site-wrapper {$heading}",
                "body.decoration-{$pattern_id} .site {$heading}"
            );

            // 各セレクターに対してスタイルを適用（デコレーションの優先度を最大限に上げる）
            foreach ($selectors as $selector) {
                // 基本スタイル
                $css .= "{$selector} {\n";

                foreach ($styles as $property => $value) {
                    // 疑似要素は別処理
                    if ($property === 'before' || $property === 'after') {
                        continue;
                    }

                    // デコレーションの見出しスタイルは詳細度で優先度を調整
                    $css .= "    " . backbone_convert_css_property($property, $value) . ";\n";
                }

                $css .= "}\n";

                // 疑似要素処理（ハードコードfallbackなし）
                if (isset($styles['before'])) {
                    $css .= backbone_generate_pseudo_element_css($selector, 'before', $styles['before']);
                }

                if (isset($styles['after'])) {
                    $css .= backbone_generate_pseudo_element_css($selector, 'after', $styles['after']);
                }
            }
        }
    }

    // リストスタイル（main内に限定、ヘッダー・フッター除外）
    if (isset($pattern['lists'])) {
        foreach ($pattern['lists'] as $list_type => $list_styles) {
            $list_selector = "body.decoration-{$pattern_id} main {$list_type}";
            $item_selector = "body.decoration-{$pattern_id} main {$list_type} li";

            // リスト自体のスタイル
            $css .= "{$list_selector} {\n";

            foreach ($list_styles as $property => $value) {
                // アイテム固有のプロパティはスキップ
                if (strpos($property, 'item_') === 0) {
                    continue;
                }

                $css .= "    " . backbone_convert_css_property($property, $value) . "\n";
            }

            $css .= "}\n";

            // リストアイテムのスタイル
            $item_styles_found = false;
            $css .= "{$item_selector} {\n";

            foreach ($list_styles as $property => $value) {
                if (strpos($property, 'item_') === 0 && $property !== 'item_before' && $property !== 'item_after') {
                    $item_property = substr($property, 5); // 'item_' を除去

                    // 特殊処理
                    if ($item_property === 'last_child_border') {
                        continue; // 後で処理
                    }

                    $css .= "    " . backbone_convert_css_property($item_property, $value) . "\n";
                    $item_styles_found = true;
                }
            }

            $css .= "}\n";

            // last-child特殊処理
            if (isset($list_styles['item_last_child_border'])) {
                $css .= "{$item_selector}:last-child {\n";
                $css .= "    border-bottom: {$list_styles['item_last_child_border']};\n";
                $css .= "}\n";
            }

            // アイテムの疑似要素（ハードコードfallbackなし）
            if (isset($list_styles['item_before'])) {
                $css .= backbone_generate_pseudo_element_css($item_selector, 'before', $list_styles['item_before']);
            }

            if (isset($list_styles['item_after'])) {
                $css .= backbone_generate_pseudo_element_css($item_selector, 'after', $list_styles['item_after']);
            }
        }
    }

    return $css;
}
