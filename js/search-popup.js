/**
 * 検索ポップアップ機能
 */
(function() {
    'use strict';

    // DOM要素
    let searchToggle = null;
    let searchPopupOverlay = null;
    let searchPopupContainer = null;
    let searchPopupClose = null;
    let searchPopupInput = null;
    let searchPopupForm = null;

    // 初期化
    function init() {
        console.log('検索ポップアップ初期化開始');
        
        // DOM要素を取得
        searchToggle = document.querySelector('.search-toggle');
        searchPopupOverlay = document.querySelector('.search-popup-overlay');
        searchPopupContainer = document.querySelector('.search-popup-container');
        searchPopupClose = document.querySelector('.search-popup-close');
        searchPopupInput = document.querySelector('.search-popup-input');
        searchPopupForm = document.querySelector('.search-popup-form');

        console.log('検索ボタン:', searchToggle);
        console.log('ポップアップオーバーレイ:', searchPopupOverlay);

        // 要素が存在しない場合は終了
        if (!searchToggle || !searchPopupOverlay) {
            console.log('必要な要素が見つかりません');
            return;
        }

        console.log('イベントリスナー設定中');
        // イベントリスナーを設定
        setupEventListeners();
    }

    // イベントリスナー設定
    function setupEventListeners() {
        // 検索ボタンクリック
        searchToggle.addEventListener('click', openSearchPopup);

        // 閉じるボタンクリック
        if (searchPopupClose) {
            searchPopupClose.addEventListener('click', closeSearchPopup);
        }

        // オーバーレイクリック（背景クリック）
        searchPopupOverlay.addEventListener('click', function(e) {
            if (e.target === searchPopupOverlay) {
                closeSearchPopup();
            }
        });

        // ESCキーで閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchPopupOverlay.classList.contains('active')) {
                closeSearchPopup();
            }
        });

        // フォーム送信
        if (searchPopupForm) {
            searchPopupForm.addEventListener('submit', handleSearchSubmit);
        }

        // 検索入力フィールドのEnterキー
        if (searchPopupInput) {
            searchPopupInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearchSubmit(e);
                }
            });
        }
    }

    // 検索ポップアップを開く
    function openSearchPopup(e) {
        console.log('検索ポップアップを開く');
        e.preventDefault();
        
        if (!searchPopupOverlay) {
            console.log('オーバーレイが見つからない');
            return;
        }

        console.log('ポップアップ表示中');
        // ポップアップを表示
        searchPopupOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // スクロール無効化

        // 少し遅延してから入力フィールドにフォーカス
        setTimeout(() => {
            if (searchPopupInput) {
                searchPopupInput.focus();
            }
        }, 100);

        // アクセシビリティ: aria属性設定
        searchPopupOverlay.setAttribute('aria-hidden', 'false');
        if (searchPopupContainer) {
            searchPopupContainer.setAttribute('aria-modal', 'true');
        }
    }

    // 検索ポップアップを閉じる
    function closeSearchPopup() {
        if (!searchPopupOverlay) return;

        // ポップアップを非表示
        searchPopupOverlay.classList.remove('active');
        document.body.style.overflow = ''; // スクロール復活

        // 検索ボタンにフォーカスを戻す
        if (searchToggle) {
            searchToggle.focus();
        }

        // アクセシビリティ: aria属性設定
        searchPopupOverlay.setAttribute('aria-hidden', 'true');
        if (searchPopupContainer) {
            searchPopupContainer.setAttribute('aria-modal', 'false');
        }
    }

    // 検索実行
    function handleSearchSubmit(e) {
        e.preventDefault();
        
        if (!searchPopupInput) return;

        const searchQuery = searchPopupInput.value.trim();
        
        if (searchQuery === '') {
            // 空の場合はフォーカスを戻す
            searchPopupInput.focus();
            return;
        }

        // 検索ページへリダイレクト
        const searchUrl = new URL(window.location.origin);
        searchUrl.searchParams.set('s', searchQuery);
        
        // ポップアップを閉じてから遷移
        closeSearchPopup();
        
        // 少し遅延してから遷移（アニメーション完了を待つ）
        setTimeout(() => {
            window.location.href = searchUrl.toString();
        }, 200);
    }

    // フォーカストラップ（モーダル内でのTab移動制御）
    function setupFocusTrap() {
        if (!searchPopupContainer) return;

        const focusableElements = searchPopupContainer.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        searchPopupContainer.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    // Shift + Tab
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    // Tab
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    }

    // リサイズ時の処理
    function handleResize() {
        // 必要に応じてリサイズ時の処理を追加
        if (searchPopupOverlay && searchPopupOverlay.classList.contains('active')) {
            // モバイルでキーボードが表示された場合の調整など
        }
    }

    // jQueryを使用して初期化を確実に実行
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function() {
            init();
            setupFocusTrap();
        });
    } else {
        // jQueryがない場合のフォールバック
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                init();
                setupFocusTrap();
            });
        } else {
            init();
            setupFocusTrap();
        }
    }

    // リサイズイベント
    window.addEventListener('resize', handleResize);

})();