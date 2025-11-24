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
    // Check if meta description is enabled in customizer settings
    if (!get_theme_mod('seo_meta_description_enabled', true)) {
        return;
    }

    $description = '';

    // 個別投稿・固定ページ
    if (is_single() || is_page()) {
        global $post;
        if ($post->post_excerpt) {
            $description = $post->post_excerpt;
        } else {
            $description = wp_trim_words($post->post_content, 25, '');
        }
    }
    // ホームページ/ブログページ
    elseif (is_home() || is_front_page()) {
        $description = get_bloginfo('description');
        if (empty($description)) {
            $description = get_bloginfo('name') . ' - ' . __('Latest posts and updates', 'backbone-seo-llmo');
        }
    }
    // カテゴリーアーカイブ
    elseif (is_category()) {
        $term_description = category_description();
        if ($term_description) {
            $description = strip_tags($term_description);
        } else {
            $category = get_queried_object();
            $description = sprintf(__('Archive for %s category', 'backbone-seo-llmo'), $category->name);
        }
    }
    // タグアーカイブ
    elseif (is_tag()) {
        $term_description = tag_description();
        if ($term_description) {
            $description = strip_tags($term_description);
        } else {
            $tag = get_queried_object();
            $description = sprintf(__('Posts tagged with %s', 'backbone-seo-llmo'), $tag->name);
        }
    }
    // カスタム投稿タイプアーカイブ
    elseif (is_post_type_archive()) {
        $post_type = get_queried_object();
        if ($post_type && isset($post_type->description) && !empty($post_type->description)) {
            $description = $post_type->description;
        } else {
            $post_type_obj = get_post_type_object(get_post_type());
            $description = sprintf(__('Archive for %s', 'backbone-seo-llmo'), $post_type_obj->labels->name);
        }
    }
    // カスタムタクソノミーアーカイブ
    elseif (is_tax()) {
        $term = get_queried_object();
        if ($term && !empty($term->description)) {
            $description = strip_tags($term->description);
        } else {
            $description = sprintf(__('Archive for %s', 'backbone-seo-llmo'), $term->name);
        }
    }
    // 著者アーカイブ
    elseif (is_author()) {
        $author = get_queried_object();
        $author_description = get_the_author_meta('description', $author->ID);
        if ($author_description) {
            $description = strip_tags($author_description);
        } else {
            $description = sprintf(__('Posts by %s', 'backbone-seo-llmo'), $author->display_name);
        }
    }
    // 日付アーカイブ
    elseif (is_date()) {
        if (is_year()) {
            $description = sprintf(__('Posts from %s', 'backbone-seo-llmo'), get_the_date('Y'));
        } elseif (is_month()) {
            $description = sprintf(__('Posts from %s', 'backbone-seo-llmo'), get_the_date('F Y'));
        } elseif (is_day()) {
            $description = sprintf(__('Posts from %s', 'backbone-seo-llmo'), get_the_date('F j, Y'));
        }
    }
    // 検索結果ページ
    elseif (is_search()) {
        $search_query = get_search_query();
        $description = sprintf(__('Search results for "%s"', 'backbone-seo-llmo'), $search_query);
    }
    // 404ページ
    elseif (is_404()) {
        $description = __('Page not found - The page you are looking for does not exist.', 'backbone-seo-llmo');
    }

    // descriptionが設定されている場合のみ出力
    if (!empty($description)) {
        // HTMLタグを除去し、改行を空白に置換、連続する空白を1つにまとめる
        $description = preg_replace('/\s+/', ' ', trim(strip_tags($description)));
        // 最大160文字に制限（SEO推奨長）
        if (strlen($description) > 160) {
            $description = mb_substr($description, 0, 157) . '...';
        }
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }
}
add_action('wp_head', 'backbone_meta_description');

/**
 * SEO対応のメタキーワード自動生成
 */
