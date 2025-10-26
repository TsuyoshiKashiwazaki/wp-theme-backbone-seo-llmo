<?php
/**
 * カスタマイザー見出しコントロール
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 見出し用カスタムコントロール
 */
class Backbone_Customize_Heading_Control extends WP_Customize_Control {
    public $type = 'heading';

    public function render_content() {
        if (!empty($this->label)) {
            echo '<div class="customize-control-heading" style="margin: 20px 0 10px; padding: 10px 0; border-bottom: 2px solid #ddd;">';
            echo '<span style="font-weight: 600; font-size: 14px; color: #555;">' . esc_html($this->label) . '</span>';
            if (!empty($this->description)) {
                echo '<p style="margin: 5px 0 0; font-size: 12px; color: #666;">' . esc_html($this->description) . '</p>';
            }
            echo '</div>';
        }
    }
}
