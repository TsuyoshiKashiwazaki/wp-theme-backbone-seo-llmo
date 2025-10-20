/**
 * カスタマイザー リピーターコントロール
 *
 * @package Backbone_SEO_LLMO
 */

(function($) {
    'use strict';

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
                            if (field.choices) {
                                Object.keys(field.choices).forEach(function(choiceKey) {
                                    var $option = $('<option value="' + choiceKey + '">' + field.choices[choiceKey] + '</option>');
                                    if (choiceKey === fieldValue) {
                                        $option.prop('selected', true);
                                    }
                                    $input.append($option);
                                });
                            }
                            break;
                        case 'textarea':
                            $input = $('<textarea class="widefat" data-field="' + fieldKey + '" rows="3">' + fieldValue + '</textarea>');
                            break;
                        case 'url':
                            $input = $('<input type="url" class="widefat" data-field="' + fieldKey + '" value="' + fieldValue + '" />');
                            break;
                        default:
                            $input = $('<input type="text" class="widefat" data-field="' + fieldKey + '" value="' + fieldValue + '" />');
                    }

                    $fieldWrapper.append($label, $input);
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
                    items[index][fieldKey] = $(this).val();
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
                    newItem[fieldKey] = '';
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

})(jQuery);
