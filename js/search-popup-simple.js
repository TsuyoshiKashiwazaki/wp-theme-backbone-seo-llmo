/**
 * 検索ポップアップ機能（シンプル版）
 */
jQuery(document).ready(function($) {
    'use strict';

    // 最後に検索を開いたボタン（閉じたときにフォーカスを戻す先）
    var $lastToggle = $();

    function openSearch($toggle) {
        $lastToggle = $toggle;
        $('.search-popup-overlay').addClass('active').attr('aria-hidden', 'false');
        $('.search-toggle-container').attr('aria-expanded', 'true');
        $('body').css('overflow', 'hidden');
        setTimeout(function() {
            $('.search-popup-input').focus();
        }, 100);
    }

    function closeSearch() {
        $('.search-popup-overlay').removeClass('active').attr('aria-hidden', 'true');
        $('.search-toggle-container').attr('aria-expanded', 'false');
        $('body').css('overflow', '');
        if ($lastToggle.length) {
            $lastToggle.trigger('focus');
        }
    }

    // 検索ボタンクリック
    $(document).on('click', '.search-toggle-container', function(e) {
        e.preventDefault();
        openSearch($(this));
    });

    // 検索ボタンは role="button" の div なので、Enter と Space でも開く（ボタンのキー操作）
    $(document).on('keydown', '.search-toggle-container', function(e) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
            e.preventDefault();
            openSearch($(this));
        }
    });

    // 閉じるボタンクリック
    $(document).on('click', '.search-popup-close', function(e) {
        e.preventDefault();
        closeSearch();
    });

    // オーバーレイクリック
    $(document).on('click', '.search-popup-overlay', function(e) {
        if ($(e.target).hasClass('search-popup-overlay')) {
            closeSearch();
        }
    });

    // ESCキーで閉じる
    $(document).keydown(function(e) {
        if (e.key === 'Escape' && $('.search-popup-overlay').hasClass('active')) {
            closeSearch();
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