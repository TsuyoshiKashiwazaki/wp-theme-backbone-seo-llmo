<?php
/**
 * タイポグラフィ関連のユーティリティ関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * タイポグラフィパターンの動的読み込み機能
 */

/**
 * タイポグラフィJSONファイルを読み込む
 */
function backbone_get_typography_patterns() {
    $typography_dir = get_template_directory() . '/inc/typography-themes/';
    $patterns = array();

    // typographyディレクトリが存在しない場合は空配列を返す
    if (!is_dir($typography_dir)) {
        return $patterns;
    }

    // JSONファイルを読み込む
    $files = glob($typography_dir . '*.json');

    foreach ($files as $file) {
        $json_content = file_get_contents($file);
        $pattern_data = json_decode($json_content, true);

        // JSONが正しく読み込めた場合のみ追加
        if ($pattern_data && isset($pattern_data['id']) && isset($pattern_data['name'])) {
            $patterns[$pattern_data['id']] = $pattern_data;
        }
    }

    return $patterns;
}

/**
 * カスタマイザー用の選択肢配列を生成
 */
function backbone_get_typography_choices() {
    $patterns = backbone_get_typography_patterns();
    $choices = array('none' => '設定なし');

    foreach ($patterns as $id => $pattern) {
        $choices[$id] = $pattern['name'];
    }

    return $choices;
}

/**
 * 特定のタイポグラフィパターン情報を取得
 */
function backbone_get_typography_pattern($pattern_id) {
    $patterns = backbone_get_typography_patterns();
    return isset($patterns[$pattern_id]) ? $patterns[$pattern_id] : null;
}

/**
 * 使用されているGoogleフォントのURLを生成
 */
function backbone_get_google_fonts_url() {
    $patterns = backbone_get_typography_patterns();
    $all_fonts = array();

    foreach ($patterns as $pattern) {
        if (isset($pattern['google_fonts']) && is_array($pattern['google_fonts'])) {
            $all_fonts = array_merge($all_fonts, $pattern['google_fonts']);
        }
    }

    // 重複を除去
    $all_fonts = array_unique($all_fonts);

    if (empty($all_fonts)) {
        return '';
    }

    return 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $all_fonts) . '&display=swap';
}

/**
 * タイポグラフィパターンのCSSを生成（レスポンシブ対応）
 */
