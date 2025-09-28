/**
 * サブディレクトリカスタマイザープレビュー用JavaScript
 */
(function($) {

    // サブディレクトリのパスを監視
    for (var i = 1; i <= 10; i++) {
        (function(index) {
            // パスが変更されたらプレビューURLを変更
            wp.customize('subdirectory_path_' + index, function(value) {
                value.bind(function(newPath) {
                    if (newPath) {
                        // プレビューURLをサブディレクトリに変更
                        var previewUrl = wp.customize.settings.url.home + newPath;
                        wp.customize.previewer.previewUrl.set(previewUrl);
                    }
                });
            });

            // デザイン設定が変更されたら自動リロード
            var settings = [
                'subdirectory_' + index + '_color_theme',
                'subdirectory_' + index + '_design_pattern',
                'subdirectory_' + index + '_text_pattern',
                'subdirectory_' + index + '_decoration_pattern'
            ];

            settings.forEach(function(settingId) {
                wp.customize(settingId, function(value) {
                    value.bind(function(newValue) {
                        // 現在のプレビューURLを取得
                        var currentUrl = wp.customize.previewer.previewUrl();
                        var subdirectoryPath = wp.customize('subdirectory_path_' + index).get();

                        // 現在のURLがこのサブディレクトリの場合のみリロード
                        if (subdirectoryPath && currentUrl.indexOf(subdirectoryPath) !== -1) {
                            wp.customize.previewer.refresh();
                        }
                    });
                });
            });
        })(i);
    }

    // セクションクリック時にプレビューURLを自動変更
    wp.customize.section.each(function(section) {
        if (section.id.indexOf('backbone_subdirectory_design_') === 0) {
            var index = section.id.replace('backbone_subdirectory_design_', '');

            section.expanded.bind(function(expanded) {
                if (expanded) {
                    // このセクションが開かれたらプレビューをサブディレクトリに変更
                    var subdirectoryPath = wp.customize('subdirectory_path_' + index).get();
                    if (subdirectoryPath) {
                        var previewUrl = wp.customize.settings.url.home + subdirectoryPath;
                        wp.customize.previewer.previewUrl.set(previewUrl);
                    }
                }
            });
        }
    });

    // メインのデザイン設定セクションが開かれたらホームに戻る
    wp.customize.section('backbone_design', function(section) {
        section.expanded.bind(function(expanded) {
            if (expanded) {
                wp.customize.previewer.previewUrl.set(wp.customize.settings.url.home);
            }
        });
    });

})(jQuery);