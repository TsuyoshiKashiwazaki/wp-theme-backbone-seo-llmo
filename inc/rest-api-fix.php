<?php
/**
 * REST API JSONエラー修正
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST API実行前に出力バッファをクリア
 */
function kashiwazaki_searchcraft_rest_api_init() {
    // REST APIリクエストの場合のみ
    if (defined('REST_REQUEST') && REST_REQUEST) {
        // 既存の出力をクリア
        if (ob_get_level()) {
            ob_clean();
        }
    }
}
add_action('rest_api_init', 'kashiwazaki_searchcraft_rest_api_init', 1);

/**
 * init時に出力バッファを開始
 */
function kashiwazaki_searchcraft_buffer_start() {
    // 管理画面でのみ
    if (is_admin()) {
        ob_start();
    }
}
add_action('init', 'kashiwazaki_searchcraft_buffer_start', 1);

/**
 * shutdown時にバッファをフラッシュ
 */
function kashiwazaki_searchcraft_buffer_end() {
    // 管理画面でのみ
    if (is_admin() && ob_get_level()) {
        ob_end_flush();
    }
}
add_action('shutdown', 'kashiwazaki_searchcraft_buffer_end', 100);

/**
 * REST APIレスポンスの前に出力をクリア
 */
function kashiwazaki_searchcraft_pre_serve_request($served, $result, $request, $server) {
    // まだ送信されていない場合
    if (!$served) {
        // 出力バッファをクリア
        while (ob_get_level()) {
            ob_end_clean();
        }
    }
    return $served;
}
add_filter('rest_pre_serve_request', 'kashiwazaki_searchcraft_pre_serve_request', 10, 4);

/**
 * デバッグ出力を無効化
 */
function kashiwazaki_searchcraft_disable_debug_on_rest() {
    if (defined('REST_REQUEST') && REST_REQUEST) {
        // REST API実行中はデバッグ表示を無効化
        @ini_set('display_errors', 0);
        @error_reporting(0);
    }
}
add_action('rest_api_init', 'kashiwazaki_searchcraft_disable_debug_on_rest', 0);