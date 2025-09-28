// カスタマイザーユーティリティモジュール
// 共通のユーティリティ関数を提供

var CustomizerUtils = (function ($) {
    'use strict';

    var module = {};

    // 初期化
    module.init = function () {
        
    };

    // 安全なjQueryラッパー
    module.safeJQuery = function (callback) {
        if (typeof jQuery !== 'undefined') {
            return callback(jQuery);
        } else {
            
            return null;
        }
    };

    // WordPressカスタマイザーの準備を待つ
    module.waitForCustomizer = function (callback, timeout) {
        timeout = timeout || 10000;
        var startTime = Date.now();

        var checkCustomizer = function () {
            if (typeof wp !== 'undefined' && typeof wp.customize !== 'undefined') {
                callback(wp.customize);
            } else {
                var elapsed = Date.now() - startTime;
                if (elapsed < timeout) {
                    setTimeout(checkCustomizer, 100);
                } else {
                    
                    callback(null);
                }
            }
        };

        checkCustomizer();
    };

    // テーマデータを待つ
    module.waitForThemeData = function (callback, timeout) {
        timeout = timeout || 10000;
        var startTime = Date.now();

        var checkThemeData = function () {
            if (window.seoOptimusThemes && Object.keys(window.seoOptimusThemes).length > 0) {
                callback(window.seoOptimusThemes);
            } else {
                var elapsed = Date.now() - startTime;
                if (elapsed < timeout) {
                    setTimeout(checkThemeData, 100);
                } else {
                    
                    callback(null);
                }
            }
        };

        checkThemeData();
    };

    // DOM要素の準備を待つ
    module.waitForElement = function (selector, callback, timeout) {
        timeout = timeout || 5000;
        var startTime = Date.now();

        var checkElement = function () {
            var element = $(selector);
            if (element.length > 0) {
                callback(element);
            } else {
                var elapsed = Date.now() - startTime;
                if (elapsed < timeout) {
                    setTimeout(checkElement, 100);
                } else {
                    
                    callback(null);
                }
            }
        };

        checkElement();
    };

    // 関数が利用可能になるまで待つ
    module.waitForFunction = function (functionName, callback, timeout) {
        timeout = timeout || 5000;
        var startTime = Date.now();

        var checkFunction = function () {
            if (typeof window[functionName] === 'function') {
                callback(window[functionName]);
            } else {
                var elapsed = Date.now() - startTime;
                if (elapsed < timeout) {
                    setTimeout(checkFunction, 100);
                } else {
                    
                    callback(null);
                }
            }
        };

        checkFunction();
    };

    // コンソールログの安全な出力
    module.safeLog = function (level, message, data) {
        if (typeof console !== 'undefined' && typeof console[level] === 'function') {
            if (data !== undefined) {
                console[level](message, data);
            } else {
                console[level](message);
            }
        }
    };

    // イベントハンドラーの安全な設定
    module.safeOn = function (selector, event, handler) {
        module.safeJQuery(function ($) {
            $(document).on(event, selector, handler);
        });
    };

    // イベントハンドラーの安全な削除
    module.safeOff = function (selector, event) {
        module.safeJQuery(function ($) {
            $(document).off(event, selector);
        });
    };

    // localStorageの安全な操作（無効化）
    module.safeLocalStorage = {
        set: function (key, value) {
            
            
            // ローカルストレージへの保存は無効化されています
            return false;
        },
        get: function (key) {
            
            
            // ローカルストレージからの取得は無効化されています
            return null;
        },
        remove: function (key) {
            
            
            // ローカルストレージからの削除は無効化されています
            return false;
        }
    };

    // JSONの安全なパース
    module.safeJsonParse = function (jsonString) {
        try {
            return JSON.parse(jsonString);
        } catch (e) {
            
            return null;
        }
    };

    // JSONの安全な文字列化
    module.safeJsonStringify = function (obj) {
        try {
            return JSON.stringify(obj);
        } catch (e) {
            
            return null;
        }
    };

    return module;

})(jQuery);
