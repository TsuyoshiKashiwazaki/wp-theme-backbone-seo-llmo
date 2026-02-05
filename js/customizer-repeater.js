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

                // REST APIで記事を取得（絶対URLを使用）
                var baseUrl = window.location.origin;
                var endpoint = baseUrl + '/wp-json/wp/v2/';
                if (postType === 'post') {
                    endpoint += 'posts';
                } else if (postType === 'page') {
                    endpoint += 'pages';
                } else {
                    endpoint += postType;
                }

                // カスタマイザーのコンテキストを回避するため、fetchを使用
                var fullUrl = endpoint + '?per_page=100&orderby=date&order=desc&_fields=id,title';

                fetch(fullUrl, {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(posts) {
                    $selectField.empty();
                    $selectField.append('<option value="0">— 選択してください —</option>');

                    if (posts && posts.length > 0) {
                        $.each(posts, function(index, post) {
                            var title = (post.title && post.title.rendered) ? post.title.rendered : '（タイトルなし）';
                            var $option = $('<option value="' + post.id + '">' + title + '</option>');
                            $selectField.append($option);
                        });

                        // 選択されていた値を復元
                        if (selectedValue && selectedValue !== '0') {
                            $selectField.val(selectedValue);
                        }
                    } else {
                        $selectField.append('<option value="0">— 記事がありません —</option>');
                    }
                })
                .catch(function() {
                    $selectField.empty();
                    $selectField.append('<option value="0">— 読み込みに失敗しました —</option>');
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
                    var $label = $('<label>' + field.label + '</label>');
                    var $input;

                    switch(field.type) {
                        case 'select':
                            $input = $('<select class="widefat" data-field="' + fieldKey + '"></select>');

                            // カテゴリー選択の特別処理
                            if (fieldKey === 'category') {
                                $input.append('<option value="0">全カテゴリー</option>');
                                var baseUrl = window.location.origin;
                                fetch(baseUrl + '/wp-json/wp/v2/categories?per_page=100', {
                                    credentials: 'same-origin'
                                })
                                .then(function(response) { return response.json(); })
                                .then(function(categories) {
                                    $.each(categories, function(i, cat) {
                                        var $option = $('<option value="' + cat.id + '">' + cat.name + '</option>');
                                        if (cat.id == fieldValue) {
                                            $option.prop('selected', true);
                                        }
                                        $input.append($option);
                                    });
                                });
                            }
                            // タグ選択の特別処理
                            else if (fieldKey === 'tag') {
                                $input.append('<option value="0">全タグ</option>');
                                var baseUrl = window.location.origin;
                                fetch(baseUrl + '/wp-json/wp/v2/tags?per_page=100', {
                                    credentials: 'same-origin'
                                })
                                .then(function(response) { return response.json(); })
                                .then(function(tags) {
                                    $.each(tags, function(i, tag) {
                                        var $option = $('<option value="' + tag.id + '">' + tag.name + '</option>');
                                        if (tag.id == fieldValue) {
                                            $option.prop('selected', true);
                                        }
                                        $input.append($option);
                                    });
                                });
                            }
                            // 投稿タイプフィルター選択の特別処理
                            else if (fieldKey === 'post_type_filter') {
                                var baseUrl = window.location.origin;
                                fetch(baseUrl + '/wp-json/wp/v2/types', {
                                    credentials: 'same-origin'
                                })
                                .then(function(response) { return response.json(); })
                                .then(function(types) {
                                    // デフォルトで投稿を追加
                                    $input.append('<option value="post">投稿</option>');

                                    $.each(types, function(slug, type) {
                                        var isInternal = slug.startsWith('wp_') || slug === 'attachment' || slug === 'nav_menu_item';
                                        var hasRestApi = type.rest_base && type.rest_base.length > 0;

                                        if (!isInternal && hasRestApi && slug !== 'post') {
                                            var $option = $('<option value="' + slug + '">' + type.name + '</option>');
                                            if (slug === fieldValue) {
                                                $option.prop('selected', true);
                                            }
                                            $input.append($option);
                                        }
                                    });

                                    if (fieldValue) {
                                        $input.val(fieldValue);
                                    }
                                });
                            }
                            // 作成者選択の特別処理
                            else if (fieldKey === 'author') {
                                $input.append('<option value="0">全作成者</option>');
                                var baseUrl = window.location.origin;
                                fetch(baseUrl + '/wp-json/wp/v2/users?per_page=100', {
                                    credentials: 'same-origin'
                                })
                                .then(function(response) { return response.json(); })
                                .then(function(users) {
                                    $.each(users, function(i, user) {
                                        var $option = $('<option value="' + user.id + '">' + user.name + '</option>');
                                        if (user.id == fieldValue) {
                                            $option.prop('selected', true);
                                        }
                                        $input.append($option);
                                    });
                                });
                            }
                            // 投稿タイプ選択の特別処理
                            else if (fieldKey === 'post_type') {
                                // デフォルトの投稿タイプ
                                $input.append('<option value="post">投稿</option>');
                                $input.append('<option value="page">固定ページ</option>');

                                // カスタム投稿タイプを取得
                                var baseUrl = window.location.origin;
                                fetch(baseUrl + '/wp-json/wp/v2/types', {
                                    credentials: 'same-origin'
                                })
                                .then(function(response) { return response.json(); })
                                .then(function(types) {
                                    $.each(types, function(slug, type) {
                                        // 内部的な投稿タイプを除外し、REST APIが有効な投稿タイプのみ表示
                                        var isInternal = slug.startsWith('wp_') || slug === 'attachment' || slug === 'nav_menu_item';
                                        var hasRestApi = type.rest_base && type.rest_base.length > 0;

                                        if (!isInternal && hasRestApi && slug !== 'post' && slug !== 'page') {
                                            var $option = $('<option value="' + slug + '">' + type.name + '</option>');
                                            $input.append($option);
                                        }
                                    });

                                    // 値を設定
                                    if (fieldValue) {
                                        $input.val(fieldValue);
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
                                    var $option = $('<option value="' + choiceKey + '">' + field.choices[choiceKey] + '</option>');
                                    if (choiceKey == fieldValue) {
                                        $option.prop('selected', true);
                                    }
                                    $input.append($option);
                                });
                            }
                            break;

                        case 'checkbox':
                            $input = $('<input type="checkbox" data-field="' + fieldKey + '" />');
                            if (fieldValue === true || fieldValue === 'true' || fieldValue === '1' || fieldValue === 1) {
                                $input.prop('checked', true);
                            }
                            break;

                        case 'textarea':
                            $input = $('<textarea class="widefat" data-field="' + fieldKey + '" rows="3">' + fieldValue + '</textarea>');
                            break;

                        case 'url':
                            $input = $('<input type="url" class="widefat" data-field="' + fieldKey + '" value="' + fieldValue + '" />');
                            break;

                        case 'number':
                            $input = $('<input type="number" class="widefat" data-field="' + fieldKey + '" value="' + fieldValue + '" min="1" max="100" />');
                            break;

                        default:
                            $input = $('<input type="text" class="widefat" data-field="' + fieldKey + '" value="' + fieldValue + '" />');
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

                var newItem = {};
                Object.keys(fieldsConfig).forEach(function(fieldKey) {
                    var field = fieldsConfig[fieldKey];
                    if (field.type === 'checkbox') {
                        newItem[fieldKey] = true;
                    } else if (fieldKey === 'post_type') {
                        newItem[fieldKey] = 'post';
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