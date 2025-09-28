/**
 * Kashiwazaki SearchCraft Theme JavaScript
 *
 * @package Kashiwazaki_SearchCraft
 */

(function ($) {
    'use strict';

    // jQueryの競合回避対応
    // WordPress コア jQuery を使用 (以前のコメントから変更、seoOptimusJQuery/seoOptimus$ も考慮)
    if (typeof window.seoOptimusJQuery !== 'undefined') {
        $ = window.seoOptimusJQuery;
    } else if (typeof window.seoOptimus$ !== 'undefined') {
        $ = window.seoOptimus$;
    } else if (typeof $ === 'undefined' && typeof window.$j !== 'undefined') {
        // Fallback for $j if seoOptimus is not present and $ is undefined
        $ = window.$j;
    }


    // DOM読み込み完了時の処理
    $(document).ready(function () {

        // スムーススクロール
        initSmoothScroll();

        // 検索フォームの拡張
        enhanceSearchForm();

        // コメントフォームの拡張
        enhanceCommentForm();

        // 画像の遅延読み込み
        initLazyLoading();

        // スクロールアニメーション（無効化）
        // initScrollAnimations();

        // テーマカラー変更の動的プレビュー
        initThemePreview();
    });



    /**
     * スムーススクロールの初期化
     */
    function initSmoothScroll() {
        // AddEventListenerを使用することでpassiveオプションを正しく設定
        document.addEventListener('click', function (e) {
            var target = e.target;
            if (target.tagName === 'A' && target.getAttribute('href') && target.getAttribute('href').startsWith('#')) {
                var scrollTarget = document.querySelector(target.getAttribute('href'));
                if (scrollTarget) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: scrollTarget.offsetTop - 100
                    }, 500);
                }
            }
        }, { passive: true });
    }

    /**
     * 検索フォームの拡張
     */
    function enhanceSearchForm() {
        $('.search-form').each(function () {
            var $form = $(this);
            var $input = $form.find('.search-field');
            var $button = $form.find('.search-submit');

            // フォーカス時のアニメーション
            $input.on('focus', function () {
                $form.addClass('focused');
            }).on('blur', function () {
                if (!$input.val()) {
                    $form.removeClass('focused');
                }
            });

            // 検索候補の表示（将来の拡張用）
            $input.on('input', function () {
                var query = $(this).val();
                if (query.length > 2) {
                    // ここに検索候補の処理を追加可能
                }
            });
        });
    }

    /**
     * コメントフォームの拡張
     */
    function enhanceCommentForm() {
        var $commentForm = $('#commentform');

        if ($commentForm.length) {
            // リアルタイムバリデーション
            $commentForm.find('input[required], textarea[required]').on('blur', function () {
                var $field = $(this);
                var value = $field.val().trim();

                if (!value) {
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });

            // フォーム送信時の処理
            $commentForm.on('submit', function () {
                $(this).find('.submit').addClass('loading');
            });
        }
    }

    /**
     * 画像の遅延読み込み（Intersection Observer使用）
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(function (img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * スクロールアニメーションの初期化（無効化）
     */
    function initScrollAnimations() {
        // ユーザーリクエストによりフェード効果を無効化
        /*
        if ('IntersectionObserver' in window) {
            var animationObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, {
                threshold: 0.1
            });

            // アニメーション対象の要素を監視
            document.querySelectorAll('.post, .page, .widget').forEach(function (el) {
                animationObserver.observe(el);
            });
        }
        */
    }

    /**
     * テーマカラー変更の動的プレビュー
     */
    function initThemePreview() {
        // カスタマイザーでの色変更をリアルタイムプレビュー
        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize('color_theme', function (value) {
                value.bind(function (newval) {
                    $('body').removeClass(function (index, className) {
                        return (className.match(/\btheme-\S+/g) || []).join(' ');
                    });

                    if (newval !== 'monochrome') {
                        $('body').addClass('theme-' + newval);
                    }
                });
            });

            wp.customize('design_pattern', function (value) {
                value.bind(function (newval) {
                    $('body').removeClass(function (index, className) {
                        return (className.match(/\bdesign-\S+/g) || []).join(' ');
                    });

                    if (newval !== 'default') {
                        $('body').addClass('design-' + newval);
                    }
                });
            });

            wp.customize('site_layout', function (value) {
                value.bind(function (newval) {
                    $('body').removeClass(function (index, className) {
                        return (className.match(/\blayout-\S+/g) || []).join(' ');
                    });

                    $('body').addClass('layout-' + newval.replace('_', '-'));
                });
            });

            // ヘッダー背景色のリアルタイムプレビュー
            wp.customize('header_bg_color', function (value) {
                value.bind(function (newval) {
                    $('#custom-header-bg').remove();
                    if (newval) {
                        $('<style id="custom-header-bg">.site-header { background: ' + newval + ' !important; }</style>')
                            .appendTo('head');
                    }
                });
            });

            // フッター背景色のリアルタイムプレビュー
            wp.customize('footer_bg_color', function (value) {
                value.bind(function (newval) {
                    $('#custom-footer-bg').remove();
                    if (newval) {
                        $('<style id="custom-footer-bg">.site-footer { background: ' + newval + ' !important; }</style>')
                            .appendTo('head');
                    }
                });
            });
        }
    }

    /**
     * アクセシビリティの向上
     */
    function enhanceAccessibility() {
        // スキップリンクの追加
        $('body').prepend('<a class="skip-link screen-reader-text" href="#main">メインコンテンツへスキップ</a>');

        // フォーカス管理の改善
        $('.skip-link').on('click', function () {
            var target = $($(this).attr('href'));
            if (target.length) {
                target.attr('tabindex', '-1').focus();
            }
        });

        // Escキーでモーダルを閉じる
        $(document).on('keydown', function (e) {
            if (e.keyCode === 27) { // Escキー
                $('.main-navigation ul.active').removeClass('active');
            }
        });
    }

    // アクセシビリティ機能の初期化
    enhanceAccessibility();

    /**
     * フォームバリデーション
     */
    function initFormValidation() {
        $('form').each(function () {
            var $form = $(this);

            $form.on('submit', function (e) {
                var isValid = true;

                $form.find('input[required], textarea[required], select[required]').each(function () {
                    var $field = $(this);
                    var value = $field.val().trim();

                    if (!value) {
                        $field.addClass('error');
                        isValid = false;
                    } else {
                        $field.removeClass('error');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    $form.find('.error').first().focus();
                }
            });
        });
    }

    // フォームバリデーションの初期化
    initFormValidation();

    /**
 * フルワイドレイアウト処理を無効化（CSSのみで制御）
 */
    // function adjustFullWidthSpacing() {
    //     // フルワイドレイアウト時のJS幅再計算を無効化
    // }

    // // 初期化処理も無効化
    // function initializeFullWidthLayout() {
    //     // 処理を無効化
    // }

    // // リサイズイベントも無効化
    // // $(window).on('resize', function () {
    // //     adjustFullWidthSpacing();
    // // });

    // // カスタマイザーイベントも無効化
    // // if (typeof wp !== 'undefined' && wp.customize) {
    // //     wp.customize('site_layout', function (value) {
    // //         value.bind(function (newval) {
    // //             setTimeout(function () {
    // //                 adjustFullWidthSpacing();
    // //             }, 100);
    // //         });
    // //     });
    // // }

})(jQuery); // <-- jQuery依存のコードはここで閉じられています。


