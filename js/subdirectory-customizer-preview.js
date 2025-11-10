/**
 * サブディレクトリプレビュー用JavaScript（プレビューフレーム内で実行）
 */
(function($, wp) {
    'use strict';

    // テーマデータをロード
    var colorThemes = {};

    // JSONファイルからテーマデータを取得する関数
    function loadThemeData(themeId, callback) {
        if (colorThemes[themeId]) {
            callback(colorThemes[themeId]);
            return;
        }

        // Ajax でテーマデータを取得
        $.ajax({
            url: wp.customize.settings.url.home + '/wp-content/themes/backbone-seo-llmo/inc/color-themes/' + themeId + '.json',
            dataType: 'json',
            cache: true,
            success: function(data) {
                colorThemes[themeId] = data;
                callback(data);
            },
            error: function() {
                // テーマのロードに失敗した場合は何もしない
            }
        });
    }

    // CSS変数を更新する関数
    function applyColorTheme(themeData) {
        if (themeData && themeData.colors) {
            Object.keys(themeData.colors).forEach(function(key) {
                const cssVar = '--' + key.replace(/_/g, '-');
                document.documentElement.style.setProperty(cssVar, themeData.colors[key]);
            });
        }
    }

    // CSS変数をクリアする関数
    function clearColorTheme() {
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
            const cssVar = '--' + key.replace(/_/g, '-');
            document.documentElement.style.removeProperty(cssVar);
        });
    }

    // 現在のパスがサブディレクトリに属するかチェック
    function isInSubdirectory(subdirectoryPath) {
        if (!subdirectoryPath) return false;

        // パスを正規化
        var normalizedPath = '/' + subdirectoryPath.replace(/^\/+|\/+$/g, '');
        var currentPath = window.location.pathname;

        // WordPressのサブディレクトリインストールを考慮
        if (wp.customize.settings.url && wp.customize.settings.url.home) {
            var homeUrl = new URL(wp.customize.settings.url.home);
            if (homeUrl.pathname && homeUrl.pathname !== '/') {
                currentPath = currentPath.replace(homeUrl.pathname, '');
            }
        }

        return currentPath.indexOf(normalizedPath) === 0;
    }

    // サブディレクトリごとにリスナーを設定
    for (var i = 1; i <= 10; i++) {
        (function(index) {
            // カラーテーマの監視
            wp.customize('subdirectory_' + index + '_color_theme', function(value) {
                value.bind(function(newValue) {
                    // 現在のサブディレクトリパスを取得
                    var subdirectoryPath = wp.customize('subdirectory_path_' + index).get();

                    // 現在のページがこのサブディレクトリに属する場合のみ更新
                    if (isInSubdirectory(subdirectoryPath)) {
                        if (newValue && newValue !== 'none') {
                            loadThemeData(newValue, applyColorTheme);
                        } else {
                            clearColorTheme();
                        }
                    }
                });
            });

            // デザインパターンの監視
            wp.customize('subdirectory_' + index + '_design_pattern', function(value) {
                value.bind(function(newValue) {
                    var subdirectoryPath = wp.customize('subdirectory_path_' + index).get();
                    if (isInSubdirectory(subdirectoryPath)) {
                        // デザインパターンの変更はページリロードが必要
                        wp.customize.previewer.refresh();
                    }
                });
            });

            // タイポグラフィパターンの監視
            wp.customize('subdirectory_' + index + '_text_pattern', function(value) {
                value.bind(function(newValue) {
                    var subdirectoryPath = wp.customize('subdirectory_path_' + index).get();
                    if (isInSubdirectory(subdirectoryPath)) {
                        // タイポグラフィパターンの変更はページリロードが必要
                        wp.customize.previewer.refresh();
                    }
                });
            });

            // デコレーションパターンの監視
            wp.customize('subdirectory_' + index + '_decoration_pattern', function(value) {
                value.bind(function(newValue) {
                    var subdirectoryPath = wp.customize('subdirectory_path_' + index).get();
                    if (isInSubdirectory(subdirectoryPath)) {
                        // デコレーションパターンの変更はページリロードが必要
                        wp.customize.previewer.refresh();
                    }
                });
            });
        })(i);
    }

    // メインのカラーテーマを監視（サブディレクトリ外のページ用）
    wp.customize('color_theme', function(value) {
        value.bind(function(newValue) {
            // どのサブディレクトリにも属していない場合のみ更新
            var isSubdirectory = false;

            for (var i = 1; i <= 10; i++) {
                var path = wp.customize('subdirectory_path_' + i).get();
                if (isInSubdirectory(path)) {
                    isSubdirectory = true;
                    break;
                }
            }

            if (!isSubdirectory) {
                if (newValue && newValue !== 'none') {
                    loadThemeData(newValue, applyColorTheme);
                } else {
                    clearColorTheme();
                }
            }
        });
    });

    // その他のメインデザイン設定
    var mainSettings = ['design_pattern', 'text_pattern', 'decoration_pattern'];

    mainSettings.forEach(function(settingId) {
        wp.customize(settingId, function(value) {
            value.bind(function(newValue) {
                // どのサブディレクトリにも属していない場合のみ更新
                var isSubdirectory = false;

                for (var i = 1; i <= 10; i++) {
                    var path = wp.customize('subdirectory_path_' + i).get();
                    if (isInSubdirectory(path)) {
                        isSubdirectory = true;
                        break;
                    }
                }

                if (!isSubdirectory) {
                    wp.customize.previewer.refresh();
                }
            });
        });
    });

})(jQuery, wp);