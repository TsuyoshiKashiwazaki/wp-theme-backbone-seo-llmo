/**
 * サブディレクトリ設定保存後の自動リロード
 * サブディレクトリパスが変更・保存された場合に、デザイン設定セクションを表示するため
 * カスタマイザーを自動的にリロードする
 */
(function($, api) {
    'use strict';

    // 初期化時のサブディレクトリパスを保存
    var initialPaths = {};
    var needsReload = false;

    // カスタマイザーの準備完了時
    api.bind('ready', function() {
        // 既存のサブディレクトリパスを記録
        for (var i = 1; i <= 10; i++) {
            (function(index) {
                var settingId = 'subdirectory_path_' + index;
                if (api.has(settingId)) {
                    // 初期値を保存
                    initialPaths[settingId] = api(settingId).get();

                    // 値の変更を監視
                    api(settingId, function(setting) {
                        setting.bind(function(newValue) {
                            // 初期値と異なる場合（新規追加または変更）
                            if (initialPaths[settingId] !== newValue) {
                                // 値が空から何かに変わった、または何かから別の値に変わった
                                if ((!initialPaths[settingId] && newValue) ||
                                    (initialPaths[settingId] && newValue && initialPaths[settingId] !== newValue)) {
                                    needsReload = true;
                                }
                            }
                        });
                    });
                }
            })(i);
        }
    });

    // 保存成功時のイベントをリッスン
    api.bind('saved', function() {
        // サブディレクトリパスが変更された場合のみリロード
        if (needsReload) {
            // 通知メッセージを表示
            var notice = $('<div class="notice notice-info" style="position:fixed;top:46px;left:50%;transform:translateX(-50%);z-index:100000;padding:12px 20px;background:#2196F3;color:white;border-radius:4px;box-shadow:0 2px 5px rgba(0,0,0,0.3);"><p>サブディレクトリ設定を反映中...</p></div>');
            $('body').append(notice);

            // 少し待ってからリロード（保存処理を完了させるため）
            setTimeout(function() {
                // カスタマイザーをリロード
                window.location.reload();
            }, 1000);
        }
    });

    // サブディレクトリ追加ボタンのクリックも監視
    $(document).on('click', 'button[onclick*="backboneAddSubdirectory"]', function() {
        // 新しいサブディレクトリが追加された場合もリロードフラグを立てる
        needsReload = true;
    });

})(jQuery, wp.customize);