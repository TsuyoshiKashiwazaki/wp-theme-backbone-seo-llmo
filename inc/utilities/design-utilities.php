<?php
/**
 * デザインパターン関連のユーティリティ関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * デザインパターン関連の関数
 */

/**
 * デザインパターンファイルを読み込み
 */
function backbone_get_design_patterns() {
    $patterns = array();
    $pattern_files = glob(get_template_directory() . '/inc/designs/*.json');

    foreach ($pattern_files as $file) {
        $json_content = file_get_contents($file);
        $pattern = json_decode($json_content, true);

        if ($pattern && isset($pattern['id'], $pattern['name'])) {
            $patterns[$pattern['id']] = $pattern;
        }
    }

    return $patterns;
}

/**
 * デザインパターンのChoices配列を生成
 */
function backbone_get_design_choices() {
    $patterns = backbone_get_design_patterns();
    $choices = array(
        'none' => '設定なし'
    );

    foreach ($patterns as $pattern) {
        $choices[$pattern['id']] = $pattern['name'];
    }

    return $choices;
}

/**
 * 特定のデザインパターンを取得
 */
function backbone_get_design_pattern($pattern_id) {
    $patterns = backbone_get_design_patterns();
    return isset($patterns[$pattern_id]) ? $patterns[$pattern_id] : null;
}

/**
 * デザインパターンのCSSを生成
 */
