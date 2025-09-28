// カスタマイザーUI管理モジュール
// ボタンのイベントハンドラーとUIの相互作用を管理

var CustomizerUI = (function ($) {
    'use strict';

    var module = {};

    // 初期化
    module.init = function () {
        
        setupButtonHandlers();
        setupThemeJsonHandlers();
    };

    // ボタンのイベントハンドラーを設定
    function setupButtonHandlers() {
        

        // 少し遅延してDOM要素が準備完了するのを待つ
        setTimeout(function () {
            setupJsonCopyHandlers();
            setupJsonApplyHandlers();
        }, 1000);

        // 定期的にボタンの存在を確認してイベントハンドラーを再設定
        setInterval(function () {
            var copyButtons = $('.copy-json-btn');
            var applyButtons = $('.apply-json-btn');

            if (copyButtons.length > 0 || applyButtons.length > 0) {
                var needsSetup = false;

                copyButtons.each(function () {
                    if (!$(this).data('handler-attached')) {
                        needsSetup = true;
                    }
                });

                applyButtons.each(function () {
                    if (!$(this).data('handler-attached')) {
                        needsSetup = true;
                    }
                });

                if (needsSetup) {
                    
                    setupJsonCopyHandlers();
                    setupJsonApplyHandlers();
                }
            }
        }, 3000);
    }

    // JSONコピーボタンのハンドラーを設定
    function setupJsonCopyHandlers() {
        $('.copy-json-btn').each(function () {
            var $button = $(this);
            if (!$button.data('handler-attached')) {
                $button.on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    handleJsonCopy($button);
                });
                $button.data('handler-attached', true);
                
            }
        });
    }

    // JSON適用ボタンのハンドラーを設定
    function setupJsonApplyHandlers() {
        $('.apply-json-btn').each(function () {
            var $button = $(this);
            if (!$button.data('handler-attached')) {
                $button.on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    handleJsonApply($button);
                });
                $button.data('handler-attached', true);
                
            }
        });
    }

    // テーマJSONのハンドラーを設定（ボタンイベントのみ）
    function setupThemeJsonHandlers() {
        // 色変更イベントは customizer-themes.js の initializeColorPickers で処理するため、ここでは何もしない
        
    }

    // JSONコピーの処理
    function handleJsonCopy($button) {
        
        var themeId = $button.data('theme-id');

        if (!themeId) {
            
            alert('エラー: テーマIDが見つかりません');
            return;
        }

        var $textarea = $('.theme-json-output[data-theme-id="' + themeId + '"]');

        if ($textarea.length === 0) {
            
            alert('エラー: JSON出力エリアが見つかりません');
            return;
        }

        var jsonText = $textarea.val();

        if (!jsonText || jsonText.trim() === '') {
            
            alert('コピーするJSONがありません');
            return;
        }

        try {
            // テキストを選択
            $textarea[0].focus();
            $textarea[0].select();

            // クリップボードにコピー
            var successful = document.execCommand('copy');
            

            if (successful) {
                
                $button.text('コピーしました！');
                setTimeout(function () {
                    $button.text('JSONをコピー');
                }, 2000);
            } else {
                
                alert('クリップボードへのコピーに失敗しました。\nテキストを選択して Ctrl+C でコピーしてください。');
                $textarea[0].select();
            }
        } catch (err) {
            
            alert('コピーエラー: ' + err.message);
        }
    }

    // JSON適用の処理
    function handleJsonApply($button) {
        
        var themeId = $button.data('theme-id');

        var $textarea = $('.theme-json-output[data-theme-id="' + themeId + '"]');

        if ($textarea.length > 0) {
            var jsonText = $textarea.val();

            if (jsonText) {
                // CustomizerThemes モジュールの applyJsonAsTheme 関数を呼び出し
                if (typeof CustomizerThemes !== 'undefined' && CustomizerThemes.applyJsonAsTheme) {
                    CustomizerThemes.applyJsonAsTheme(jsonText, $button);
                } else {
                    
                    alert('エラー: テーマ適用機能が利用できません');
                }
            } else {
                
                alert('反映するJSONがありません。');
            }
        } else {
            
            alert('JSON出力エリアが見つかりません。');
        }
    }

    // カスタマイズされたテーマJSONを生成
    function generateCustomizedThemeJSON(themeId) {
        if (!themeId || !window.seoOptimusThemes || !window.seoOptimusThemes[themeId]) {
            return null;
        }

        var originalTheme = window.seoOptimusThemes[themeId];
        var customizedColors = {};

        // 現在の色設定を取得
        $('.color-input[data-theme-id="' + themeId + '"]').each(function () {
            var $input = $(this);
            var colorKey = $input.data('color-key');
            var currentValue = $input.val();

            if (currentValue) {
                customizedColors[colorKey] = currentValue;
            }
        });

        // カスタマイズされたテーマJSONを作成
        var customizedTheme = {
            name: originalTheme.name,
            id: originalTheme.id + '_customized',
            description: originalTheme.description,
            colors: customizedColors,
            customized: true,
            originalThemeId: themeId,
            lastModified: new Date().toISOString()
        };

        return customizedTheme;
    }

    // カスタマイズされたテーマをtextareaに表示
    function updateThemeJSONOutput(themeId, customizedTheme) {
        if (!customizedTheme) return false;

        try {
            var $textarea = $('.theme-json-output[data-theme-id="' + themeId + '"]');
            if ($textarea.length > 0) {
                var jsonString = JSON.stringify(customizedTheme, null, 2);
                $textarea.val(jsonString);
                
                return true;
            } else {
                
                return false;
            }
        } catch (e) {
            
            return false;
        }
    }

    return module;

})(jQuery);
