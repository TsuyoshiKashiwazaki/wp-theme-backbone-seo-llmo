// カスタマイザーテーマ管理モジュール
// 移植元テーマの選択とテーマデータの管理機能を提供

var CustomizerThemes = (function ($) {
    'use strict';

    var module = {};

    // 初期化
    module.init = function () {
        // ページ読み込み時にsessionStorageからJSONプレビューデータを復元
        setTimeout(function() {
            var restoredThemeId = restoreJsonPreviewFromSession();
        }, 500);

        setupThemeListeners();
    };

    // 移植元テーマの選択変更を監視する関数
    function setupThemeListeners() {
        var waitForThemeData = function (callback) {
            var maxWaitTime = 10000;
            var startTime = Date.now();
            var checkThemeData = function () {
                if (window.seoOptimusThemes && Object.keys(window.seoOptimusThemes).length > 0) {
                    callback();
                } else {
                    var elapsed = Date.now() - startTime;
                    if (elapsed < maxWaitTime) {
                        setTimeout(checkThemeData, 100);
                    } else {
                        callback(); // タイムアウトしても実行
                    }
                }
            };
            checkThemeData();
        };

        var themeSelectDetected = false;

        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize('color_import_source', function (setting) {
                setting.bind(function (value) {
                    if (value && value !== 'none') {                      
                        // まず過去の色設定をクリア
                        var colorSettings = [
                            'background_color', 'primary_color', 'secondary_color', 'accent_color',
                            'text_primary_color', 'text_secondary_color', 'text_light_color',
                            'link_color', 'link_hover_color', 'header_link_color', 'header_link_hover_color',
                            'footer_link_color', 'footer_link_hover_color', 'border_color',
                            'button_background_color', 'button_text_color', 'button_hover_background_color',
                            'form_background_color', 'form_focus_color', 'search_button_color'
                        ];
                        
                        colorSettings.forEach(function(settingName) {
                            if (wp.customize(settingName)) {
                                wp.customize(settingName).set('');
                                // プレビューにも空値を送信
                                if (wp.customize(settingName).preview) {
                                    wp.customize(settingName).preview();
                                }
                            }
                        });
                        
                        // CSSスタイルも削除
                        $('style[id*="custom"]').remove();
                        $('style[id*="individual"]').remove();
                        $('style[data-source="customizer"]').remove();
                        
                        // ファイル存在チェック
                        $.post(ajaxurl, {
                            action: 'check_custom_colors'
                        }, function(response) {
                            if (!response.success || !response.data.has_colors) {
                                
                                // UIを表示
                                $('#customize-control-color_import_source').show();
                                $('#theme-color-customizer').show();
                                $('#original-theme-status').hide();
                                
                                // 強制的にカラーピッカーを複数回構築
                                if (typeof window.buildThemeColorCustomizer === 'function') {
                                    window.buildThemeColorCustomizer(value);
                                    setTimeout(function() {
                                        window.buildThemeColorCustomizer(value);
                                    }, 300);
                                    setTimeout(function() {
                                        window.buildThemeColorCustomizer(value);
                                    }, 600);
                                }
                            }
                        });
                    }
                    waitForThemeData(function () {
                    });
                    themeSelectDetected = true;
                });
            });
        }

        $(document).on('change', 'select[data-customize-setting-link="color_import_source"]', function () {
            var selectedValue = $(this).val();

            // 現在の実際の値を取得
            var currentValue = wp.customize('color_import_source').get();

            if (currentValue && currentValue !== 'none') {
                // まず過去の色設定をクリア
                var colorSettings = [
                    'background_color', 'primary_color', 'secondary_color', 'accent_color',
                    'text_primary_color', 'text_secondary_color', 'text_light_color',
                    'link_color', 'link_hover_color', 'header_link_color', 'header_link_hover_color',
                    'footer_link_color', 'footer_link_hover_color', 'border_color',
                    'button_background_color', 'button_text_color', 'button_hover_background_color',
                    'form_background_color', 'form_focus_color', 'search_button_color'
                ];
                
                colorSettings.forEach(function(settingName) {
                    if (wp.customize(settingName)) {
                        wp.customize(settingName).set('');
                        // プレビューにも空値を送信
                        if (wp.customize(settingName).preview) {
                            wp.customize(settingName).preview();
                        }
                    }
                });
                
                // CSSスタイルも削除
                $('style[id*="custom"]').remove();
                $('style[id*="individual"]').remove();
                $('style[data-source="customizer"]').remove();
                
                // 既存のカラーピッカーを削除
                $('.custom-theme-color-picker').remove();
                $('#theme-color-pickers').empty();
                
                // ファイル存在チェック
                $.post(ajaxurl, {
                    action: 'check_custom_colors'
                }, function(response) {
                    if (response.success && response.data.has_colors) {
                        $('#customize-control-color_import_source').hide();
                        $('#selected-theme-colors').hide();
                        $('#theme-color-customizer').hide();
                        $('#original-theme-status').show();
                    } else {
                        
                        
                        // UIを表示
                        $('#customize-control-color_import_source').show();
                        $('#theme-color-customizer').show();
                        $('#original-theme-status').hide();
                        
                        // カラーピッカーを強制的に構築（複数回試行）
                        function forceShowColorPickers() {
                            if (typeof window.buildThemeColorCustomizer === 'function') {
                                window.buildThemeColorCustomizer(currentValue);
                                
                                // 表示確認
                                setTimeout(function() {
                                    if ($('#theme-color-pickers').children().length === 0) {
                                        window.buildThemeColorCustomizer(currentValue);
                                    }
                                }, 100);
                            } else {
                                setTimeout(forceShowColorPickers, 200);
                            }
                        }
                        
                        // 即座に実行
                        forceShowColorPickers();
                        
                        // 念のため遅延実行も
                        setTimeout(forceShowColorPickers, 500);
                        setTimeout(forceShowColorPickers, 1000);
                    }
                }).fail(function() {
                    // エラー時もカラーピッカーを表示
                    if (typeof window.buildThemeColorCustomizer === 'function') {
                        window.buildThemeColorCustomizer(currentValue);
                    }
                });
            } else {
                // 空文字の場合は 'none' に設定（ループ防止）
                if (currentValue === '') {
                    wp.customize('color_import_source').set('none');
                    currentValue = 'none';
                } else if (currentValue && currentValue !== 'none' && window.seoOptimusThemes && Object.keys(window.seoOptimusThemes).length > 0) {
                    if (Object.keys(window.seoOptimusThemes).indexOf(currentValue) === -1) {
                        wp.customize('color_import_source').set('none');
                        currentValue = 'none';
                    }
                }

            }
        });
    }

    // JSONデータを移植元テーマの機能と同じ方法で適用
    module.applyJsonAsTheme = function (jsonText, $button) {
        try {
            var themeData = JSON.parse(jsonText);

            if (themeData && themeData.colors) {
                // JSON内容を一時保存（リロード時に復元するため）
                // localStorageが無効化されているため、sessionStorageを使用
                try {
                    var sessionKey = 'wp_customizer_json_preview_' + Date.now();
                    sessionStorage.setItem(sessionKey, jsonText);
                    sessionStorage.setItem('wp_customizer_json_preview_key', sessionKey);
                } catch (e) {
                }

                // WordPressのカスタマイザー設定に色を直接適用
                var colorsApplied = 0;
                var totalColors = Object.keys(themeData.colors).length;

                // 各色をWordPressの設定に適用
                Object.keys(themeData.colors).forEach(function(colorKey) {
                    var colorValue = themeData.colors[colorKey];
                    var settingKey = window.getSettingKeyFromColorKey ? window.getSettingKeyFromColorKey(colorKey) : null;

                    if (settingKey && wp.customize(settingKey)) {
                        // WordPressの設定値を更新
                        wp.customize(settingKey).set(colorValue);

                        // カスタマイザーによる自動プレビュー更新を待つ
                        setTimeout(function() {
                            // プレビューに色を直接反映（CSS変数を使用）
                            if (window.applyColorToPreview) {
                                window.applyColorToPreview(settingKey, colorValue);
                            } else {
                                // 代替手段：CSS変数を直接更新
                                applyColorToCSS(settingKey, colorValue);
                            }
                        }, 100);

                        colorsApplied++;
                    }
                });

                // 成功メッセージ
                if ($button) {
                    if (colorsApplied > 0) {
                        $button.text('反映しました！ (' + colorsApplied + '色)');
                        setTimeout(function () {
                            $button.text('プレビューに反映');
                        }, 3000);
                    } else {
                        $button.text('反映失敗');
                        setTimeout(function () {
                            $button.text('プレビューに反映');
                        }, 2000);
                    }
                }

                // テーマデータを一時的に保存（UI更新用）
                var tempThemeId = 'json_preview_theme';
                if (!window.seoOptimusThemes) {
                    window.seoOptimusThemes = {};
                }
                window.seoOptimusThemes[tempThemeId] = themeData;

            } else {
                alert('JSONに色情報が含まれていません。');
            }
        } catch (err) {
            alert('JSONの形式が正しくありません: ' + err.message);
        }
    };

    // テーマデータを取得
    module.getThemeData = function (themeId) {
        if (window.seoOptimusThemes && window.seoOptimusThemes[themeId]) {
            return window.seoOptimusThemes[themeId];
        }
        return null;
    };

    // 利用可能なテーマ一覧を取得
    module.getAvailableThemes = function () {
        if (window.seoOptimusThemes) {
            return Object.keys(window.seoOptimusThemes);
        }
        return [];
    };





    // IDセレクタを使ったCSSルールを動的に生成して適用
    function applyColorToCSS(settingKey, colorValue) {
        if (!colorValue) return;

        try {
            // プレビューフレームを取得
            var previewFrame = document.querySelector('#customize-preview iframe');
            if (!previewFrame) {
                return;
            }

            var previewDoc = previewFrame.contentDocument;
            if (!previewDoc || !previewDoc.documentElement) {
                setTimeout(function() {
                    applyColorToCSS(settingKey, colorValue);
                }, 500);
                return;
            }

            // 既存のstyle要素を取得または作成
            var styleId = 'wp-customizer-json-preview-styles';
            var styleElement = previewDoc.getElementById(styleId);

            if (!styleElement) {
                styleElement = previewDoc.createElement('style');
                styleElement.id = styleId;
                styleElement.type = 'text/css';
                previewDoc.head.appendChild(styleElement);
            }

            // 各設定キーに対応するCSSセレクタとプロパティを定義
            // WordPressの標準的なCSS変数と実際のテーマで使われるクラスを考慮
            var colorMappings = {
                'primary_color': {
                    selectors: [
                        // WordPress標準セレクタ
                        '[style*="--wp--preset--color--primary"]',
                        '.wp-block-button__link',
                        '.wp-block-button.is-style-primary',
                        // 一般的なセレクタ
                        '.primary-color', '.btn-primary', '.primary',
                        'button.primary', '.button.primary',
                        // テーマ固有のセレクタ
                        '.main-color', '.theme-primary'
                    ],
                    properties: ['background-color', 'color'],
                    cssVar: '--wp--preset--color--primary'
                },
                'secondary_color': {
                    selectors: [
                        '[style*="--wp--preset--color--secondary"]',
                        '.wp-block-button.is-style-secondary',
                        '.secondary-color', '.btn-secondary', '.secondary',
                        '.sub-color', '.theme-secondary'
                    ],
                    properties: ['background-color'],
                    cssVar: '--wp--preset--color--secondary'
                },
                'accent_color': {
                    selectors: [
                        '[style*="--wp--preset--color--accent"]',
                        '.accent-color', '.accent',
                        '.highlight-color', '.theme-accent'
                    ],
                    properties: ['background-color', 'border-color'],
                    cssVar: '--wp--preset--color--accent'
                },
                'background_color': {
                    selectors: [
                        'body', '.site-background',
                        '.wp-site-blocks',
                        '[style*="--wp--preset--color--background"]'
                    ],
                    properties: ['background-color'],
                    cssVar: '--wp--preset--color--background'
                },
                'background_secondary_color': {
                    selectors: [
                        '.bg-secondary', '.secondary-background',
                        '.wp-block-group.has-secondary-background-color',
                        '.wp-block-columns.has-secondary-background-color'
                    ],
                    properties: ['background-color'],
                    cssVar: '--wp--preset--color--secondary-background'
                },
                'text_primary_color': {
                    selectors: [
                        'body', '.text-primary', 'p', 'div',
                        '.wp-block-paragraph',
                        '[style*="--wp--preset--color--foreground"]'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--foreground'
                },
                'text_secondary_color': {
                    selectors: [
                        '.text-secondary', '.secondary-text',
                        '.wp-block-paragraph.has-secondary-color',
                        '.muted-text', '.gray-text'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--secondary-foreground'
                },
                'text_light_color': {
                    selectors: [
                        '.text-light', '.light-text',
                        '.wp-block-paragraph.has-light-color',
                        '.subtle-text', '.faded-text'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--tertiary-foreground'
                },
                'link_color': {
                    selectors: [
                        'a', '.link', '.wp-block-link',
                        '.wp-block-paragraph a',
                        '[style*="--wp--preset--color--link"]'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--link'
                },
                'link_hover_color': {
                    selectors: [
                        'a:hover', '.link:hover',
                        '.wp-block-link:hover',
                        '[style*="--wp--preset--color--link-hover"]'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--link-hover'
                },
                'header_link_color': {
                    selectors: [
                        '.header a', '.site-header a', '.nav-link',
                        '.wp-block-navigation-link a',
                        '.site-navigation a'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--header-link'
                },
                'header_link_hover_color': {
                    selectors: [
                        '.header a:hover', '.site-header a:hover', '.nav-link:hover',
                        '.wp-block-navigation-link a:hover',
                        '.site-navigation a:hover'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--header-link-hover'
                },
                'footer_link_color': {
                    selectors: [
                        '.footer a', '.site-footer a',
                        '.wp-block-site-footer a',
                        '.footer-navigation a'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--footer-link'
                },
                'footer_link_hover_color': {
                    selectors: [
                        '.footer a:hover', '.site-footer a:hover',
                        '.wp-block-site-footer a:hover',
                        '.footer-navigation a:hover'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--footer-link-hover'
                },
                'border_color': {
                    selectors: [
                        '.border', 'hr', 'input', 'textarea', 'select',
                        '.wp-block-separator',
                        '.wp-block-table td',
                        '.wp-block-table th'
                    ],
                    properties: ['border-color'],
                    cssVar: '--wp--preset--color--border'
                },
                'button_background_color': {
                    selectors: [
                        '.btn', 'button', 'input[type="submit"]', 'input[type="button"]',
                        '.wp-block-button__link',
                        '.wp-block-button.is-style-primary',
                        '.wp-element-button'
                    ],
                    properties: ['background-color'],
                    cssVar: '--wp--preset--color--button-background'
                },
                'button_text_color': {
                    selectors: [
                        '.btn', 'button', 'input[type="submit"]', 'input[type="button"]',
                        '.wp-block-button__link',
                        '.wp-element-button'
                    ],
                    properties: ['color'],
                    cssVar: '--wp--preset--color--button-text'
                },
                'button_hover_background_color': {
                    selectors: [
                        '.btn:hover', 'button:hover', 'input[type="submit"]:hover', 'input[type="button"]:hover',
                        '.wp-block-button__link:hover',
                        '.wp-element-button:hover'
                    ],
                    properties: ['background-color'],
                    cssVar: '--wp--preset--color--button-hover-background'
                },
                'form_background_color': {
                    selectors: [
                        'input', 'textarea', 'select',
                        '.wp-block-form input',
                        '.wp-block-form textarea',
                        '.wp-block-form select'
                    ],
                    properties: ['background-color'],
                    cssVar: '--wp--preset--color--form-background'
                },
                'form_focus_color': {
                    selectors: [
                        'input:focus', 'textarea:focus', 'select:focus',
                        '.wp-block-form input:focus',
                        '.wp-block-form textarea:focus',
                        '.wp-block-form select:focus'
                    ],
                    properties: ['border-color', 'box-shadow'],
                    cssVar: '--wp--preset--color--form-focus'
                },
                'search_button_color': {
                    selectors: [
                        '.search-submit', '.search-button',
                        '.wp-block-search__button',
                        '.search-form button'
                    ],
                    properties: ['background-color'],
                    cssVar: '--wp--preset--color--search-button'
                }
            };

            // 設定キーに対応するCSSルールを生成
            var cssRules = '';
            var colorMapping = colorMappings[settingKey];

            if (colorMapping) {
                // 通常のCSSルールを生成
                colorMapping.selectors.forEach(function(selector) {
                    colorMapping.properties.forEach(function(property) {
                        if (property === 'box-shadow') {
                            // box-shadowの場合は特殊な処理
                            cssRules += selector + ' { ' + property + ': 0 0 0 2px ' + colorValue + '; } ';
                        } else {
                            cssRules += selector + ' { ' + property + ': ' + colorValue + ' !important; } ';
                        }
                    });
                });

                // CSS変数も設定（WordPressの標準的な方法）
                if (colorMapping.cssVar) {
                    cssRules += ':root { ' + colorMapping.cssVar + ': ' + colorValue + '; } ';
                }

            } else {
                // デフォルトでCSS変数を使用
                var cssVarName = '--' + settingKey.replace(/_/g, '-');
                cssRules = ':root { ' + cssVarName + ': ' + colorValue + '; } ';
            }

            // 既存のCSSルールを取得
            var existingCSS = styleElement.textContent || '';

            // 設定キーに関連する既存ルールを削除
            var regex = new RegExp(settingKey.replace(/_/g, '_') + '[^}]*}', 'g');
            existingCSS = existingCSS.replace(regex, '');

            // 新しいルールを追加
            existingCSS += '\n/* ' + settingKey + ' */\n' + cssRules;

            // style要素にCSSを適用
            styleElement.textContent = existingCSS.trim();
        } catch (e) {
        }
    }

    // sessionStorageからJSONプレビューデータを復元
    function restoreJsonPreviewFromSession() {
        try {
            var sessionKey = sessionStorage.getItem('wp_customizer_json_preview_key');
            if (sessionKey) {
                var jsonText = sessionStorage.getItem(sessionKey);
                if (jsonText) {
                    var themeData = JSON.parse(jsonText);
                    if (themeData && themeData.colors) {
                        // window.seoOptimusThemesに復元
                        var tempThemeId = 'json_preview_theme';
                        if (!window.seoOptimusThemes) {
                            window.seoOptimusThemes = {};
                        }
                        window.seoOptimusThemes[tempThemeId] = themeData;
                        
                        return tempThemeId;
                    }
                }
            }
        } catch (e) {
        }
        return null;
    }

    // 色入力フォームを初期化する関数
    function initializeColorPickers(themeId, theme) {
        if (!theme || !theme.colors) return;

        // sessionStorageからJSONプレビューデータを復元
        var restoredThemeId = restoreJsonPreviewFromSession();
        if (restoredThemeId) {
            // 復元されたテーマがある場合は、そのテーマの色入力を初期化
            setTimeout(function() {
                initializeColorPickers(restoredThemeId, window.seoOptimusThemes[restoredThemeId]);
            }, 500);
        }

        // 少し遅延してDOM要素が準備完了するのを待つ
        setTimeout(function () {

            $('.color-input[data-theme-id="' + themeId + '"]').each(function () {
                var $input = $(this);
                var colorKey = $input.data('color-key');

                // 初期値をWordPressの設定値と同期
                var settingKey = window.getSettingKeyFromColorKey ? window.getSettingKeyFromColorKey(colorKey) : null;
                if (settingKey && wp.customize(settingKey)) {
                    var currentValue = wp.customize(settingKey).get();
                    if (currentValue && currentValue !== $input.val()) {
                        $input.val(currentValue);
                        var $colorDisplay = $('.color-code-display[data-color-key="' + colorKey + '"][data-theme-id="' + themeId + '"]');
                        if ($colorDisplay.length > 0) {
                            $colorDisplay.text(currentValue);
                        }
                    }
                }

                // HTML5 color inputの初期化（既に初期化されていない場合のみ）
                if (!$input.hasClass('color-input-initialized')) {
                    $input.addClass('color-input-initialized');

                    // 複数のイベントで色変更を監視
                    var updateColorDisplay = function(newColor) {
                        var $colorDisplay = $('.color-code-display[data-color-key="' + colorKey + '"][data-theme-id="' + themeId + '"]');

                        if ($colorDisplay.length > 0) {
                            // 複数の方法で更新
                            $colorDisplay.text(newColor);
                            $colorDisplay.html(newColor);
                            $colorDisplay[0].textContent = newColor;
                            $colorDisplay[0].innerHTML = newColor;
                        }
                    };

                    // changeイベント（色選択完了時）
                    $input.off('change').on('change', function (e) {
                        var newColor = $(this).val();
                        updateColorDisplay(newColor);
                        handleColorChange(themeId, colorKey, newColor, $input);
                    });

                    // inputイベント（リアルタイム更新用）
                    $input.off('input').on('input', function (e) {
                        var newColor = $(this).val();
                        updateColorDisplay(newColor);
                    });

                    // blurイベント（フォーカスが外れた時）
                    $input.off('blur').on('blur', function (e) {
                        var newColor = $(this).val();
                        updateColorDisplay(newColor);
                    });


                }


            });

            // 定期的に色入力のイベントを再バインド（WordPressのカスタマイザー環境対策）
            var rebindInterval = setInterval(function() {
                $('.color-input[data-theme-id="' + themeId + '"]').each(function() {
                    var $input = $(this);
                    var colorKey = $input.data('color-key');

                    if (!$input.hasClass('color-events-bound')) {

                        var updateColorDisplay = function(newColor) {
                            var $colorDisplay = $('.color-code-display[data-color-key="' + colorKey + '"][data-theme-id="' + themeId + '"]');
                            if ($colorDisplay.length > 0) {
                                $colorDisplay.text(newColor);
                            }
                        };

                        $input.off('change').on('change', function() {
                            var newColor = $(this).val();
                            updateColorDisplay(newColor);
                            handleColorChange(themeId, colorKey, newColor, $input);
                        });

                        $input.off('input').on('input', function() {
                            var newColor = $(this).val();
                            updateColorDisplay(newColor);
                        });

                        $input.addClass('color-events-bound');
                    }
                });
            }, 2000); // 2秒ごとにチェック

            // 30秒後にインターバルをクリア
            setTimeout(function() {
                clearInterval(rebindInterval);
            }, 30000);

        }, 100);
    }

    // 色変更時の共通処理関数
    function handleColorChange(themeId, colorKey, newColor, $input) {
        // テーマデータを更新
        if (window.seoOptimusThemes && window.seoOptimusThemes[themeId]) {
            window.seoOptimusThemes[themeId].colors[colorKey] = newColor;
        }

        // WordPressの設定値も更新
        var settingKey = window.getSettingKeyFromColorKey ? window.getSettingKeyFromColorKey(colorKey) : null;

        if (settingKey && wp.customize(settingKey)) {
            wp.customize(settingKey).set(newColor);

            // プレビューに色を即座に反映（リアルタイム）
            if (window.applyColorToPreview) {
                window.applyColorToPreview(settingKey, newColor);
            }
        }

        // カスタマイズされたテーマをJSONとしてtextareaに表示
        // 複数回の試行で確実に更新
        var jsonUpdateAttempts = 0;
        var maxJsonAttempts = 3;

        function attemptJsonUpdate() {
            jsonUpdateAttempts++;

            // updateThemeJSONOutput関数を使用して部分更新を実行
            var customizedTheme = generateCustomizedThemeJSON(themeId);
            var result = false;

            if (customizedTheme && customizedTheme.colors) {
                result = updateThemeJSONOutput(themeId, customizedTheme);
            }

            if (result) {
                return;
            }
        }

        // 即座に試行
        attemptJsonUpdate();

        // 少し遅れて再試行（確実性を高める）
        if (maxJsonAttempts > 1) {
            setTimeout(attemptJsonUpdate, 200);
        }
        if (maxJsonAttempts > 2) {
            setTimeout(attemptJsonUpdate, 500);
        }
    }

    // カスタマイズされたテーマJSONを生成（現在のtextarea内容をベースに部分更新）
    function generateCustomizedThemeJSON(themeId) {
        if (!themeId || !window.seoOptimusThemes || !window.seoOptimusThemes[themeId]) {
            return null;
        }

        var originalTheme = window.seoOptimusThemes[themeId];

        // 既存のtextarea内容を取得してベースにする
        var $textarea = $('.theme-json-output[data-theme-id="' + themeId + '"]');
        var baseTheme = null;

        if ($textarea.length > 0) {
            var currentJsonText = $textarea.val();

            if (currentJsonText && currentJsonText.trim()) {
                try {
                    baseTheme = JSON.parse(currentJsonText);
                } catch (e) {
                    baseTheme = null;
                }
            }
        }

        // ベースとなるテーマがない場合は新規作成
        if (!baseTheme) {
            baseTheme = {
                name: originalTheme.name,
                id: originalTheme.id + '_customized',
                description: originalTheme.description,
                colors: {},
                customized: true,
                originalThemeId: themeId,
                lastModified: new Date().toISOString()
            };
        }

        // 現在の色設定を取得して更新
        var $colorInputs = $('.color-input[data-theme-id="' + themeId + '"]');

        $colorInputs.each(function (index) {
            var $input = $(this);
            var colorKey = $input.data('color-key');
            var currentValue = $input.val();

            if (currentValue && currentValue.trim()) {
                baseTheme.colors[colorKey] = currentValue;
            }
        });

        return baseTheme;
    }

    // カスタマイズされたテーマをtextareaに表示
    function updateThemeJSONOutput(themeId, customizedTheme) {
        if (!customizedTheme) {
            return false;
        }

        try {
            var selector = '.theme-json-output[data-theme-id="' + themeId + '"]';
            var $textarea = $(selector);

            if ($textarea.length > 0) {
                var jsonString = JSON.stringify(customizedTheme, null, 2);

                // textareaの内容を設定（複数回試行して確実に設定）
                $textarea.val(jsonString);
                $textarea[0].value = jsonString; // 直接DOM要素にも設定
                $textarea.trigger('input'); // inputイベントをトリガー
                $textarea.trigger('change'); // changeイベントをトリガー

                return true;
            } else {
                return false;
            }
        } catch (e) {
            return false;
        }
    }

    // グローバル関数として公開
    window.applyColorToCSS = applyColorToCSS;

    return module;
})(jQuery);
