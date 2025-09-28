/**
 * 検索ポップアップ機能（シンプル版）
 */
jQuery(document).ready(function($) {
    'use strict';

    // 検索ボタンクリック
    $(document).on('click', '.search-toggle', function(e) {
        e.preventDefault();
        $('.search-popup-overlay').addClass('active');
        $('body').css('overflow', 'hidden');
        setTimeout(function() {
            $('.search-popup-input').focus();
        }, 100);
    });

    // 閉じるボタンクリック
    $(document).on('click', '.search-popup-close', function(e) {
        e.preventDefault();
        $('.search-popup-overlay').removeClass('active');
        $('body').css('overflow', '');
    });

    // オーバーレイクリック
    $(document).on('click', '.search-popup-overlay', function(e) {
        if ($(e.target).hasClass('search-popup-overlay')) {
            $(this).removeClass('active');
            $('body').css('overflow', '');
        }
    });

    // ESCキーで閉じる
    $(document).keydown(function(e) {
        if (e.key === 'Escape' && $('.search-popup-overlay').hasClass('active')) {
            $('.search-popup-overlay').removeClass('active');
            $('body').css('overflow', '');
        }
    });

    // フォーム送信
    $(document).on('submit', '.search-popup-form', function(e) {
        var searchQuery = $('.search-popup-input').val().trim();
        
        if (searchQuery === '') {
            e.preventDefault();
            $('.search-popup-input').focus();
            return false;
        }
    });
});