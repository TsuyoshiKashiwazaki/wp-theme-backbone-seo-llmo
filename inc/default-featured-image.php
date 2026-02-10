<?php
/**
 * デフォルトアイキャッチ画像機能
 *
 * アイキャッチ画像が未設定の投稿・固定ページに対して、
 * カスタマイザーで設定したデフォルト画像を自動的に適用する。
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * デフォルトアイキャッチ画像のAttachment IDを取得
 *
 * @return int|false デフォルト画像のID、未設定の場合はfalse
 */
function backbone_get_default_featured_image_id() {
    static $default_image_id = null;

    if ($default_image_id === null) {
        $id = get_theme_mod('default_featured_image', '');
        $default_image_id = $id ? absint($id) : false;
    }

    return $default_image_id;
}

/**
 * アイキャッチ画像が未設定の場合、デフォルト画像のIDを返すフィルター
 *
 * @param int|false $thumbnail_id サムネイルのAttachment ID
 * @param int|WP_Post $post 投稿IDまたは投稿オブジェクト
 * @return int|false
 */
function backbone_default_featured_image_filter($thumbnail_id, $post) {
    if ($thumbnail_id) {
        return $thumbnail_id;
    }

    $default_id = backbone_get_default_featured_image_id();

    if ($default_id && wp_get_attachment_image_url($default_id)) {
        return $default_id;
    }

    return $thumbnail_id;
}
add_filter('post_thumbnail_id', 'backbone_default_featured_image_filter', 10, 2);

/**
 * 投稿が本来のアイキャッチ画像を持っているかチェック
 * （デフォルト画像ではなく、投稿自体に設定されたもの）
 *
 * @param int|null $post_id 投稿ID
 * @return bool
 */
function backbone_has_own_featured_image($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (!$post_id) {
        return false;
    }

    return !empty(get_post_meta($post_id, '_thumbnail_id', true));
}
