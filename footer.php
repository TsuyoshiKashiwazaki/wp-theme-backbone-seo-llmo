        </div>

        <?php
        $layout = backbone_get_layout();
        $is_full_width = !backbone_has_sidebar();
        ?>

        <?php if (!$is_full_width) : ?>
            <?php if (backbone_is_three_columns()) : ?>
                <aside class="sidebar sidebar-2">
                    <?php if (is_active_sidebar('sidebar-2')) : ?>
                        <?php dynamic_sidebar('sidebar-2'); ?>
                    <?php else : ?>
                        <div class="widget">
                            <h3 class="widget-title"><?php _e('サイドバー2', 'backbone-seo-llmo'); ?></h3>
                            <p><?php _e('ウィジェットエリアです。管理画面の「外観 > ウィジェット」からコンテンツを追加してください。', 'backbone-seo-llmo'); ?></p>
                        </div>
                    <?php endif; ?>
                </aside>
            <?php elseif (backbone_is_two_columns()) : ?>
                <aside class="sidebar sidebar-1">
                    <?php if (is_active_sidebar('sidebar-1')) : ?>
                        <?php dynamic_sidebar('sidebar-1'); ?>
                    <?php else : ?>
                        <div class="widget">
                            <h3 class="widget-title"><?php _e('サイドバー1', 'backbone-seo-llmo'); ?></h3>
                            <p><?php _e('ウィジェットエリアです。管理画面の「外観 > ウィジェット」からコンテンツを追加してください。', 'backbone-seo-llmo'); ?></p>
                        </div>
                    <?php endif; ?>
                </aside>
            <?php endif; ?>
        <?php endif; ?>
        
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <?php 
            if (is_active_sidebar('footer-widgets')) :
                // ウィジェットの内容を取得して空かどうかチェック
                ob_start();
                dynamic_sidebar('footer-widgets');
                $widget_content = ob_get_clean();
                
                // 空のHTMLタグのみの場合は出力しない
                $cleaned_content = trim(strip_tags($widget_content));
                if (!empty($cleaned_content)) :
            ?>
                <div class="footer-widgets">
                    <?php echo $widget_content; ?>
                </div>
            <?php 
                endif;
            endif; 
            ?>

            <?php
            // サブディレクトリ対応のフッターメッセージを取得
            if (function_exists('backbone_get_footer_message')) {
                $footer_message = backbone_get_footer_message();
            } else {
                $footer_message = get_theme_mod('footer_message');
            }
            if ($footer_message) :
            ?>
                <div class="footer-message">
                    <?php echo wp_kses_post($footer_message); ?>
                </div>
            <?php endif; ?>

            <div class="footer-content">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_id'        => 'footer-menu',
                    'container'      => 'nav',
                    'container_class' => 'footer-navigation',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ));
                ?>

                <p class="site-info">
                    <?php
                    $copyright = get_theme_mod('footer_copyright', sprintf(__('© %s All rights reserved.', 'kashiwazaki-searchcraft'), date('Y')));
                    echo esc_html($copyright);
                    ?>
                </p>

                <p class="powered-by">
                    <span>WP Theme: </span>
                    <a href="https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo" target="_blank" rel="noopener">
                        Backbone Theme for SEO + LLMO
                    </a>
                    <span class="sep"> | </span>
                    <span>Creator: </span>
                    <a href="https://www.tsuyoshikashiwazaki.jp/profile/" target="_blank" rel="noopener">
                        柏崎剛
                    </a>
                </p>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
