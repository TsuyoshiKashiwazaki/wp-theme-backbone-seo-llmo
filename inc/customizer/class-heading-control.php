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
            // 見出し
            echo '<div class="customize-control-heading archive-section-start" style="margin: 25px -12px 0; padding: 12px 15px; background: #f9f9f9; border: 1px solid #ddd; border-bottom: none;">';
            echo '<span style="font-weight: 600; font-size: 14px; color: #333; display: block;">' . esc_html($this->label) . '</span>';
            if (!empty($this->description)) {
                echo '<p style="margin: 8px 0 0; font-size: 12px; color: #666; line-height: 1.5;">' . esc_html($this->description) . '</p>';
            }
            echo '</div>';

            // JavaScriptで次のコントロールに枠線を適用
            ?>
            <script>
            (function() {
                var headingId = <?php echo json_encode($this->id); ?>;
                var $heading = jQuery('#' + headingId);

                if ($heading.length) {
                    var $nextControls = jQuery();
                    var $current = $heading.next('li');

                    // 次の見出しが見つかるまでコントロールを収集
                    while ($current.length && !$current.hasClass('customize-control-heading')) {
                        $nextControls = $nextControls.add($current);
                        $current = $current.next('li');
                    }

                    // スタイルを適用
                    $nextControls.css({
                        'margin-left': '-12px',
                        'margin-right': '-12px',
                        'padding-left': '15px',
                        'padding-right': '15px',
                        'border-left': '1px solid #ddd',
                        'border-right': '1px solid #ddd',
                        'background': '#fafafa'
                    });

                    // 最後のコントロールに下線を追加
                    if ($nextControls.length) {
                        $nextControls.last().css({
                            'border-bottom': '1px solid #ddd',
                            'margin-bottom': '15px',
                            'padding-bottom': '12px'
                        });
                    }
                }
            })();
            </script>
            <?php
        }
    }
}
