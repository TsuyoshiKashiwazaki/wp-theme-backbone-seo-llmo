/**
 * インデックス設定（noindex）のカスタマイザー連動
 *
 * 「記事数がしきい値以下のものだけ」を選んだときだけ、しきい値欄を表示する。
 * PHP側の active_callback は初期表示時にしか評価されないため、
 * セレクトの変更に即座に追従させる目的でこのスクリプトを使う。
 */
( function ( wp ) {
    'use strict';

    if ( ! wp || ! wp.customize ) {
        return;
    }

    // モード選択 → 連動して表示を切り替えるしきい値コントロール
    var pairs = {
        'noindex_tag': 'noindex_tag_threshold',
        'noindex_category': 'noindex_category_threshold'
    };

    wp.customize.bind( 'ready', function () {
        Object.keys( pairs ).forEach( function ( settingId ) {
            var controlId = pairs[ settingId ];

            wp.customize( settingId, function ( setting ) {
                wp.customize.control( controlId, function ( control ) {
                    var toggle = function ( value ) {
                        control.active.set( 'thin' === value );
                    };

                    // サーバー側の同期に上書きされないよう、判定をこちらに固定する
                    control.active.validate = function () {
                        return 'thin' === setting.get();
                    };

                    toggle( setting.get() );
                    setting.bind( toggle );
                } );
            } );
        } );
    } );
} )( window.wp );
