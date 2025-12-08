<?php
/**
 * 管理画面設定ページ
 *
 * @package Backbone_SEO_LLMO
 */

// セキュリティ：直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 管理画面にテーマ設定ページを追加
 */
function backbone_admin_menu() {
    add_theme_page(
        __('Backbone Theme 設定', 'backbone-seo-llmo'),
        __('テーマ設定', 'backbone-seo-llmo'),
        'manage_options',
        'seo-optimus-settings',
        'backbone_settings_page'
    );
}
add_action('admin_menu', 'backbone_admin_menu');

/**
 * 表示設定ページにカスタムフロントページ使用中の通知を追加
 */
function backbone_reading_settings_notice() {
    // 表示設定ページでのみ表示
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'options-reading') {
        return;
    }

    $current_mode = get_theme_mod('backbone_front_page_mode', 'custom');

    if ($current_mode === 'custom') {
        ?>
        <div class="notice notice-info">
            <p>
                <strong><?php _e('ℹ️ カスタムフロントページ使用中', 'backbone-seo-llmo'); ?></strong><br>
                <?php _e('現在、テーマ独自のカスタムフロントページ機能を使用しています。下記の「ホームページの表示」設定は無効化されています。', 'backbone-seo-llmo'); ?><br>
                <a href="<?php echo admin_url('customize.php?autofocus[section]=static_front_page'); ?>">
                    <?php _e('カスタマイザーで設定を変更', 'backbone-seo-llmo'); ?>
                </a>
            </p>
        </div>
        <style>
            /* カスタムモード時にホームページ表示設定を半透明にして無効化 */
            #front-static-pages { position: relative; }
            #front-static-pages fieldset { opacity: 0.5; pointer-events: none; }
            /* グレーアウト理由を説明するオーバーレイ */
            #front-static-pages::before {
                content: "⚠️ この設定は現在無効です。テーマのカスタムフロントページ機能を使用中のため、上記の通知から設定を変更してください。";
                display: block;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                padding: 12px;
                margin-bottom: 15px;
                font-size: 13px;
                line-height: 1.6;
                color: #2c3338;
            }
        </style>
        <?php
    }
}
add_action('admin_notices', 'backbone_reading_settings_notice');

/**
 * テーマ設定ページの内容
 */
