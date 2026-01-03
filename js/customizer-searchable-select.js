/**
 * 検索可能セレクトボックス カスタマイザーコントロール用JavaScript
 *
 * @package Backbone_SEO_LLMO
 */

(function($) {
    'use strict';

    // 初期化を遅延実行
    function init() {
        // 初回実行
        initSearchableSelects();

        // カスタマイザーのセクション展開時に再初期化
        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize.section.each(function(section) {
                section.expanded.bind(function(expanded) {
                    if (expanded) {
                        setTimeout(initSearchableSelects, 100);
                    }
                });
            });

            // 設定変更時にも再初期化（active_callback対応）
            wp.customize.bind('change', function() {
                setTimeout(initSearchableSelects, 100);
            });
        }

        // MutationObserverで動的に追加された要素を監視
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    setTimeout(initSearchableSelects, 50);
                }
            });
        });

        var customizerPane = document.getElementById('customize-controls');
        if (customizerPane) {
            observer.observe(customizerPane, {
                childList: true,
                subtree: true
            });
        }

        // ドキュメント全体のクリックで閉じる
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.searchable-select-wrapper').length) {
                closeAllDropdowns();
            }
        });
    }

    function closeAllDropdowns() {
        $('.searchable-select-wrapper.is-open').each(function() {
            var $wrapper = $(this);
            $wrapper.removeClass('is-open');
            $wrapper.find('.searchable-select-search-container').hide();
            $wrapper.find('.searchable-select-results').hide();
            $wrapper.find('.searchable-select-search').val('');
        });
    }

    function initSearchableSelects() {
        $('.searchable-select-wrapper').each(function() {
            var $wrapper = $(this);

            // 既に初期化済みの場合はスキップ
            if ($wrapper.data('searchable-initialized')) {
                return;
            }
            $wrapper.data('searchable-initialized', true);

            var $current = $wrapper.find('.searchable-select-current');
            var $currentText = $wrapper.find('.searchable-select-current-text');
            var $searchContainer = $wrapper.find('.searchable-select-search-container');
            var $search = $wrapper.find('.searchable-select-search');
            var $select = $wrapper.find('.searchable-select-dropdown');
            var $results = $wrapper.find('.searchable-select-results');
            var $options = $results.find('.searchable-select-option');

            // 現在の選択値を表示
            updateCurrentDisplay();

            // 現在の選択値クリックでドロップダウン開閉
            $current.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if ($wrapper.hasClass('is-open')) {
                    // 閉じる
                    closeDropdown();
                } else {
                    // 他のドロップダウンを閉じる
                    closeAllDropdowns();
                    // 開く
                    openDropdown();
                }
            });

            // 検索入力時
            $search.on('input keyup', function() {
                var query = $(this).val().toLowerCase();
                filterOptions(query);
            });

            // 検索フィールドクリックでイベント伝播を止める
            $search.on('click', function(e) {
                e.stopPropagation();
            });

            // オプションクリック時
            $options.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $option = $(this);
                var value = $option.attr('data-value');

                // selectの値を更新
                $select.val(value);

                // カスタマイザーに変更を通知
                $select.trigger('change');

                // 表示を更新
                updateCurrentDisplay();

                // ドロップダウンを閉じる
                closeDropdown();
            });

            // キーボード操作
            $search.on('keydown', function(e) {
                var $visible = $options.filter(':visible');
                var $current = $visible.filter('.highlighted');
                var index = $visible.index($current);

                switch (e.keyCode) {
                    case 40: // Down
                        e.preventDefault();
                        $visible.removeClass('highlighted');
                        if (index < $visible.length - 1) {
                            $visible.eq(index + 1).addClass('highlighted');
                            scrollToOption($visible.eq(index + 1));
                        } else {
                            $visible.eq(0).addClass('highlighted');
                            scrollToOption($visible.eq(0));
                        }
                        break;

                    case 38: // Up
                        e.preventDefault();
                        $visible.removeClass('highlighted');
                        if (index > 0) {
                            $visible.eq(index - 1).addClass('highlighted');
                            scrollToOption($visible.eq(index - 1));
                        } else {
                            $visible.eq($visible.length - 1).addClass('highlighted');
                            scrollToOption($visible.eq($visible.length - 1));
                        }
                        break;

                    case 13: // Enter
                        e.preventDefault();
                        if ($current.length) {
                            var value = $current.attr('data-value');
                            $select.val(value).trigger('change');
                            updateCurrentDisplay();
                            closeDropdown();
                        }
                        break;

                    case 27: // Escape
                        e.preventDefault();
                        closeDropdown();
                        break;
                }
            });

            function openDropdown() {
                $wrapper.addClass('is-open');
                $searchContainer.show();
                filterOptions(''); // 全てのオプションを表示
                $results.show();
                $search.focus();
            }

            function closeDropdown() {
                $wrapper.removeClass('is-open');
                $searchContainer.hide();
                $results.hide();
                $search.val('');
                $options.removeClass('highlighted');
            }

            function filterOptions(query) {
                // スペースで分割して複数キーワードのAND検索に対応
                var keywords = query.trim().split(/\s+/).filter(function(k) {
                    return k.length > 0;
                });

                var visibleCount = 0;

                $options.each(function() {
                    var $opt = $(this);
                    var text = $opt.text().toLowerCase();
                    var matches = true;

                    if (keywords.length === 0) {
                        // 空のクエリは全て表示
                        matches = true;
                    } else {
                        // 全てのキーワードが含まれているかチェック（AND検索）
                        for (var i = 0; i < keywords.length; i++) {
                            if (text.indexOf(keywords[i]) === -1) {
                                matches = false;
                                break;
                            }
                        }
                    }

                    $opt.toggle(matches);
                    if (matches) visibleCount++;
                });

                $options.removeClass('highlighted');
                // 最初の表示オプションをハイライト
                $options.filter(':visible').first().addClass('highlighted');
            }

            function updateCurrentDisplay() {
                var selectedText = $select.find('option:selected').text();
                $currentText.text(selectedText);
            }

            function scrollToOption($option) {
                if ($option.length && $results.is(':visible')) {
                    var optionTop = $option.position().top;
                    var optionHeight = $option.outerHeight();
                    var containerHeight = $results.height();
                    var scrollTop = $results.scrollTop();

                    if (optionTop < 0) {
                        $results.scrollTop(scrollTop + optionTop);
                    } else if (optionTop + optionHeight > containerHeight) {
                        $results.scrollTop(scrollTop + optionTop + optionHeight - containerHeight);
                    }
                }
            }
        });
    }

    // DOMContentLoadedとカスタマイザー準備完了の両方で初期化
    $(document).ready(function() {
        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize.bind('ready', init);
        } else {
            init();
        }

        // 念のため遅延初期化も
        setTimeout(init, 500);
        setTimeout(init, 1000);
    });

})(jQuery);
