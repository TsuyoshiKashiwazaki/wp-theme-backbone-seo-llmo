/**
 * カスタマイザー リピーターコントロール
 *
 * @package Backbone_SEO_LLMO
 */

(function($) {
    'use strict';

    // DOMとCSSが完全に読み込まれるまで待機
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRepeater);
    } else {
        initRepeater();
    }

    /**
     * REST API の rendered（HTML）やエンティティを含む文字列を、表示用のプレーンテキストにする。
     * DOMParser の文書ではスクリプトもイベントハンドラーも動かない。結果は .text() で入れるので HTML として解釈されない。
     */
    function plainText(value) {
        if (value === null || value === undefined) {
            return '';
        }
        var doc = new DOMParser().parseFromString(String(value), 'text/html');
        return doc.body ? doc.body.textContent : '';
    }

    /**
     * <option> を作る。値も表示名も文字列の連結で HTML に入れない（保存値や REST の名前に " や < があっても壊れない）
     */
    function makeOption(value, label) {
        return $('<option></option>').val(String(value)).text(label);
    }

    /**
     * REST API の URL。PHP の rest_url() で作った起点を使うので、サブディレクトリ設置や
     * 基本のパーマリンク（?rest_route=）のサイトでも正しい URL になる。
     * 起点にクエリが付いていても（rest_url フィルターで ?lang=ja を足す構成など）壊さないよう、文字列の連結ではなく URL として組み立てる:
     * ?rest_route= の形ならその値の後ろに、そうでなければパスの後ろにルートを足し、引数はクエリに入れる
     */
    function restUrl(path, params) {
        var data = window.backboneRepeaterData || {};
        var root = data.restRoot || (window.location.origin + '/wp-json/');
        var route = String(path).replace(/^\/+/, '');
        var url = new URL(root, window.location.href);
        if (url.searchParams.has('rest_route')) {
            url.searchParams.set('rest_route', url.searchParams.get('rest_route').replace(/\/+$/, '') + '/' + route);
        } else {
            url.pathname = url.pathname.replace(/\/+$/, '') + '/' + route;
        }
        $.each(params || {}, function (key, value) {
            url.searchParams.set(key, String(value));
        });
        return url.toString();
    }

    /**
     * 投稿タイプの一覧の REST パス（rest_namespace / rest_base。PHP から渡す）。無ければ wp/v2/<スラッグ>
     */
    function postTypeRestPath(postType) {
        var data = window.backboneRepeaterData || {};
        if (data.restPaths && data.restPaths[postType]) {
            return data.restPaths[postType];
        }
        if (postType === 'post') {
            return 'wp/v2/posts';
        }
        if (postType === 'page') {
            return 'wp/v2/pages';
        }
        return 'wp/v2/' + postType;
    }

    /**
     * 一覧をすべてのページ取得する。per_page の上限は 100 なので、X-WP-TotalPages を見て順に取る
     */
    function fetchAllPages(path, params) {
        var all = [];
        var maxPages = 100;
        function getPage(page) {
            var query = $.extend({}, params || {}, { per_page: 100, page: page });
            // restUrl() は起点が URL として不正だと例外を投げるので、Promise の中で呼び、呼び出し元の .catch（保存値を残す）に流す
            return Promise.resolve()
                .then(function() {
                    return fetch(restUrl(path, query), { credentials: 'same-origin' });
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    var totalPages = parseInt(response.headers.get('X-WP-TotalPages'), 10) || 1;
                    return response.json().then(function(rows) {
                        if (Array.isArray(rows)) {
                            all = all.concat(rows);
                        }
                        if (page < totalPages && page < maxPages) {
                            return getPage(page + 1);
                        }
                        return all;
                    });
                });
        }
        return getPage(1);
    }

    /**
     * 1 回だけ取得する（投稿タイプの一覧など、ページ送りの無いもの）
     */
    function fetchJson(path) {
        return Promise.resolve()
            .then(function() {
                return fetch(restUrl(path), { credentials: 'same-origin' });
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            });
    }

    /**
     * 保存済みの値が選択肢に無いとき（非公開になった記事・削除されたターム等）、値を消さずに残す
     */
    function keepSavedValue($select, savedValue) {
        if (savedValue === undefined || savedValue === null || savedValue === '' || String(savedValue) === '0') {
            return;
        }
        var exists = $select.find('option').filter(function() {
            return $(this).val() === String(savedValue);
        }).length > 0;
        if (!exists) {
            $select.append(makeOption(savedValue, '（保存済み: ' + savedValue + '・一覧に無い項目）'));
        }
        $select.val(String(savedValue));
    }

    function initRepeater() {
        /**
         * リピーターコントロールの初期化
         */
        wp.customize.controlConstructor.repeater = wp.customize.Control.extend({
            ready: function() {
            var control = this;
            var $wrapper = control.container.find('.repeater-control-wrapper');
            var $container = $wrapper.find('.repeater-items-container');
            var $addButton = $wrapper.find('.repeater-add-item');
            var $dataField = $wrapper.find('.repeater-data-field');
            var fieldsConfig = JSON.parse($wrapper.find('.repeater-fields-config').text());
            var maxItems = parseInt($addButton.data('max-items')) || 0;

            // 初期値を読み込み
            var items = [];
            try {
                var value = control.setting.get();
                if (value && value !== '') {
                    items = JSON.parse(value);
                    if (!Array.isArray(items)) {
                        items = [];
                    }
                }
            } catch(e) {
                items = [];
            }

            // 項目を描画
            function renderItems() {
                $container.empty();

                if (items.length === 0) {
                    $container.html('<p class="repeater-empty-message">項目がありません。「項目を追加」ボタンで追加してください。</p>');
                } else {
                    items.forEach(function(itemData, index) {
                        var $item = createItemElement(itemData, index);
                        $container.append($item);
                    });
                }

                // 最大項目数のチェック
                if (maxItems > 0 && items.length >= maxItems) {
                    $addButton.prop('disabled', true);
                } else {
                    $addButton.prop('disabled', false);
                }

                updateDataField();
            }

            // 投稿タイプごとに記事を読み込む関数
            function loadPostsForType(postType, $selectField, selectedValue) {
                if (!postType) return;

                // クリアして再追加
                $selectField.empty();
                $selectField.append('<option value="0">— 読み込み中... —</option>');

                // REST APIで記事を取得（rest_url() 起点・投稿タイプの rest_base・全ページ）
                // カスタマイザーのコンテキストを回避するため、fetchを使用
                fetchAllPages(postTypeRestPath(postType), { orderby: 'date', order: 'desc', _fields: 'id,title' })
                .then(function(posts) {
                    $selectField.empty();
                    $selectField.append(makeOption(0, '— 選択してください —'));

                    if (posts && posts.length > 0) {
                        $.each(posts, function(index, post) {
                            var title = (post.title && post.title.rendered) ? plainText(post.title.rendered) : '';
                            $selectField.append(makeOption(post.id, title !== '' ? title : '（タイトルなし）'));
                        });
                    } else {
                        $selectField.append(makeOption(0, '— 記事がありません —'));
                    }

                    // 選択されていた値を復元（一覧に無くなっていても値は残す）
                    keepSavedValue($selectField, selectedValue);
                })
                .catch(function() {
                    $selectField.empty();
                    $selectField.append(makeOption(0, '— 読み込みに失敗しました —'));
                    // 取得に失敗しても、保存済みの選択は消さない（そのまま保存しても失われないように）
                    keepSavedValue($selectField, selectedValue);
                });
            }

            // 項目要素を作成
            function createItemElement(itemData, index) {
                var $item = $('<div class="repeater-item" data-index="' + index + '"></div>');
                var $header = $('<div class="repeater-item-header"></div>');
                var $handle = $('<span class="repeater-item-handle dashicons dashicons-menu"></span>');
                var $title = $('<span class="repeater-item-title">項目 ' + (index + 1) + '</span>');
                var $toggle = $('<button type="button" class="repeater-item-toggle dashicons dashicons-arrow-down"></button>');
                var $remove = $('<button type="button" class="repeater-item-remove dashicons dashicons-no-alt"></button>');

                $header.append($handle, $title, $toggle, $remove);
                $item.append($header);

                var $content = $('<div class="repeater-item-content"></div>');

                // フィールドを作成
                Object.keys(fieldsConfig).forEach(function(fieldKey) {
                    var field = fieldsConfig[fieldKey];
                    var fieldValue = itemData[fieldKey] || '';
                    var $fieldWrapper = $('<div class="repeater-field"></div>');
                    var $label = $('<label></label>').text(field.label);
                    var $input;

                    switch(field.type) {
                        case 'select':
                            $input = $('<select class="widefat"></select>').attr('data-field', fieldKey);

                            // カテゴリー選択の特別処理
                            if (fieldKey === 'category') {
                                $input.append(makeOption(0, '全カテゴリー'));
                                fetchAllPages('wp/v2/categories', { _fields: 'id,name' })
                                .then(function(categories) {
                                    $.each(categories, function(i, cat) {
                                        $input.append(makeOption(cat.id, plainText(cat.name)));
                                    });
                                    keepSavedValue($input, fieldValue);
                                })
                                .catch(function() {
                                    keepSavedValue($input, fieldValue);
                                });
                            }
                            // タグ選択の特別処理
                            else if (fieldKey === 'tag') {
                                $input.append(makeOption(0, '全タグ'));
                                fetchAllPages('wp/v2/tags', { _fields: 'id,name' })
                                .then(function(tags) {
                                    $.each(tags, function(i, tag) {
                                        $input.append(makeOption(tag.id, plainText(tag.name)));
                                    });
                                    keepSavedValue($input, fieldValue);
                                })
                                .catch(function() {
                                    keepSavedValue($input, fieldValue);
                                });
                            }
                            // 投稿タイプフィルター選択の特別処理
                            else if (fieldKey === 'post_type_filter') {
                                // デフォルトで投稿を追加
                                $input.append(makeOption('post', '投稿'));
                                fetchJson('wp/v2/types')
                                .then(function(types) {
                                    $.each(types, function(slug, type) {
                                        var isInternal = slug.startsWith('wp_') || slug === 'attachment' || slug === 'nav_menu_item';
                                        var hasRestApi = type.rest_base && type.rest_base.length > 0;

                                        if (!isInternal && hasRestApi && slug !== 'post') {
                                            $input.append(makeOption(slug, plainText(type.name)));
                                        }
                                    });
                                    keepSavedValue($input, fieldValue);
                                })
                                .catch(function() {
                                    keepSavedValue($input, fieldValue);
                                });
                            }
                            // 作成者選択の特別処理
                            else if (fieldKey === 'author') {
                                $input.append(makeOption(0, '全作成者'));
                                fetchAllPages('wp/v2/users', { _fields: 'id,name' })
                                .then(function(users) {
                                    $.each(users, function(i, user) {
                                        $input.append(makeOption(user.id, plainText(user.name)));
                                    });
                                    keepSavedValue($input, fieldValue);
                                })
                                .catch(function() {
                                    keepSavedValue($input, fieldValue);
                                });
                            }
                            // 投稿タイプ選択の特別処理
                            else if (fieldKey === 'post_type') {
                                // デフォルトの投稿タイプ
                                $input.append(makeOption('post', '投稿'));
                                $input.append(makeOption('page', '固定ページ'));

                                // カスタム投稿タイプを取得
                                fetchJson('wp/v2/types')
                                .then(function(types) {
                                    $.each(types, function(slug, type) {
                                        // 内部的な投稿タイプを除外し、REST APIが有効な投稿タイプのみ表示
                                        var isInternal = slug.startsWith('wp_') || slug === 'attachment' || slug === 'nav_menu_item';
                                        var hasRestApi = type.rest_base && type.rest_base.length > 0;

                                        if (!isInternal && hasRestApi && slug !== 'post' && slug !== 'page') {
                                            $input.append(makeOption(slug, plainText(type.name)));
                                        }
                                    });

                                    // 値を設定
                                    if (fieldValue) {
                                        keepSavedValue($input, fieldValue);
                                    }
                                })
                                .catch(function() {
                                    if (fieldValue) {
                                        keepSavedValue($input, fieldValue);
                                    }
                                });

                                // 既存の値を先に設定
                                if (fieldValue) {
                                    $input.val(fieldValue);
                                }

                                // 投稿タイプが変更されたら、投稿IDドロップダウンを更新
                                $input.on('change', function() {
                                    var selectedType = $(this).val();
                                    var $postIdField = $(this).closest('.repeater-item').find('[data-field="post_id"]');

                                    if ($postIdField.length) {
                                        loadPostsForType(selectedType, $postIdField, null);
                                    }
                                });
                            }
                            // 投稿ID選択の特別処理
                            else if (fieldKey === 'post_id') {
                                $input.append('<option value="0">— 選択してください —</option>');

                                // クロージャで$inputと$itemを保持
                                (function($inputField, $currentItem, currentFieldValue) {
                                    // 少し遅延させてpost_typeフィールドが確実に存在するようにする
                                    setTimeout(function() {
                                        var $postTypeField = $currentItem.find('[data-field="post_type"]');
                                        var postType = $postTypeField.val() || itemData.post_type || 'post';
                                        loadPostsForType(postType, $inputField, currentFieldValue);
                                    }, 500);
                                })($input, $item, fieldValue);
                            }
                            // その他の通常の選択フィールド
                            else if (field.choices) {
                                Object.keys(field.choices).forEach(function(choiceKey) {
                                    var $option = makeOption(choiceKey, field.choices[choiceKey]);
                                    if (choiceKey == fieldValue) {
                                        $option.prop('selected', true);
                                    }
                                    $input.append($option);
                                });
                            }
                            break;

                        // 保存値は文字列の連結で HTML に入れない（" や < を含む見出しで属性が壊れたり、
                        // 属性を抜け出したイベントハンドラーが動いたりしないよう、.attr() と .val() で入れる）
                        case 'checkbox':
                            $input = $('<input type="checkbox" />').attr('data-field', fieldKey);
                            if (fieldValue === true || fieldValue === 'true' || fieldValue === '1' || fieldValue === 1) {
                                $input.prop('checked', true);
                            }
                            break;

                        case 'textarea':
                            $input = $('<textarea class="widefat" rows="3"></textarea>').attr('data-field', fieldKey).val(String(fieldValue));
                            break;

                        case 'url':
                            $input = $('<input type="url" class="widefat" />').attr('data-field', fieldKey).val(String(fieldValue));
                            break;

                        case 'number':
                            $input = $('<input type="number" class="widefat" min="1" max="100" />').attr('data-field', fieldKey).val(String(fieldValue));
                            break;

                        default:
                            $input = $('<input type="text" class="widefat" />').attr('data-field', fieldKey).val(String(fieldValue));
                    }

                    $fieldWrapper.append($label, $input);

                    // display_typeフィールドの場合、他のフィールドの表示/非表示を制御
                    if (fieldKey === 'display_type') {
                        $input.on('change', function() {
                            var selectedType = $(this).val();
                            var $item = $(this).closest('.repeater-item');

                            // 全ての関連フィールドを非表示
                            $item.find('[data-field="category"]').closest('.repeater-field').hide();
                            $item.find('[data-field="tag"]').closest('.repeater-field').hide();
                            $item.find('[data-field="post_type_filter"]').closest('.repeater-field').hide();
                            $item.find('[data-field="include_child_post_types"]').closest('.repeater-field').hide();
                            $item.find('[data-field="author"]').closest('.repeater-field').hide();
                            $item.find('[data-field="date_range"]').closest('.repeater-field').hide();

                            // 選択された表示対象のフィールドのみ表示
                            switch(selectedType) {
                                case 'category':
                                    $item.find('[data-field="category"]').closest('.repeater-field').show();
                                    break;
                                case 'tag':
                                    $item.find('[data-field="tag"]').closest('.repeater-field').show();
                                    break;
                                case 'post_type':
                                    $item.find('[data-field="post_type_filter"]').closest('.repeater-field').show();
                                    $item.find('[data-field="include_child_post_types"]').closest('.repeater-field').show();
                                    break;
                                case 'author':
                                    $item.find('[data-field="author"]').closest('.repeater-field').show();
                                    break;
                                case 'date':
                                    $item.find('[data-field="date_range"]').closest('.repeater-field').show();
                                    break;
                            }
                        });

                        // 初期状態で適切なフィールドを表示
                        setTimeout(function() {
                            $input.trigger('change');
                        }, 100);
                    }

                    // show_archive_linkチェックボックスの条件付き表示
                    if (fieldKey === 'show_archive_link') {
                        $input.on('change', function() {
                            var isChecked = $(this).prop('checked');
                            var $item = $(this).closest('.repeater-item');
                            var $linkTypeField = $item.find('[data-field="archive_link_type"]').closest('.repeater-field');
                            var $customUrlField = $item.find('[data-field="archive_link_custom_url"]').closest('.repeater-field');

                            if (isChecked) {
                                $linkTypeField.show();
                                // archive_link_typeの値に応じてカスタムURLフィールドを表示
                                var linkType = $item.find('[data-field="archive_link_type"]').val();
                                if (linkType === 'custom') {
                                    $customUrlField.show();
                                } else {
                                    $customUrlField.hide();
                                }
                            } else {
                                $linkTypeField.hide();
                                $customUrlField.hide();
                            }
                        });

                        // 初期状態で適切なフィールドを表示
                        setTimeout(function() {
                            $input.trigger('change');
                        }, 150);
                    }

                    // archive_link_typeセレクトボックスの条件付き表示
                    if (fieldKey === 'archive_link_type') {
                        $input.on('change', function() {
                            var selectedType = $(this).val();
                            var $item = $(this).closest('.repeater-item');
                            var $customUrlField = $item.find('[data-field="archive_link_custom_url"]').closest('.repeater-field');

                            if (selectedType === 'custom') {
                                $customUrlField.show();
                            } else {
                                $customUrlField.hide();
                            }
                        });

                        // 初期状態で適切なフィールドを表示
                        setTimeout(function() {
                            $input.trigger('change');
                        }, 200);
                    }

                    // archive_link_custom_urlのバリデーション（カスタムURL入力時）
                    if (fieldKey === 'archive_link_custom_url') {
                        $input.on('blur', function() {
                            var $item = $(this).closest('.repeater-item');
                            var linkType = $item.find('[data-field="archive_link_type"]').val();
                            var showArchiveLink = $item.find('[data-field="show_archive_link"]').prop('checked');
                            var customUrl = $(this).val().trim();

                            // カスタムURLが選択されていて、一覧表示リンクがONで、URLが空の場合
                            if (showArchiveLink && linkType === 'custom' && !customUrl) {
                                $(this).css('border-color', '#dc3232');
                                if (!$(this).next('.archive-url-error').length) {
                                    $(this).after('<p class="archive-url-error" style="color: #dc3232; font-size: 12px; margin: 4px 0 0;">カスタムURLを入力してください</p>');
                                }
                            } else {
                                $(this).css('border-color', '');
                                $(this).next('.archive-url-error').remove();
                            }
                        });
                    }

                    $content.append($fieldWrapper);
                });

                $item.append($content);

                // イベント
                $toggle.on('click', function() {
                    $item.toggleClass('collapsed');
                    $(this).toggleClass('dashicons-arrow-down dashicons-arrow-up');
                });

                $remove.on('click', function() {
                    if (confirm('この項目を削除してもよろしいですか？')) {
                        items.splice(index, 1);
                        renderItems();
                    }
                });

                $content.find('input, select, textarea').on('change input', function() {
                    var fieldKey = $(this).data('field');
                    if ($(this).attr('type') === 'checkbox') {
                        items[index][fieldKey] = $(this).prop('checked');
                    } else {
                        items[index][fieldKey] = $(this).val();
                    }
                    updateDataField();
                });

                return $item;
            }

            // データフィールドを更新
            function updateDataField() {
                var jsonValue = JSON.stringify(items);
                $dataField.val(jsonValue);
                control.setting.set(jsonValue);
            }

            // 項目を追加
            $addButton.on('click', function() {
                if (maxItems > 0 && items.length >= maxItems) {
                    alert('最大' + maxItems + '項目まで追加できます。');
                    return;
                }

                // select は、画面で最初に表示される選択肢を初期値にする。
                // 空文字のままだと、画面の表示（例「1カラム」「今月」）と保存される値が食い違う
                var selectFirstValues = {
                    category: '0',
                    tag: '0',
                    author: '0',
                    post_type_filter: 'post',
                    post_id: '0'
                };
                var newItem = {};
                Object.keys(fieldsConfig).forEach(function(fieldKey) {
                    var field = fieldsConfig[fieldKey];
                    if (fieldKey === 'include_child_post_types') {
                        newItem[fieldKey] = false;
                    } else if (field.type === 'checkbox') {
                        newItem[fieldKey] = true;
                    } else if (fieldKey === 'post_type') {
                        newItem[fieldKey] = 'post';
                    } else if (field.type === 'select' && field.choices && Object.keys(field.choices).length > 0) {
                        newItem[fieldKey] = Object.keys(field.choices)[0];
                    } else if (field.type === 'select' && selectFirstValues.hasOwnProperty(fieldKey)) {
                        newItem[fieldKey] = selectFirstValues[fieldKey];
                    } else {
                        newItem[fieldKey] = '';
                    }
                });

                items.push(newItem);
                renderItems();
            });

            // ソート可能にする
            $container.sortable({
                handle: '.repeater-item-handle',
                placeholder: 'repeater-item-placeholder',
                update: function() {
                    var newItems = [];
                    $container.find('.repeater-item').each(function() {
                        var index = $(this).data('index');
                        newItems.push(items[index]);
                    });
                    items = newItems;
                    renderItems();
                }
            });

            // 初期描画
            renderItems();
        }
    });
    }

})(jQuery);