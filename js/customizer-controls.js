/**
 * カスタマイザーコントロールのスクリプト
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // アーカイブセクションの見出しを処理
        $('.archive-section-start').each(function() {
            var $heading = $(this).closest('li');
            var $nextControls = $();

            // 見出しの後に続くコントロールを収集（次の見出しまで）
            $heading.nextAll('li').each(function() {
                if ($(this).find('.archive-section-start').length > 0) {
                    return false; // 次の見出しが見つかったら停止
                }
                $nextControls = $nextControls.add($(this));
            });

            // 見出しとコントロールに枠線用のクラスを追加
            $heading.addClass('archive-section-heading-item');
            $nextControls.addClass('archive-section-content-item');

            // 最後のコントロールに特別なクラスを追加
            if ($nextControls.length > 0) {
                $nextControls.last().addClass('archive-section-last-item');
            }
        });
    });
})(jQuery);
