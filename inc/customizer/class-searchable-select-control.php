<?php
/**
 * 検索可能セレクトボックス カスタマイザーコントロール
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタマイザー用検索可能セレクトボックスコントロール
 */
class Backbone_Customize_Searchable_Select_Control extends WP_Customize_Control {
    /**
     * コントロールのタイプ
     *
     * @var string
     */
    public $type = 'searchable-select';

    /**
     * スクリプトとスタイルをエンキュー
     */
    public function enqueue() {
        wp_enqueue_script(
            'backbone-searchable-select',
            get_template_directory_uri() . '/js/customizer-searchable-select.js',
            array('jquery', 'customize-controls'),
            filemtime(get_template_directory() . '/js/customizer-searchable-select.js'),
            true
        );

        wp_enqueue_style(
            'backbone-searchable-select',
            get_template_directory_uri() . '/css/customizer-searchable-select.css',
            array(),
            filemtime(get_template_directory() . '/css/customizer-searchable-select.css')
        );
    }

    /**
     * コントロールをレンダリング
     */
    public function render_content() {
        if (empty($this->choices)) {
            return;
        }

        // 現在選択されている値のラベルを取得
        $current_value = $this->value();
        $current_label = isset($this->choices[$current_value]) ? $this->choices[$current_value] : '';
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo $this->description; ?></span>
            <?php endif; ?>

            <div class="searchable-select-wrapper">
                <!-- 現在の選択値を表示（読み取り専用） -->
                <div class="searchable-select-current">
                    <span class="searchable-select-current-text"><?php echo esc_html($current_label); ?></span>
                    <span class="searchable-select-arrow">▼</span>
                </div>

                <!-- 検索入力（ドロップダウン展開時に表示） -->
                <div class="searchable-select-search-container" style="display: none;">
                    <input type="text"
                           class="searchable-select-search"
                           placeholder="<?php esc_attr_e('文字列を入力して検索...', 'backbone-seo-llmo'); ?>"
                           autocomplete="off" />
                </div>

                <!-- 隠しセレクト（実際の値を保持） -->
                <select class="searchable-select-dropdown" <?php $this->link(); ?>>
                    <?php foreach ($this->choices as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($this->value(), $value); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- 検索結果 -->
                <div class="searchable-select-results" style="display: none;">
                    <?php foreach ($this->choices as $value => $label) : ?>
                        <div class="searchable-select-option" data-value="<?php echo esc_attr($value); ?>">
                            <?php echo esc_html($label); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </label>
        <?php
    }
}
