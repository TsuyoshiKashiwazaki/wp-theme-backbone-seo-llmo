<?php
/**
 * セクション順序カスタマイザーコントロール
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * セクション順序用カスタムコントロール
 */
class Backbone_Customize_Section_Order_Control extends WP_Customize_Control {
    /**
     * コントロールのタイプ
     *
     * @var string
     */
    public $type = 'section_order';

    /**
     * セクション情報
     *
     * @var array
     */
    public $sections = array();

    /**
     * コントロールをレンダリング
     */
    public function render_content() {
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo $this->description; ?></span>
            <?php endif; ?>
        </label>

        <div class="section-order-control-wrapper">
            <ul class="section-order-list">
                <!-- JavaScriptで動的に生成 -->
            </ul>

            <input type="hidden"
                   class="section-order-field"
                   value="<?php echo esc_attr($this->value()); ?>"
                   <?php $this->link(); ?> />

            <script type="application/json" class="section-order-config">
                <?php echo wp_json_encode($this->sections); ?>
            </script>
        </div>
        <?php
    }
}
