<?php
/**
 * WYSIWYGカスタマイザーコントロール
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタマイザー用WYSIWYGエディタコントロール（簡易版）
 */
class Backbone_Customize_WYSIWYG_Control extends WP_Customize_Control {
    /**
     * コントロールのタイプ
     *
     * @var string
     */
    public $type = 'wysiwyg';

    /**
     * エディタの設定
     *
     * @var array
     */
    public $editor_settings = array();

    /**
     * プレースホルダーテキスト
     *
     * @var string
     */
    public $placeholder = '';

    /**
     * コントロールをレンダリング
     */
    public function render_content() {
        $rows = isset($this->editor_settings['textarea_rows']) ? $this->editor_settings['textarea_rows'] : 8;
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo $this->description; ?></span>
            <?php endif; ?>

            <div class="customize-control-wysiwyg-help" style="margin: 10px 0; padding: 10px; background: #f0f0f1; border-left: 3px solid #2271b1; font-size: 12px;">
                <strong>使用可能なHTMLタグ:</strong><br>
                <code>&lt;p&gt;</code> 段落 |
                <code>&lt;br&gt;</code> 改行 |
                <code>&lt;strong&gt;</code> <strong>太字</strong> |
                <code>&lt;em&gt;</code> <em>斜体</em><br>
                <code>&lt;a href="URL"&gt;</code> リンク |
                <code>&lt;ul&gt;&lt;li&gt;</code> 箇条書き |
                <code>&lt;ol&gt;&lt;li&gt;</code> 番号付きリスト
            </div>

            <textarea
                class="widefat customize-control-wysiwyg-textarea"
                rows="<?php echo esc_attr($rows); ?>"
                <?php $this->link(); ?>
                <?php if (!empty($this->placeholder)) : ?>
                    placeholder="<?php echo esc_attr($this->placeholder); ?>"
                <?php endif; ?>
                style="font-family: Consolas, Monaco, monospace; font-size: 13px; line-height: 1.6;"
            ><?php echo esc_textarea($this->value()); ?></textarea>
        </label>
        <?php
    }
}
