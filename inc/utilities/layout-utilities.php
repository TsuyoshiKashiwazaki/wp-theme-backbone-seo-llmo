<?php
/**
 * レイアウト関連のユーティリティ関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * レイアウト取得関数（投稿タイプ別対応）
 */
function backbone_get_layout() {
    // 配布テーマ用：厳重なバリデーション付きレイアウト取得
    $valid_layouts = array('single-column', 'two-columns', 'three-columns', 'full-width');

    // 個別投稿・固定ページのレイアウト設定をチェック（最優先）
    if (is_singular() || (is_home() && get_option('show_on_front') === 'page')) {
        $queried_object_id = get_queried_object_id();
        if ($queried_object_id) {
            // 新しいキーを優先
            $meta_layout = get_post_meta($queried_object_id, '_backbone_layout_settings', true);
            
            // 古いキーのフォールバック（互換性維持）
            if (empty($meta_layout)) {
                $meta_layout = get_post_meta($queried_object_id, '_backbone_page_layout', true);
            }
            
            if (!empty($meta_layout) && $meta_layout !== 'default' && in_array($meta_layout, $valid_layouts)) {
                return $meta_layout;
            }
        }
    }

    $raw_site_layout = get_theme_mod('site_layout', backbone_get_default_layout());
    $default_layout = in_array($raw_site_layout, $valid_layouts) ? $raw_site_layout : backbone_get_default_layout();

    // 現在のページタイプを判定
    $current_type = '';

    // タクソノミールートページ（/tag/, /category/）は最優先で「その他のアーカイブ」扱い
    if (get_query_var('taxonomy_root')) {
        $current_type = 'archive';
    } elseif (is_home() || is_front_page()) {
        // URLパス解析でカスタム投稿タイプアーカイブの可能性をチェック
        $request_uri = $_SERVER['REQUEST_URI'];
        $path_parts = explode('/', trim($request_uri, '/'));

        // パターン: /親/投稿タイプ/ の形式をチェック
        if (count($path_parts) >= 2) {
            $potential_post_type = $path_parts[count($path_parts) - 1]; // 最後の部分
            $custom_post_types = get_post_types(array('public' => true, '_builtin' => false));

            // 空文字列やクエリパラメータをフィルタ
            $potential_post_type = trim($potential_post_type);
            if (!empty($potential_post_type) && in_array($potential_post_type, $custom_post_types)) {
                $current_type = $potential_post_type;
            } else {
                $current_type = (is_home() && !is_front_page()) ? 'home' : 'front_page';
            }
        } else {
            $current_type = (is_home() && !is_front_page()) ? 'home' : 'front_page';
        }
    } elseif (is_single()) {
        $post_type = get_post_type();
        $current_type = $post_type;
    } elseif (is_page()) {
        $current_type = get_post_type(); // 'page' のハードコード排除
    } elseif (is_attachment()) {
        $current_type = 'attachment';
    } elseif (is_category()) {
        $current_type = 'category';
    } elseif (is_tag()) {
        $current_type = 'tag';
    } elseif (is_archive()) {
        // カスタム投稿タイプのアーカイブかチェック（複数の方法で検出）
        $post_type = get_query_var('post_type');

        if (is_post_type_archive() && !empty($post_type)) {
            // 標準的なカスタム投稿タイプアーカイブ
            $current_type = $post_type;
        } elseif (!empty($post_type)) {
            // クエリ変数にpost_typeが設定されている場合
            $current_type = $post_type;
        } else {
            // 他のアーカイブタイプを確認
            $queried_object = get_queried_object();
            if ($queried_object && isset($queried_object->query_var)) {
                // カスタム投稿タイプのアーカイブページの可能性
                $current_type = $queried_object->query_var;
            } else {
                $current_type = 'archive'; // 汎用的なアーカイブ
            }
        }
    } elseif (is_search()) {
        $current_type = 'search';
    } else {
        $current_type = get_post_type() ?: 'post'; // 動的取得、フォールバック付き
    }

    // 投稿タイプ別の設定を取得
    $post_type_layout = get_theme_mod("post_type_layout_{$current_type}", 'inherit');

    // 設定値のバリデーション（上記で定義済み）

    // 'inherit'の場合は全体設定を使用
    if ($post_type_layout === 'inherit') {
        $final_layout = $default_layout;
    } else {
        // 個別設定が有効な値かチェック
        $final_layout = in_array($post_type_layout, $valid_layouts) ? $post_type_layout : $default_layout;
    }

    // 最終的な値もバリデーション
    $final_layout = in_array($final_layout, $valid_layouts) ? $final_layout : 'two-columns';

    return $final_layout;
}

/**
 * サイドバー表示判定
 */
function backbone_has_sidebar() {
    $layout = backbone_get_layout();
    $layouts_with_sidebar = array('two-columns', 'three-columns');
    return in_array($layout, $layouts_with_sidebar);
}

/**
 * レイアウト判定のヘルパー関数（ハードコード排除）
 */
function backbone_is_layout($check_layout) {
    return backbone_get_layout() === $check_layout;
}

function backbone_is_three_columns() {
    return backbone_is_layout('three-columns');
}

function backbone_is_two_columns() {
    return backbone_is_layout('two-columns');
}

function backbone_is_single_column() {
    return backbone_is_layout('single-column');
}

function backbone_is_full_width() {
    return backbone_is_layout('full-width');
}
