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
     * スティッキーサイドバー（追従機能）
     * サイドバーの下端が画面下端に到達したら追従開始
     */
    function handleStickySidebar() {
        var sidebars = document.querySelectorAll('.sidebar-1, .sidebar-2');

        if (!sidebars.length) return;

        var sidebarData = [];

        // 各サイドバーの初期位置を記録
        sidebars.forEach(function (sidebar) {
            sidebarData.push({
                element: sidebar,
                originalTop: 0,
                originalLeft: 0,
                isSticky: false
            });
        });

        var updateSidebarPositions = function () {
            var windowWidth = window.innerWidth;

            // PC（1280px以上）でのみ有効
            if (windowWidth < 1280) {
                sidebarData.forEach(function (data) {
                    data.element.classList.remove('is-sticky');
                    data.element.style.position = '';
                    data.element.style.top = '';
                    data.element.style.bottom = '';
                    data.element.style.left = '';
                    data.element.style.width = '';
                    data.isSticky = false;
                });
                return;
            }

            sidebarData.forEach(function (data) {
                var sidebar = data.element;
                var contentArea = document.querySelector('.content-area');

                if (!contentArea) return;

                var sidebarHeight = sidebar.offsetHeight;
                var contentHeight = contentArea.offsetHeight;

                // サイドバーがコンテンツより高い場合は追従しない
                if (sidebarHeight > contentHeight) {
                    sidebar.classList.remove('is-sticky');
                    sidebar.style.position = '';
                    sidebar.style.top = '';
                    sidebar.style.bottom = '';
                    sidebar.style.left = '';
                    sidebar.style.width = '';
                    data.isSticky = false;
                    return;
                }

                // サイドバーの元の位置を取得（初回のみ）
                if (data.originalTop === 0) {
                    sidebar.style.position = '';
                    sidebar.style.top = '';
                    sidebar.style.bottom = '';
                    sidebar.style.left = '';
                    var rect = sidebar.getBoundingClientRect();
                    data.originalTop = rect.top + window.pageYOffset;
                    data.originalLeft = rect.left;
                }

                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                var windowHeight = window.innerHeight;
                var sidebarBottom = data.originalTop + sidebarHeight;
                var scrollBottom = scrollTop + windowHeight;

                // サイドバーの下端が画面の下端に到達したら固定開始
                if (scrollBottom >= sidebarBottom && !data.isSticky) {
                    var sidebarWidth = sidebar.offsetWidth;
                    sidebar.classList.add('is-sticky');
                    sidebar.style.width = sidebarWidth + 'px';
                    sidebar.style.left = data.originalLeft + 'px';

                    // サイドバーの高さに応じて固定方法を変更
                    if (sidebarHeight <= windowHeight) {
                        // サイドバーが画面より短い：上端固定（ヘッダーの下に配置）
                        var header = document.querySelector('header, .site-header');
                        var headerHeight = header ? header.offsetHeight : 0;

                        sidebar.style.position = 'fixed';
                        sidebar.style.top = headerHeight + 'px';
                        sidebar.style.bottom = 'auto';
                    } else {
                        // サイドバーが画面より長い：下端固定（下の方から見える）
                        sidebar.style.position = 'fixed';
                        sidebar.style.bottom = '0';
                        sidebar.style.top = 'auto';
                    }

                    data.isSticky = true;
                }
                // 上にスクロールして元の位置より上に戻ったら固定解除
                else if (scrollBottom < sidebarBottom && data.isSticky) {
                    sidebar.classList.remove('is-sticky');
                    sidebar.style.position = '';
                    sidebar.style.bottom = '';
                    sidebar.style.top = '';
                    sidebar.style.left = '';
                    sidebar.style.width = '';
                    data.isSticky = false;
                }

                // フッターを突き抜けないように制御
                if (data.isSticky) {
                    var footer = document.querySelector('footer, .site-footer');
                    if (footer) {
                        var footerRect = footer.getBoundingClientRect();
                        var footerTop = footerRect.top + scrollTop;

                        // .main-content の位置を取得（親要素）
                        var mainContent = sidebar.closest('.main-content');
                        var mainContentRect = mainContent ? mainContent.getBoundingClientRect() : null;
                        var mainContentTop = mainContentRect ? (mainContentRect.top + scrollTop) : 0;

                        // サイドバーのマージンを取得
                        var sidebarStyle = window.getComputedStyle(sidebar);
                        var marginTop = parseFloat(sidebarStyle.marginTop) || 0;
                        var marginBottom = parseFloat(sidebarStyle.marginBottom) || 0;

                        // フッターの上にサイドバーを配置する最大位置（ページ全体での絶対位置）
                        // マージンも考慮する（1pxのバッファを追加して確実にフッターを突き抜けないようにする）
                        var maxSidebarTop = footerTop - sidebarHeight - marginTop - marginBottom - 1;

                        // position: absolute の top は親要素からの相対位置なので変換
                        var maxSidebarTopRelative = maxSidebarTop - mainContentTop;

                        // サイドバーの下端がフッターに到達したら固定を解除してフッターの上に配置
                        if (sidebarHeight <= windowHeight) {
                            // 短いサイドバー：上端固定の場合
                            var header = document.querySelector('header, .site-header');
                            var headerHeight = header ? header.offsetHeight : 0;

                            // 現在のサイドバーの位置（ページ全体での絶対位置）
                            var currentSidebarTop = scrollTop + headerHeight;
                            var currentSidebarBottom = currentSidebarTop + sidebarHeight;

                            // サイドバーの下端がフッターに到達したかチェック
                            if (currentSidebarBottom >= footerTop) {
                                // absoluteに切り替え：フッターの手前で止める
                                // Grid レイアウト対応：grid-columnを保持
                                var computedStyle = window.getComputedStyle(sidebar);
                                var gridColumn = computedStyle.gridColumn;
                                var gridArea = computedStyle.gridArea;

                                sidebar.style.position = 'absolute';
                                sidebar.style.top = maxSidebarTopRelative + 'px';
                                sidebar.style.bottom = 'auto';
                                sidebar.style.left = ''; // Grid レイアウトでは left を削除

                                // Grid レイアウトの列位置を明示的に保持
                                if (gridColumn && gridColumn !== 'auto') {
                                    sidebar.style.gridColumn = gridColumn;
                                }
                                if (gridArea && gridArea !== 'auto') {
                                    sidebar.style.gridArea = gridArea;
                                }
                            } else {
                                // フッターから離れている：固定位置で追従
                                sidebar.style.position = 'fixed';
                                sidebar.style.top = headerHeight + 'px';
                                sidebar.style.bottom = 'auto';
                            }
                        } else {
                            // 長いサイドバー：下端固定の場合
                            var currentSidebarTop = scrollTop + windowHeight - sidebarHeight;
                            var currentSidebarBottom = scrollTop + windowHeight;

                            // サイドバーの下端がフッターに到達したかチェック
                            if (currentSidebarBottom >= footerTop) {
                                // absoluteに切り替え：フッターの手前で止める
                                // Grid レイアウト対応：grid-columnを保持
                                var computedStyle = window.getComputedStyle(sidebar);
                                var gridColumn = computedStyle.gridColumn;
                                var gridArea = computedStyle.gridArea;

                                sidebar.style.position = 'absolute';
                                sidebar.style.top = maxSidebarTopRelative + 'px';
                                sidebar.style.bottom = 'auto';
                                sidebar.style.left = ''; // Grid レイアウトでは left を削除

                                // Grid レイアウトの列位置を明示的に保持
                                if (gridColumn && gridColumn !== 'auto') {
                                    sidebar.style.gridColumn = gridColumn;
                                }
                                if (gridArea && gridArea !== 'auto') {
                                    sidebar.style.gridArea = gridArea;
                                }
                            } else {
                                // フッターから離れている：画面下端に固定
                                sidebar.style.position = 'fixed';
                                sidebar.style.top = 'auto';
                                sidebar.style.bottom = '0';
                            }
                        }
                    }
                }
            });
        };

        // 初期実行
        setTimeout(updateSidebarPositions, 100);

        // スクロール時の実行（スロットル付き）
        var scrollTimer;
        var isScrolling = false;
        window.addEventListener('scroll', function () {
            if (!isScrolling) {
                window.requestAnimationFrame(function() {
                    updateSidebarPositions();
                    isScrolling = false;
                });
                isScrolling = true;
            }
        }, { passive: true });

        // リサイズ時の実行（デバウンス付き）
        var sidebarResizeTimer;
        window.addEventListener('resize', function () {
            // リサイズ時は初期位置をリセット
            sidebarData.forEach(function (data) {
                data.originalTop = 0;
                data.originalLeft = 0;
                data.isSticky = false;
            });
            clearTimeout(sidebarResizeTimer);
            sidebarResizeTimer = setTimeout(updateSidebarPositions, 250);
        });

        // レイアウト変更時も再計算（カスタマイザー対応）
        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize('site_layout', function (value) {
                value.bind(function () {
                    sidebarData.forEach(function (data) {
                        data.originalTop = 0;
                        data.originalLeft = 0;
                        data.isSticky = false;
                    });
                    setTimeout(updateSidebarPositions, 100);
                });
            });
        }
    }

    // スティッキーサイドバーの初期化（設定が有効な場合のみ）
    if (typeof backboneThemeSettings !== 'undefined' && backboneThemeSettings.enableStickySidebar) {
        handleStickySidebar();
    }

    /**
     * スティッキーヘッダーの高さ調整
     */
    function adjustStickyHeaderPadding() {
        // スティッキーヘッダーが有効かチェック
        if (!document.body.classList.contains('sticky-header-enabled')) {
            return;
        }

        var header = document.querySelector('.site-header');
        var siteWrapper = document.querySelector('.site-wrapper');

        if (!header || !siteWrapper) {
            return;
        }

        // ヘッダーの実際の高さを取得
        var headerHeight = header.offsetHeight;

        // site-wrapperのpadding-topを動的に設定
        siteWrapper.style.paddingTop = headerHeight + 'px';
    }

    // スティッキーヘッダーの初期化
    if (typeof backboneThemeSettings !== 'undefined' && backboneThemeSettings.enableStickyHeader) {
        // 初回実行
        adjustStickyHeaderPadding();

        // ウィンドウリサイズ時に再計算
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                adjustStickyHeaderPadding();
            }, 100);
        });

        // DOM変更時にも再計算（メニューの開閉など）
        if (typeof MutationObserver !== 'undefined') {
            var headerObserver = new MutationObserver(function() {
                adjustStickyHeaderPadding();
            });

            var header = document.querySelector('.site-header');
            if (header) {
                headerObserver.observe(header, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['class', 'style']
                });
            }
        }

        // スクロール時にヘッダーの透明度を変更
        var scrollThreshold = 50; // スクロール量の閾値（px）
        var lastScrollTop = 0;
        var autohideEnabled = typeof backboneThemeSettings !== 'undefined' && backboneThemeSettings.stickyHeaderAutohide;

        function handleHeaderScroll() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > scrollThreshold) {
                document.body.classList.add('header-scrolled');
            } else {
                document.body.classList.remove('header-scrolled');
            }

            // 自動非表示が有効な場合、スクロール方向を検知
            if (autohideEnabled && scrollTop > scrollThreshold) {
                if (scrollTop > lastScrollTop) {
                    // スクロールダウン：ヘッダーを隠す
                    document.body.classList.add('header-hidden');
                } else {
                    // スクロールアップ：ヘッダーを表示
                    document.body.classList.remove('header-hidden');
                }
            } else if (scrollTop <= scrollThreshold) {
                // トップ付近ではヘッダーを常に表示
                document.body.classList.remove('header-hidden');
            }

            lastScrollTop = scrollTop;
        }

        // 初回実行
        handleHeaderScroll();

        // スクロールイベント
        var scrollTimer;
        window.addEventListener('scroll', function() {
            if (scrollTimer) {
                window.cancelAnimationFrame(scrollTimer);
            }
            scrollTimer = window.requestAnimationFrame(function() {
                handleHeaderScroll();
            });
        }, { passive: true });

        // ヘッダー表示用のタブを追加（自動非表示が有効な場合）
        if (autohideEnabled) {
            var headerToggle = document.createElement('div');
            headerToggle.className = 'header-toggle-tab';
            headerToggle.innerHTML = '<span>▼</span>';
            headerToggle.setAttribute('aria-label', 'ヘッダーを表示');
            headerToggle.setAttribute('role', 'button');
            headerToggle.setAttribute('tabindex', '0');

            document.body.appendChild(headerToggle);

            // タブクリックでヘッダーを表示
            headerToggle.addEventListener('click', function() {
                document.body.classList.remove('header-hidden');
                // 一時的にヘッダーを表示し続ける
                setTimeout(function() {
                    // スクロール位置によっては自動的に隠れる
                }, 3000);
            });

            // キーボード対応
            headerToggle.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    headerToggle.click();
                }
            });
        }
    }

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
