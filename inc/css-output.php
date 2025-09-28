<?php
/**
 * CSS出力関連の機能 - インデックスファイル
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * モジュール化されたCSS出力機能を読み込み
 */

// Bodyクラス関連機能
require_once get_template_directory() . '/inc/css-body-classes.php';

// カスタム色設定機能
require_once get_template_directory() . '/inc/css-colors.php';

// タイポグラフィ機能
require_once get_template_directory() . '/inc/css-typography.php';

// パターン関連機能
require_once get_template_directory() . '/inc/css-patterns.php';

// テーマ関連機能
require_once get_template_directory() . '/inc/css-themes.php';

// レイアウト関連機能
require_once get_template_directory() . '/inc/css-layout.php';