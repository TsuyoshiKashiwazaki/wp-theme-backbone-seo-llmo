/**
 * セクション順序コントロール
 *
 * @package Backbone_SEO_LLMO
 */

(function($) {
    'use strict';

    wp.customize.controlConstructor.section_order = wp.customize.Control.extend({
        ready: function() {
            var control = this;
            var $wrapper = control.container.find('.section-order-control-wrapper');
            var $list = $wrapper.find('.section-order-list');
            var $field = $wrapper.find('.section-order-field');
            var sections = JSON.parse($wrapper.find('.section-order-config').text());

            // 初期値を読み込み
            var currentOrder = [];
            try {
                var value = control.setting.get();
                if (value && value !== '') {
                    currentOrder = JSON.parse(value);
                }
            } catch(e) {
                currentOrder = [];
            }

            // デフォルト順序（空の場合）
            if (currentOrder.length === 0) {
                currentOrder = Object.keys(sections);
            } else {
                // 設定に存在するがcurrentOrderに存在しないセクションを追加
                var updated = false;
                Object.keys(sections).forEach(function(sectionKey) {
                    if (currentOrder.indexOf(sectionKey) === -1) {
                        currentOrder.push(sectionKey);
                        updated = true;
                    }
                });

                // 新しいセクションが追加された場合、即座に保存
                if (updated) {
                    var jsonValue = JSON.stringify(currentOrder);
                    $field.val(jsonValue);
                    control.setting.set(jsonValue);
                }
            }

            // リストを描画
            function renderList() {
                $list.empty();

                currentOrder.forEach(function(sectionKey) {
                    if (!sections[sectionKey]) return;

                    var section = sections[sectionKey];
                    var $item = $('<li class="section-order-item" data-section="' + sectionKey + '"></li>');

                    var $handle = $('<span class="dashicons dashicons-menu section-order-handle"></span>');
                    var $label = $('<span class="section-order-label">' + section.label + '</span>');
                    var $type = $('<span class="section-order-type">' + section.type + '</span>');

                    $item.append($handle, $label, $type);
                    $list.append($item);
                });

                updateField();
            }

            // データフィールドを更新
            function updateField() {
                var jsonValue = JSON.stringify(currentOrder);
                $field.val(jsonValue);
                control.setting.set(jsonValue);
            }

            // ソート可能にする
            $list.sortable({
                handle: '.section-order-handle',
                placeholder: 'section-order-placeholder',
                update: function() {
                    currentOrder = [];
                    $list.find('.section-order-item').each(function() {
                        currentOrder.push($(this).data('section'));
                    });
                    updateField();
                }
            });

            // 初期描画
            renderList();
        }
    });

})(jQuery);