function backbone_generate_design_css($pattern_id) {
    $pattern = backbone_get_design_pattern($pattern_id);
    if (!$pattern) {
        return '';
    }

    $css = "/* デザインパターン: {$pattern['name']} */\n";

    // レイアウトスタイル（JSON設定を正しく反映）
    if (isset($pattern['layout'])) {
        $layout = $pattern['layout'];

        // 基本コンテナ設定をJSONから適用（max_width未指定時はフルワイドに）- mainコンテナのみ
        $css .= ".design-{$pattern_id} main .container {\n";
        if (isset($layout['max_width'])) {
            if ($layout['max_width'] === '100%') {
                $css .= "    width: 100%;\n";
                $css .= "    max-width: 100%;\n";
            } else {
                $css .= "    max-width: {$layout['max_width']};\n";
                $css .= "    width: 100%;\n";
            }
        } else {
            // デフォルト: フルワイド
            $css .= "    width: 100%;\n";
            $css .= "    max-width: 100%;\n";
        }
        if (isset($layout['container_padding'])) {
            $css .= "    padding: {$layout['container_padding']};\n";
        }
        $css .= "    margin: 0 auto;\n";
        $css .= "    box-sizing: border-box;\n";
        $css .= "}\n";
        
        // 1カラムレイアウトのPCサイズで左右に余白を追加
        $css .= "@media (min-width: 1280px) {\n";
        $css .= "    body.layout-single-column.design-{$pattern_id} .main-content {\n";
        $css .= "        padding: 0 20px;\n";
        $css .= "    }\n";
        $css .= "}\n";

        // メインコンテンツ設定
        $css .= ".design-{$pattern_id} .main-content {\n";
        if (isset($layout['content_width'])) {
            $css .= "    width: {$layout['content_width']};\n";
            if ($layout['content_width'] === '100%') {
                $css .= "    max-width: 100%;\n";
            }
        }
        $css .= "    margin: 0 auto;\n";
        $css .= "    box-sizing: border-box;\n";
        $css .= "}\n";
        
        // 1カラムレイアウトPCサイズでのパディングを強制
        $css .= "@media (min-width: 1280px) {\n";
        $css .= "    body.layout-single-column.design-{$pattern_id} .main-content {\n";
        $css .= "        padding-left: 20px !important;\n";
        $css .= "        padding-right: 20px !important;\n";
        $css .= "    }\n";
        $css .= "}\n";

        // サイドバー設定（該当する場合）
        if (isset($layout['sidebar_position']) && $layout['sidebar_position'] !== 'none') {
            $css .= ".design-{$pattern_id} .content-area {\n";
            $css .= "    display: flex;\n";
            if (isset($layout['column_gap'])) {
                $css .= "    gap: {$layout['column_gap']};\n";
            }
            if ($layout['sidebar_position'] === 'left') {
                $css .= "    flex-direction: row-reverse;\n";
            }
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-main {\n";
            if (isset($layout['content_width'])) {
                $css .= "    flex: 0 0 {$layout['content_width']};\n";
            }
            $css .= "}\n";

            if (isset($layout['sidebar_width'])) {
                $css .= ".design-{$pattern_id} .widget-area {\n";
                $css .= "    flex: 0 0 {$layout['sidebar_width']};\n";
                $css .= "}\n";
            }
        }
    }

    // コンテンツ専用設定をJSONから適用
    if (isset($pattern['content'])) {
        $content = $pattern['content'];

        $css .= ".design-{$pattern_id} .content-area {\n";
        if (isset($content['max_width'])) {
            if ($content['max_width'] === 'none') {
                // 制約なし = フルワイド
                $css .= "    max-width: 100%;\n";
            } else {
                $css .= "    max-width: {$content['max_width']};\n";
            }
        } else {
            // デフォルト: フルワイド
            $css .= "    max-width: 100%;\n";
        }
        if (isset($content['margin'])) {
            $css .= "    margin: {$content['margin']};\n";
        } else {
            $css .= "    margin: 0 auto;\n";
        }
        if (isset($content['main_padding'])) {
            $css .= "    padding: {$content['main_padding']};\n";
        }
        $css .= "    box-sizing: border-box;\n";
        $css .= "}\n";
    }

    // ヘッダー構造スタイル：全レイアウトに適用
    if (isset($pattern['header'])) {
        $header = $pattern['header'];

        // 全レイアウトに適用するためセレクタを強化
        $css .= "body.design-{$pattern_id} .site-header,\n";
        $css .= "body.design-{$pattern_id}.layout-full-width .site-header,\n";
        $css .= "body.design-{$pattern_id}.layout-single-column .site-header,\n";
        $css .= "body.design-{$pattern_id}.layout-two-columns .site-header,\n";
        $css .= "body.design-{$pattern_id}.layout-three-columns .site-header {\n";

        if (isset($header['layout'])) {
            switch ($header['layout']) {
                case 'fullwidth':
                    $css .= "    width: 100%;\n";
                    $css .= "    max-width: 100%;\n";
                    $css .= "    margin: 0;\n";
                    $css .= "    left: 0;\n";
                    $css .= "    right: 0;\n";
                    $css .= "    position: relative;\n";
                    break;
                case 'contained':
                    $css .= "    width: 100%;\n";
                    $css .= "    max-width: 100%;\n";
                    $css .= "    margin: 0 auto;\n";
                    $css .= "    left: auto;\n";
                    $css .= "    right: auto;\n";
                    $css .= "    margin-left: auto;\n";
                    $css .= "    margin-right: auto;\n";
                    $css .= "    position: relative;\n";
                    break;
                case 'minimal':
                    $css .= "    width: 100%;\n";
                    $css .= "    max-width: 100%;\n";
                    $css .= "    margin: 0 auto;\n";
                    $css .= "    left: auto;\n";
                    $css .= "    right: auto;\n";
                    $css .= "    margin-left: auto;\n";
                    $css .= "    margin-right: auto;\n";
                    $css .= "    box-shadow: none;\n";
                    $css .= "    border: none;\n";
                    $css .= "    position: relative;\n";
                    break;
                case 'floating':
                    $css .= "    max-width: 1000px;\n";
                    $css .= "    margin: 2rem auto;\n";
                    $css .= "    width: auto;\n";
                    $css .= "    left: auto;\n";
                    $css .= "    right: auto;\n";
                    $css .= "    margin-left: auto;\n";
                    $css .= "    margin-right: auto;\n";
                    /* border-radius はデコレーションパターンで管理 */
                    $css .= "    box-shadow: var(--floating-shadow, 0 8px 32px rgba(0,0,0,0.1));\n";
                    $css .= "    position: relative;\n";
                    break;
                case 'side':
                    $css .= "    position: fixed;\n";
                    $css .= "    top: 0;\n";
                    $css .= "    left: 0;\n";
                    $css .= "    width: 300px;\n";
                    $css .= "    height: 100vh;\n";
                    $css .= "    z-index: 1000;\n";
                    $css .= "    margin-left: 0;\n";
                    $css .= "    margin-right: 0;\n";
                    $css .= "    right: auto;\n";
                    break;
                case 'overlay':
                    $css .= "    position: absolute;\n";
                    $css .= "    top: 0;\n";
                    $css .= "    left: 0;\n";
                    $css .= "    right: 0;\n";
                    $css .= "    width: 100%;\n";
                    $css .= "    margin-left: 0;\n";
                    $css .= "    margin-right: 0;\n";
                    $css .= "    background: var(--header-background, rgba(0,0,0,0.8));\n";
                    $css .= "    backdrop-filter: blur(10px);\n";
                    break;
            }
        }

        // ヘッダーのパディング設定 - 統一された高さ
        // デザインパターンによらず固定の高さを保つ
        $css .= "    padding: 1.25rem 0 !important;\n";

        if (isset($header['alignment'])) {
            switch ($header['alignment']) {
                case 'center':
                    $css .= "    text-align: center;\n";
                    break;
                case 'spread':
                    $css .= "    display: flex;\n";
                    $css .= "    justify-content: space-between;\n";
                    $css .= "    align-items: center;\n";
                    break;
                case 'stack':
                    $css .= "    display: flex;\n";
                    $css .= "    flex-direction: column;\n";
                    $css .= "    align-items: center;\n";
                    break;
            }
        }

        if (isset($header['position'])) {
            if ($header['position'] === 'sticky') {
                $css .= "    position: sticky;\n";
                $css .= "    top: 0;\n";
                $css .= "    z-index: 1000;\n";
            } elseif ($header['position'] === 'fixed') {
                $css .= "    position: fixed;\n";
                $css .= "    top: 0;\n";
                $css .= "    left: 0;\n";
                $css .= "    right: 0;\n";
                $css .= "    z-index: 1000;\n";
            }
        }

        $css .= "}\n";

        // フローティングヘッダー専用：内部要素も丸める
        if (isset($header['layout']) && $header['layout'] === 'floating') {
            $css .= ".design-{$pattern_id} .site-header .main-navigation {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "    position: relative;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .site-branding {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation ul {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation li:first-child a {\n";
            $css .= "    border-top-left-radius: 12px;\n";
            $css .= "    border-top-right-radius: 12px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation li:last-child a {\n";
            $css .= "    border-bottom-left-radius: 12px;\n";
            $css .= "    border-bottom-right-radius: 12px;\n";
            $css .= "}\n";

            // サブメニュー専用設定
            $css .= ".design-{$pattern_id} .site-header .main-navigation .sub-menu {\n";
            $css .= "    position: absolute;\n";
            $css .= "    top: 100%;\n";
            $css .= "    left: 0;\n";
            $css .= "    z-index: 9999;\n";
            $css .= "    background: var(--submenu-background, rgba(255, 255, 255, 0.95));\n";
            $css .= "    backdrop-filter: blur(10px);\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "    box-shadow: var(--submenu-shadow, 0 4px 20px rgba(0,0,0,0.15));\n";
            $css .= "    min-width: 200px;\n";
            $css .= "    margin-top: 0.5rem;\n";
            $css .= "    overflow: visible;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation .sub-menu li {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "    width: 100%;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation .sub-menu li:first-child {\n";
            $css .= "    border-top-left-radius: 8px;\n";
            $css .= "    border-top-right-radius: 8px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation .sub-menu li:last-child {\n";
            $css .= "    border-bottom-left-radius: 8px;\n";
            $css .= "    border-bottom-right-radius: 8px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation .sub-menu a {\n";
            $css .= "    padding: 0.75rem 1rem;\n";
            $css .= "    display: block;\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "    transition: all 0.2s ease;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .main-navigation .sub-menu a:hover {\n";
            $css .= "    background: var(--submenu-hover-background, rgba(0,0,0,0.05));\n";
            $css .= "}\n";
        }

        // ヘッダーコンテナ調整：全レイアウトに適用
        if (isset($header['layout'])) {
            switch ($header['layout']) {
                case 'contained':
                    // 全レイアウトに適用するため詳細度を上げる
                    $css .= "body.design-{$pattern_id} .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-full-width .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-single-column .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-two-columns .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-three-columns .site-header .container {\n";
                    $css .= "    margin: 0 auto;\n";
                    $css .= "    padding: 0;\n";
                    $css .= "    width: auto;\n";
                    $css .= "}\n";
                    break;
                case 'minimal':
                    $css .= "body.design-{$pattern_id} .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-full-width .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-single-column .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-two-columns .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-three-columns .site-header .container {\n";
                    $css .= "    margin: 0 auto;\n";
                    $css .= "    padding: 0;\n";
                    $css .= "    width: auto;\n";
                    $css .= "}\n";
                    break;
                case 'floating':
                    $css .= "body.design-{$pattern_id} .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-full-width .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-single-column .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-two-columns .site-header .container,\n";
                    $css .= "body.design-{$pattern_id}.layout-three-columns .site-header .container {\n";
                    $css .= "    max-width: 1000px;\n";
                    $css .= "    margin: 0 auto;\n";
                    $css .= "    padding: 0;\n";
                    $css .= "    width: auto;\n";
                    $css .= "}\n";
                    break;
                case 'fullwidth':
                    // フルワイドヘッダーのコンテナはcomponents-header.cssで管理
                    break;
            }
        }

        // ヘッダーコンテンツ構造
        if (isset($header['layout']) && $header['layout'] === 'side') {
            $css .= ".design-{$pattern_id} .main-content {\n";
            $css .= "    margin-left: 300px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-header .container {\n";
            $css .= "    max-width: 100%;\n";
            $css .= "    margin: 0;\n";
            $css .= "    padding: 1rem;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .header-content {\n";
            $css .= "    flex-direction: column;\n";
            $css .= "    align-items: flex-start;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .main-navigation {\n";
            $css .= "    margin-top: 2rem;\n";
            $css .= "    width: 100%;\n";
            $css .= "}\n";
        }

        // ヘッダー構造の処理（two_tier など）
        if (isset($header['structure'])) {
            switch ($header['structure']) {
                case 'two_tier':
                    // 2段ヘッダー構造：全レイアウトに適用（最高優先度）
                    $css .= "/* Two-tier header structure for all layouts with maximum priority */\n";
                    $css .= "html body.design-{$pattern_id} .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-full-width .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-single-column .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-two-columns .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-three-columns .site-header {\n";
                    $css .= "    display: flex !important;\n";
                    $css .= "    flex-direction: column !important;\n";
                    $css .= "}\n";
                    
                    // ヘッダー全体のパディングを統一 - 固定値
                    $css .= "html body.design-{$pattern_id} .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-full-width .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-single-column .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-two-columns .site-header,\n";
                    $css .= "html body.design-{$pattern_id}.layout-three-columns .site-header {\n";
                    $css .= "    padding: 1.25rem 0 !important;\n";
                    $css .= "    box-sizing: border-box !important;\n";
                    $css .= "}\n";
                    
                    $css .= "html body.design-{$pattern_id} .header-content,\n";
                    $css .= "html body.design-{$pattern_id}.layout-full-width .header-content,\n";
                    $css .= "html body.design-{$pattern_id}.layout-single-column .header-content,\n";
                    $css .= "html body.design-{$pattern_id}.layout-two-columns .header-content,\n";
                    $css .= "html body.design-{$pattern_id}.layout-three-columns .header-content {\n";
                    $css .= "    display: flex !important;\n";
                    $css .= "    flex-direction: column !important;\n";
                    $css .= "    align-items: center !important;\n";
                    $css .= "    justify-content: flex-start !important;\n";
                    $css .= "    width: 100% !important;\n";
                    $css .= "}\n";
                    
                    // サイトブランディングの中央配置
                    $css .= "html body.design-{$pattern_id} .site-branding,\n";
                    $css .= "html body.design-{$pattern_id}.layout-full-width .site-branding,\n";
                    $css .= "html body.design-{$pattern_id}.layout-single-column .site-branding,\n";
                    $css .= "html body.design-{$pattern_id}.layout-two-columns .site-branding,\n";
                    $css .= "html body.design-{$pattern_id}.layout-three-columns .site-branding {\n";
                    $css .= "    display: flex !important;\n";
                    $css .= "    flex-direction: column !important;\n";
                    $css .= "    align-items: center !important;\n";
                    $css .= "    text-align: center !important;\n";
                    $css .= "    width: 100% !important;\n";
                    $css .= "    margin: 0 auto !important;\n";
                    $css .= "}\n";
                    break;
                    
                case 'single_tier':
                    // 1段ヘッダー構造
                    $css .= "body.design-{$pattern_id} .header-content {\n";
                    $css .= "    display: flex;\n";
                    $css .= "    flex-direction: row;\n";
                    $css .= "    justify-content: space-between;\n";
                    $css .= "    align-items: center;\n";
                    $css .= "}\n";
                    break;
                    
                case 'split':
                    // 分割ヘッダー構造
                    $css .= "body.design-{$pattern_id} .header-content {\n";
                    $css .= "    display: grid;\n";
                    $css .= "    grid-template-columns: 1fr auto 1fr;\n";
                    $css .= "    align-items: center;\n";
                    $css .= "}\n";
                    break;
            }
        }

        // ヘッダーナビゲーション調整
        if (isset($header['nav_position'])) {
            if ($header['nav_position'] === 'below') {
                // ナビゲーションを下部に配置：全レイアウトに適用（最高優先度）
                $css .= "/* Navigation below header for all layouts with maximum priority */\n";
                $css .= "html body.design-{$pattern_id} .header-content,\n";
                $css .= "html body.design-{$pattern_id}.layout-full-width .header-content,\n";
                $css .= "html body.design-{$pattern_id}.layout-single-column .header-content,\n";
                $css .= "html body.design-{$pattern_id}.layout-two-columns .header-content,\n";
                $css .= "html body.design-{$pattern_id}.layout-three-columns .header-content {\n";
                $css .= "    flex-direction: column !important;\n";
                $css .= "}\n";
                
                $css .= "html body.design-{$pattern_id} .main-navigation,\n";
                $css .= "html body.design-{$pattern_id}.layout-full-width .main-navigation,\n";
                $css .= "html body.design-{$pattern_id}.layout-single-column .main-navigation,\n";
                $css .= "html body.design-{$pattern_id}.layout-two-columns .main-navigation,\n";
                $css .= "html body.design-{$pattern_id}.layout-three-columns .main-navigation {\n";
                $css .= "    margin-top: 1rem !important;\n";
                $css .= "    width: 100% !important;\n";
                $css .= "    order: 2 !important;\n";
                $css .= "}\n";
                
                // サイトブランディングを上部に配置
                $css .= "html body.design-{$pattern_id} .site-branding,\n";
                $css .= "html body.design-{$pattern_id}.layout-full-width .site-branding,\n";
                $css .= "html body.design-{$pattern_id}.layout-single-column .site-branding,\n";
                $css .= "html body.design-{$pattern_id}.layout-two-columns .site-branding,\n";
                $css .= "html body.design-{$pattern_id}.layout-three-columns .site-branding {\n";
                $css .= "    order: 1 !important;\n";
                $css .= "}\n";
            } elseif ($header['nav_position'] === 'overlay') {
                $css .= ".design-{$pattern_id} .main-navigation {\n";
                $css .= "    position: absolute;\n";
                $css .= "    top: 100%;\n";
                $css .= "    left: 0;\n";
                $css .= "    right: 0;\n";
                $css .= "}\n";
            }
        }
    }

    // ナビゲーション構造スタイル：全レイアウトに適用
    if (isset($pattern['navigation'])) {
        $navigation = $pattern['navigation'];

        // 全レイアウトに適用するためセレクタを強化
        $css .= "body.design-{$pattern_id} .main-navigation,\n";
        $css .= "body.design-{$pattern_id}.layout-full-width .main-navigation,\n";
        $css .= "body.design-{$pattern_id}.layout-single-column .main-navigation,\n";
        $css .= "body.design-{$pattern_id}.layout-two-columns .main-navigation,\n";
        $css .= "body.design-{$pattern_id}.layout-three-columns .main-navigation {\n";
        
        // ナビゲーション位置が'below_header'の場合のスタイル
        if (isset($navigation['position']) && $navigation['position'] === 'below_header') {
            $css .= "    width: 100%;\n";
        }
        
        // ナビゲーションの背景色（transparentの場合は明示的に透明に設定）
        if (isset($navigation['background'])) {
            if ($navigation['background'] === 'transparent') {
                $css .= "    background: transparent !important;\n";
            } else {
                $css .= "    background: {$navigation['background']};\n";
            }
        }
        
        // ナビゲーションのパディング - 統一された値を使用
        // JSONの値に関わらず固定値を適用
        $css .= "    padding: 0.5rem 0;\n";
        
        // ナビゲーションのボーダー（全レイアウトでビューポート全幅）
        if (isset($navigation['border_top'])) {
            $css .= "    position: relative;\n";
            $css .= "    border-top: none;\n";
        }
        if (isset($navigation['border_bottom'])) {
            $css .= "    border-bottom: {$navigation['border_bottom']};\n";
        }
        
        // ナビゲーションの配置
        if (isset($navigation['alignment'])) {
            switch ($navigation['alignment']) {
                case 'center':
                    $css .= "    text-align: center;\n";
                    $css .= "    justify-content: center;\n";
                    break;
                case 'right':
                    $css .= "    text-align: right;\n";
                    $css .= "    justify-content: flex-end;\n";
                    break;
                case 'left':
                    $css .= "    text-align: left;\n";
                    $css .= "    justify-content: flex-start;\n";
                    break;
                case 'justify':
                    $css .= "    display: flex;\n";
                    $css .= "    justify-content: space-between;\n";
                    break;
            }
        }

        $css .= "}\n";
        
        // ナビゲーションのborder-topをビューポート全幅で表示
        if (isset($navigation['border_top'])) {
            $css .= "body.design-{$pattern_id} .main-navigation::before,\n";
            $css .= "body.design-{$pattern_id}.layout-full-width .main-navigation::before,\n";
            $css .= "body.design-{$pattern_id}.layout-single-column .main-navigation::before,\n";
            $css .= "body.design-{$pattern_id}.layout-two-columns .main-navigation::before,\n";
            $css .= "body.design-{$pattern_id}.layout-three-columns .main-navigation::before {\n";
            $css .= "    content: '';\n";
            $css .= "    position: absolute;\n";
            $css .= "    top: 0;\n";
            $css .= "    left: 50%;\n";
            $css .= "    transform: translateX(-50%);\n";
            $css .= "    width: 100vw;\n";
            $css .= "    height: 1px;\n";
            $css .= "    background: var(--border-color);\n";
            $css .= "    z-index: 1;\n";
            $css .= "}\n";
        }
        
        // ナビゲーションメニューのスタイル
        $css .= "body.design-{$pattern_id} .main-navigation ul,\n";
        $css .= "body.design-{$pattern_id}.layout-full-width .main-navigation ul,\n";
        $css .= "body.design-{$pattern_id}.layout-single-column .main-navigation ul,\n";
        $css .= "body.design-{$pattern_id}.layout-two-columns .main-navigation ul,\n";
        $css .= "body.design-{$pattern_id}.layout-three-columns .main-navigation ul {\n";
        if (isset($navigation['alignment']) && $navigation['alignment'] === 'center') {
            $css .= "    justify-content: center;\n";
        }
        $css .= "}\n";
    }

    // フッター構造スタイル
    if (isset($pattern['footer'])) {
        $footer = $pattern['footer'];

        $css .= ".design-{$pattern_id} .site-footer {\n";

        if (isset($footer['layout'])) {
            switch ($footer['layout']) {
                case 'minimal':
                    $css .= "    text-align: center;\n";
                    $css .= "    padding: 1rem 0;\n";
                    $css .= "    border-top: 1px solid var(--border-color);\n";
                    break;
                case 'floating':
                    $css .= "    max-width: 1000px;\n";
                    $css .= "    margin: 2rem auto;\n";
                    $css .= "    padding: 2rem;\n";
                    /* border-radius はデコレーションパターンで管理 */
                    $css .= "    box-shadow: var(--floating-shadow, 0 8px 32px rgba(0,0,0,0.1));\n";
                    $css .= "    position: relative;\n";
                    $css .= "    width: auto;\n";
                    $css .= "    left: auto;\n";
                    $css .= "    right: auto;\n";
                    $css .= "    text-align: center;\n";
                    break;
                case 'three_column':
                    $css .= "    display: grid;\n";
                    $css .= "    grid-template-columns: 1fr 1fr 1fr;\n";
                    $css .= "    gap: 2rem;\n";
                    $css .= "    padding: 3rem 0;\n";
                    break;
                case 'wide':
                    $css .= "    width: 100%;\n";
                    $css .= "    max-width: 100%;\n";
                    $css .= "    margin: 0;\n";
                    $css .= "    left: 0;\n";
                    $css .= "    right: 0;\n";
                    $css .= "    position: relative;\n";
                    break;
                case 'fixed':
                    $css .= "    position: fixed;\n";
                    $css .= "    bottom: 0;\n";
                    $css .= "    left: 0;\n";
                    $css .= "    right: 0;\n";
                    $css .= "    z-index: 1000;\n";
                    break;
                case 'floating':
                    $css .= "    max-width: 1000px;\n";
                    $css .= "    margin: 2rem auto;\n";
                    /* border-radius はデコレーションパターンで管理 */
                    $css .= "    box-shadow: var(--floating-shadow, 0 8px 32px rgba(0,0,0,0.1));\n";
                    break;
            }
        }

        // フッターのパディング - 統一された値を使用
        // JSONの値に関わらず固定値を適用
        $css .= "    padding: 40px 0 20px;\n";

        $css .= "}\n";

        // フローティングフッター専用：内部要素も丸める
        if (isset($footer['layout']) && $footer['layout'] === 'floating') {
            $css .= ".design-{$pattern_id} .site-footer * {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .footer-navigation {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "    overflow: hidden;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .footer-navigation ul {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .footer-navigation li:first-child {\n";
            $css .= "    border-top-left-radius: 12px;\n";
            $css .= "    border-top-right-radius: 12px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .footer-navigation li:first-child a {\n";
            $css .= "    border-top-left-radius: 12px;\n";
            $css .= "    border-top-right-radius: 12px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .footer-navigation li:last-child {\n";
            $css .= "    border-bottom-left-radius: 12px;\n";
            $css .= "    border-bottom-right-radius: 12px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .footer-navigation li:last-child a {\n";
            $css .= "    border-bottom-left-radius: 12px;\n";
            $css .= "    border-bottom-right-radius: 12px;\n";
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .widget {\n";
            /* border-radius はデコレーションパターンで管理 */
            $css .= "}\n";

            $css .= ".design-{$pattern_id} .site-footer .widget-title {\n";
            $css .= "    border-top-left-radius: 12px;\n";
            $css .= "    border-top-right-radius: 12px;\n";
            $css .= "}\n";
        }
    }

    // タイポグラフィスタイル
    // タイポグラフィ設定はタイポグラフィパターンに依存（削除）

    // カラム設定のCSS生成（JSONベース）
    if (isset($pattern['columns'])) {
        $columns = $pattern['columns'];

        // JSONからCSS設定を読み込み
        if (isset($columns['css'])) {
            $css_settings = $columns['css'];

            if (isset($css_settings['selectors']) && isset($css_settings['properties'])) {
                $selectors = $css_settings['selectors'];
                $properties = $css_settings['properties'];

                // セレクタを生成（{id}プレースホルダーを実際のIDに置換し、詳細度を向上）
                $processed_selectors = array_map(function($selector) use ($pattern_id) {
                    $processed = str_replace('{id}', $pattern_id, $selector);
                    // bodyプレフィックスを追加して詳細度を向上
                    return 'body ' . $processed;
                }, $selectors);
                
                // フルワイドレイアウト用のセレクタを自動追加
                $additional_selectors = array(
                    "body .design-{$pattern_id}.layout-full-width .content-area",
                    "body .design-{$pattern_id}.layout-full-width .site-main",
                    "body .design-{$pattern_id}.layout-full-width .entry-content",
                    "body .design-{$pattern_id}.layout-single-column .content-area",
                    "body .design-{$pattern_id}.layout-single-column .site-main"
                );
                $processed_selectors = array_merge($processed_selectors, $additional_selectors);

                $css .= implode(",\n", $processed_selectors) . " {\n";

                // プロパティを追加
                foreach ($properties as $property => $value) {
                    $css .= "    {$property}: {$value};\n";
                }

                $css .= "}\n";
            }
        }

        // ボーダー設定（JSONベース）
        if (isset($columns['border'])) {
            $border = $columns['border'];

            // ボーダーCSS設定をJSONに追加
            if (isset($columns['css'])) {
                $selectors = $columns['css']['selectors'];
                $processed_selectors = array_map(function($selector) use ($pattern_id) {
                    return str_replace('{id}', $pattern_id, $selector);
                }, $selectors);
                
                // フルワイドレイアウト用のセレクタを自動追加
                $additional_selectors = array(
                    ".design-{$pattern_id}.layout-full-width .content-area",
                    ".design-{$pattern_id}.layout-full-width .site-main",
                    ".design-{$pattern_id}.layout-full-width .entry-content",
                    ".design-{$pattern_id}.layout-single-column .content-area",
                    ".design-{$pattern_id}.layout-single-column .site-main"
                );
                $processed_selectors = array_merge($processed_selectors, $additional_selectors);

                // ボーダーCSSを生成
                if (isset($border['style']) && isset($border['width']) && isset($border['color'])) {
                    $width = $border['width'];
                    $style = $border['style'];
                    
                    // none style -> スキップ
                    if ($style === 'none' || $width === '0') {
                        return '';
                    }
                    
                    $border_value = $width . ' ' . $style . ' ' . $border['color'];
                    $border_position = isset($border['position']) ? $border['position'] : 'all';

                    $border_css = "";
                    
                    // 複数位置指定に対応（カンマ区切り）
                    if (strpos($border_position, ',') !== false) {
                        $positions = explode(',', $border_position);
                        foreach ($positions as $pos) {
                            $pos = trim($pos);
                            switch ($pos) {
                                case 'top':
                                    $border_css .= "border-top: {$border_value}; ";
                                    break;
                                case 'right':
                                    $border_css .= "border-right: {$border_value}; ";
                                    break;
                                case 'bottom':
                                    $border_css .= "border-bottom: {$border_value}; ";
                                    break;
                                case 'left':
                                    $border_css .= "border-left: {$border_value}; ";
                                    break;
                            }
                        }
                    } else {
                        // 単一位置指定
                        switch ($border_position) {
                            case 'all':
                                $border_css = "border: {$border_value};";
                                break;
                            case 'top':
                                $border_css = "border-top: {$border_value};";
                                break;
                            case 'right':
                                $border_css = "border-right: {$border_value};";
                                break;
                            case 'bottom':
                                $border_css = "border-bottom: {$border_value};";
                                break;
                            case 'left':
                                $border_css = "border-left: {$border_value};";
                                break;
                        }
                    }

                    // ボーダー半径の追加
                    if (isset($border['radius'])) {
                        $border_css .= " border-radius: {$border['radius']};";
                    }

                    if (!empty($border_css)) {
                        $css .= implode(",\n", $processed_selectors) . " {\n";
                        $css .= "    {$border_css}\n";
                        $css .= "}\n";
                    }
                }
            }
        }

        // シャドウ設定（JSONベース）
        if (isset($columns['shadow'])) {
            $shadow = $columns['shadow'];

            if (isset($columns['css'])) {
                $selectors = $columns['css']['selectors'];
                $processed_selectors = array_map(function($selector) use ($pattern_id) {
                    return str_replace('{id}', $pattern_id, $selector);
                }, $selectors);
                
                // フルワイドレイアウト用のセレクタを自動追加
                $additional_selectors = array(
                    ".design-{$pattern_id}.layout-full-width .content-area",
                    ".design-{$pattern_id}.layout-full-width .site-main",
                    ".design-{$pattern_id}.layout-full-width .entry-content",
                    ".design-{$pattern_id}.layout-single-column .content-area",
                    ".design-{$pattern_id}.layout-single-column .site-main"
                );
                $processed_selectors = array_merge($processed_selectors, $additional_selectors);

                // シャドウCSSを生成
                if (isset($shadow['type']) && isset($shadow['intensity'])) {
                    $shadow_type = $shadow['type'];
                    $shadow_intensity = $shadow['intensity'];

                    // 基本的なシャドウ値
                    $x_offset = isset($shadow['x_offset']) ? $shadow['x_offset'] : '0';
                    $y_offset = isset($shadow['y_offset']) ? $shadow['y_offset'] : '2px';
                    $blur = isset($shadow['blur']) ? $shadow['blur'] : '8px';
                    $spread = isset($shadow['spread']) ? $shadow['spread'] : '0';
                    $color = isset($shadow['color']) ? $shadow['color'] : 'rgba(0,0,0,0.08)';

                    // シャドウタイプによる調整
                    if ($shadow_type === 'inner') {
                        $shadow_css = "box-shadow: inset {$x_offset} {$y_offset} {$blur} {$spread} {$color};";
                    } else {
                        $shadow_css = "box-shadow: {$x_offset} {$y_offset} {$blur} {$spread} {$color};";
                    }

                    // 強度による調整
                    switch ($shadow_intensity) {
                        case 'light':
                            // デフォルト値を使用
                            break;
                        case 'medium':
                            if ($shadow_type !== 'inner') {
                                $shadow_css = "box-shadow: {$x_offset} {$y_offset} 12px 2px {$color};";
                            }
                            break;
                        case 'heavy':
                            if ($shadow_type !== 'inner') {
                                $shadow_css = "box-shadow: {$x_offset} {$y_offset} 16px 4px {$color};";
                            }
                            break;
                    }

                    if (!empty($shadow_css)) {
                        $css .= implode(",\n", $processed_selectors) . " {\n";
                        $css .= "    {$shadow_css}\n";
                        $css .= "}\n";
                    }
                }
            }
        }
    }

    // ヘッダーに重なる問題を修正
    if (isset($pattern['header'])) {
        $header = $pattern['header'];
        if (isset($header['position']) && $header['position'] === 'fixed') {
            $css .= ".design-{$pattern_id} .layout-two-columns .content-area,\n";
            $css .= ".design-{$pattern_id} .layout-three-columns .content-area {\n";
            $css .= "    margin-top: 100px;\n";
            $css .= "}\n";
        } elseif (isset($header['position']) && $header['position'] === 'sticky') {
            $css .= ".design-{$pattern_id} .layout-two-columns .content-area,\n";
            $css .= ".design-{$pattern_id} .layout-three-columns .content-area {\n";
            $css .= "    margin-top: 80px;\n";
            $css .= "}\n";
        }
    }

    // カラム固有の追加設定
    if (isset($pattern['columns'])) {
        // カラム固有の追加スタイルがあればここに追加
    }

    // スペーシングスタイル
    if (isset($pattern['spacing'])) {
        $spacing = $pattern['spacing'];

        if (isset($spacing['article_margin'])) {
            $css .= ".design-{$pattern_id} .hentry {\n";
            $css .= "    margin-bottom: {$spacing['article_margin']};\n";
            $css .= "}\n";
        }

        if (isset($spacing['content_padding'])) {
            $css .= ".design-{$pattern_id} .entry-content {\n";
            $css .= "    padding: {$spacing['content_padding']};\n";
            $css .= "}\n";
        }
    }

    // モネデザイン特別修正：ヘッダー重複防止
    if ($pattern_id === 'tk-design-monet') {
        $css .= "/* Monet design special fixes */\n";
        $css .= "body.design-tk-design-monet .site-header,\n";
        $css .= "body.design-tk-design-monet.layout-full-width .site-header,\n";
        $css .= "body.design-tk-design-monet.layout-single-column .site-header,\n";
        $css .= "body.design-tk-design-monet.layout-two-columns .site-header,\n";
        $css .= "body.design-tk-design-monet.layout-three-columns .site-header {\n";
        $css .= "    min-height: auto !important;\n";
        $css .= "    height: auto !important;\n";
        $css .= "}\n";
        
        // ヘッダーコンテンツが一度だけ表示されるように
        $css .= "body.design-tk-design-monet .site-branding:not(:first-of-type),\n";
        $css .= "body.design-tk-design-monet.layout-full-width .site-branding:not(:first-of-type),\n";
        $css .= "body.design-tk-design-monet.layout-single-column .site-branding:not(:first-of-type),\n";
        $css .= "body.design-tk-design-monet.layout-two-columns .site-branding:not(:first-of-type),\n";
        $css .= "body.design-tk-design-monet.layout-three-columns .site-branding:not(:first-of-type) {\n";
        $css .= "    display: none !important;\n";
        $css .= "}\n";
    }
    
    // 安全な両端切れ修正（左飛び防止）
    $css .= "/* 安全な両端切れ修正 */\n";

    // 一般コンテンツ用：詳細度ベースの安定制御（ヘッダーは除外）
    $css .= "body.design-{$pattern_id} main .container {\n";
    $css .= "    width: 100%;\n";
    $css .= "    max-width: 100%;\n";
    $css .= "    box-sizing: border-box;\n";
    $css .= "    padding: 0;\n";
    $css .= "    margin: 0 auto;\n";
    $css .= "}\n";

    // main-content専用：中央配置確保
    $css .= "body.design-{$pattern_id} .site .main-content,\n";
    $css .= "body.design-{$pattern_id} .main-content {\n";
    $css .= "    width: 100%;\n";
    $css .= "    max-width: 100%;\n";
    $css .= "    box-sizing: border-box;\n";
    $css .= "    margin: 0 auto;\n";
    $css .= "    padding: 0;\n";
    $css .= "}\n";

    // 内部コンテンツの適切な制限
    $css .= "body.design-{$pattern_id} .site .content-area,\n";
    $css .= "body.design-{$pattern_id} .content-area,\n";
    $css .= "body.design-{$pattern_id} .entry-content {\n";
    $css .= "    margin: 30px auto 0 auto;\n";
    $css .= "    padding: 0;\n";
    $css .= "    box-sizing: border-box;\n";
    $css .= "}\n";

    // タブレット対応（768px〜1279px）
    $css .= "@media (min-width: 768px) and (max-width: 1279px) {\n";
    $css .= "    body.design-{$pattern_id} main .container {\n";
    $css .= "        padding: 0 1rem;\n";
    $css .= "    }\n";
    $css .= "    body.design-{$pattern_id} .content-area,\n";
    $css .= "    body.design-{$pattern_id} .entry-content {\n";
    $css .= "        padding: 0;\n";
    $css .= "    }\n";
    $css .= "}\n";
    $css .= "\n";
    $css .= "\n    // モバイル対応（768px以下）\n";
    $css .= "@media (max-width: 768px) {\n";
    $css .= "    body.design-{$pattern_id} main .container {\n";
    $css .= "        padding: 0 0.5rem;\n";
    $css .= "    }\n";
    $css .= "    body.design-{$pattern_id} .content-area,\n";
    $css .= "    body.design-{$pattern_id} .entry-content {\n";
    $css .= "        padding: 0;\n";
    $css .= "    }\n";
    $css .= "}\n";

    // 特定パターンの追加調整（完全レスポンシブ）
    if ($pattern_id === 'tk-design-card') {
        // カードの内部コンテンツサイズ制御
        $css .= ".design-{$pattern_id} .card-inner {\n";
        $css .= "    margin: 0 auto;\n";
        $css .= "    padding: 0 0.2rem;\n";
        $css .= "}\n";

        $css .= ".design-{$pattern_id} .entry,\n";
        $css .= ".design-{$pattern_id} .post {\n";
        $css .= "    padding: 1.5rem;\n";
        $css .= "    margin: 0 auto 1.5rem auto;\n";
        /* border-radius はデコレーションパターンで管理 */
        $css .= "    box-shadow: var(--card-shadow, 0 8px 24px rgba(0,0,0,0.08));\n";
        $css .= "    box-sizing: border-box;\n";
        $css .= "}\n";
    }

    // その他のパターンでは個別調整なし（共通で解決）
    if ($pattern_id === 'tk-design-stack' ||
        $pattern_id === 'tk-design-sticky' ||
        $pattern_id === 'tk-design-tiny') {
        // 共通設定で十分、個別調整不要
    }

    // サイドヘッダーデザインは削除済み

    // コンテナスタイル（border-radius, paddingなどの共通設定）
    if (isset($pattern['containers'])) {
        $containers = $pattern['containers'];

        // .meta-badgeやその他の小要素に適用
        $css .= ".design-{$pattern_id} .meta-badge {\n";
        if (isset($containers['border_radius'])) {
            $css .= "    border-radius: {$containers['border_radius']};\n";
        }
        $css .= "}\n";

        // article要素に border-radius を適用
        $css .= ".design-{$pattern_id} article,\n";
        $css .= ".design-{$pattern_id} .post,\n";
        $css .= ".design-{$pattern_id} .page {\n";
        if (isset($containers['border_radius'])) {
            $css .= "    border-radius: {$containers['border_radius']};\n";
        }
        if (isset($containers['box_shadow'])) {
            $css .= "    box-shadow: {$containers['box_shadow']};\n";
        }
        $css .= "}\n";

        // widgetにも適用
        $css .= ".design-{$pattern_id} .widget {\n";
        if (isset($containers['border_radius'])) {
            $css .= "    border-radius: {$containers['border_radius']};\n";
        }
        $css .= "}\n";
    }

    return $css;
}


