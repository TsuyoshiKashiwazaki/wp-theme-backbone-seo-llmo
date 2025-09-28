// カスタマイザーストレージ管理モジュール
// localStorage関連の機能を提供（無効化）

var CustomizerStorage = (function ($) {
    'use strict';

    var module = {};

    // 初期化（何もしない）
    module.init = function () {
        
        // ローカルストレージ機能は無効化されています
        // 必要に応じてここに保存処理を実装してください
    };

    // localStorageからJSONを復元する関数（無効化）
    function restoreJsonFromStorage() {
        
        // ローカルストレージからの復元は無効化されています
    }

    // JSONをlocalStorageに保存（無効化）
    module.saveJson = function (jsonText) {
        
        // ローカルストレージへの保存は無効化されています
        // 必要に応じてここに保存処理を実装してください
    };

    // localStorageからJSONを取得（無効化）
    module.getSavedJson = function () {
        
        return null; // 常にnullを返す
    };

    // localStorageからJSONを削除（無効化）
    module.clearSavedJson = function () {
        
        // ローカルストレージのクリアは無効化されています
    };

    return module;

})(jQuery);
