<?php
/**
 * レイアウト設定関連の機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * レイアウト設定CSSの動的出力
 */
function backbone_dynamic_layout_output() {
    $css = '';

    // サイドバーの幅設定に基づくコンテンツエリアの幅自動計算
    $sidebar_width_raw = get_theme_mod('sidebar_width', '300');
    
    if (is_numeric($sidebar_width_raw)) {
        $sidebar_width = intval($sidebar_width_raw);
    } else {
        $sidebar_width = 300; // デフォルト値
    }

    // サイドバー幅が有効な場合のみレイアウト調整
    if ($sidebar_width > 0) {
        // CSS変数としてサイドバー幅を出力
        $css .= "/* サイドバー幅のCSS変数定義 */\n";
        $css .= ":root {\n";
        $css .= "    --sidebar-width: {$sidebar_width}px;\n";
        $css .= "}\n\n";

        // ハードコード値完全削除：サイドバー幅のみ可変の真の動的レイアウト
        $css .= "/* .container完全上書き + main-content動的レイアウト */\n";
        $css .= "@media (min-width: 1280px) {\n";
        
        $css .= "    /* .container + 静的CSSの全max-width完全削除 - 最高優先度 */\n";
        $css .= "    html body .layout-three-columns .container,\n";
        $css .= "    html body .layout-two-columns .container,\n";
        $css .= "    html body .layout-three-columns.sidebar-right .main-content,\n";
        $css .= "    html body .layout-three-columns.sidebar-left .main-content,\n";
        $css .= "    html body .layout-two-columns.sidebar-right .main-content,\n";
        $css .= "    html body .layout-two-columns.sidebar-left .main-content {\n";
        $css .= "        max-width: none !important;\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* 中央配置専用ラッパー作成 */\n";
        $css .= "    html body .layout-three-columns .main-content,\n";
        $css .= "    html body .layout-two-columns .main-content {\n";
        $css .= "        display: flex !important;\n";
        $css .= "        justify-content: center !important;\n";
        $css .= "        align-items: flex-start !important;\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 40px 0 !important;\n";
        $css .= "        max-width: unset !important;\n";
        $css .= "        grid-template-columns: unset !important;\n";
        $css .= "        grid-template-areas: unset !important;\n";
        $css .= "        justify-items: unset !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* 内部グループ: コンテンツとサイドバーを一体化 */\n";
        $css .= "    html body .layout-three-columns .main-content::before {\n";
        $css .= "        content: '' !important;\n";
        $css .= "        display: none !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* フレックスアイテムとして30px間隔で配置 */\n";
        $css .= "    html body .layout-three-columns .content-area,\n";
        $css .= "    html body .layout-three-columns .sidebar-1,\n";
        $css .= "    html body .layout-three-columns .sidebar-2,\n";
        $css .= "    html body .layout-two-columns .content-area,\n";
        $css .= "    html body .layout-two-columns .sidebar-1 {\n";
        $css .= "        margin-right: 15px !important;\n";
        $css .= "        margin-left: 15px !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* 最初と最後の要素の外側マージン削除 */\n";
        $css .= "    html body .layout-three-columns .sidebar-1:first-child,\n";
        $css .= "    html body .layout-three-columns .sidebar-2:first-child,\n";
        $css .= "    html body .layout-two-columns .sidebar-1:first-child,\n";
        $css .= "    html body .layout-two-columns .content-area:first-child {\n";
        $css .= "        margin-left: 0 !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    html body .layout-three-columns .sidebar-1:last-child,\n";
        $css .= "    html body .layout-three-columns .sidebar-2:last-child,\n";
        $css .= "    html body .layout-two-columns .sidebar-1:last-child,\n";
        $css .= "    html body .layout-two-columns .content-area:last-child {\n";
        $css .= "        margin-right: 0 !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* サイドバー：設定値のみ */\n";
        $css .= "    html body .layout-three-columns .sidebar-1,\n";
        $css .= "    html body .layout-three-columns .sidebar-2,\n";
        $css .= "    html body .layout-two-columns .sidebar-1 {\n";
        $css .= "        width: {$sidebar_width}px !important;\n";
        $css .= "        flex: 0 0 {$sidebar_width}px !important;\n";
        $css .= "        grid-column: unset !important;\n";
        $css .= "        grid-area: unset !important;\n";
        $css .= "        justify-self: unset !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* コンテンツエリア：完全自動 + entry-content制限解除 */\n";
        $css .= "    html body .layout-three-columns .content-area,\n";
        $css .= "    html body .layout-two-columns .content-area {\n";
        $css .= "        flex: 1 !important;\n";
        $css .= "        width: auto !important;\n";
        $css .= "        min-width: unset !important;\n";
        $css .= "        max-width: unset !important;\n";
        $css .= "        grid-column: unset !important;\n";
        $css .= "        grid-area: unset !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* entry-content幅制限完全解除 - デザインユーティリティも上書き */\n";
        $css .= "    html body .layout-three-columns .entry-content,\n";
        $css .= "    html body .layout-two-columns .entry-content,\n";
        $css .= "    html body .layout-three-columns .content-area .entry-content,\n";
        $css .= "    html body .layout-two-columns .content-area .entry-content {\n";
        $css .= "        max-width: none !important;\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 10px !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* デザインパターンの上書きも含めて完全制御 */\n";
        $css .= "    html body[class*=\"design-\"] .layout-three-columns .entry-content,\n";
        $css .= "    html body[class*=\"design-\"] .layout-two-columns .entry-content {\n";
        $css .= "        max-width: none !important;\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 10px !important;\n";
        $css .= "    }\n\n";
        
        $css .= "    /* 要素順序の制御 */\n";
        // 3カラム：grid-areaで制御するため、orderは不要（layout-three-columns.cssで定義）

        // 2カラム：grid-areaを使わないため、orderで制御
        // 2カラム：sidebar-right = サイドバー1を右側に配置
        $css .= "    html body .layout-two-columns.sidebar-right .content-area { order: 1 !important; }\n";
        $css .= "    html body .layout-two-columns.sidebar-right .sidebar-1 { order: 2 !important; }\n";
        // 2カラム：sidebar-left = サイドバー1を左側に配置
        $css .= "    html body .layout-two-columns.sidebar-left .sidebar-1 { order: 1 !important; }\n";
        $css .= "    html body .layout-two-columns.sidebar-left .content-area { order: 2 !important; }\n\n";
        
        $css .= "}\n\n";

        // レイアウトに応じてコンテンツエリアの幅を自動計算（投稿タイプ別設定を優先）
        $site_layout = backbone_get_layout();

        if ($site_layout === 'two-columns') {
            // 2カラムレイアウト：コンテンツエリア + 1つのサイドバーとして計算
            // 1280px - サイドバー幅 - 余白(40px) = コンテンツエリアの幅
            $content_width = 1280 - $sidebar_width - 40;
            $content_width = max(800, $content_width); // 最小800px（コンテンツエリアを広く）

            $css .= "/* 2カラムレイアウト：サイドバー幅に基づく自動調整 */\n";
            $css .= "body .layout-two-columns .content-area,\n";
            $css .= "html body .layout-two-columns .content-area {\n";
            $css .= "    width: 100% !important;\n";
            $css .= "    max-width: none !important;\n";
            $css .= "}\n\n";

            // 2カラムレイアウト用のサイドバー幅設定
            $css .= "/* 2カラムレイアウト用サイドバー幅設定 */\n";
            $css .= "body .layout-two-columns .sidebar-1,\n";
            $css .= "html body .layout-two-columns .sidebar-1,\n";
            $css .= "body .layout-two-columns .sidebar,\n";
            $css .= "html body .layout-two-columns .sidebar {\n";
            $css .= "    width: {$sidebar_width}px !important;\n";
            $css .= "    min-width: {$sidebar_width}px !important;\n";
            $css .= "    max-width: {$sidebar_width}px !important;\n";
            $css .= "    flex: 0 0 {$sidebar_width}px !important;\n";
            $css .= "}\n\n";

        } elseif ($site_layout === 'three-columns') {
            // 3カラムレイアウト：狭いサイドバー（10px程度）の場合は間隔を詰める
            if ($sidebar_width <= 50) {
                // 狭いサイドバーの場合：完全に密接配置
                $css .= "/* 3カラムレイアウト：狭いサイドバー用の密接配置 */\n";
                $css .= "body .layout-three-columns .main-content,\n";
                $css .= "html body .layout-three-columns .main-content {\n";
                $css .= "    gap: 0px !important;\n";
                $css .= "    max-width: none !important;\n";
                $css .= "    width: 100% !important;\n";
                $css .= "    margin: 0 !important;\n";
                $css .= "    display: flex !important;\n";
                $css .= "    align-items: flex-start !important;\n";
                $css .= "    justify-content: flex-start !important;\n";
                $css .= "    align-content: flex-start !important;\n";
                $css .= "}\n\n";
                
                $css .= "/* コンテナとグリッド設定の制限を解除 */\n";
                $css .= "body .layout-three-columns .container,\n";
                $css .= "html body .layout-three-columns .container {\n";
                $css .= "    max-width: none !important;\n";
                $css .= "    width: 100% !important;\n";
                $css .= "    margin: 0 !important;\n";
                $css .= "    padding: 0 !important;\n";
                $css .= "}\n\n";
                
                $css .= "/* サイト全体のwrapper制限も解除 */\n";
                $css .= "body .layout-three-columns .site-wrapper,\n";
                $css .= "body .layout-three-columns .site,\n";
                $css .= "body .layout-three-columns .site-main,\n";
                $css .= "html body .layout-three-columns .site-wrapper,\n";
                $css .= "html body .layout-three-columns .site,\n";
                $css .= "html body .layout-three-columns .site-main {\n";
                $css .= "    max-width: none !important;\n";
                $css .= "    width: 100% !important;\n";
                $css .= "    margin: 0 !important;\n";
                $css .= "    padding: 0 !important;\n";
                $css .= "}\n\n";
                
                $css .= "/* html/body レベルでも制限解除 */\n";
                $css .= "body.layout-three-columns,\n";
                $css .= "html body.layout-three-columns {\n";
                $css .= "    margin: 0 !important;\n";
                $css .= "    padding: 0 !important;\n";
                $css .= "    overflow-x: visible !important;\n";
                $css .= "}\n\n";
                
                $css .= "/* デザインパターンによる制限も解除 */\n";
                $css .= ".design-tk-design-picasso .layout-three-columns .container,\n";
                $css .= ".design-tk-design-picasso .layout-three-columns .main-content {\n";
                $css .= "    max-width: none !important;\n";
                $css .= "    width: 100% !important;\n";
                $css .= "    margin: 0 !important;\n";
                $css .= "    padding: 0 !important;\n";
                $css .= "    box-sizing: border-box !important;\n";
                $css .= "}\n\n";
                
                $css .= "/* 静的CSSのグリッド設定を上書き */\n";
                $css .= "@media (min-width: 1280px) {\n";
                $css .= "    body .layout-three-columns.sidebar-right .main-content,\n";
                $css .= "    body .layout-three-columns.sidebar-left .main-content,\n";
                $css .= "    html body .layout-three-columns.sidebar-right .main-content,\n";
                $css .= "    html body .layout-three-columns.sidebar-left .main-content {\n";
                $css .= "        max-width: none !important;\n";
                $css .= "        width: 100% !important;\n";
                $css .= "        margin: 0 !important;\n";
                $css .= "        gap: 0px !important;\n";
                $css .= "        display: flex !important;\n";
                $css .= "        justify-content: flex-start !important;\n";
                $css .= "        align-content: flex-start !important;\n";
                $css .= "        grid-template-columns: unset !important;\n";
                $css .= "        grid-template-areas: unset !important;\n";
                $css .= "    }\n";
                $css .= "}\n\n";
                
                $css .= "/* 狭いサイドバー用の幅設定 */\n";
                $css .= "body .layout-three-columns .sidebar-1,\n";
                $css .= "body .layout-three-columns .sidebar-2,\n";
                $css .= "html body .layout-three-columns .sidebar-1,\n";
                $css .= "html body .layout-three-columns .sidebar-2 {\n";
                $css .= "    width: {$sidebar_width}px !important;\n";
                $css .= "    min-width: {$sidebar_width}px !important;\n";
                $css .= "    max-width: {$sidebar_width}px !important;\n";
                $css .= "    flex: 0 0 {$sidebar_width}px !important;\n";
                $css .= "    margin: 0 !important;\n";
                $css .= "}\n\n";
                
                $css .= "/* コンテンツエリアは残り幅を使用 */\n";
                $css .= "body .layout-three-columns .content-area,\n";
                $css .= "html body .layout-three-columns .content-area {\n";
                $css .= "    width: auto !important;\n";
                $css .= "    max-width: none !important;\n";
                $css .= "    flex: 1 !important;\n";
                $css .= "    margin: 0 !important;\n";
                $css .= "}\n\n";
                
                $css .= "/* PCサイズでの順序設定 */\n";
                $css .= "@media (min-width: 1280px) {\n";
                $css .= "    body .layout-three-columns.sidebar-right .main-content {\n";
                $css .= "        flex-direction: row !important;\n";
                $css .= "    }\n";
                $css .= "    body .layout-three-columns.sidebar-right .sidebar-1 {\n";
                $css .= "        order: 1 !important;\n";
                $css .= "    }\n";
                $css .= "    body .layout-three-columns.sidebar-right .content-area {\n";
                $css .= "        order: 2 !important;\n";
                $css .= "    }\n";
                $css .= "    body .layout-three-columns.sidebar-right .sidebar-2 {\n";
                $css .= "        order: 3 !important;\n";
                $css .= "    }\n";
                $css .= "}\n\n";
            } else {
                // 通常のサイドバー幅の場合の計算
                $content_width = 1280 - ($sidebar_width * 2) - 40;
                $content_width = max(500, $content_width);

                $css .= "/* 3カラムレイアウト：サイドバー幅に基づく自動調整 */\n";
                $css .= "body .layout-three-columns .content-area,\n";
                $css .= "html body .layout-three-columns .content-area {\n";
                $css .= "    width: {$content_width}px !important;\n";
                $css .= "    max-width: {$content_width}px !important;\n";
                $css .= "}\n\n";

                // 3カラムレイアウト用のサイドバー幅設定
                $css .= "/* 3カラムレイアウト用サイドバー幅設定 */\n";
                $css .= "body .layout-three-columns .sidebar-1,\n";
                $css .= "body .layout-three-columns .sidebar-2,\n";
                $css .= "html body .layout-three-columns .sidebar-1,\n";
                $css .= "html body .layout-three-columns .sidebar-2 {\n";
                $css .= "    width: {$sidebar_width}px !important;\n";
                $css .= "    min-width: {$sidebar_width}px !important;\n";
                $css .= "    max-width: {$sidebar_width}px !important;\n";
                $css .= "}\n\n";
            }
        }

        // サイドバーの幅設定
        $css .= "/* サイドバーの幅設定 */\n";
        $css .= "body .sidebar,\n";
        $css .= "html body .sidebar {\n";
        $css .= "    width: {$sidebar_width}px !important;\n";
        $css .= "    flex: 0 0 {$sidebar_width}px !important;\n";
        $css .= "}\n\n";

        // 既存のCSSファイルの設定を確実に上書き
        $css .= "/* 既存CSSファイルの設定を上書き */\n";
        $css .= "body .layout-full-width .content-area,\n";
        $css .= "html body .layout-full-width .content-area {\n";
        $css .= "    width: 100% !important;\n";
        $css .= "}\n\n";
    }

    // フルワイドレイアウト：スマホサイズでの右側隙間解消
    $css .= "/* フルワイドレイアウト：スマホサイズでの右側隙間解消 */\n";
    $css .= "@media (max-width: 767px) {\n";
        $css .= "    body.layout-full-width,\n";
        $css .= "    html.layout-full-width {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        /* ヘッダー部分の縦スクロールを防止 */\n";
        $css .= "        overflow: visible !important;\n";
        $css .= "        overflow-x: visible !important;\n";
        $css .= "        overflow-y: visible !important;\n";
        $css .= "    }\n\n";
        $css .= "    .layout-full-width .site-wrapper,\n";
        $css .= "    .layout-full-width .site,\n";
        $css .= "    .layout-full-width .site-main {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        /* ヘッダー部分の縦スクロールを防止 */\n";
        $css .= "        overflow: visible !important;\n";
        $css .= "        overflow-x: visible !important;\n";
        $css .= "        overflow-y: visible !important;\n";
        $css .= "    }\n\n";
        $css .= "    .layout-full-width .entry-content,\n";
        $css .= "    .layout-full-width .entry-header,\n";
        $css .= "    .layout-full-width .content-area,\n";
        $css .= "    .layout-full-width .main-content,\n";
        $css .= "    .layout-full-width .container {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        left: 0 !important;\n";
        $css .= "        right: 0 !important;\n";
        $css .= "        position: relative !important;\n";
        $css .= "        box-sizing: border-box !important;\n";
        $css .= "        overflow: visible !important;\n";
        $css .= "        overflow-x: visible !important;\n";
        $css .= "        overflow-y: visible !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：タブレットサイズでの右側隙間完全解消
        $css .= "/* フルワイドレイアウト：タブレットサイズでの右側隙間完全解消 */\n";
        $css .= "@media (min-width: 768px) and (max-width: 1279px) {\n";
        $css .= "    .layout-full-width {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        overflow: visible !important;\n";
        $css .= "        overflow-x: visible !important;\n";
        $css .= "        overflow-y: visible !important;\n";
        $css .= "    }\n\n";
        $css .= "    .layout-full-width .site-wrapper,\n";
        $css .= "    .layout-full-width .site,\n";
        $css .= "    .layout-full-width .site-main {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        overflow: visible !important;\n";
        $css .= "        overflow-x: visible !important;\n";
        $css .= "        overflow-y: visible !important;\n";
        $css .= "    }\n\n";
        $css .= "    .layout-full-width .entry-content,\n";
        $css .= "    .layout-full-width .entry-header,\n";
        $css .= "    .layout-full-width .content-area,\n";
        $css .= "    .layout-full-width .main-content,\n";
        $css .= "    .layout-full-width .container {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        left: 0 !important;\n";
        $css .= "        right: auto !important;\n";
        $css .= "        position: relative !important;\n";
        $css .= "        box-sizing: border-box !important;\n";
        $css .= "        overflow: visible !important;\n";
        $css .= "    }\n\n";
        $css .= "    body.layout-full-width,\n";
        $css .= "    html.layout-full-width {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 !important;\n";
        $css .= "        overflow: visible !important;\n";
        $css .= "        overflow-x: visible !important;\n";
        $css .= "        overflow-y: visible !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：site-wrapperの縦スクロール防止
        $css .= "/* フルワイドレイアウト：site-wrapperの縦スクロール防止 */\n";
        $css .= ".layout-full-width .site-wrapper {\n";
        $css .= "    /* ヘッダー部分の縦スクロールを防止 */\n";
        $css .= "    min-height: auto !important;\n";
        $css .= "    height: auto !important;\n";
        $css .= "    overflow: visible !important;\n";
        $css .= "    overflow-y: visible !important;\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：htmlとbodyの高さ制限を解除
        $css .= "/* フルワイドレイアウト：htmlとbodyの高さ制限を解除 */\n";
        $css .= ".layout-full-width html,\n";
        $css .= ".layout-full-width body {\n";
        $css .= "    height: auto !important;\n";
        $css .= "    min-height: auto !important;\n";
        $css .= "    overflow: visible !important;\n";
        $css .= "    overflow-y: visible !important;\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：ナビゲーションメニューのサブメニュー表示修正
        $css .= "/* フルワイドレイアウト：ナビゲーションメニューのサブメニュー表示修正 */\n";
        $css .= ".layout-full-width .main-navigation {\n";
        $css .= "    /* フルワイドレイアウトでのナビゲーション表示修正 */\n";
        $css .= "    position: relative !important;\n";
        $css .= "    z-index: 99999 !important;\n";
        $css .= "}\n\n";

        $css .= ".layout-full-width .main-navigation ul {\n";
        $css .= "    /* フルワイドレイアウトでのメインメニュー表示修正 */\n";
        $css .= "    position: relative !important;\n";
        $css .= "    z-index: 99999 !important;\n";
        $css .= "}\n\n";

        $css .= ".layout-full-width .main-navigation .sub-menu {\n";
        $css .= "    /* フルワイドレイアウトでのサブメニュー表示修正 */\n";
        $css .= "    z-index: 999999 !important;\n";
        $css .= "    /* メインコンテンツにめり込まないようにする */\n";
        $css .= "    position: absolute !important;\n";
        $css .= "    top: 100% !important;\n";
        $css .= "    left: 0 !important;\n";
        $css .= "    /* 背景とボーダーは削除（個別項目のみに背景を適用） */\n";
        $css .= "    box-shadow: none !important;\n";
        $css .= "    border-radius: 0 !important;\n";
        $css .= "    background: transparent !important;\n";
        $css .= "    border: none !important;\n";
        $css .= "}\n\n";

        $css .= ".layout-full-width .main-navigation ul.active {\n";
        $css .= "    /* フルワイドレイアウトでのハンバーガーメニュー表示修正 */\n";
        $css .= "    z-index: 999999 !important;\n";
        $css .= "    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：ヘッダー表示の修正
        $css .= "/* フルワイドレイアウト：ヘッダー表示の修正 */\n";
        $css .= ".layout-full-width .site-header {\n";
        $css .= "    width: 100% !important;\n";
        $css .= "    position: relative !important;\n";
        $css .= "    left: 0 !important;\n";
        $css .= "    right: 0 !important;\n";
        $css .= "    margin: 0 !important;\n";
        $css .= "    overflow: visible !important;\n";
        $css .= "    overflow-x: visible !important;\n";
        $css .= "    overflow-y: visible !important;\n";
        $css .= "    /* ヘッダーの縦スクロールを防止 */\n";
        $css .= "    min-height: auto !important;\n";
        $css .= "    max-height: none !important;\n";
        $css .= "}\n\n";

        $css .= ".layout-full-width .site-header .container {\n";
        $css .= "    width: 100% !important;\n";
        $css .= "    margin: 0 auto !important;\n";
        $css .= "    padding: 0 40px !important;\n";
        $css .= "    box-sizing: border-box !important;\n";
        $css .= "    overflow: visible !important;\n";
        $css .= "    overflow-y: visible !important;\n";
        $css .= "    /* ヘッダーコンテナの縦スクロールを防止 */\n";
        $css .= "    min-height: auto !important;\n";
        $css .= "    max-height: none !important;\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：PCサイズでのヘッダー完全フルワイド化
        $css .= "/* フルワイドレイアウト：PCサイズでのヘッダー完全フルワイド化 */\n";
        $css .= "@media (min-width: 1280px) {\n";
        $css .= "    .layout-full-width .site-header .container {\n";
        $css .= "        max-width: none !important;\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        padding: 0 40px !important;\n";
        $css .= "        /* PCサイズでは左右に完全に広がる */\n";
        $css .= "        box-sizing: border-box !important;\n";
        $css .= "    }\n\n";
        $css .= "    .layout-full-width .site-header {\n";
        $css .= "        /* ヘッダー全体もフルワイド化 */\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        max-width: none !important;\n";
        $css .= "        margin: 0 !important;\n";
        $css .= "        /* 通常のヘッダーと同じ高さを維持 */\n";
        $css .= "        left: 0 !important;\n";
        $css .= "        right: 0 !important;\n";
        $css .= "    }\n\n";
        $css .= "    .layout-full-width .site-header .header-content {\n";
        $css .= "        /* ヘッダーコンテンツの高さを通常と同じに維持 */\n";
        $css .= "        display: flex !important;\n";
        $css .= "        justify-content: space-between !important;\n";
        $css .= "        align-items: flex-start !important;\n";
        $css .= "        position: relative !important;\n";
        $css .= "        z-index: 1 !important;\n";
        $css .= "    }\n\n";
        $css .= "    .layout-full-width .site-header .site-branding {\n";
        $css .= "        /* サイトブランディングの高さを通常と同じに維持 */\n";
        $css .= "        display: block !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        $css .= ".layout-full-width .site-header .header-content {\n";
        $css .= "    /* ヘッダーコンテンツの縦スクロールを防止 */\n";
        $css .= "    overflow: visible !important;\n";
        $css .= "    overflow-y: visible !important;\n";
        $css .= "    min-height: auto !important;\n";
        $css .= "    max-height: none !important;\n";
        $css .= "}\n\n";

        $css .= ".layout-full-width .site-header .site-branding {\n";
        $css .= "    /* サイトブランディングの縦スクロールを防止 */\n";
        $css .= "    overflow: visible !important;\n";
        $css .= "    overflow-y: visible !important;\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：タブレットサイズでのヘッダー表示修正
        $css .= "/* フルワイドレイアウト：タブレットサイズでのヘッダー表示修正 */\n";
        $css .= "@media (max-width: 1279px) {\n";
        $css .= "    .layout-full-width .site-header .container {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        padding: 0 20px !important;\n";
        $css .= "        box-sizing: border-box !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        // フルワイドレイアウト：スマホサイズでのヘッダー表示修正
        $css .= "/* フルワイドレイアウト：スマホサイズでのヘッダー表示修正 */\n";
        $css .= "@media (max-width: 767px) {\n";
        $css .= "    .layout-full-width .site-header .container {\n";
        $css .= "        width: 100% !important;\n";
        $css .= "        padding: 0 15px !important;\n";
        $css .= "        box-sizing: border-box !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        // 最高優先度での上書き
        $css .= "/* 最高優先度での上書き設定 */\n";
        $css .= "body .content-area,\n";
        $css .= "html body .content-area {\n";
        $css .= "    width: 100% !important;\n";
        $css .= "}\n\n";

    // ヘッダーメッセージの表示
    $header_message = get_theme_mod('header_message', '');
    if (!empty($header_message)) {
        $css .= "/* ヘッダーメッセージの表示 */\n";
        $css .= ".header-message {\n";
        $css .= "    display: block !important;\n";
        $css .= "    padding: 1rem;\n";
        $css .= "    margin: 1rem 0;\n";
        $css .= "    background: var(--background-secondary);\n";
        $css .= "    border: 1px solid var(--border-color);\n";
        $css .= "    border-radius: 4px;\n";
        $css .= "}\n\n";
    }

    // フッターメッセージの表示
    $footer_message = get_theme_mod('footer_message', '');
    if (!empty($footer_message)) {
        $css .= "/* フッターメッセージの表示 */\n";
        $css .= ".footer-message {\n";
        $css .= "    display: block !important;\n";
        $css .= "    padding: 1rem;\n";
        $css .= "    margin: 1rem 0;\n";
        $css .= "    color: var(--text-light);\n";
        $css .= "    text-align: center;\n";
        $css .= "}\n\n";
    }

    // 1カラムレイアウト時のコンテンツ最大幅
    $single_column_max_width = get_theme_mod('single_column_max_width', '1200');
    if ($single_column_max_width > 0) {
        $css .= "/* 1カラムレイアウト時のコンテンツ最大幅 - 全デザインパターンに対応 */\n";
        $css .= "@media (min-width: 1280px) {\n";
        $css .= "    html body.layout-single-column .main-content,\n";
        $css .= "    html body.layout-single-column .content-area,\n";
        $css .= "    html body[class*=\"design-\"].layout-single-column .main-content,\n";
        $css .= "    html body[class*=\"design-\"].layout-single-column .content-area {\n";
        $css .= "        max-width: {$single_column_max_width}px !important;\n";
        $css .= "        margin-left: auto !important;\n";
        $css .= "        margin-right: auto !important;\n";
        $css .= "    }\n";
        $css .= "    \n";
        $css .= "    /* フロントページのセクションも連動 */\n";
        $css .= "    html body.layout-single-column .hero-description,\n";
        $css .= "    html body.layout-single-column .posts-list-section,\n";
        $css .= "    html body.layout-single-column .pickup-section,\n";
        $css .= "    html body.layout-single-column .services-section,\n";
        $css .= "    html body.layout-single-column .free-content-section {\n";
        $css .= "        max-width: {$single_column_max_width}px !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";
    }

    // ブロックウィジェットのmin-height問題を解決
    $css .= "/* ブロックウィジェットのmin-heightオーバーライド */\n";
    $css .= ".widget.widget_block,\n";
    $css .= ".site-footer .widget.widget_block,\n";
    $css .= ".sidebar .widget.widget_block,\n";
    $css .= ".footer-widgets .widget.widget_block {\n";
    $css .= "    min-height: auto !important;\n";
    $css .= "    height: auto !important;\n";
    $css .= "}\n\n";

    if (!empty($css)) {
        echo '<style type="text/css" id="dynamic-layout-css">' . "\n";
        echo $css;
        echo '</style>' . "\n";

        // デバッグ用：設定値の確認
        echo "<!-- レイアウト設定デバッグ -->\n";
        echo "<!-- sidebar_width (raw): " . $sidebar_width_raw . " -->\n";
        echo "<!-- sidebar_width (processed): " . $sidebar_width . " -->\n";
        echo "<!-- is_numeric check: " . (is_numeric($sidebar_width_raw) ? 'true' : 'false') . " -->\n";
        echo "<!-- 適用されるレイアウト: " . backbone_get_layout() . " -->\n";
        echo "<!-- 計算されたコンテンツエリア幅: " . (isset($content_width) ? $content_width : 'N/A') . " -->\n";
        echo "<!-- 生成されたCSS: " . strlen($css) . " 文字 -->\n";
    }
}
add_action('wp_head', 'backbone_dynamic_layout_output', 999);