function backbone_meta_keywords() {
    // Check if meta keywords is enabled in customizer settings
    if (!get_theme_mod('seo_meta_keywords_enabled', true)) {
        return;
    }

    $keywords = array();

    // 個別投稿・固定ページ
    if (is_single() || is_page()) {
        global $post;
        $title_keywords = array(); // タイトルキーワードを後で追加するために保存

        // 1. カスタムフィールドからフォーカスキーワードを取得（最優先）
        $focus_keyword = get_post_meta($post->ID, 'focus_keyword', true);
        if (!empty($focus_keyword)) {
            $keywords[] = $focus_keyword;
        }

        // 2. 投稿の場合：タグとカテゴリーを優先的に追加
        if (is_single()) {
            // タグを取得（重要度：高）
            $post_tags = get_the_tags($post->ID);
            if ($post_tags) {
                foreach ($post_tags as $tag) {
                    $keywords[] = $tag->name;
                }
            }

            // カテゴリーを取得（重要度：高）
            $categories = get_the_category($post->ID);
            if ($categories) {
                foreach ($categories as $category) {
                    $keywords[] = $category->name;
                }
            }
        }

        // 3. 固定ページの場合：ページタグを優先的に追加
        if (is_page()) {
            $page_tags = get_the_tags($post->ID);
            if ($page_tags) {
                foreach ($page_tags as $tag) {
                    $keywords[] = $tag->name;
                }
            }
        }

        // 4. タイトルからキーワードを抽出（補助的・最後に追加）
        $title = get_the_title($post->ID);
        $title_keywords = backbone_extract_keywords_from_text($title);

        // タイトルキーワードは最後に追加（優先度：低）
        $keywords = array_merge($keywords, $title_keywords);
    }
    // ホームページ/ブログページ
    elseif (is_home() || is_front_page()) {
        // 1. 主要カテゴリーを優先的に取得（重要度：最高）
        $categories = get_categories(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 5
        ));
        foreach ($categories as $category) {
            $keywords[] = $category->name;
        }

        // 2. 人気のあるタグも追加（重要度：高）
        $tags = get_tags(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 3
        ));
        foreach ($tags as $tag) {
            $keywords[] = $tag->name;
        }

        // 3. サイトタイトルからキーワードを抽出（補助的）
        $site_name = get_bloginfo('name');
        if (!empty($site_name)) {
            $site_keywords = backbone_extract_keywords_from_text($site_name);
            $keywords = array_merge($keywords, $site_keywords);
        }

        // 4. キャッチフレーズからキーワードを抽出（補助的）
        $tagline = get_bloginfo('description');
        if (!empty($tagline)) {
            $tagline_keywords = backbone_extract_keywords_from_text($tagline);
            $keywords = array_merge($keywords, $tagline_keywords);
        }
    }
    // カテゴリーアーカイブ
    elseif (is_category()) {
        $category = get_queried_object();
        $keywords[] = $category->name;

        // 親カテゴリーがある場合は追加
        if ($category->parent) {
            $parent_category = get_category($category->parent);
            $keywords[] = $parent_category->name;
        }

        // カテゴリー説明からキーワードを抽出
        if (!empty($category->description)) {
            $desc_keywords = backbone_extract_keywords_from_text($category->description);
            $keywords = array_merge($keywords, $desc_keywords);
        }
    }
    // タグアーカイブ
    elseif (is_tag()) {
        $tag = get_queried_object();
        $keywords[] = $tag->name;

        // 関連するタグを取得
        $related_tags = get_tags(array(
            'number' => 5,
            'orderby' => 'count',
            'order' => 'DESC'
        ));
        foreach ($related_tags as $related_tag) {
            if ($related_tag->term_id !== $tag->term_id) {
                $keywords[] = $related_tag->name;
            }
        }
    }
    // カスタム投稿タイプアーカイブ
    elseif (is_post_type_archive()) {
        $post_type = get_queried_object();
        if ($post_type) {
            $keywords[] = $post_type->labels->name;
            $keywords[] = $post_type->labels->singular_name;
        }
    }
    // カスタムタクソノミーアーカイブ
    elseif (is_tax()) {
        $term = get_queried_object();
        if ($term) {
            $keywords[] = $term->name;

            // タクソノミー名も追加
            $taxonomy = get_taxonomy($term->taxonomy);
            if ($taxonomy) {
                $keywords[] = $taxonomy->labels->singular_name;
            }
        }
    }
    // 著者アーカイブ
    elseif (is_author()) {
        $author = get_queried_object();
        $keywords[] = $author->display_name;
        $keywords[] = 'author';
        $keywords[] = '著者';
    }
    // 日付アーカイブ
    elseif (is_date()) {
        if (is_year()) {
            $keywords[] = get_the_date('Y年');
            $keywords[] = get_the_date('Y');
        } elseif (is_month()) {
            $keywords[] = get_the_date('Y年n月');
            $keywords[] = get_the_date('F Y');
        }
        $keywords[] = 'アーカイブ';
        $keywords[] = 'archive';
    }
    // 検索結果ページ
    elseif (is_search()) {
        $search_query = get_search_query();
        if (!empty($search_query)) {
            $search_keywords = backbone_extract_keywords_from_text($search_query);
            $keywords = array_merge($keywords, $search_keywords);
        }
        $keywords[] = '検索結果';
        $keywords[] = 'search';
    }

    // キーワードの重複を削除し、空の要素を除外
    $keywords = array_filter(array_unique($keywords));

    // フィルターフックを提供（プラグインや子テーマでカスタマイズ可能）
    $keywords = apply_filters('backbone_meta_keywords_list', $keywords);

    // 最大10個に制限
    $keywords = array_slice($keywords, 0, 10);

    // キーワードが存在する場合のみ出力
    if (!empty($keywords)) {
        // 各キーワードをエスケープしてカンマ区切りで結合
        $escaped_keywords = array_map('esc_attr', $keywords);
        $keywords_string = implode(', ', $escaped_keywords);
        echo '<meta name="keywords" content="' . $keywords_string . '">' . "\n";
    }
}

