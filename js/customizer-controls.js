// カスタマイザーコントロールファイル読み込み完了

// WordPressカスタマイザー用の安全な初期化
if (typeof wp !== 'undefined' && typeof wp.customize !== 'undefined') {

    wp.customize.bind('ready', function () {

        // 初回読み込み時のみcolor_import_sourceを'none'に設定
        setTimeout(function() {
            var currentValue = wp.customize('color_import_source') ? wp.customize('color_import_source').get() : null;
            
            // 初回読み込み時で値が設定されていない場合のみ'none'を設定
            if (!currentValue || currentValue === '' || currentValue === undefined) {
                if (wp.customize('color_import_source')) {
                    wp.customize('color_import_source').set('none');
                }
                var $select = jQuery('select[data-customize-setting-link="color_import_source"]');
                if ($select.length > 0) {
                    $select.val('none');
                }
            }
        }, 1000);
        
        // ページ離脱時（カスタマイザーを閉じる時）に'none'にリセット
        jQuery(window).on('beforeunload', function() {
            if (wp.customize('color_import_source')) {
                wp.customize('color_import_source').set('none');
            }
        });
        
        // 公開処理の監視（ログのみ）
        if (wp.customize.state) {
            wp.customize.state('saved').bind(function(isSaved) {
            });
            
            wp.customize.state('processing').bind(function(isProcessing) {
            });
        }

        // jQueryが利用可能かチェック
        if (typeof jQuery === 'undefined') {
            return;
        }

        // 各モジュールの初期化
        if (typeof CustomizerStorage !== 'undefined') {
            CustomizerStorage.init();
        }
        if (typeof CustomizerPreview !== 'undefined') {
            CustomizerPreview.init();
        }
        if (typeof CustomizerThemes !== 'undefined') {
            CustomizerThemes.init();
        }
        if (typeof CustomizerUtils !== 'undefined') {
            CustomizerUtils.init();
        }
        if (typeof CustomizerUI !== 'undefined') {
            CustomizerUI.init();
        }

        // 定期的にボタンの状態を確認
        setInterval(function () {
            if (jQuery('.copy-json-btn').length > 0 && jQuery('.apply-json-btn').length > 0) {
                if (!window.jsonHandlersSetupLogged) {
                    window.jsonHandlersSetupLogged = true;
                }
            }
        }, 2000);
    });
}

