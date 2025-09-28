<?php
/**
 * Bodyクラス関連の機能
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * body クラスに設定を追加（配布テーマ用：不正値の除去機能付き）
 */
function backbone_body_classes($classes) {
    // 配布テーマ用：不正なレイアウトクラスを除去
    $classes = array_filter($classes, function($class) {
        return !preg_match('/^layout-(flex|flexbox|grid|float)$/', $class);
    });
    $color_theme = get_theme_mod('color_theme', 'none');
    $design_pattern = get_theme_mod('design_pattern', 'none');
    $text_pattern = get_theme_mod('text_pattern', 'none');

    // 配布テーマ用：site_layout の厳重バリデーション
    $valid_layouts = array('single-column', 'two-columns', 'three-columns', 'full-width');
    $raw_site_layout = get_theme_mod('site_layout', backbone_get_default_layout());
    $site_layout = in_array($raw_site_layout, $valid_layouts) ? $raw_site_layout : backbone_get_default_layout();
    $sidebar_position = get_theme_mod('sidebar_position', 'right');

    // カラーテーマクラス
    if ($color_theme !== 'none') {
        $classes[] = 'color-theme-' . str_replace('-', '-', $color_theme);
    }

    // デザインパターンクラス
    if ($design_pattern !== 'none') {
        $classes[] = 'design-' . str_replace('-', '-', $design_pattern);
    }

        // タイポグラフィパターンクラス
    if ($text_pattern !== 'none') {
        $classes[] = 'typo-' . str_replace('-', '-', $text_pattern);
    }

    // デコレーションパターンクラス
    $decoration_pattern = get_theme_mod('decoration_pattern', 'none');
    if ($decoration_pattern !== 'none') {
        $classes[] = 'decoration-' . str_replace('-', '-', $decoration_pattern);
    }

    // サブディレクトリ設定を確認して上書き
    if (function_exists('backbone_get_current_subdirectory_design_settings')) {
        $subdirectory_settings = backbone_get_current_subdirectory_design_settings();
        if ($subdirectory_settings) {
            // サブディレクトリのデコレーションパターンがある場合、通常のクラスを削除して新しいクラスを追加
            if (!empty($subdirectory_settings['decoration_pattern']) && $subdirectory_settings['decoration_pattern'] !== 'none') {
                // 既存のデコレーションクラスを削除
                $classes = array_filter($classes, function($class) {
                    return strpos($class, 'decoration-') !== 0;
                });
                // サブディレクトリのデコレーションクラスを追加
                $classes[] = 'decoration-' . str_replace('-', '-', $subdirectory_settings['decoration_pattern']);
            }

            // 他のサブディレクトリ設定も同様に処理（カラーテーマ、デザインパターン、タイポグラフィ）
            if (!empty($subdirectory_settings['color_theme']) && $subdirectory_settings['color_theme'] !== 'none') {
                $classes = array_filter($classes, function($class) {
                    return strpos($class, 'color-theme-') !== 0;
                });
                $classes[] = 'color-theme-' . str_replace('-', '-', $subdirectory_settings['color_theme']);
            }

            if (!empty($subdirectory_settings['design_pattern']) && $subdirectory_settings['design_pattern'] !== 'none') {
                $classes = array_filter($classes, function($class) {
                    return strpos($class, 'design-') !== 0;
                });
                $classes[] = 'design-' . str_replace('-', '-', $subdirectory_settings['design_pattern']);
            }

            if (!empty($subdirectory_settings['text_pattern']) && $subdirectory_settings['text_pattern'] !== 'none') {
                $classes = array_filter($classes, function($class) {
                    return strpos($class, 'typo-') !== 0;
                });
                $classes[] = 'typo-' . str_replace('-', '-', $subdirectory_settings['text_pattern']);
            }
        }
    }

    // 実際に使用されるレイアウトクラス（投稿タイプ別設定を反映）
    $actual_layout = backbone_get_layout();

    // レイアウト値のバリデーション（上記で実施済み）
    $actual_layout = in_array($actual_layout, $valid_layouts) ? $actual_layout : 'two-columns';



    $classes[] = 'layout-' . str_replace('_', '-', $actual_layout);

    // サイドバー位置クラス
    $classes[] = 'sidebar-' . $sidebar_position;

    // 現在のページタイプクラス（backbone_get_layout()と同じロジックを使用）
    $page_type = '';

    if (is_home() || is_front_page()) {
        // URLパス解析でカスタム投稿タイプアーカイブの可能性をチェック（utilities.phpと同じロジック）
        $request_uri = $_SERVER['REQUEST_URI'];
        $path_parts = explode('/', trim($request_uri, '/'));

                // パターン: /親/投稿タイプ/ の形式をチェック
        if (count($path_parts) >= 2) {
            $potential_post_type = $path_parts[count($path_parts) - 1]; // 最後の部分
            $custom_post_types = get_post_types(array('public' => true, '_builtin' => false));

            // 空文字列やクエリパラメータをフィルタ
            $potential_post_type = trim($potential_post_type);
            if (!empty($potential_post_type) && in_array($potential_post_type, $custom_post_types)) {
                $page_type = $potential_post_type;
            } else {
                $page_type = (is_home() && !is_front_page()) ? 'home' : 'front_page';
            }
        } else {
            $page_type = (is_home() && !is_front_page()) ? 'home' : 'front_page';
        }
    } elseif (is_single()) {
        $page_type = get_post_type();
    } elseif (is_page()) {
        $page_type = get_post_type(); // 動的取得
    } elseif (is_attachment()) {
        $page_type = 'attachment';
    } elseif (is_category()) {
        $page_type = 'category';
    } elseif (is_tag()) {
        $page_type = 'tag';
    } elseif (is_archive()) {
        // カスタム投稿タイプのアーカイブかチェック（utilities.phpと同じロジック）
        $post_type = get_query_var('post_type');

        if (is_post_type_archive() && !empty($post_type)) {
            // 標準的なカスタム投稿タイプアーカイブ
            $page_type = $post_type;
        } elseif (!empty($post_type)) {
            // クエリ変数にpost_typeが設定されている場合
            $page_type = $post_type;
        } else {
            // URLパス解析でカスタム投稿タイプを推測（utilities.phpと同じロジック）
            $request_uri = $_SERVER['REQUEST_URI'];
            $path_parts = explode('/', trim($request_uri, '/'));

            if (count($path_parts) >= 1) {
                $potential_post_type = $path_parts[count($path_parts) - 1]; // 最後の部分
                $custom_post_types = get_post_types(array('public' => true, '_builtin' => false));

                if (!empty($potential_post_type) && in_array($potential_post_type, $custom_post_types)) {
                    $page_type = $potential_post_type;
                } else {
                    $page_type = 'archive';
                }
            } else {
                $page_type = 'archive'; // 汎用的なアーカイブ
            }
        }
    } elseif (is_search()) {
        $page_type = 'search';
    } else {
        $page_type = get_post_type() ?: 'post'; // 動的取得、フォールバック付き
    }

    if (!empty($page_type)) {
        $classes[] = 'page-type-' . $page_type;
    }

    return $classes;
}
add_filter('body_class', 'backbone_body_classes');

