/**
 * カスタマイザープレビュー用JavaScript（独自カラーテーマ）
 */
(function($) {
    'use strict';

    // カラー設定のキー一覧
    const colorKeys = [
        'primary_color', 'secondary_color', 'accent_color',
        'background_color', 'background_secondary',
        'text_primary', 'text_secondary', 'text_light',
        'link_color', 'link_hover_color',
        'header_link_color', 'header_link_hover_color',
        'footer_link_color', 'footer_link_hover_color',
        'border_color',
        'button_background_color', 'button_text_color', 'button_hover_background_color',
        'form_background_color', 'form_focus_color', 'search_button_color'
    ];

    // 各カラー設定のライブプレビュー
    colorKeys.forEach(function(key) {
        wp.customize('custom_color_' + key, function(value) {
            value.bind(function(newVal) {
                if (newVal) {
                    const cssVar = '--' + key.replace(/_/g, '-');
                    document.documentElement.style.setProperty(cssVar, newVal);
                }
            });
        });
    });

    // ベーステーマが変更された時
    wp.customize('custom_color_base_theme', function(value) {
        value.bind(function(themeId) {
            if (themeId === 'none') {
                // 未選択の場合、すべての色をクリアしてカスタマイザーの値も空にする
                colorKeys.forEach(function(key) {
                    const cssVar = '--' + key.replace(/_/g, '-');
                    document.documentElement.style.removeProperty(cssVar);
                    
                    // カスタマイザーの値も空にしてプレビューに反映
                    if (wp.customize('custom_color_' + key)) {
                        // 一時的に値を空に設定（プレビューのみ）
                        const setting = wp.customize('custom_color_' + key);
                        if (setting.get()) {
                            document.documentElement.style.removeProperty(cssVar);
                        }
                    }
                });
                
                // プレビューを更新
                if (wp.customize.previewer) {
                    wp.customize.previewer.refresh();
                }
            } else if (typeof seoOptimusColorThemes !== 'undefined' && seoOptimusColorThemes[themeId]) {
                const theme = seoOptimusColorThemes[themeId];
                
                // 各色をプレビューに反映
                if (theme.colors) {
                    Object.keys(theme.colors).forEach(function(key) {
                        const cssVar = '--' + key.replace(/_/g, '-');
                        document.documentElement.style.setProperty(cssVar, theme.colors[key]);
                    });
                }
            }
        });
    });

})(jQuery);