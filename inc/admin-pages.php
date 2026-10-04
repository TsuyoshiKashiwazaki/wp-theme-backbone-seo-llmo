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
 * バックアップのエクスポート処理（HTML出力前に実行）
 */
function backbone_handle_backup_export() {
    if (!isset($_POST['backbone_backup_action']) || $_POST['backbone_backup_action'] !== 'export') {
        return;
    }

    if (!isset($_POST['backup_index'])) {
        return;
    }

    // nonceチェック
    if (!check_admin_referer('backbone_backup_nonce')) {
        return;
    }

    // 権限チェック
    if (!current_user_can('manage_options')) {
        return;
    }

    $index = intval($_POST['backup_index']);
    $backups = get_option('backbone_settings_backups', array());

    if (!isset($backups[$index])) {
        wp_die(__('バックアップが見つかりません。', 'backbone-seo-llmo'));
    }

    $backup = $backups[$index];

    // エクスポート用のデータを準備
    $export_data = array(
        'export_version' => '1.0',
        'export_date' => current_time('Y-m-d H:i:s'),
        'backup' => $backup,
    );

    // ファイル名を生成
    $date_slug = isset($backup['date']) ? sanitize_title($backup['date']) : 'backup';
    $site_slug = sanitize_title(parse_url(home_url(), PHP_URL_HOST));
    $filename = sprintf('backbone-backup-%s-%s.json', $site_slug, $date_slug);

    // HTTPヘッダーを設定
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // JSONを出力
    echo json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
add_action('admin_init', 'backbone_handle_backup_export');

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
    // リセット処理
    if (isset($_POST['backbone_reset_action']) && check_admin_referer('backbone_reset_nonce')) {
        backbone_reset_theme_settings();
        echo '<div class="notice notice-success"><p>' . __('テーマ設定をリセットしました。', 'backbone-seo-llmo') . '</p></div>';
    }
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

        <div class="card">
            <h2><?php _e('設定のリセット', 'backbone-seo-llmo'); ?></h2>
            <p><?php _e('すべてのテーマ設定をデフォルト値に戻します。この操作は元に戻せません。', 'backbone-seo-llmo'); ?></p>
            <form method="post">
                <?php wp_nonce_field('backbone_reset_nonce'); ?>
                <input type="hidden" name="backbone_reset_action" value="1">
                <button type="submit" class="button" style="color: #d63638;" onclick="return confirm('<?php echo esc_js(__('本当にすべてのテーマ設定をリセットしますか？\n\nこの操作は元に戻せません。事前にバックアップを作成することをお勧めします。', 'backbone-seo-llmo')); ?>');">
                    <?php _e('テーマ設定をリセット', 'backbone-seo-llmo'); ?>
                </button>
            </form>
        </div>
    </div>
    <?php
}

/**
 * テーマ設定をリセット
 */
function backbone_reset_theme_settings() {
    // リセット前にバックアップを作成（変更ログへの記録は backbone_create_settings_backup が行う）
    backbone_create_settings_backup('reset');

    // すべてのtheme_modsを削除
    remove_theme_mods();

    // サブディレクトリ設定の独立バックアップも削除する
    // 残しておくと、次のリクエストで backbone_subdirectory_fallback_check() が「設定が消えた」と判断して復活させてしまう
    // （リセット前の設定は上で作成した通常のバックアップに含まれている）
    delete_option('backbone_subdirectory_settings');

    // 変更ログに記録
    backbone_log_customizer_change(__('テーマ設定をリセット', 'backbone-seo-llmo'));
}

/**
 * テーマ設定のバックアップと復元ページを追加
 */
function backbone_add_diagnostics_page() {
    add_theme_page(
        __('テーマ設定のバックアップと復元', 'backbone-seo-llmo'),
        __('バックアップと復元', 'backbone-seo-llmo'),
        'manage_options',
        'backbone-diagnostics',
        'backbone_diagnostics_page'
    );
}
add_action('admin_menu', 'backbone_add_diagnostics_page');

/**
 * テーマ設定のバックアップと復元ページの内容
 */
