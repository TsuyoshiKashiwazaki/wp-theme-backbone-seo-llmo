<?php
/**
 * リピーターカスタマイザーコントロール
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタマイザー用リピーターコントロール
 */
class Backbone_Customize_Repeater_Control extends WP_Customize_Control {
    /**
     * コントロールのタイプ
     *
     * @var string
     */
    public $type = 'repeater';

    /**
     * 追加ボタンのラベル
     *
     * @var string
     */
    public $add_button_label = '項目を追加';

    /**
     * フィールド設定
     *
     * @var array
     */
    public $fields = array();

    /**
     * 最大項目数（0 = 無制限）
     *
     * @var int
     */
    public $max_items = 0;

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

        <div class="repeater-control-wrapper" data-control-id="<?php echo esc_attr($this->id); ?>">
            <div class="repeater-items-container">
                <!-- 項目はJavaScriptで動的に追加 -->
            </div>

            <button type="button" class="button button-secondary repeater-add-item"
                    data-max-items="<?php echo esc_attr($this->max_items); ?>">
                <?php echo esc_html($this->add_button_label); ?>
            </button>

            <!-- データ保存用の隠しフィールド -->
            <input type="hidden"
                   class="repeater-data-field"
                   value="<?php echo esc_attr($this->value()); ?>"
                   <?php $this->link(); ?> />

            <!-- フィールド設定をJSON形式で保存（JavaScriptで使用） -->
            <script type="application/json" class="repeater-fields-config">
                <?php echo wp_json_encode($this->fields); ?>
            </script>
        </div>
        <?php
    }

    /**
     * JavaScriptとCSSをエンキュー
     */
    public function enqueue() {
        // JavaScriptとCSSは後で追加
    }
}
