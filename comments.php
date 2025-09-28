<?php
/**
 * コメントテンプレート
 *
 * @package Backbone_SEO_LLMO
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            if ('1' === $comment_count) {
                printf(__('「%1$s」への1件のコメント', 'backbone-seo-llmo'), '<span>' . get_the_title() . '</span>');
            } else {
                printf(
                    _nx(
                        '「%1$s」への%2$s件のコメント',
                        '「%1$s」への%2$s件のコメント',
                        $comment_count,
                        'comments title',
                        'backbone-seo-llmo'
                    ),
                    '<span>' . get_the_title() . '</span>',
                    number_format_i18n($comment_count)
                );
            }
            ?>
        </h2>

        <?php the_comments_navigation(); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'callback'   => 'backbone_comment_callback',
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation();

        // コメントが閉じられている場合のメッセージ
        if (!comments_open()) :
        ?>
            <p class="no-comments"><?php _e('コメントは受け付けていません。', 'backbone-seo-llmo'); ?></p>
        <?php endif; ?>

    <?php endif; // have_comments() ?>

    <?php
    comment_form(array(
        'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h2>',
        'title_reply'        => __('コメントを残す', 'backbone-seo-llmo'),
        'title_reply_to'     => __('%s にコメントする', 'backbone-seo-llmo'),
        'cancel_reply_link'  => __('返信をキャンセル', 'backbone-seo-llmo'),
        'label_submit'       => __('コメントを投稿', 'backbone-seo-llmo'),
        'submit_button'      => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
        'submit_field'       => '<p class="form-submit">%1$s %2$s</p>',
        'format'             => 'xhtml',
        'comment_field'      => '<p class="comment-form-comment"><label for="comment">' . __('コメント', 'backbone-seo-llmo') . ' <span class="required">*</span></label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>',
        'fields'             => array(
            'author' => '<p class="comment-form-author">' .
                       '<label for="author">' . __('名前', 'backbone-seo-llmo') . ' <span class="required">*</span></label> ' .
                       '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245" required="required" /></p>',
            'email'  => '<p class="comment-form-email">' .
                       '<label for="email">' . __('メールアドレス', 'backbone-seo-llmo') . ' <span class="required">*</span></label> ' .
                       '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes" required="required" /></p>',
            'url'    => '<p class="comment-form-url">' .
                       '<label for="url">' . __('ウェブサイト', 'backbone-seo-llmo') . '</label> ' .
                       '<input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" /></p>',
        ),
    ));
    ?>

</div>

<?php
/**
 * カスタムコメント表示コールバック
 */
function backbone_comment_callback($comment, $args, $depth) {
    if ('div' === $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
    <?php if ('div' != $args['style']) : ?>
        <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <?php endif; ?>

    <div class="comment-author vcard">
        <?php if ($args['avatar_size'] != 0) echo get_avatar($comment, $args['avatar_size']); ?>
        <div class="comment-metadata">
            <cite class="fn"><?php echo get_comment_author_link(); ?></cite>
            <div class="comment-meta commentmetadata">
                <a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>">
                    <?php
                    printf(
                        __('%1$s %2$s', 'backbone-seo-llmo'),
                        get_comment_date(),
                        get_comment_time()
                    );
                    ?>
                </a>
                <?php edit_comment_link(__('(編集)', 'backbone-seo-llmo'), '  ', ''); ?>
            </div>
        </div>
    </div>

    <?php if ($comment->comment_approved == '0') : ?>
        <em class="comment-awaiting-moderation"><?php _e('コメントは承認待ちです。', 'backbone-seo-llmo'); ?></em>
        <br />
    <?php endif; ?>

    <div class="comment-content">
        <?php comment_text(); ?>
    </div>

    <div class="reply">
        <?php
        comment_reply_link(array_merge($args, array(
            'add_below' => $add_below,
            'depth'     => $depth,
            'max_depth' => $args['max_depth'],
            'reply_text' => __('返信', 'backbone-seo-llmo')
        )));
        ?>
    </div>

    <?php if ('div' != $args['style']) : ?>
        </div>
    <?php endif; ?>
    <?php
}
?>
