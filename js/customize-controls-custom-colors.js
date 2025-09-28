/**
 * カスタマイザーコントロール用JavaScript（独自カラーテーマ）
 */
(function($, api) {
    'use strict';

    api.bind('ready', function() {
        
        // カラーテーマが「none」の時のみ独自カラーテーマセクションを表示
        function toggleCustomColorSection() {
            const colorTheme = api('color_theme').get();
            const section = api.section('backbone_custom_color_theme');
            
            if (section) {
                if (colorTheme === 'none') {
                    section.activate();
                    section.expanded(false); // 自動展開はしない
                } else {
                    section.deactivate();
                }
            }
        }

        // 初期状態をチェック
        toggleCustomColorSection();

        // カラーテーマ変更時の処理
        api('color_theme', function(setting) {
            setting.bind(function(value) {
                toggleCustomColorSection();
            });
        });

        // ベーステーマ選択時の処理
        let isUpdatingColors = false; // フラグを追加
        
        api('custom_color_base_theme', function(setting) {
            setting.bind(function(themeId) {
                if (isUpdatingColors) return; // 更新中は処理をスキップ
                
                isUpdatingColors = true; // フラグをセット
                
                if (themeId === 'none') {
                    // 未選択の場合、すべての色をクリア
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
                    
                    colorKeys.forEach(function(key) {
                        const controlId = 'custom_color_' + key;
                        if (api(controlId)) {
                            // 値を設定（これが保存される）
                            api(controlId).set('');
                            
                            // カラーピッカーの表示を更新
                            setTimeout(function() {
                                const control = api.control(controlId);
                                if (control && control.container) {
                                    const colorInput = control.container.find('input.wp-color-picker');
                                    if (colorInput.length) {
                                        colorInput.val('').trigger('change');
                                        if (colorInput.data('wpWpColorPicker')) {
                                            colorInput.wpColorPicker('color', '');
                                        }
                                    }
                                }
                            }, 100);
                        }
                    });
                } else if (typeof seoOptimusColorThemes !== 'undefined' && seoOptimusColorThemes[themeId]) {
                    const theme = seoOptimusColorThemes[themeId];
                    
                    // 各色を更新
                    if (theme.colors) {
                        Object.keys(theme.colors).forEach(function(key) {
                            const controlId = 'custom_color_' + key;
                            if (api(controlId)) {
                                // 値を設定（これが保存される）
                                api(controlId).set(theme.colors[key]);
                                
                                // カラーピッカーの表示を更新（少し遅延を追加）
                                setTimeout(function() {
                                    const control = api.control(controlId);
                                    if (control && control.container) {
                                        const colorInput = control.container.find('input.wp-color-picker');
                                        if (colorInput.length) {
                                            colorInput.val(theme.colors[key]).trigger('change');
                                            if (colorInput.data('wpWpColorPicker')) {
                                                colorInput.wpColorPicker('color', theme.colors[key]);
                                            }
                                        }
                                    }
                                }, 100);
                            }
                        });
                    }
                }
                
                // フラグをリセット
                setTimeout(function() {
                    isUpdatingColors = false;
                }, 500);
            });
        });

        // リセットボタンの処理
        $(document).on('click', '.custom-color-reset-button', function(e) {
            e.preventDefault();
            
            const baseThemeId = api('custom_color_base_theme').get();
            
            if (baseThemeId === 'none') {
                alert('ベーステーマを選択してください。');
                return;
            }
            
            if (typeof seoOptimusColorThemes !== 'undefined' && seoOptimusColorThemes[baseThemeId]) {
                const theme = seoOptimusColorThemes[baseThemeId];
                
                if (confirm('ベーステーマの色にリセットしますか？')) {
                    if (theme.colors) {
                        Object.keys(theme.colors).forEach(function(key) {
                            const controlId = 'custom_color_' + key;
                            if (api(controlId)) {
                                api(controlId).set(theme.colors[key]);
                                
                                // カラーピッカーの表示も更新
                                setTimeout(function() {
                                    const control = api.control(controlId);
                                    if (control && control.container) {
                                        const colorInput = control.container.find('input.wp-color-picker');
                                        if (colorInput.length) {
                                            colorInput.val(theme.colors[key]).trigger('change');
                                            if (colorInput.data('wpWpColorPicker')) {
                                                colorInput.wpColorPicker('color', theme.colors[key]);
                                            }
                                        }
                                    }
                                }, 100);
                            }
                        });
                    }
                }
            }
        });

    });

})(jQuery, wp.customize);