/**
 * Vanilla JavaScript（jQueryに依存しない部分）
 */
// DOMContentLoadedのチェックは、既に document.addEventListener の前に存在するため不要
document.addEventListener('DOMContentLoaded', function () {

    /**
     * パフォーマンス監視
     */
    if ('performance' in window) {
        // window.addEventListener のチェックは、既にその前に存在するため不要
        window.addEventListener('load', function () {
            // ここにパフォーマンス関連のコードを追加
        });
    }

    /**
     * プリフェッチ機能（ナビゲーション以外のみ）
     */
    function initPrefetch() {
        var links = document.querySelectorAll('a[href^="/"], a[href^="' + window.location.origin + '"]');

        links.forEach(function (link) {
            // ナビゲーションメニューのリンクは除外
            if (link.closest('.main-navigation')) {
                return;
            }

            // `link`は`querySelectorAll`の結果なので`addEventListener`は常に存在する
            // `typeof`チェックは冗長なので削除
            var timer;
            link.addEventListener('mouseenter', function () {
                var href = this.href;
                timer = setTimeout(function () {
                    // `typeof document.querySelector === 'function'`はモダンブラウザでは常にtrueなので不要
                    if (href && !document.querySelector('link[rel="prefetch"][href="' + href + '"]')) {
                        var prefetchLink = document.createElement('link');
                        prefetchLink.rel = 'prefetch';
                        prefetchLink.href = href;
                        // `document.head`はHTML5以降常に存在するため`if`チェックは不要
                        document.head.appendChild(prefetchLink);
                    }
                }, 500); // 500ms遅延でプリフェッチ
            });

            link.addEventListener('mouseleave', function () {
                clearTimeout(timer);
            });
        });
    } // initPrefetch の閉じ括弧

    // プリフェッチ機能の初期化
    initPrefetch(); // initPrefetch の呼び出し位置

    /**
     * レスポンシブレイアウト制御（レイアウトタイプ別最適化）
     */
    function handleResponsiveLayout() {
        var body = document.body;
        var mainContent = document.querySelector('.main-content');

        if (!mainContent) return;

        var updateLayout = function () {
            var windowWidth = window.innerWidth;

            // ブレイクポイントに応じたclass管理
            body.classList.remove('mobile-view', 'tablet-view', 'desktop-view');

            // 1カラムレイアウトの制御（メインコンテンツのみ）
            if (body.classList.contains('layout-single-column')) {
                // 1カラムは常に同じ設定（CSS側で制御）
                body.classList.add('desktop-view');
                mainContent.style.gridTemplateColumns = '';
                return;
            }

            // 2カラムレイアウトの制御（7:3比率）
            if (body.classList.contains('layout-two-columns')) {
                if (windowWidth <= 1279) {
                    body.classList.add('mobile-view');
                    // 2カラム→1カラム化
                    mainContent.style.gridTemplateColumns = '1fr';
                } else {
                    body.classList.add('desktop-view');
                    // CSS側で制御するためJSではリセット
                    mainContent.style.gridTemplateColumns = '';
                }
                return;
            }

            // 3カラムレイアウトの制御（2:6:2比率）
            if (body.classList.contains('layout-three-columns')) {
                if (windowWidth <= 1279) {
                    body.classList.add('mobile-view');
                    body.classList.remove('desktop-view');
                } else {
                    body.classList.add('desktop-view');
                    body.classList.remove('mobile-view');
                }
                return;
            }
        };

        // 初期実行
        updateLayout();

        // リサイズ時の実行（デバウンス付き）
        var resizeTimer;
        // window.addEventListener のチェックは不要
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(updateLayout, 250);
        });
    } // handleResponsiveLayout の閉じ括弧

    // レスポンシブレイアウト制御の初期化
    handleResponsiveLayout(); // handleResponsiveLayout の呼び出し位置

    /**
     * サブメニュー（ドロップダウン）の機能強化
     */
    function initDropdownMenu() {
        var menuItems = document.querySelectorAll('.main-navigation .menu-item-has-children');

        menuItems.forEach(function (item) {
            var link = item.querySelector('a');
            var submenu = item.querySelector('.sub-menu');

            if (!link || !submenu) return;

            // キーボードナビゲーション対応
            // `link.addEventListener` のチェックは不要
            link.addEventListener('focus', function () {
                item.classList.add('focus');
            });

            link.addEventListener('blur', function () {
                setTimeout(function () {
                    // `item.contains` のチェックは不要
                    if (!item.contains(document.activeElement)) {
                        item.classList.remove('focus');
                    }
                }, 100);
            });

            // Escapeキーでサブメニューを閉じる
            // `submenu.addEventListener` のチェックは不要
            submenu.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    link.focus();
                    item.classList.remove('focus');
                }
            });

            // サブメニューの最後の項目からTabで次のメイン項目に移動
            var lastSubmenuItem = submenu.querySelector('li:last-child a');
            if (lastSubmenuItem) {
                // `lastSubmenuItem.addEventListener` のチェックは不要
                lastSubmenuItem.addEventListener('blur', function () {
                    setTimeout(function () {
                        // `item.contains` のチェックは不要
                        if (!item.contains(document.activeElement)) {
                            item.classList.remove('focus');
                        }
                    }, 100);
                });
            }
        }); // forEach の閉じ括弧

        // サブメニュー外をクリックしたら閉じる
        // `document.addEventListener` のチェックは不要
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.main-navigation')) {
                menuItems.forEach(function (item) {
                    item.classList.remove('focus');
                });
            }
        });
    } // initDropdownMenu の閉じ括弧

    // サブメニュー機能の初期化
    initDropdownMenu(); // initDropdownMenu の呼び出し位置

}); // <-- document.addEventListener('DOMContentLoaded', ...); の閉じ括弧