function backbone_diagnostics_page() {
    $restore_warnings = array();

    // バックアップ操作の処理
    if (isset($_POST['backbone_backup_action']) && check_admin_referer('backbone_backup_nonce')) {
        $action = sanitize_text_field($_POST['backbone_backup_action']);

        if ($action === 'create') {
            backbone_create_settings_backup();
            echo '<div class="notice notice-success"><p>' . __('バックアップを作成しました。', 'backbone-seo-llmo') . '</p></div>';
        } elseif ($action === 'restore' && isset($_POST['backup_index'])) {
            $index = intval($_POST['backup_index']);
            // まず検証を実行
            $warnings = backbone_restore_settings_backup($index, true);
            if (!empty($warnings)) {
                $restore_warnings = $warnings;
            }
            // 復元を実行
            if (backbone_restore_settings_backup($index, false) === true) {
                echo '<div class="notice notice-success"><p>' . __('バックアップを復元しました。', 'backbone-seo-llmo') . '</p></div>';
            } elseif (!current_user_can('unfiltered_html')) {
                echo '<div class="notice notice-error"><p>' . __('バックアップを復元する権限がありません（HTMLを無制限に扱う権限が必要です）。', 'backbone-seo-llmo') . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>' . __('バックアップを復元できませんでした。', 'backbone-seo-llmo') . '</p></div>';
            }
            if (!empty($restore_warnings)) {
                echo '<div class="notice notice-warning"><p><strong>' . __('注意: 以下の問題が検出されました:', 'backbone-seo-llmo') . '</strong></p><ul style="margin-left: 20px; list-style: disc;">';
                foreach ($restore_warnings as $warning) {
                    echo '<li>' . esc_html($warning) . '</li>';
                }
                echo '</ul></div>';
            }
        } elseif ($action === 'delete' && isset($_POST['backup_index'])) {
            $index = intval($_POST['backup_index']);
            backbone_delete_settings_backup($index);
            echo '<div class="notice notice-success"><p>' . __('バックアップを削除しました。', 'backbone-seo-llmo') . '</p></div>';
        } elseif ($action === 'delete_all') {
            backbone_delete_all_backups();
            echo '<div class="notice notice-success"><p>' . __('すべてのバックアップを削除しました。', 'backbone-seo-llmo') . '</p></div>';
        } elseif ($action === 'import') {
            $result = backbone_import_backup_json();
            if (is_wp_error($result)) {
                echo '<div class="notice notice-error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                echo '<div class="notice notice-success"><p>' . __('バックアップをインポートしました。', 'backbone-seo-llmo') . '</p></div>';
            }
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e('テーマ設定のバックアップと復元', 'backbone-seo-llmo'); ?></h1>

        <div class="card">
            <h2><?php _e('設定バックアップ', 'backbone-seo-llmo'); ?></h2>
            <p><?php _e('テーマ設定のバックアップを作成・復元できます。最大5件まで保持されます。', 'backbone-seo-llmo'); ?></p>
            <p class="description"><?php _e('バックアップには画像IDや投稿IDなどサイト固有のデータが含まれます。他のサイトに適用する場合は、復元後にこれらの設定を再確認してください。', 'backbone-seo-llmo'); ?></p>

            <div style="margin-top: 15px;">
                <form method="post" style="display:inline-block; margin-right: 10px;">
                    <?php wp_nonce_field('backbone_backup_nonce'); ?>
                    <input type="hidden" name="backbone_backup_action" value="create">
                    <button type="submit" class="button button-primary">
                        <?php _e('バックアップを作成', 'backbone-seo-llmo'); ?>
                    </button>
                </form>

                <form method="post" enctype="multipart/form-data" style="display:inline-block;">
                    <?php wp_nonce_field('backbone_backup_nonce'); ?>
                    <input type="hidden" name="backbone_backup_action" value="import">
                    <input type="file" name="backbone_import_file" accept=".json" style="display:inline-block; width: auto;">
                    <button type="submit" class="button">
                        <?php _e('JSONファイルをインポート', 'backbone-seo-llmo'); ?>
                    </button>
                </form>
            </div>

            <h3 style="margin-top: 20px;"><?php _e('保存済みバックアップ', 'backbone-seo-llmo'); ?></h3>
            <?php
            $backups = get_option('backbone_settings_backups', array());
            if (!empty($backups)) {
                echo '<table class="widefat" style="margin-top: 10px;">';
                echo '<thead><tr>';
                echo '<th>' . __('日時', 'backbone-seo-llmo') . '</th>';
                echo '<th>' . __('情報', 'backbone-seo-llmo') . '</th>';
                echo '<th>' . __('操作', 'backbone-seo-llmo') . '</th>';
                echo '</tr></thead>';
                echo '<tbody>';
                foreach ($backups as $index => $backup) {
                    // 保存済みのデータが壊れていても（インポートした値の型が違う等）一覧を表示できるよう、型を確かめてから使う
                    $date = (isset($backup['date']) && is_scalar($backup['date'])) ? (string) $backup['date'] : __('日時不明', 'backbone-seo-llmo');
                    $meta = (isset($backup['meta']) && is_array($backup['meta'])) ? $backup['meta'] : array();

                    // バックアップの種類（自動のものと、手動・リセット前などのものを見分けられるように）
                    $type_labels = array(
                        'auto' => __('自動（公開時）', 'backbone-seo-llmo'),
                        'manual' => __('手動', 'backbone-seo-llmo'),
                        'reset' => __('リセット前', 'backbone-seo-llmo'),
                        'theme_switch' => __('テーマ切り替え前', 'backbone-seo-llmo'),
                        'import' => __('インポート', 'backbone-seo-llmo'),
                    );
                    $type_label = (isset($backup['type']) && is_string($backup['type']) && isset($type_labels[$backup['type']]))
                        ? $type_labels[$backup['type']]
                        : '';

                    echo '<tr>';
                    echo '<td>' . esc_html($date) . ($type_label !== '' ? '<br><small style="color:#666;">' . esc_html($type_label) . '</small>' : '') . '</td>';

                    // メタ情報を表示
                    echo '<td>';
                    if (!empty($meta)) {
                        $info_parts = array();
                        // 親テーマのバージョンを表示（設定互換性の判断基準）
                        $display_version = isset($meta['parent_theme_version'])
                            ? $meta['parent_theme_version']
                            : (isset($meta['theme_version']) ? $meta['theme_version'] : null);
                        if ($display_version && is_scalar($display_version)) {
                            $info_parts[] = 'Backbone SEO LLMO v' . esc_html((string) $display_version);
                        }
                        if (isset($meta['site_url']) && is_string($meta['site_url'])) {
                            $parsed = parse_url($meta['site_url']);
                            if (is_array($parsed) && isset($parsed['host'])) {
                                $info_parts[] = '<small style="color:#666;">' . esc_html($parsed['host']) . '</small>';
                            }
                        }
                        echo implode(' ', $info_parts);
                    } else {
                        echo '<span style="color:#999;">' . __('(メタ情報なし)', 'backbone-seo-llmo') . '</span>';
                    }
                    echo '</td>';

                    echo '<td>';
                    // 復元ボタン
                    echo '<form method="post" style="display:inline;">';
                    wp_nonce_field('backbone_backup_nonce');
                    echo '<input type="hidden" name="backbone_backup_action" value="restore">';
                    echo '<input type="hidden" name="backup_index" value="' . $index . '">';
                    echo '<button type="submit" class="button" onclick="return confirm(\'' . esc_js(__('このバックアップを復元しますか？\\n\\n注意: サイト固有の設定（画像、投稿、URLなど）は再設定が必要な場合があります。', 'backbone-seo-llmo')) . '\');">' . __('復元', 'backbone-seo-llmo') . '</button>';
                    echo '</form> ';

                    // エクスポートボタン
                    echo '<form method="post" style="display:inline;">';
                    wp_nonce_field('backbone_backup_nonce');
                    echo '<input type="hidden" name="backbone_backup_action" value="export">';
                    echo '<input type="hidden" name="backup_index" value="' . $index . '">';
                    echo '<button type="submit" class="button">' . __('ダウンロード', 'backbone-seo-llmo') . '</button>';
                    echo '</form> ';

                    // 削除ボタン
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
            <h2><?php _e('他サイトへの適用について', 'backbone-seo-llmo'); ?></h2>
            <p><?php _e('バックアップを他のサイトに適用する場合、以下の設定は再設定が必要です：', 'backbone-seo-llmo'); ?></p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><strong><?php _e('ロゴ画像', 'backbone-seo-llmo'); ?></strong>: <?php _e('サイトロゴ、サブディレクトリロゴ', 'backbone-seo-llmo'); ?></li>
                <li><strong><?php _e('フロントページ画像', 'backbone-seo-llmo'); ?></strong>: <?php _e('ヒーロー画像', 'backbone-seo-llmo'); ?></li>
                <li><strong><?php _e('個別記事セクション', 'backbone-seo-llmo'); ?></strong>: <?php _e('選択した投稿・固定ページ', 'backbone-seo-llmo'); ?></li>
                <li><strong><?php _e('サブディレクトリURL', 'backbone-seo-llmo'); ?></strong>: <?php _e('ホームURL設定', 'backbone-seo-llmo'); ?></li>
            </ul>
            <p class="description"><?php _e('デザイン設定（カラーテーマ、レイアウト、タイポグラフィなど）はそのまま適用されます。', 'backbone-seo-llmo'); ?></p>
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
 * バックアップの種類ごとの最大件数
 *
 * カスタマイザーで「公開」するたびに作られる自動バックアップ（auto）と、それ以外
 * （手動・リセット前・テーマ切り替え前・インポート、および種類の記録が無い既存のもの）を分けて数える。
 * 一緒に数えると、5 回公開するだけで手動のバックアップやリセット前の退避が押し出されて黙って消える。
 */
if (!defined('BACKBONE_BACKUP_MAX_AUTO')) {
    define('BACKBONE_BACKUP_MAX_AUTO', 5);
}
if (!defined('BACKBONE_BACKUP_MAX_KEPT')) {
    define('BACKBONE_BACKUP_MAX_KEPT', 5);
}

/**
 * バックアップの一覧を、種類ごとの最大件数まで減らす（新しい順に並んでいる前提）
 *
 * @param array $backups バックアップの配列（新しい順）
 * @return array 減らした配列（並び順はそのまま）
 */
function backbone_trim_settings_backups($backups) {
    if (!is_array($backups)) {
        return array();
    }
    $auto_count = 0;
    $kept_count = 0;
    $trimmed = array();
    foreach ($backups as $backup) {
        $is_auto = is_array($backup) && isset($backup['type']) && $backup['type'] === 'auto';
        if ($is_auto) {
            if ($auto_count >= BACKBONE_BACKUP_MAX_AUTO) {
                continue;
            }
            $auto_count++;
        } else {
            if ($kept_count >= BACKBONE_BACKUP_MAX_KEPT) {
                continue;
            }
            $kept_count++;
        }
        $trimmed[] = $backup;
    }
    return $trimmed;
}

/**
 * 設定バックアップを作成
 *
 * @param string        $type  バックアップの種類（auto = カスタマイザーの公開時 / manual / reset / theme_switch）
 * @param WP_Theme|null $theme バックアップするテーマ。省略時は今のテーマ。
 *                             テーマ切り替えのフック（switch_theme）は切り替えた後に呼ばれるので、
 *                             切り替え前のテーマを渡して、そのテーマの設定を読む
 */
function backbone_create_settings_backup($type = 'manual', $theme = null) {
    $is_current_theme = !($theme instanceof WP_Theme) || $theme->get_stylesheet() === get_stylesheet();
    if (!($theme instanceof WP_Theme)) {
        $theme = wp_get_theme();
    }

    // テーマの設定（theme_mods）。今のテーマでなければ、そのテーマの theme_mods_<stylesheet> を直接読む
    $theme_mods = $is_current_theme ? get_theme_mods() : get_option('theme_mods_' . $theme->get_stylesheet(), array());
    // 数値インデックスを除外してクリーンな配列にする。
    // 空文字の値は捨てない（意図して空にした設定が、復元で既定値に戻らないように。復元はバックアップに無い設定を削除する）
    if (is_array($theme_mods)) {
        $theme_mods = array_filter($theme_mods, function($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
    } else {
        $theme_mods = array();
    }
    $backups = get_option('backbone_settings_backups', array());
    if (!is_array($backups)) {
        $backups = array();
    }

    // テーマ情報を取得
    $parent_theme = $theme->parent();

    // 親テーマのバージョンを取得（子テーマの場合は親テーマから、親テーマの場合は自身から）
    $parent_version = $parent_theme ? $parent_theme->get('Version') : $theme->get('Version');

    // 新しいバックアップを先頭に追加（メタ情報を含む）
    array_unshift($backups, array(
        'date' => current_time('Y-m-d H:i:s'),
        'type' => $type,
        'data' => $theme_mods,
        'meta' => array(
            'theme_version' => $theme->get('Version'),
            'theme_name' => $theme->get('Name'),
            'theme_stylesheet' => $theme->get_stylesheet(),
            'parent_theme' => $parent_theme ? $parent_theme->get_stylesheet() : null,
            'parent_theme_version' => $parent_version, // 親テーマのバージョン（設定互換性の判定に使用）
            'is_child_theme' => (bool) $parent_theme,
            'site_url' => home_url(),
            'wp_version' => get_bloginfo('version'),
        ),
    ));

    // 種類ごとの最大件数まで保持（自動バックアップが手動などを押し出さない）
    $backups = backbone_trim_settings_backups($backups);

    update_option('backbone_settings_backups', $backups);

    // サブディレクトリ設定の独立バックアップも保存（今のテーマの設定から作るので、今のテーマのときだけ）
    if ($is_current_theme) {
        backbone_save_independent_backup();
    }

    // 変更ログに記録
    $log_messages = array(
        'auto' => __('自動バックアップを作成（カスタマイザーの公開）', 'backbone-seo-llmo'),
        'reset' => __('リセット前のバックアップを作成', 'backbone-seo-llmo'),
        'theme_switch' => __('テーマ切り替え前のバックアップを作成', 'backbone-seo-llmo'),
    );
    backbone_log_customizer_change(isset($log_messages[$type]) ? $log_messages[$type] : __('手動バックアップを作成', 'backbone-seo-llmo'));
}

/**
 * 設定バックアップを復元
 *
 * @param int $index バックアップのインデックス
 * @param bool $validate_only 検証のみ行う場合はtrue
 * @return array|bool 検証モードの場合は警告配列、復元モードの場合はtrue/false
 */
function backbone_restore_settings_backup($index, $validate_only = false) {
    // バックアップの値はカスタマイザーのサニタイズを通らずに theme_mod へ書き込まれ、
    // 一部はそのまま <style> やページに出力される。HTML を無制限に扱える権限を持つユーザーだけが復元できる。
    if (!current_user_can('unfiltered_html')) {
        return false;
    }

    $backups = get_option('backbone_settings_backups', array());

    if (!isset($backups[$index]) || !isset($backups[$index]['data']) || !is_array($backups[$index]['data'])) {
        return false;
    }

    $backup_data = $backups[$index]['data'];
    $backup_meta = (isset($backups[$index]['meta']) && is_array($backups[$index]['meta'])) ? $backups[$index]['meta'] : array();
    // 保存済みのメタ情報が壊れていても（以前にインポートした値の型が違う等）検証で止まらないよう、
    // 文字列として使う値は文字列だけを残す（配列を sprintf・version_compare に渡すと PHP 8 で TypeError になる）
    foreach (array('site_url', 'theme_stylesheet', 'parent_theme', 'parent_theme_version', 'theme_version') as $meta_key) {
        if (isset($backup_meta[$meta_key]) && !is_scalar($backup_meta[$meta_key])) {
            unset($backup_meta[$meta_key]);
        }
    }
    $warnings = array();

    // サイト固有データの検証
    $site_specific_keys = backbone_get_site_specific_keys();

    foreach ($backup_data as $key => $value) {
        // メディアIDの検証
        if (backbone_is_media_id_key($key) && !empty($value)) {
            if (!wp_get_attachment_url($value)) {
                $warnings[] = sprintf(
                    __('メディアID %d（%s）は存在しません。画像を再設定する必要があります。', 'backbone-seo-llmo'),
                    $value,
                    $key
                );
            }
        }

        // 投稿IDの検証（JSON内の投稿IDを含む）
        if (backbone_is_post_id_key($key) && !empty($value)) {
            $post_ids = backbone_extract_post_ids_from_value($value);
            foreach ($post_ids as $post_id) {
                if (!get_post($post_id)) {
                    $warnings[] = sprintf(
                        __('投稿ID %d（%s）は存在しません。記事を再選択する必要があります。', 'backbone-seo-llmo'),
                        $post_id,
                        $key
                    );
                }
            }
        }

        // 絶対URLの検証
        if (backbone_is_url_key($key) && !empty($value)) {
            $current_host = parse_url(home_url(), PHP_URL_HOST);
            $backup_host = parse_url($value, PHP_URL_HOST);
            if ($backup_host && $current_host !== $backup_host) {
                $warnings[] = sprintf(
                    __('URL "%s"（%s）は異なるドメインを指しています。URLを更新する必要があります。', 'backbone-seo-llmo'),
                    $value,
                    $key
                );
            }
        }
    }

    // メタ情報から追加の警告を生成
    if (!empty($backup_meta)) {
        // 異なるサイトからのバックアップ
        if (isset($backup_meta['site_url']) && $backup_meta['site_url'] !== home_url()) {
            $warnings[] = sprintf(
                __('このバックアップは異なるサイト（%s）で作成されました。サイト固有の設定（画像、投稿、URLなど）の再設定が必要な場合があります。', 'backbone-seo-llmo'),
                $backup_meta['site_url']
            );
        }

        // テーマ互換性チェック（親テーマ↔子テーマは互換性あり）
        $current_template = get_template(); // 親テーマのスラッグ
        $current_stylesheet = get_stylesheet(); // 現在のテーマのスラッグ

        // バックアップ元のテーマ情報
        $backup_stylesheet = isset($backup_meta['theme_stylesheet']) ? $backup_meta['theme_stylesheet'] : '';
        $backup_parent = isset($backup_meta['parent_theme']) ? $backup_meta['parent_theme'] : null;

        // 親テーマのスラッグ一覧（このテーマ系統を識別）
        $parent_theme_slugs = array('backbone-seo-llmo', 'wp-theme-backbone-seo-llmo-main');

        // 現在のテーマがこのテーマ系統か
        $current_is_backbone = in_array($current_template, $parent_theme_slugs, true) ||
                               in_array($current_stylesheet, $parent_theme_slugs, true);

        // バックアップ元がこのテーマ系統か
        $backup_is_backbone = in_array($backup_stylesheet, $parent_theme_slugs, true) ||
                              in_array($backup_parent, $parent_theme_slugs, true);

        // 異なるテーマ系統の場合のみ警告（親↔子テーマ間は互換性あり）
        if (!$current_is_backbone || !$backup_is_backbone) {
            if (!empty($backup_stylesheet)) {
                $warnings[] = sprintf(
                    __('このバックアップは異なるテーマ（%s）で作成されました。設定に互換性がない場合があります。', 'backbone-seo-llmo'),
                    $backup_stylesheet
                );
            }
        }

        // 同じテーマ系統内でのバージョン比較
        if ($current_is_backbone && $backup_is_backbone) {
            // 現在の親テーマのバージョンを取得
            $current_parent_theme = wp_get_theme(get_template());
            $current_parent_version = $current_parent_theme->get('Version');

            // バックアップの親テーマバージョンを取得（なければtheme_versionを使用）
            $backup_parent_version = isset($backup_meta['parent_theme_version'])
                ? $backup_meta['parent_theme_version']
                : (isset($backup_meta['theme_version']) ? $backup_meta['theme_version'] : null);

            if ($backup_parent_version && $current_parent_version) {
                $version_compare = version_compare($backup_parent_version, $current_parent_version);

                if ($version_compare < 0) {
                    // バックアップが古いバージョンで作成された
                    $warnings[] = sprintf(
                        __('このバックアップはテーマ v%s で作成されました（現在: v%s）。新しいバージョンで追加された設定はデフォルト値になります。', 'backbone-seo-llmo'),
                        $backup_parent_version,
                        $current_parent_version
                    );
                } elseif ($version_compare > 0) {
                    // バックアップが新しいバージョンで作成された
                    $warnings[] = sprintf(
                        __('このバックアップはテーマ v%s で作成されました（現在: v%s）。一部の設定が認識されない場合があります。テーマを最新版にアップデートすることをお勧めします。', 'backbone-seo-llmo'),
                        $backup_parent_version,
                        $current_parent_version
                    );
                }
            }
        }
    }

    // 検証のみの場合は警告を返す
    if ($validate_only) {
        return $warnings;
    }

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

    // サブディレクトリ設定の独立バックアップを、復元後の theme_mods から作り直す
    // 古いままだと、復元でサブディレクトリ設定が消えた場合に、次のリクエストで
    // backbone_subdirectory_fallback_check() が「設定が消えた」と判断して復元前の設定を書き戻してしまう
    delete_option('backbone_subdirectory_settings');
    backbone_save_independent_backup();

    // 変更ログに記録
    backbone_log_customizer_change(sprintf(__('バックアップ #%d を復元', 'backbone-seo-llmo'), $index + 1));

    return true;
}

/**
 * メディアIDを保存するキーかどうかを判定
 */
function backbone_is_media_id_key($key) {
    $media_keys = array(
        'custom_logo',
        'backbone_front_hero_image',
    );

    // 完全一致
    if (in_array($key, $media_keys, true)) {
        return true;
    }

    // パターンマッチ
    if (preg_match('/^subdirectory_logo_\d+$/', $key)) {
        return true;
    }

    return false;
}

/**
 * 投稿IDを含む可能性のあるキーかどうかを判定
 */
function backbone_is_post_id_key($key) {
    $post_id_keys = array(
        'backbone_front_individual_sections_1',
        'backbone_front_individual_sections_2',
        'backbone_front_individual_sections_3',
        'backbone_front_individual_sections_4',
        'backbone_front_individual_sections_5',
    );

    return in_array($key, $post_id_keys, true);
}

/**
 * 値から投稿IDを抽出
 */
function backbone_extract_post_ids_from_value($value) {
    $post_ids = array();

    // JSON文字列の場合
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                if (isset($item['post_id']) && is_numeric($item['post_id']) && $item['post_id'] > 0) {
                    $post_ids[] = intval($item['post_id']);
                }
            }
        }
    }

    return $post_ids;
}

/**
 * URLを保存するキーかどうかを判定
 */
function backbone_is_url_key($key) {
    // パターンマッチ
    if (preg_match('/^subdirectory_home_\d+$/', $key)) {
        return true;
    }

    return false;
}

/**
 * サイト固有データのキー一覧を取得
 */
function backbone_get_site_specific_keys() {
    return array(
        'media_ids' => array(
            'custom_logo',
            'backbone_front_hero_image',
            'subdirectory_logo_*',
        ),
        'post_ids' => array(
            'backbone_front_individual_sections_*',
        ),
        'urls' => array(
            'subdirectory_home_*',
        ),
    );
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
 * JSONファイルからバックアップをインポート
 *
 * @return true|WP_Error 成功時はtrue、失敗時はWP_Error
 */
function backbone_import_backup_json() {
    // インポートしたデータは復元時にサニタイズを通らずに書き込まれるため、復元と同じ権限を要求する
    if (!current_user_can('unfiltered_html')) {
        return new WP_Error('forbidden', __('バックアップをインポートする権限がありません（HTMLを無制限に扱う権限が必要です）。', 'backbone-seo-llmo'));
    }

    // ファイルがアップロードされているか確認
    if (!isset($_FILES['backbone_import_file']) || $_FILES['backbone_import_file']['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('no_file', __('ファイルがアップロードされていません。', 'backbone-seo-llmo'));
    }

    $file = $_FILES['backbone_import_file'];

    // ファイル拡張子を確認
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'json') {
        return new WP_Error('invalid_type', __('JSONファイルのみインポートできます。', 'backbone-seo-llmo'));
    }

    // ファイルサイズを確認（最大1MB）
    if ($file['size'] > 1024 * 1024) {
        return new WP_Error('file_too_large', __('ファイルサイズが大きすぎます（最大1MB）。', 'backbone-seo-llmo'));
    }

    // ファイルを読み込む
    $content = file_get_contents($file['tmp_name']);
    if ($content === false) {
        return new WP_Error('read_error', __('ファイルの読み込みに失敗しました。', 'backbone-seo-llmo'));
    }

    // BOM（Byte Order Mark）を除去
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

    // 先頭・末尾の空白を除去
    $content = trim($content);

    // JSONをパース
    $import_data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // デバッグ用：最初の100文字を表示
        $preview = substr($content, 0, 100);
        return new WP_Error('json_error', sprintf(
            __('JSONの解析に失敗しました: %s（ファイル先頭: %s）', 'backbone-seo-llmo'),
            json_last_error_msg(),
            esc_html($preview)
        ));
    }

    // データ構造を検証
    if (!isset($import_data['backup']) || !isset($import_data['backup']['data'])) {
        return new WP_Error('invalid_format', __('バックアップデータの形式が正しくありません。', 'backbone-seo-llmo'));
    }

    $backup = $import_data['backup'];

    // 設定の本体は連想配列でなければならない（文字列・数値などは復元できない）
    if (!is_array($backup['data'])) {
        return new WP_Error('invalid_format', __('バックアップデータの形式が正しくありません。', 'backbone-seo-llmo'));
    }

    // メタ情報は、一覧の表示と復元の互換性の判定で使う値だけを、決まった型（文字列か真偽値）で取り込む。
    // 配列などがそのまま保存されると、一覧の表示（parse_url 等）が PHP 8 で TypeError になり、一覧の操作に届かなくなる
    $raw_meta = (isset($backup['meta']) && is_array($backup['meta'])) ? $backup['meta'] : array();
    $meta = array();
    $string_meta_keys = array('theme_version', 'theme_name', 'theme_stylesheet', 'parent_theme', 'parent_theme_version', 'site_url', 'wp_version');
    foreach ($string_meta_keys as $meta_key) {
        if (isset($raw_meta[$meta_key]) && is_scalar($raw_meta[$meta_key])) {
            $meta[$meta_key] = (string) $raw_meta[$meta_key];
        }
    }
    if (isset($raw_meta['is_child_theme'])) {
        $meta['is_child_theme'] = (bool) $raw_meta['is_child_theme'];
    }

    // インポート元の情報を追加
    $meta['imported_from'] = isset($meta['site_url']) ? $meta['site_url'] : __('不明', 'backbone-seo-llmo');
    $meta['import_date'] = current_time('Y-m-d H:i:s');

    $date = (isset($backup['date']) && is_scalar($backup['date'])) ? (string) $backup['date'] : current_time('Y-m-d H:i:s');

    // 既存のバックアップを取得
    $backups = get_option('backbone_settings_backups', array());
    if (!is_array($backups)) {
        $backups = array();
    }

    // インポートしたバックアップを先頭に追加
    array_unshift($backups, array(
        'date' => $date . ' (imported)',
        'type' => 'import',
        'data' => $backup['data'],
        'meta' => $meta,
    ));

    // 種類ごとの最大件数まで保持（自動バックアップが手動・インポートなどを押し出さない）
    $backups = backbone_trim_settings_backups($backups);

    update_option('backbone_settings_backups', $backups);

    // 変更ログに記録
    // インポート元は型を確かめた後の値を使う（生の値が配列だと sprintf が警告を出す）
    backbone_log_customizer_change(sprintf(
        __('バックアップをインポート（元: %s）', 'backbone-seo-llmo'),
        $meta['imported_from']
    ));

    return true;
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
    backbone_create_settings_backup('auto');
}
add_action('customize_save_after', 'backbone_auto_backup_on_customizer_save');

/**
 * テーマ切り替え時に自動バックアップ
 */
function backbone_auto_backup_on_theme_switch($new_name, $new_theme, $old_theme) {
    // 親テーマのスラッグ一覧（将来の変更に備えて配列で管理）
    $parent_theme_slugs = array(
        'backbone-seo-llmo',
        'wp-theme-backbone-seo-llmo-main',
    );

    $old_stylesheet = $old_theme->get_stylesheet();
    $old_template = $old_theme->get_template();

    // 親テーマ自体か、親テーマを使用した子テーマの場合にバックアップ
    $is_backbone_theme = in_array($old_stylesheet, $parent_theme_slugs, true) ||
                         in_array($old_template, $parent_theme_slugs, true);

    if ($is_backbone_theme) {
        // switch_theme は切り替えた後に呼ばれるので、切り替え前のテーマ（$old_theme）の設定をバックアップする
        // （変更ログへの記録は backbone_create_settings_backup が行う）
        backbone_create_settings_backup('theme_switch', $old_theme);
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