function backbone_generate_typography_css($pattern_id) {
    $pattern = backbone_get_typography_pattern($pattern_id);

    if (!$pattern) {
        return '';
    }

    $css = '';
    
    // レスポンシブ用のスケール設定
    $scale_mobile = 0.85;    // モバイル: 85%
    $scale_tablet = 0.92;    // タブレット: 92%
    $scale_desktop = 1.0;    // デスクトップ: 100%

    // ベースフォントファミリー
    if (isset($pattern['font_family'])) {
        $css .= "body {\n";
        $css .= "    font-family: {$pattern['font_family']};\n";
        $css .= "}\n";
    }

    // エントリーコンテンツ（デスクトップ）
    if (isset($pattern['entry_content'])) {
        $css .= ".entry-content {\n";
        foreach ($pattern['entry_content'] as $property => $value) {
            if (is_array($value)) {
                continue;
            }
            $css_property = str_replace('_', '-', $property);
            // font-sizeの場合はcalcを使用してレスポンシブに
            if ($property === 'font_size' && preg_match('/([0-9.]+)(rem|em)/', $value, $matches)) {
                $base_size = $matches[1];
                $unit = $matches[2];
                $css .= "    {$css_property}: clamp(" . ($base_size * 0.8) . "{$unit}, " . ($base_size * 0.9) . "{$unit} + 0.5vw, {$value});\n";
            } else {
                $css .= "    {$css_property}: {$value};\n";
            }
        }
        $css .= "}\n";
    }

    // 見出し（新構造：h1〜h6個別設定）レスポンシブ対応
    if (isset($pattern['headings'])) {
        // 各見出しレベルを個別に処理
        foreach ($pattern['headings'] as $heading_level => $heading_styles) {
            if (is_array($heading_styles)) {
                // 個別見出し設定（h1, h2, h3...）
                $css .= "{$heading_level}, .entry-content {$heading_level} {\n";

                // font-familyを明示的に設定
                if (isset($pattern['font_family'])) {
                    $css .= "    font-family: {$pattern['font_family']};\n";
                }

                foreach ($heading_styles as $property => $value) {
                    if (is_array($value)) {
                        continue;
                    }
                    $css_property = str_replace('_', '-', $property);
                    
                    // font-sizeをレスポンシブに
                    if ($property === 'font_size' && preg_match('/([0-9.]+)(rem|em)/', $value, $matches)) {
                        $base_size = $matches[1];
                        $unit = $matches[2];
                        // clamp(最小値, 推奨値, 最大値) - モバイル/タブレット用に大幅縮小
                        $min_size = $base_size * 0.55;  // 55%まで縮小
                        $max_size = $base_size;
                        $css .= "    {$css_property}: clamp({$min_size}{$unit}, {$base_size}{$unit} - 1.2rem + 0.8vw, {$max_size}{$unit});\n";
                    } else {
                        $css .= "    {$css_property}: {$value};\n";
                    }
                }
                $css .= "}\n";
            } else {
                // 旧構造：全見出し共通設定（後方互換性）
                if (!isset($headings_common_processed)) {
                    $css .= "h1, h2, h3, h4, h5, h6,\n";
                    $css .= ".entry-content h1, .entry-content h2, .entry-content h3,\n";
                    $css .= ".entry-content h4, .entry-content h5, .entry-content h6 {\n";

                    // font-familyを明示的に設定
                    if (isset($pattern['font_family'])) {
                        $css .= "    font-family: {$pattern['font_family']};\n";
                    }

                    foreach ($pattern['headings'] as $property => $value) {
                        if (is_array($value)) {
                            continue; // 配列の場合はスキップ
                        }
                        $css_property = str_replace('_', '-', $property);
                        $css .= "    {$css_property}: {$value};\n";
                    }
                    $css .= "}\n";
                    $headings_common_processed = true;
                }
                break;
            }
        }
    }

    // 段落（レスポンシブ対応）
    if (isset($pattern['paragraphs'])) {
        $css .= ".entry-content p {\n";
        foreach ($pattern['paragraphs'] as $property => $value) {
            if (is_array($value)) {
                continue;
            }
            $css_property = str_replace('_', '-', $property);
            
            // margin-bottomをレスポンシブに
            if ($property === 'margin_bottom' && preg_match('/([0-9.]+)(rem|em)/', $value, $matches)) {
                $base_size = $matches[1];
                $unit = $matches[2];
                $min_size = $base_size * 0.75;
                $css .= "    {$css_property}: clamp({$min_size}{$unit}, {$value}, {$value});\n";
            } else {
                $css .= "    {$css_property}: {$value};\n";
            }
        }
        $css .= "}\n";
    }

    // リスト
    if (isset($pattern['lists'])) {
        $css .= ".entry-content ul, .entry-content ol {\n";
        foreach ($pattern['lists'] as $property => $value) {
            if (is_array($value)) {
                continue; // 配列の場合はスキップ
            }
            $css_property = str_replace('_', '-', $property);
            $css .= "    {$css_property}: {$value};\n";
        }
        $css .= "}\n";
    }

    // 要素（elements）レスポンシブ対応
    if (isset($pattern['elements'])) {
        $element_map = array(
            'site_title' => '.site-title',
            'site_description' => '.site-description',
            'entry_meta' => '.entry-meta',
            'widget_title' => '.widget-title',
            'comments_title' => '.comments-title',
            'comment_meta' => '.comment-meta',
            'comment_reply_link' => '.comment-reply-link',
            'wp_caption_text' => '.wp-caption-text',
            'archive_description' => '.archive-description',
            'footer_text' => '.footer-content .powered-by',
            'navigation_menu' => '.main-navigation a',
            'navigation_icon' => '.main-navigation .menu-item-has-children>a::after',
            'button' => '.btn',
            'form_input' => '.comment-form input, .comment-form textarea',
            'skip_link' => '.skip-link'
        );

        foreach ($pattern['elements'] as $element_key => $element_styles) {
            if (isset($element_map[$element_key])) {
                $selector = $element_map[$element_key];
                $css .= "{$selector} {\n";

                // font-familyを明示的に設定
                if (isset($pattern['font_family'])) {
                    $css .= "    font-family: {$pattern['font_family']};\n";
                }

                foreach ($element_styles as $property => $value) {
                    if (is_array($value)) {
                        continue;
                    }
                    $css_property = str_replace('_', '-', $property);
                    
                    // 特定要素のfont-sizeをレスポンシブに
                    if ($property === 'font_size' && preg_match('/([0-9.]+)(rem|em)/', $value, $matches)) {
                        $base_size = $matches[1];
                        $unit = $matches[2];
                        
                        // 要素タイプによってスケールを調整
                        if (in_array($element_key, ['site_title', 'widget_title', 'comments_title'])) {
                            // タイトル系も小さめに調整
                            $min_size = $base_size * 0.55;  // 0.7から0.55に縮小
                            $css .= "    {$css_property}: clamp({$min_size}{$unit}, {$base_size}{$unit} - 0.5rem + 1vw, {$value});\n";
                        } else {
                            // その他も小さめに
                            $min_size = $base_size * 0.75;  // 0.85から0.75に縮小
                            $css .= "    {$css_property}: clamp({$min_size}{$unit}, {$value}, {$value});\n";
                        }
                    } else {
                        $css .= "    {$css_property}: {$value};\n";
                    }
                }
                $css .= "}\n";
            }
        }
    }

    return $css;
}
