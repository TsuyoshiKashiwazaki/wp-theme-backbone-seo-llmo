/**
 * ウィジェットブロックエディタの拡張と修正
 *
 * @package Backbone_SEO_LLMO
 */

(function(wp, $) {
    'use strict';

    // jQueryが利用できない場合は終了
    if (!$) {
        return;
    }

    const { domReady } = wp;
    const { select, dispatch, subscribe } = wp.data;

    /**
     * ウィジェット管理画面での修正
     */
    function fixWidgetAdminPage() {
        // ウィジェット管理画面かどうか確認
        if (!document.body.classList.contains('widgets-php')) {
            return;
        }

        // すべてのウィジェットエリアを強制的に開けるようにする
        const forceOpenWidgetAreas = () => {
            // すべてのパネルボディを取得
            const panels = document.querySelectorAll('.components-panel__body');

            panels.forEach(panel => {
                // 閉じているパネルを開く
                if (!panel.classList.contains('is-opened')) {
                    const toggle = panel.querySelector('.components-panel__body-toggle');
                    if (toggle) {
                        // aria-expandedを設定
                        toggle.setAttribute('aria-expanded', 'true');
                        panel.classList.add('is-opened');
                    }
                }
            });

            // サイドバー2の特別処理
            const sidebar2 = document.querySelector('[data-widget-area-id="sidebar-2"]');
            if (sidebar2) {
                const parentPanel = sidebar2.closest('.components-panel__body');
                if (parentPanel && !parentPanel.classList.contains('is-opened')) {
                    parentPanel.classList.add('is-opened');
                }
            }

            // フッターエリアの特別処理
            const footerArea = document.querySelector('[data-widget-area-id="footer-widgets"]');
            if (footerArea) {
                const parentPanel = footerArea.closest('.components-panel__body');
                if (parentPanel && !parentPanel.classList.contains('is-opened')) {
                    parentPanel.classList.add('is-opened');
                }
            }
        };

        // データストアの監視
        const unsubscribe = subscribe(() => {
            const widgetAreas = select('core/edit-widgets')?.getWidgetAreas();

            if (widgetAreas && widgetAreas.length > 0) {
                // 各ウィジェットエリアが正しく展開できるようにする
                widgetAreas.forEach(area => {
                    const areaElement = document.querySelector(`[data-widget-area-id="${area.id}"]`);
                    if (areaElement) {
                        // クリック可能にする
                        areaElement.style.cursor = 'pointer';
                        areaElement.setAttribute('aria-expanded', 'true');
                    }
                });

                // 使用停止中のウィジェットエリアも修正
                const inactiveArea = document.querySelector('[data-widget-area-id="wp_inactive_widgets"]');
                if (inactiveArea) {
                    inactiveArea.style.display = 'block';
                    inactiveArea.style.cursor = 'pointer';
                }

                // 強制的にパネルを開く
                setTimeout(forceOpenWidgetAreas, 500);
                setTimeout(forceOpenWidgetAreas, 1500);

                unsubscribe();
            }
        });

        // CSSで強制的に表示
        const style = document.createElement('style');
        style.textContent = `
            /* すべてのウィジェットエリアを表示 */
            .edit-widgets-widget-areas__panels {
                display: block !important;
            }

            /* 各パネルを展開可能にする */
            .components-panel__body {
                display: block !important;
            }

            /* サイドバー2を表示と動作修正 */
            [data-widget-area-id="sidebar-2"],
            .edit-widgets-sidebar__panel-tab-content [aria-label*="サイドバー 2"] {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }

            /* フッターエリアを表示と動作修正 */
            [data-widget-area-id="footer-widgets"],
            .edit-widgets-sidebar__panel-tab-content [aria-label*="フッターウィジェットエリア"] {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }

            /* 使用停止中のウィジェットを表示 */
            [data-widget-area-id="wp_inactive_widgets"],
            .edit-widgets-sidebar__panel-tab-content [aria-label*="使用停止中"] {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }

            /* パネルのトグルボタンを常に表示・動作可能に */
            .components-panel__body-toggle {
                display: flex !important;
                cursor: pointer !important;
                pointer-events: auto !important;
            }

            /* エリアのコンテンツを表示 */
            .components-panel__body.is-opened .components-panel__body-content {
                display: block !important;
            }

            /* パネルボディの高さ制限を解除 */
            .components-panel__body {
                max-height: none !important;
                overflow: visible !important;
            }

            /* パネルをデフォルトで開いた状態にする */
            .edit-widgets-sidebar__panel .components-panel__body:not(.is-opened) .components-panel__body-toggle::after {
                content: none !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * カスタマイザー内でのレガシーウィジェット対応
     */
    function setupCustomizerLegacyWidgets() {
        if (!window.wp || !window.wp.customize) {
            return;
        }

        // カスタマイザー内での処理
        wp.customize.bind('ready', function() {
            // レガシーウィジェットが正しく動作することを確認
            wp.customize.section.each(function(section) {
                if (section.id.indexOf('sidebar-widgets-') === 0) {
                    // レガシーウィジェットのまま使用
                    section.container.find('.add-new-widget').off('click').on('click', function(e) {
                        e.preventDefault();
                        // レガシーウィジェット追加ダイアログを開く
                        $(this).closest('.customize-control-sidebar_widgets').find('.add-new-widget').trigger('click');
                    });
                }
            });
        });
    }

    /**
     * 相互誘導リンクを追加
     */
    function addNavigationLinks() {
        // ウィジェット管理画面の場合
        if (document.body.classList.contains('widgets-php')) {
            domReady(function() {
                // backboneWidgetEditorが利用可能か確認
                if (!window.backboneWidgetEditor) {
                    return;
                }

                const customizerUrl = window.backboneWidgetEditor.customizerUrl;

                // ヘッダーに通知を追加
                const header = document.querySelector('.edit-widgets-header');
                if (header) {
                    const notice = document.createElement('div');
                    notice.className = 'notice notice-info inline';
                    notice.style.margin = '10px 0';
                    notice.innerHTML = `
                        <p>
                            <strong>💡 ヒント:</strong>
                            リアルタイムプレビューを見ながら編集したい場合は、
                            <a href="${customizerUrl}">
                                カスタマイザーのウィジェット設定
                            </a>
                            も利用できます（レガシーウィジェット）。
                        </p>
                    `;
                    header.parentNode.insertBefore(notice, header.nextSibling);
                }

                // サイドバーにもリンクを追加
                setTimeout(() => {
                    const sidebar = document.querySelector('.interface-interface-skeleton__sidebar');
                    if (sidebar) {
                        const linkBox = document.createElement('div');
                        linkBox.style.padding = '16px';
                        linkBox.style.borderTop = '1px solid #e0e0e0';
                        linkBox.innerHTML = `
                            <h3 style="margin: 0 0 8px 0; font-size: 13px;">その他のオプション</h3>
                            <a href="${customizerUrl}"
                               class="components-button is-secondary"
                               style="width: 100%; justify-content: center;">
                                カスタマイザーで編集
                            </a>
                        `;
                        sidebar.appendChild(linkBox);
                    }
                }, 2000);
            });
        }

        // カスタマイザー内の場合（既存のコードを修正）
        if (window.wp && window.wp.customize) {
            // backbone_customizer_widget_noticeの内容を修正済み
        }
    }

    /**
     * ウィジェットエリアの展開/折りたたみを修正
     */
    function fixWidgetAreaToggle() {
        if (!document.body.classList.contains('widgets-php')) {
            return;
        }

        // MutationObserverで動的に追加される要素を監視
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        // パネルトグルボタンを探す
                        const toggleButtons = node.querySelectorAll('.components-panel__body-toggle');
                        toggleButtons.forEach(button => {
                            // クリックイベントが正しく動作するようにする
                            button.style.pointerEvents = 'auto';
                            button.style.cursor = 'pointer';
                        });
                    }
                });
            });
        });

        // 監視開始
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // 既存のトグルボタンも修正
        document.querySelectorAll('.components-panel__body-toggle').forEach(button => {
            button.style.pointerEvents = 'auto';
            button.style.cursor = 'pointer';
        });
    }

    /**
     * 初期化処理
     */
    domReady(function() {
        // ウィジェット管理画面の修正
        fixWidgetAdminPage();
        fixWidgetAreaToggle();

        // カスタマイザーでのレガシーウィジェット対応
        setupCustomizerLegacyWidgets();

        // 相互誘導リンクを追加
        addNavigationLinks();

        // 少し遅延してから再度修正を試みる
        setTimeout(() => {
            fixWidgetAdminPage();
            fixWidgetAreaToggle();
        }, 1000);
    });

})(window.wp, window.jQuery);