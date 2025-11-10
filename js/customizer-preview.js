// カスタマイザープレビュー管理モジュール
// プレビューの適用と色設定の反映機能を提供

var CustomizerPreview = (function ($) {
    'use strict';

    var module = {};

    // 初期化
    module.init = function () {

        bindTypographyControls();
        bindColorControls();
        bindHeroImageControls();
        bindLayoutControls();
    };

    // タイポグラフィ設定のライブプレビュー
    function bindTypographyControls() {
        // 本文フォントサイズ
        wp.customize('custom_body_font_size', function(value) {
            value.bind(function(newval) {
                $('.entry-content').css('font-size', newval + 'px');
            });
        });

        // 行間
        wp.customize('custom_line_height', function(value) {
            value.bind(function(newval) {
                $('.entry-content').css('line-height', newval);
            });
        });

        // 文字間隔
        wp.customize('custom_letter_spacing', function(value) {
            value.bind(function(newval) {
                $('.entry-content').css('letter-spacing', newval + 'em');
            });
        });

        // 段落間隔
        wp.customize('custom_paragraph_spacing', function(value) {
            value.bind(function(newval) {
                $('.entry-content p').css('margin-bottom', newval + 'em');
            });
        });

        // 本文フォントウェイト
        wp.customize('custom_body_font_weight', function(value) {
            value.bind(function(newval) {
                $('.entry-content').css('font-weight', newval);
            });
        });

        // 見出しフォントウェイト
        wp.customize('custom_heading_font_weight', function(value) {
            value.bind(function(newval) {
                $('h1, h2, h3, h4, h5, h6').css('font-weight', newval);
            });
        });

        // 見出しのフォントサイズと行間、マージン
        for (var i = 1; i <= 6; i++) {
            (function(level) {
                wp.customize('custom_h' + level + '_font_size', function(value) {
                    value.bind(function(newval) {
                        $('h' + level + ', .entry-content h' + level).css('font-size', newval + 'px');
                    });
                });

                wp.customize('custom_h' + level + '_line_height', function(value) {
                    value.bind(function(newval) {
                        $('h' + level + ', .entry-content h' + level).css('line-height', newval);
                    });
                });

                wp.customize('custom_h' + level + '_margin_top', function(value) {
                    value.bind(function(newval) {
                        $('h' + level + ', .entry-content h' + level).css('margin-top', newval + 'em');
                    });
                });
            })(i);
        }
    }

    // カラー設定のライブプレビュー
    function bindColorControls() {
        // ボタン背景色
        wp.customize('button_background_color', function(value) {
            value.bind(function(newval) {
                if (newval) {
                    $('body').get(0).style.setProperty('--button-background-color', newval);
                    
                }
            });
        });

        // ボタンテキスト色
        wp.customize('button_text_color', function(value) {
            value.bind(function(newval) {
                if (newval) {
                    $('body').get(0).style.setProperty('--button-text-color', newval);
                    
                }
            });
        });

        // ボタンホバー背景色
        wp.customize('button_hover_background_color', function(value) {
            value.bind(function(newval) {
                if (newval) {
                    $('body').get(0).style.setProperty('--button-hover-background-color', newval);
                    
                }
            });
        });

        // その他の色設定
        var colorSettings = [
            'background_color', 'primary_color', 'secondary_color', 'accent_color',
            'text_primary_color', 'text_secondary_color', 'text_light_color',
            'link_color', 'link_hover_color', 'header_link_color', 'header_link_hover_color',
            'footer_link_color', 'footer_link_hover_color', 'border_color',
            'form_background_color', 'form_focus_color', 'search_button_color'
        ];

        colorSettings.forEach(function(setting) {
            wp.customize(setting, function(value) {
                value.bind(function(newval) {
                    if (newval) {
                        var cssVar = '--' + setting.replace(/_/g, '-');
                        $('body').get(0).style.setProperty(cssVar, newval);
                        
                    }
                });
            });
        });
    }

    // レイアウト設定のライブプレビュー
    function bindLayoutControls() {
        // サイドバー位置の変更
        wp.customize('sidebar_position', function(value) {
            value.bind(function(newval) {
                // body classを更新
                $('body').removeClass('sidebar-left sidebar-right');
                $('body').addClass('sidebar-' + newval);
            });
        });

        // スティッキーヘッダー透明度の変更
        wp.customize('sticky_header_opacity', function(value) {
            value.bind(function(newval) {
                var opacityValue = parseFloat(newval) / 100;
                var $style = $('#sticky-header-opacity-style');
                if ($style.length === 0) {
                    $style = $('<style id="sticky-header-opacity-style"></style>');
                    $('head').append($style);
                }
                $style.text(':root { --sticky-header-opacity: ' + opacityValue + '; }');
            });
        });
    }

    // ヒーローイメージ設定のライブプレビュー
    function bindHeroImageControls() {
        // 共通設定: スタイル変更
        wp.customize('hero_image_style_common', function(value) {
            value.bind(function(newStyle) {
                var settingMode = wp.customize('hero_image_setting_mode') ? wp.customize('hero_image_setting_mode').get() : 'common';

                // 共通設定モードの場合のみ反映
                if (settingMode === 'common') {
                    var $heroImage = $('.hero-image');
                    if ($heroImage.length) {
                        // 既存のスタイルクラスを削除
                        $heroImage.removeClass('hero-standard hero-fullwidth hero-circle hero-card');
                        // 新しいスタイルクラスを追加
                        $heroImage.addClass('hero-' + newStyle);
                    }
                }
            });
        });

        // 個別設定: 各投稿タイプのスタイル変更
        // 投稿タイプのリストを取得（PHPから渡される）
        if (typeof seoOptimusThemeData !== 'undefined' && seoOptimusThemeData.supportedPostTypes) {
            var postTypes = seoOptimusThemeData.supportedPostTypes;

            Object.keys(postTypes).forEach(function(postType) {
                wp.customize('hero_image_style_' + postType, function(value) {
                    value.bind(function(newStyle) {
                        var settingMode = wp.customize('hero_image_setting_mode') ? wp.customize('hero_image_setting_mode').get() : 'common';

                        // 個別設定モードで、かつ現在のページが該当する投稿タイプの場合のみ反映
                        if (settingMode === 'individual') {
                            var $heroImage = $('.hero-image');
                            if ($heroImage.length) {
                                // 既存のスタイルクラスを削除
                                $heroImage.removeClass('hero-standard hero-fullwidth hero-circle hero-card');
                                // 新しいスタイルクラスを追加
                                $heroImage.addClass('hero-' + newStyle);
                            }
                        }
                    });
                });
            });
        }
    }

    // カラーキーから設定キーを取得する関数（グローバルに公開）
    window.getSettingKeyFromColorKey = function (colorKey) {
        var keyMapping = {
            'background': 'background_color',
            'background_secondary': 'background_secondary_color',
            'primary': 'primary_color',
            'secondary': 'secondary_color',
            'accent': 'accent_color',
            'text_primary': 'text_primary_color',
            'text_secondary': 'text_secondary_color',
            'text_light': 'text_light_color',
            'link': 'link_color',
            'link_hover': 'link_hover_color',
            'header_link': 'header_link_color',
            'header_link_hover': 'header_link_hover_color',
            'footer_link': 'footer_link_color',
            'footer_link_hover': 'footer_link_hover_color',
            'border': 'border_color',
            'button_background': 'button_background_color',
            'button_text': 'button_text_color',
            'button_hover_background': 'button_hover_background_color',
            'form_background': 'form_background_color',
            'form_focus': 'form_focus_color',
            'search_button': 'search_button_color'
        };
        return keyMapping[colorKey] || colorKey;
    };

    // プレビューに色を適用する関数（グローバルに公開）
    window.applyColorToPreview = function (settingKey, colorValue) {
        if (!colorValue) return;

        // プレビュー領域のみを対象とする
        var previewFrame = document.querySelector('#customize-preview iframe');
        if (!previewFrame) {
            // プレビューフレームが存在しない場合は何もしない
            return;
        }

        // プレビューフレームの読み込み完了を待つ
        if (previewFrame.contentDocument && previewFrame.contentDocument.readyState === 'complete') {
            window.applyColorToPreviewFrame(settingKey, colorValue, previewFrame);
        } else {
            // プレビューフレームが読み込み中の場合、読み込み完了を待つ
            var loadHandler = function () {
                window.applyColorToPreviewFrame(settingKey, colorValue, previewFrame);
                previewFrame.removeEventListener('load', loadHandler);
            };
            previewFrame.addEventListener('load', loadHandler);

            // タイムアウト処理（5秒後にタイムアウト）
            setTimeout(function () {
                previewFrame.removeEventListener('load', loadHandler);
            }, 5000);
        }
    };

    // プレビューフレーム内に色を適用（グローバルに公開）
    window.applyColorToPreviewFrame = function (settingKey, colorValue, previewFrame) {
        var previewDoc = previewFrame.contentDocument;
        if (!previewDoc || !previewDoc.documentElement) return;

        try {
            var previewRoot = previewDoc.documentElement;

            // README.mdの仕様に従ったCSS変数名を設定（プレビュー内のみ）
            var cssVariableMapping = {
                'background_color': '--background-color',
                'background_secondary_color': '--background-secondary-color',
                'primary_color': '--primary-color',
                'secondary_color': '--secondary-color',
                'accent_color': '--accent-color',
                'text_primary_color': '--text-primary-color',
                'text_secondary_color': '--text-secondary-color',
                'text_light_color': '--text-light-color',
                'link_color': '--link-color',
                'link_hover_color': '--link-hover-color',
                'header_link_color': '--header-link-color',
                'header_link_hover_color': '--header-link-hover-color',
                'footer_link_color': '--footer-link-color',
                'footer_link_hover_color': '--footer-link-hover-color',
                'border_color': '--border-color',
                'button_background_color': '--button-background-color',
                'button_text_color': '--button-text-color',
                'button_hover_background_color': '--button-hover-background-color',
                'form_background_color': '--form-background-color',
                'form_focus_color': '--form-focus-color',
                'search_button_color': '--search-button-color'
            };

            var cssVariable = cssVariableMapping[settingKey];
            if (cssVariable) {
                previewRoot.style.setProperty(cssVariable, colorValue);
                
            } else {
                
            }
        } catch (err) {
            
        }
    };

    // 移植元テーマの機能と同じ方法でテーマデータをプレビューに適用する関数
    function applyThemeDataToPreview(themeData, $button) {
        
        

        if (!themeData || !themeData.colors) {
            
            return;
        }

        var colorKeys = Object.keys(themeData.colors);
        var processedCount = 0;
        var totalCount = colorKeys.length;

        

        // 移植元テーマの機能と同じ方法で各色を処理
        colorKeys.forEach(function (colorKey) {
            var colorValue = themeData.colors[colorKey];
            var settingKey = window.getSettingKeyFromColorKey(colorKey);

            

            if (settingKey && colorValue) {
                // WordPressのカスタマイザー設定に直接設定
                if (wp.customize && wp.customize(settingKey)) {
                    wp.customize(settingKey).set(colorValue);
                    

                    // プレビューに即座に反映（移植元テーマの機能と同じ方法）
                    setTimeout(function () {
                        if (window.applyColorToPreview) {
                            window.applyColorToPreview(settingKey, colorValue);
                            
                        }
                        processedCount++;

                        // すべての色が処理されたら完了メッセージを表示
                        if (processedCount >= totalCount) {
                            
                            if ($button) {
                                $button.text('反映しました！');
                                setTimeout(function () {
                                    $button.text('プレビューに反映');
                                }, 2000);
                            }
                        }
                    }, 100);
                } else {
                    
                    processedCount++;
                }
            } else {
                
                processedCount++;
            }
        });

        // 念のため、すべての処理が完了したことを確認
        setTimeout(function () {
            if (processedCount < totalCount) {
                
                if ($button) {
                    $button.text('反映しました！');
                    setTimeout(function () {
                        $button.text('プレビューに反映');
                    }, 2000);
                }
            }
        }, 2000);
    }

    // 移植元テーマの機能と同じ方法でテーマをプレビューに適用する関数（既存の関数は残しておく）
    window.applyThemeToPreview = function (themeData, $button) {
        
        

        if (!themeData || !themeData.colors) {
            
            return;
        }

        var colorKeys = Object.keys(themeData.colors);
        var processedCount = 0;
        var totalCount = colorKeys.length;

        

        colorKeys.forEach(function (colorKey) {
            var colorValue = themeData.colors[colorKey];
            var settingKey = window.getSettingKeyFromColorKey(colorKey);

            

            if (settingKey && colorValue) {
                // WordPressのカスタマイザー設定を更新
                if (wp.customize && wp.customize(settingKey)) {
                    wp.customize(settingKey).set(colorValue);
                    

                    // プレビューに即座に反映
                    setTimeout(function () {
                        if (window.applyColorToPreview) {
                            window.applyColorToPreview(settingKey, colorValue);
                            
                        }
                        processedCount++;

                        // すべての色が処理されたら完了メッセージを表示
                        if (processedCount >= totalCount) {
                            
                            if ($button) {
                                $button.text('反映しました！');
                                setTimeout(function () {
                                    $button.text('プレビューに反映');
                                }, 2000);
                            }
                        }
                    }, 100); // 少し遅延させて設定が反映されるのを待つ
                } else {
                    
                    processedCount++;
                }
            } else {
                
                processedCount++;
            }
        });

        // 念のため、すべての処理が完了したことを確認
        setTimeout(function () {
            if (processedCount < totalCount) {
                
                if ($button) {
                    $button.text('反映しました！');
                    setTimeout(function () {
                        $button.text('プレビューに反映');
                    }, 2000);
                }
            }
        }, 2000);
    };

    // 外部からアクセス可能な関数
    module.applyThemeToPreview = window.applyThemeToPreview;
    module.applyThemeDataToPreview = applyThemeDataToPreview;

    return module;

})(jQuery);

// モジュール初期化（jQuery読み込み保証）
(function() {
    function initWhenReady() {
        if (typeof jQuery !== 'undefined' && typeof CustomizerPreview !== 'undefined') {
            jQuery(document).ready(function() {
                CustomizerPreview.init();
            });
        } else {
            // jQueryが読み込まれるまで待機（最大5秒）
            setTimeout(initWhenReady, 100);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWhenReady);
    } else {
        initWhenReady();
    }
})();