/**
 * アーカイブタイトルを動的にカスタム投稿タイプ名に変更（ハードコード回避）
 */
function backbone_custom_archive_title($title) {
    if (is_post_type_archive()) {
        $post_type = get_query_var('post_type');

        // URLパス解析でのフォールバック
        if (empty($post_type) && !empty($_SERVER['REQUEST_URI'])) {
            $path_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
            if (count($path_parts) >= 2) {
                $potential_type = end($path_parts);
                $custom_post_types = get_post_types(array('public' => true, '_builtin' => false));
                if (in_array($potential_type, $custom_post_types)) {
                    $post_type = $potential_type;
                }
            }
        }

        if (!empty($post_type)) {
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj && !empty($post_type_obj->labels->name)) {
                return $post_type_obj->labels->name;
            }
        }
    }
    return $title;
}
add_filter('get_the_archive_title', 'backbone_custom_archive_title', 10, 1);

/**
 * アーカイブタイトルの「アーカイブ:」プレフィックスを削除
 */
function backbone_remove_archive_prefix($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif (is_year()) {
        $title = get_the_date('Y');
    } elseif (is_month()) {
        $title = get_the_date('F Y');
    } elseif (is_day()) {
        $title = get_the_date('F j, Y');
    } elseif (is_post_type_archive()) {
        $post_type = get_query_var('post_type');

        // URLパス解析でのフォールバック
        if (empty($post_type) && !empty($_SERVER['REQUEST_URI'])) {
            $path_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
            if (count($path_parts) >= 2) {
                $potential_type = end($path_parts);
                $custom_post_types = get_post_types(array('public' => true, '_builtin' => false));
                if (in_array($potential_type, $custom_post_types)) {
                    $post_type = $potential_type;
                }
            }
        }

        if (!empty($post_type)) {
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj && !empty($post_type_obj->labels->name)) {
                $title = $post_type_obj->labels->name;
            }
        }
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    }

    return $title;
}
add_filter('get_the_archive_title', 'backbone_remove_archive_prefix', 5, 1);
