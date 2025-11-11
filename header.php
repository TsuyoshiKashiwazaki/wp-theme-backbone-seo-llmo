<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<!-- 既存訪問者のキャッシュを一度だけクリア（Service Worker含む） -->
<script>
(function forceClearLegacyCache() {
    var clearFlag = 'backbone_cache_cleared_20250111_v1';

    if (!localStorage.getItem(clearFlag)) {
        try {
            // LocalStorage/SessionStorageをクリア
            localStorage.clear();
            sessionStorage.clear();

            // Service Workerのキャッシュもクリア
            if ('serviceWorker' in navigator && 'caches' in window) {
                caches.keys().then(function(cacheNames) {
                    return Promise.all(
                        cacheNames.map(function(cacheName) {
                            return caches.delete(cacheName);
                        })
                    );
                }).then(function() {
                    console.log('All caches cleared successfully');
                    // Service Workerも登録解除
                    return navigator.serviceWorker.getRegistrations();
                }).then(function(registrations) {
                    return Promise.all(
                        registrations.map(function(registration) {
                            return registration.unregister();
                        })
                    );
                }).then(function() {
                    console.log('Service Workers unregistered');
                    localStorage.setItem(clearFlag, 'true');
                    // ページをリロードして完全にクリア
                    location.reload(true);
                }).catch(function(error) {
                    console.error('Failed to clear Service Worker cache:', error);
                    localStorage.setItem(clearFlag, 'true');
                });
            } else {
                localStorage.setItem(clearFlag, 'true');
                console.log('Legacy cache cleared successfully');
            }
        } catch (e) {
            console.error('Failed to clear cache:', e);
        }
    }
})();
</script>

<div class="site-wrapper">
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="site-branding">
                    <?php
                    // サブディレクトリ対応のロゴ表示関数を使用
                    if (function_exists('backbone_display_custom_logo')) {
                        $logo_displayed = backbone_display_custom_logo();
                        if (!$logo_displayed) {
                            // ロゴが設定されていない場合はサイトタイトルを表示
                            $logo_settings = backbone_get_subdirectory_logo_settings();
                            ?>
                            <h1 class="site-title">
                                <a href="<?php echo esc_url($logo_settings['home_url']); ?>" rel="home">
                                    <?php echo esc_html(backbone_get_site_title()); ?>
                                </a>
                            </h1>
                            <?php
                        }
                    } else {
                        // 関数が存在しない場合は従来の処理
                        if (has_custom_logo()) {
                            the_custom_logo();
                        } else {
                            ?>
                            <h1 class="site-title">
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                    <?php echo esc_html(backbone_get_site_title()); ?>
                                </a>
                            </h1>
                            <?php
                        }
                    }
                    ?>

                    <?php
                    // サブディレクトリ対応のヘッダーメッセージとキャッチフレーズを取得
                    if (function_exists('backbone_get_header_message')) {
                        $header_message = backbone_get_header_message();
                        if ($header_message) :
                        ?>
                            <p class="site-description" style="color: var(--header-link-color) !important;"><?php echo wp_kses_post($header_message); ?></p>
                        <?php else : ?>
                            <?php
                            $description = backbone_get_tagline();
                            if ($description || is_customize_preview()) :
                            ?>
                                <p class="site-description" style="color: var(--header-link-color) !important;"><?php echo esc_html($description); ?></p>
                            <?php endif; ?>
                        <?php endif;
                    } else {
                        // 関数が存在しない場合は従来の処理
                        $header_message = get_theme_mod('header_message');
                        if ($header_message) :
                        ?>
                            <p class="site-description" style="color: var(--header-link-color) !important;"><?php echo esc_html($header_message); ?></p>
                        <?php else : ?>
                            <?php
                            $description = get_bloginfo('description', 'display');
                            if ($description || is_customize_preview()) :
                            ?>
                                <p class="site-description" style="color: var(--header-link-color) !important;"><?php echo $description; ?></p>
                            <?php endif; ?>
                        <?php endif;
                    }
                    ?>
                </div>

                <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('メインメニュー', 'backbone-seo-llmo'); ?>">
                    <?php
                    // 通常のメニューを表示
                    $menu_items = wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => 'backbone_fallback_menu',
                        'echo'           => false,
                    ));
                    
                    // 検索ボタンが有効の場合、メニューの最後に追加
                    if (get_theme_mod('search_button_enabled', true)) {
                        $search_button = '<li class="menu-item menu-item-search menu-item-depth-0">
                            <button class="search-toggle" aria-label="検索を開く" aria-expanded="false">
                                <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                </svg>
                            </button>
                        </li>';
                        
                        // </ul>の直前に検索ボタンを挿入
                        if ($menu_items) {
                            $menu_items = str_replace('</ul>', $search_button . '</ul>', $menu_items);
                        }
                    }
                    
                    echo $menu_items;
                    ?>
                </nav>
            </div>
        </div>
    </header>

    <?php if (get_theme_mod('search_button_enabled', true)) : ?>
        <!-- 検索ポップアップ -->
        <div class="search-popup-overlay" aria-hidden="true">
            <div class="search-popup-container" role="dialog" aria-modal="true" aria-labelledby="search-popup-title">
                <div class="search-popup-header">
                    <h2 id="search-popup-title" class="search-popup-title">サイト内検索</h2>
                    <button class="search-popup-close" aria-label="検索を閉じる">&times;</button>
                </div>
                <form class="search-popup-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" 
                           class="search-popup-input" 
                           name="s" 
                           placeholder="検索キーワードを入力..." 
                           aria-label="検索キーワード"
                           autocomplete="off">
                    <button type="submit" class="search-popup-submit" aria-label="検索実行">
                        <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <main>
        <?php
        $layout = backbone_get_layout();
        $sidebar_position = get_theme_mod('sidebar_position', 'right');
        $is_full_width = !backbone_has_sidebar();
        ?>
        
        <div class="container">
            <div class="main-content">
                <?php if (!$is_full_width && backbone_is_three_columns()) : ?>
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

                <div class="content-area">