function backbone_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Backbone Theme テーマ設定', 'backbone-seo-llmo'); ?></h1>
        <p><?php _e('このテーマの詳細設定は「外観 > カスタマイズ」から行えます。', 'backbone-seo-llmo'); ?></p>

        <div class="card">
            <h2><?php _e('テーマの特徴', 'backbone-seo-llmo'); ?></h2>
            <ul>
                <li><?php _e('21つのカラーテーマ（モノクロ、ミッドナイトフレア、オーシャンストーム、フォレストエッジ、ブラッドムーン、他）', 'backbone-seo-llmo'); ?></li>
                <li><?php _e('6つのデザインパターン（クラシック、ミニマル、ソフト、プレーン、アウトライン、カード）', 'backbone-seo-llmo'); ?></li>
                <li><?php _e('4つのレイアウト（1カラム、2カラム、3カラム、フルワイド）', 'backbone-seo-llmo'); ?></li>
                <li><?php _e('詳細なタイポグラフィ設定（11種類のプリセット + カスタム設定）', 'backbone-seo-llmo'); ?></li>
                <li><?php _e('投稿タイプ別レイアウト設定', 'backbone-seo-llmo'); ?></li>

                <li><?php _e('レスポンシブデザイン対応', 'backbone-seo-llmo'); ?></li>
                <li><?php _e('SEO最適化', 'backbone-seo-llmo'); ?></li>
            </ul>
        </div>

        <div class="card">
            <h2><?php _e('設定リンク', 'backbone-seo-llmo'); ?></h2>
            <p>
                <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">
                    <?php _e('カスタマイザーを開く', 'backbone-seo-llmo'); ?>
                </a>

                <a href="<?php echo admin_url('widgets.php'); ?>" class="button">
                    <?php _e('ウィジェット設定', 'backbone-seo-llmo'); ?>
                </a>
                <a href="<?php echo admin_url('options-permalink.php'); ?>" class="button">
                    <?php _e('パーマリンク設定', 'backbone-seo-llmo'); ?>
                </a>
            </p>
        </div>

        <div class="card">
            <h2><?php _e('ヘルプ', 'backbone-seo-llmo'); ?></h2>
            <h3><?php _e('よくある問題の解決方法', 'backbone-seo-llmo'); ?></h3>
            <ul>


                <li><strong><?php _e('ウィジェットが表示されない', 'backbone-seo-llmo'); ?></strong><br>
                    → <?php _e('レイアウト設定を2カラムまたは3カラムに変更してください', 'backbone-seo-llmo'); ?></li>

                <li><strong><?php _e('タイポグラフィの詳細設定をしたい', 'backbone-seo-llmo'); ?></strong><br>
                    → <?php _e('カスタマイザーのタイポグラフィパターンで「カスタム」を選択', 'backbone-seo-llmo'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * テーマ診断・バックアップページを追加
 */
function backbone_add_diagnostics_page() {
    add_theme_page(
        __('テーマ診断・バックアップ', 'backbone-seo-llmo'),
        __('診断・バックアップ', 'backbone-seo-llmo'),
        'manage_options',
        'backbone-diagnostics',
        'backbone_diagnostics_page'
    );
}
add_action('admin_menu', 'backbone_add_diagnostics_page');

/**
 * テーマ診断・バックアップページの内容
 */
function backbone_diagnostics_page() {
    // バックアップ操作の処理
    if (isset($_POST['backbone_backup_action']) && check_admin_referer('backbone_backup_nonce')) {
        $action = sanitize_text_field($_POST['backbone_backup_action']);

        if ($action === 'create') {
            backbone_create_settings_backup();
            echo '<div class="notice notice-success"><p>' . __('バックアップを作成しました。', 'backbone-seo-llmo') . '</p></div>';
        } elseif ($action === 'restore' && isset($_POST['backup_index'])) {
            $index = intval($_POST['backup_index']);
            backbone_restore_settings_backup($index);
            echo '<div class="notice notice-success"><p>' . __('バックアップを復元しました。', 'backbone-seo-llmo') . '</p></div>';
        } elseif ($action === 'delete' && isset($_POST['backup_index'])) {
            $index = intval($_POST['backup_index']);
            backbone_delete_settings_backup($index);
            echo '<div class="notice notice-success"><p>' . __('バックアップを削除しました。', 'backbone-seo-llmo') . '</p></div>';
        } elseif ($action === 'delete_all') {
            backbone_delete_all_backups();
            echo '<div class="notice notice-success"><p>' . __('すべてのバックアップを削除しました。', 'backbone-seo-llmo') . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e('テーマ診断・バックアップ', 'backbone-seo-llmo'); ?></h1>

        <div class="card">
            <h2><?php _e('設定バックアップ', 'backbone-seo-llmo'); ?></h2>
            <p><?php _e('テーマ設定のバックアップを作成・復元できます。最大5件まで保持されます。', 'backbone-seo-llmo'); ?></p>

            <form method="post">
                <?php wp_nonce_field('backbone_backup_nonce'); ?>
                <input type="hidden" name="backbone_backup_action" value="create">
                <button type="submit" class="button button-primary">
                    <?php _e('バックアップを作成', 'backbone-seo-llmo'); ?>
                </button>
            </form>

            <h3 style="margin-top: 20px;"><?php _e('保存済みバックアップ', 'backbone-seo-llmo'); ?></h3>
            <?php
            $backups = get_option('backbone_settings_backups', array());
            if (!empty($backups)) {
                echo '<table class="widefat" style="margin-top: 10px;">';
                echo '<thead><tr><th>' . __('日時', 'backbone-seo-llmo') . '</th><th>' . __('操作', 'backbone-seo-llmo') . '</th></tr></thead>';
                echo '<tbody>';
                foreach ($backups as $index => $backup) {
                    $date = isset($backup['date']) ? $backup['date'] : __('日時不明', 'backbone-seo-llmo');
                    echo '<tr>';
                    echo '<td>' . esc_html($date) . '</td>';
                    echo '<td>';
                    echo '<form method="post" style="display:inline;">';
                    wp_nonce_field('backbone_backup_nonce');
                    echo '<input type="hidden" name="backbone_backup_action" value="restore">';
                    echo '<input type="hidden" name="backup_index" value="' . $index . '">';
                    echo '<button type="submit" class="button" onclick="return confirm(\'' . esc_js(__('このバックアップを復元しますか？', 'backbone-seo-llmo')) . '\');">' . __('復元', 'backbone-seo-llmo') . '</button>';
                    echo '</form> ';
                    echo '<form method="post" style="display:inline;">';
                    wp_nonce_field('backbone_backup_nonce');
                    echo '<input type="hidden" name="backbone_backup_action" value="delete">';
                    echo '<input type="hidden" name="backup_index" value="' . $index . '">';
                    echo '<button type="submit" class="button" onclick="return confirm(\'' . esc_js(__('このバックアップを削除しますか？', 'backbone-seo-llmo')) . '\');">' . __('削除', 'backbone-seo-llmo') . '</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';

                echo '<form method="post" style="margin-top: 10px;">';
                wp_nonce_field('backbone_backup_nonce');
                echo '<input type="hidden" name="backbone_backup_action" value="delete_all">';
                echo '<button type="submit" class="button" onclick="return confirm(\'' . esc_js(__('すべてのバックアップを削除しますか？', 'backbone-seo-llmo')) . '\');">' . __('すべて削除', 'backbone-seo-llmo') . '</button>';
                echo '</form>';
            } else {
                echo '<p>' . __('バックアップはありません。', 'backbone-seo-llmo') . '</p>';
            }
            ?>
        </div>

        <div class="card">
            <h2><?php _e('カスタマイザー変更ログ', 'backbone-seo-llmo'); ?></h2>
            <?php
            $change_log = get_option('backbone_customizer_change_log', array());
            if (!empty($change_log)) {
                echo '<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ccd0d4; padding: 10px; background: #f6f7f7;">';
                echo '<ul style="margin: 0; padding-left: 20px;">';
                foreach (array_reverse($change_log) as $log) {
                    echo '<li><code>' . esc_html($log['date']) . '</code> - ' . esc_html($log['message']) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<p>' . __('変更ログはありません。', 'backbone-seo-llmo') . '</p>';
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * 設定バックアップを作成
 */
function backbone_create_settings_backup() {
    $theme_mods = get_theme_mods();
    // 数値インデックスや空の値を除外してクリーンな配列にする
    if (is_array($theme_mods)) {
        $theme_mods = array_filter($theme_mods, function($value, $key) {
            return !is_numeric($key) && $value !== '';
        }, ARRAY_FILTER_USE_BOTH);
    }
    $backups = get_option('backbone_settings_backups', array());

    // 新しいバックアップを先頭に追加
    array_unshift($backups, array(
        'date' => current_time('Y-m-d H:i:s'),
        'data' => $theme_mods
    ));

    // 最大5件まで保持
    $backups = array_slice($backups, 0, 5);

    update_option('backbone_settings_backups', $backups);

    // サブディレクトリ設定の独立バックアップも保存
    backbone_save_independent_backup();

    // 変更ログに記録
    backbone_log_customizer_change(__('手動バックアップを作成', 'backbone-seo-llmo'));
}

/**
 * 設定バックアップを復元
 */
function backbone_restore_settings_backup($index) {
    $backups = get_option('backbone_settings_backups', array());

    if (isset($backups[$index]) && isset($backups[$index]['data'])) {
        $backup_data = $backups[$index]['data'];

        // 現在の設定を取得
        $current_mods = get_theme_mods();

        // バックアップデータで上書き
        foreach ($backup_data as $key => $value) {
            set_theme_mod($key, $value);
        }

        // バックアップに存在しない設定を削除
        foreach ($current_mods as $key => $value) {
            if (!isset($backup_data[$key])) {
                remove_theme_mod($key);
            }
        }

        // 変更ログに記録
        backbone_log_customizer_change(sprintf(__('バックアップ #%d を復元', 'backbone-seo-llmo'), $index + 1));
    }
}

/**
 * 設定バックアップを削除
 */
function backbone_delete_settings_backup($index) {
    $backups = get_option('backbone_settings_backups', array());

    if (isset($backups[$index])) {
        unset($backups[$index]);
        $backups = array_values($backups); // インデックスを振り直し
        update_option('backbone_settings_backups', $backups);

        // 変更ログに記録
        backbone_log_customizer_change(sprintf(__('バックアップ #%d を削除', 'backbone-seo-llmo'), $index + 1));
    }
}

/**
 * すべてのバックアップを削除
 */
function backbone_delete_all_backups() {
    delete_option('backbone_settings_backups');

    // 変更ログに記録
    backbone_log_customizer_change(__('すべてのバックアップを削除', 'backbone-seo-llmo'));
}

/**
 * サブディレクトリ設定の独立バックアップを保存
 */
function backbone_save_independent_backup() {
    $theme_mods = get_theme_mods();
    $subdirectory_settings = array();

    // サブディレクトリ関連の設定を抽出（数値キーは除外）
    if (is_array($theme_mods)) {
        foreach ($theme_mods as $key => $value) {
            if (!is_numeric($key) && strpos($key, 'subdirectory') !== false) {
                $subdirectory_settings[$key] = $value;
            }
        }
    }

    if (!empty($subdirectory_settings)) {
        update_option('backbone_subdirectory_settings', $subdirectory_settings);
    }
}

/**
 * カスタマイザー変更をログに記録
 */
function backbone_log_customizer_change($message) {
    $log = get_option('backbone_customizer_change_log', array());

    // 新しいログを先頭に追加
    array_unshift($log, array(
        'date' => current_time('Y-m-d H:i:s'),
        'message' => $message
    ));

    // 最大50件まで保持
    $log = array_slice($log, 0, 50);

    update_option('backbone_customizer_change_log', $log);
}

/**
 * カスタマイザー保存時に自動バックアップ
 */
function backbone_auto_backup_on_customizer_save() {
    backbone_create_settings_backup();
}
add_action('customize_save_after', 'backbone_auto_backup_on_customizer_save');

/**
 * テーマ切り替え時に自動バックアップ
 */
function backbone_auto_backup_on_theme_switch($new_name, $new_theme, $old_theme) {
    if ($old_theme->get_stylesheet() === 'backbone-seo-llmo' || $old_theme->get_stylesheet() === 'wp-theme-backbone-seo-llmo-main') {
        backbone_create_settings_backup();
        backbone_log_customizer_change(__('テーマ切り替え前の自動バックアップ', 'backbone-seo-llmo'));
    }
}
add_action('switch_theme', 'backbone_auto_backup_on_theme_switch', 10, 3);

/**
 * theme_modsが空の場合のフォールバック復元
 */
function backbone_subdirectory_fallback_check() {
    $theme_mods = get_theme_mods();
    $subdirectory_count = isset($theme_mods['subdirectory_count']) ? $theme_mods['subdirectory_count'] : 0;

    // サブディレクトリ設定が消えている場合、独立バックアップから復元を試みる
    if ($subdirectory_count === 0) {
        $independent_backup = get_option('backbone_subdirectory_settings', array());

        if (!empty($independent_backup) && isset($independent_backup['subdirectory_count']) && $independent_backup['subdirectory_count'] > 0) {
            foreach ($independent_backup as $key => $value) {
                set_theme_mod($key, $value);
            }

            backbone_log_customizer_change(__('独立バックアップからサブディレクトリ設定を復元', 'backbone-seo-llmo'));
        }
    }
}
add_action('after_setup_theme', 'backbone_subdirectory_fallback_check');

