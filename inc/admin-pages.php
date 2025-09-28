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



