<?php
/**
 * コアユーティリティ・ヘルパー関数
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 抜粋の長さを変更
 */
function backbone_excerpt_length($length) {
    return 100;
}
add_filter('excerpt_length', 'backbone_excerpt_length');

/**
 * 抜粋の省略記号を変更
 */
function backbone_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'backbone_excerpt_more');

/**
 * ページネーション
 */
function backbone_pagination() {
    global $wp_query;

    $big = 999999999; // 大きな数値

    $paginate_links = paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages,
        'prev_text' => __('&laquo; 前へ', 'kashiwazaki-searchcraft'),
        'next_text' => __('次へ &raquo;', 'kashiwazaki-searchcraft'),
        'type' => 'list',
    ));

    if ($paginate_links) {
        echo '<nav class="pagination-wrapper">';
        echo $paginate_links;
        echo '</nav>';
    }
}

/**
 * SEO対応のメタディスクリプション
 */
function backbone_meta_description() {
    if (is_single() || is_page()) {
        global $post;
        if ($post->post_excerpt) {
            echo '<meta name="description" content="' . esc_attr($post->post_excerpt) . '">' . "\n";
        } else {
            $description = wp_trim_words($post->post_content, 25, '');
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
    } elseif (is_category()) {
        $description = category_description();
        if ($description) {
            echo '<meta name="description" content="' . esc_attr(strip_tags($description)) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'backbone_meta_description');

/**
 * カスタムタイポグラフィ用のヘルパー関数
 */
function backbone_sanitize_float($input) {
    return floatval($input);
}

function backbone_get_default_heading_size($level) {
    $sizes = array(
        1 => '1.75',
        2 => '1.25',
        3 => '1.25',
        4 => '1.125',
        5 => '1.0',
        6 => '0.875'
    );
    return $sizes[$level];
}

function backbone_get_default_heading_line_height($level) {
    $line_heights = array(
        1 => '1.25',
        2 => '1.3',
        3 => '1.35',
        4 => '1.4',
        5 => '1.45',
        6 => '1.5'
    );
    return $line_heights[$level];
}

function backbone_get_default_heading_margin($level) {
    $margins = array(
        1 => '30',
        2 => '30',
        3 => '25',
        4 => '20',
        5 => '18',
        6 => '15'
    );
    return $margins[$level];
}

/**
 * HEXカラーをRGBに変換
 */
function backbone_hex_to_rgb($hex) {
    // #を除去
    $hex = ltrim($hex, '#');

    // 3文字の場合は6文字に展開
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    // 6文字でない場合は無効
    if (strlen($hex) !== 6) {
        return false;
    }

    // RGB値を抽出
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    return array('r' => $r, 'g' => $g, 'b' => $b);
}

/**
 * デフォルトCSS変数を出力（カラーテーマが選択されていない場合）
 */
function backbone_output_default_css_variables() {
    // カラーテーマが設定されていない場合は何も出力しない
    // 強制的な変数設定を削除
    return;
}

/**
 * 現在のアーカイブタイプを判定
 *
 * @return string アーカイブタイプ（category/tag/author/date/search/cpt_{post_type}/unified）
 */
function backbone_get_current_archive_type() {
    if (is_category()) {
        return 'category';
    } elseif (is_tag()) {
        return 'tag';
    } elseif (is_author()) {
        return 'author';
    } elseif (is_date()) {
        return 'date';
    } elseif (is_search()) {
        return 'search';
    } elseif (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        return 'cpt_' . $post_type;
    } elseif (is_tax()) {
        // カスタムタクソノミーの場合は、関連する投稿タイプを取得
        $tax = get_queried_object();
        if ($tax) {
            $taxonomies = get_object_taxonomies($tax->taxonomy, 'objects');
            if (!empty($taxonomies)) {
                $taxonomy_obj = reset($taxonomies);
                if (!empty($taxonomy_obj->object_type)) {
                    $post_type = reset($taxonomy_obj->object_type);
                    return 'cpt_' . $post_type;
                }
            }
        }
        return 'unified';
    }

    return 'unified';
}

/**
 * アーカイブ設定を取得（統一/個別を自動判定）
 *
 * @param string $key 設定キー（例: 'grid_columns', 'show_thumbnail'）
 * @param mixed $default デフォルト値
 * @return mixed 設定値
 */
function backbone_get_archive_setting($key, $default = null) {
    // 統一設定モードをチェック
    $use_unified = get_theme_mod('archive_use_unified_settings', true);

    if ($use_unified) {
        // 統一設定を使用
        return get_theme_mod('archive_' . $key, $default);
    }

    // 個別設定を使用
    $archive_type = backbone_get_current_archive_type();
    $setting_key = 'archive_' . $archive_type . '_' . $key;

    // 個別設定が存在するかチェック
    $individual_setting = get_theme_mod($setting_key, null);

    if ($individual_setting !== null) {
        return $individual_setting;
    }

    // 個別設定が存在しない場合は統一設定にフォールバック
    return get_theme_mod('archive_' . $key, $default);
}