/**
 * テキストからキーワードを抽出するヘルパー関数
 */
function backbone_extract_keywords_from_text($text, $max_words = 5) {
    $keywords = array();

    // HTMLタグを除去
    $text = strip_tags($text);

    // 記号や特殊文字を除去
    $text = preg_replace('/[!@#$%^&*()_+=\[\]{};:"\'<>,.?\/\\|`~]/', ' ', $text);

    // 日本語の処理
    // 助詞や接続詞を区切り文字として使用
    $japanese_delimiters = array('の', 'を', 'に', 'は', 'が', 'で', 'と', 'から', 'まで', 'より', 'へ', 'や', 'など', 'ため');
    foreach ($japanese_delimiters as $delimiter) {
        $text = str_replace($delimiter, ' ', $text);
    }

    // 連続する空白を1つにまとめる
    $text = preg_replace('/\s+/', ' ', trim($text));

    // スペースと句読点で分割
    $text = str_replace(array('、', '。', '・', '｜', '|', '/', '－', '−'), ' ', $text);
    $words = explode(' ', $text);

    // ストップワードリスト（拡張版）
    $stop_words = array(
        // 日本語ストップワード
        'です', 'ます', 'する', 'なる', 'ある', 'いる', 'こと', 'もの', 'ため',
        'これ', 'それ', 'あれ', 'この', 'その', 'あの', 'どの', 'ここ', 'そこ',
        'いつ', 'どこ', 'だれ', 'なに', 'どう', 'どんな', 'という', 'といった',
        'ような', 'ように', 'よう', 'みたい', 'らしい', 'そう', 'でしょう',
        '向け', '無料', '公開', '初心者', 'について', '関する',
        // 英語ストップワード
        'the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'i',
        'it', 'for', 'not', 'on', 'with', 'he', 'as', 'you', 'do', 'at',
        'this', 'but', 'his', 'by', 'from', 'they', 'we', 'say', 'her', 'she',
        'or', 'an', 'will', 'my', 'one', 'all', 'would', 'there', 'their',
        'what', 'so', 'up', 'out', 'if', 'about', 'who', 'get', 'which', 'go'
    );

    foreach ($words as $word) {
        $word = trim($word);

        // 空文字や短すぎる単語をスキップ（日本語は2文字以上、英語は3文字以上）
        $word_length = mb_strlen($word);
        if ($word_length < 2) {
            continue;
        }

        // 数字のみの単語をスキップ
        if (preg_match('/^[0-9]+$/', $word)) {
            continue;
        }

        // ひらがなのみの短い単語（3文字以下）をスキップ
        if (preg_match('/^[ぁ-ん]{1,3}$/', $word)) {
            continue;
        }

        // ストップワードをスキップ
        if (in_array(mb_strtolower($word), $stop_words)) {
            continue;
        }

        // 重要そうな単語を抽出
        // 漢字、カタカナ、英数字を含む単語を優先
        if (preg_match('/[一-龯々ァ-ヶａ-ｚＡ-Ｚa-zA-Z0-9]/', $word)) {
            $keywords[] = $word;
        }
    }

    // 重複を削除
    $keywords = array_unique($keywords);

    // 最大数を制限
    return array_slice($keywords, 0, $max_words);
}

add_action('wp_head', 'backbone_meta_keywords');

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
