# 更新履歴

Backbone Theme for SEO + LLMO のすべての重要な変更はこのファイルに記録されます。

## [1.0.32] - 2025-12-25

### 修正
- CSS依存関係バグ修正（`array('style')` → `array('seo-optimus-style')`）
- `get_stylesheet_uri()` を `get_template_directory_uri() . '/style.css'` に変更（子テーマ対応）
- 1920px以上で2カラム・3カラムレイアウトのメインコンテンツが縮小する問題を修正（`typography-responsive.css`）

### 追加
- パターンファイルの子テーマ対応（typography/decoration/design utilities）

## [1.0.31] - 2025-12-24

### 追加
- フロントページ一覧表示セクションにアイキャッチ画像サイズ選択オプション（デフォルト: フルサイズ）

## [1.0.30] - 2025-12-20

### 追加
- アーカイブページのアイキャッチ画像サイズ選択機能（フルサイズ/大/中大/中/サムネイル）
- サブメニュー表示時に他のサブメニューを自動で閉じる機能

### 変更
- README.mdから更新履歴を削除（CHANGELOG.mdに一元化）

## [1.0.29] - 2025-12-08

### 追加
- 外観メニュー配下にテーマ診断・バックアップページ（`backbone-diagnostics`）を追加
- 設定バックアップ機能（作成・復元・削除、最大5件まで保持）
- サブディレクトリ設定の独立バックアップ機能（`backbone_subdirectory_settings`に自動保存）
- カスタマイザー保存時とテーマ切り替え時の自動バックアップ
- カスタマイザー変更ログ（最新50件まで保持）
- theme_modsが空の場合のフォールバック復元機能（独立バックアップから自動復元）

### 改善
- サブディレクトリの追加・削除時に独立バックアップを更新しログに記録

## [1.0.28] - 2025-12-04

### 追加
- タクソノミールートページテンプレート（`taxonomy-root.php`）を `/tag/` および `/category/` URL用に追加
- タクソノミールートページに投稿数に基づく動的フォントサイズのタグクラウド表示
- `/tag/` と `/category/` ルートページのリライトルール追加（WordPressコア設計の制限を修正）

### 修正
- `/tag/` と `/category/` ルートURLが404を返す問題（WordPressはタクソノミーディレクトリのルートページを提供しない）
- カテゴリーとタグアーカイブが関連するすべての投稿タイプの投稿を表示しない問題
- `backbone_include_pages_in_tag_archives()` をすべての公開投稿タイプを含むように変更
- タクソノミールートページがカスタマイザーの「その他のアーカイブ」（`post_type_layout_archive`）レイアウト設定を正しく使用するように
- タクソノミールートページに `page-type-archive` ボディクラスが正しく適用されるように（誤って `page-type-front_page` が表示されていた）

### 技術的変更
- タクソノミールートページ検出用の `taxonomy_root` クエリ変数を追加
- タクソノミールートテンプレート読み込み用の `template_include` フィルターを追加
- タクソノミールートページの適切なページタイトル用 `document_title_parts` フィルターを追加
- タクソノミールートページの正しいHTTPステータス設定用 `parse_query` アクションを追加

## [1.0.27] - 2025-12-02

### 修正
- `layout-two-columns.css` の不適切な全幅計算によりモバイルでサイドバーコンテンツが切れる問題
- `components-sidebar.css` の `overflow: hidden` によりモバイルでサイドバーウィジェットが表示されない問題
- モバイルでサイドバーがメインコンテンツの上に表示される順序の問題

### 変更
- 不安定な `width: 100vw` ハックを標準的な `width: 100%` に置き換え、親コンテナのパディングを尊重
- モバイルでのサイドバー順序をデフォルト（メインコンテンツの下）に復元

## [1.0.26] - 2025-11-26

### 追加
- 全投稿タイプ（投稿、カスタム投稿タイプ）に個別レイアウト設定を拡張

### 変更
- カスタマイザー用語と「全幅」ラベルを統一
- より広い互換性のためメタボックス定義を更新

## [1.0.25] - 2025-11-25

### 修正
- `submenu-third-vertical` レイアウトで親アイテムにホバー時に垂直サブメニュー（3階層以上）が消える問題
- 垂直レイアウトで親アイテム下にホバー領域を拡張するCSS `::after` 疑似要素を追加
- JavaScript経由でサブメニュー表示を維持するための `.submenu-active` クラスサポートを追加
- 垂直レイアウトで子サブメニューが表示されている場合にメニュークローズを遅延させるJavaScript `mouseleave` ハンドラーを修正

### 技術的変更
- 変更: `css/components-navigation.css` - 垂直レイアウト用のホバー領域拡張と `.submenu-active` セレクターを追加
- 変更: `js/theme.js` - 垂直レイアウト検出と `mouseleave` イベントでの特別処理を追加

## [1.0.24] - 2025-11-24

### 追加
- カスタマイザーにSEOメタタグの有効/無効設定（`seo_meta_description_enabled`、`seo_meta_keywords_enabled`）
- ストップワードフィルタリング付きのインテリジェントキーワード抽出ヘルパー関数 `backbone_extract_keywords_from_text()`
- すべてのWordPressページタイプ（ホーム、アーカイブ、検索、404等）の包括的なメタディスクリプション生成
- タグ、カテゴリー、投稿タイトル、コンテンツからの自動メタキーワード抽出

### 改善
- `backbone_meta_description()` 関数がすべてのページタイプに適切な説明文をサポート
- SEO用のメタディスクリプション文字数制限強制（最大160文字）
- メタキーワードがコンテンツ抽出よりタグとカテゴリーを優先
- より良いキーワード品質のための日本語・英語ストップワードフィルタリング

## [1.0.23] - 2025-11-23

### 修正
- レンタルサーバーでプレビューの無限ループを引き起こすカスタマイザーのjQuery依存エラー
- カスタマイザーコントロールでのスクリプト重複登録（`functions.php` と `inc/customizer/index.php`）
- `custom-js-output.php` でjQuery読み込み前にインラインスクリプトが実行される問題
- `custom-css-output.php` でjQuery読み込み前にインラインスクリプトが実行される問題
- `widget-working-solution.php` でjQuery読み込み前にインラインスクリプトが実行される問題

### 改善
- `wp_add_inline_script` を使用した適切な依存関係によるインラインスクリプト依存関係管理
- カスタマイザープレビュー用のスクリプト読み込み順序（`customize-preview` 依存関係を使用）
- カスタマイザーコントロール用のスクリプト読み込み順序（`customize-controls` 依存関係を使用）
- `inc/customizer/index.php` に不足していたカスタマイザースクリプト（`customizer-storage.js`、`customizer-themes.js`、`customizer-ui.js`）を追加

### 変更
- `functions.php` から重複する `backbone_enqueue_customizer_modules()` 関数を削除
- `functions.php` から重複する `backbone_enqueue_customizer_controls_assets()` 関数を削除
- `backbone_customizer_notice()` フックを `customize_controls_print_footer_scripts` から `customize_controls_enqueue_scripts` に変更

## [1.0.22] - 2025-11-18

### 追加
- モバイルメニューブレークポイントのカスタマイザー設定（`mobile_menu_hide_breakpoint`）
- ナビゲーションCSS動的出力モジュール（`inc/css-navigation.php`）
- 3つのブレークポイントオプション: 常に表示、モバイルのみ（≤767px）、タブレット以下（≤1279px）
- プラグインベースのハンバーガーメニュー機能用 `.active` クラスサポート

### 変更
- ナビゲーション設定セクションにモバイルメニューブレークポイントコントロールを含む
- responsive.css のモバイルメニュースタイルを動的CSS生成に置き換え
- メニュー表示制御をハードコードCSSからカスタマイザー設定に委譲

### 改善
- プラグイン優先アーキテクチャ: テーマがブレークポイント制御を提供、プラグインがハンバーガーUIを担当
- 必要な場合のみ出力する動的CSSによるパフォーマンス向上
- テーマ（ブレークポイント制御）とプラグイン（UI/インタラクション）の責任分離を明確化

### 修正
- `inc/css-navigation.php` のファイルパーミッション問題（600から644に変更）

## [1.0.21] - 2025-11-17

### 追加
- WordPressネイティブメニューパネルに統合されたナビゲーションメニューカスタマイザー設定（`inc/customizer/navigation-settings.php`）
- 深い階層のサブメニュー表示方向設定（3階層以上で垂直/水平）
- 投稿メタ設定の動的表示制御（統一/個別モード）
- サブメニュー方向のボディクラス（`submenu-third-vertical` または `submenu-third-horizontal`）

### 変更
- サブメニュー3階層目のデフォルト方向を「水平」から「垂直」（階段状インデント）に変更
- ナビゲーション設定を: 外観 → カスタマイズ → メニュー → メニュー表示オプション に移動
- 投稿メタ設定が統一/個別モード選択に基づいて動的に表示/非表示
- テキスト下のアンダーラインアニメーションでサブメニューホバー効果を改善
- モバイルメニュー非表示スタイルをコメントアウト（プラグイン制御に委譲）

### 改善
- `css/components-navigation.css` でナビゲーションCSSをリファクタリング:
  - トップレベルメニューアイテムは下部ボーダー装飾のみ表示
  - サブメニューアイテムはテキストアンダーラインホバー効果
  - より良いパディングと背景処理でパネルデザインを改善
  - オーバーフロー設定で全幅レイアウトのサブメニュー表示を修正
- active_callback関数で投稿メタ設定UIをよりクリーンに:
  - `backbone_is_unified_post_meta_settings_enabled()`
  - `backbone_is_individual_post_meta_settings_enabled()`

### 修正
- 全幅レイアウトでのサブメニュー表示（`css/components-header.css`）
- ナビゲーションホバー修正CSSを `components-navigation.css` に統合
- ネストアイテムがある場合の3階層垂直サブメニューのスペーシング問題
- オーバーラップ時の3階層垂直サブメニュー背景透明化による視認性問題
- 3階層垂直サブメニューのzインデックスレイヤリング（下位メニューアイテムの上に適切にオーバーラップ）

### 技術的変更
- 変更: `inc/customizer/index.php` - navigation-settings.php登録を追加
- 変更: `inc/css-body-classes.php` - サブメニュー方向ボディクラスを追加
- 変更: `inc/css-layout.php` - サブメニュー背景/ボーダーを削除（個別アイテムのみに適用）
- 変更: `inc/theme-setup.php` - navigation-hover-fix.css読み込みをコメントアウト
- 変更: `css/responsive.css` - モバイルメニュー非表示をコメントアウト（プラグインが処理）
- 変更: `css/components-navigation.css` - 3階層垂直サブメニュー表示動作を修正:
  - 非表示時のスペース占有を防ぐため初期表示を `block` から `none` に変更
  - 適切な視認性のため背景を `transparent` から `var(--primary-color)` に変更
  - 兄弟メニューアイテム上への適切なレイヤリングのためzインデックスを `9999999` に増加
  - 視覚的区別のためbox-shadowとpaddingを追加

## [1.0.20] - 2025-11-14

### 追加
- カスタム著者URL設定機能（`inc/author-custom-urls.php`）
- WordPress著者URL（`/author/{username}/`）を任意のURLにカスタマイズ可能
- 相対パス（`profile`）または完全URL（`https://example.com/profile/`）のサポート
- `author_link` フィルターによる全プラグイン・テーマとの自動連携

## [1.0.19] - 2025-11-13

### 変更
- レイアウト設定との矛盾を解消するため、フロントページ設定から「コンテンツ最大幅（px）」設定を削除
- 1カラムレイアウト専用の「1カラム最大幅」設定をレイアウト設定に追加

### 修正
- レイアウト設定のサイドバー幅（「コンテンツエリア幅は自動調整」）とフロントページ設定のコンテンツ最大幅の矛盾を修正
- コンテンツ最大幅が1カラムレイアウトのみに適用され、2カラム・3カラムレイアウトには影響しないように

### 技術的変更
- 変更: `inc/customizer/front-page-settings.php`: `backbone_front_content_max_width` 設定を削除（227-248行目）
- 変更: `functions.php`: `backbone_output_front_page_content_width()` 関数を削除（464-497行目）
- 変更: `inc/customizer/layout-settings.php`: `single_column_max_width` 設定を追加（256-272行目）
- 変更: `inc/css-layout.php`: 1カラム最大幅のCSS出力を追加（612-624行目）

## [1.0.18] - 2025-11-11

### 追加
- 20種類の新カラーテーマ（10ペア ダーク/ライト）: 55 Cancri e、GJ 1214b、HAT-P-7b、HD 189733b、HD 209458b、KELT-9b、PSR B1257+12、TOI-5205b、TrES-2b、WASP-76b
- 20種類の新デザインテーマ（アーティスト名）: Banksy、Basquiat、Calder、Chagall、Dalí、Duchamp、Escher、Gaudí、Haring、Hokusai、Kahlo、Kandinsky、Klimt、Kusama、Matisse、Miró、Mondrian、Mucha、Pollock、Rembrandt、Rothko、Warhol
- 10種類の新装飾テーマ: Velvet、Marble、Wood、Glass、Paper、Neon、Leather、Fabric、Concrete、Ceramic
- 既存訪問者用のワンタイムキャッシュクリアスクリプトをheader.phpに追加（LocalStorage、SessionStorage、Service Workerキャッシュをクリア）

### 修正
- WordPress管理バー表示時のスティッキーサイドバー位置問題（js/theme.jsにadminBarHeight計算を追加）

### 変更
- ディレクトリ命名を `-themes` サフィックスに統一: `inc/designs/` → `inc/design-themes/`、`inc/typography/` → `inc/typography-themes/`
- color-themesドキュメントを単一のREADME.mdに統合
- design-utilities.phpとtypography-utilities.phpを新しいディレクトリパスを使用するよう更新
- 太いボーダー（2px以上）をすべて1pxと透明度で調整し可読性向上
- 全新デザインテーマでシャドウ強度を最適化

### 削除
- `inc/color-themes/old/` ディレクトリを削除（45のレガシーテーマファイル）
- バックアップと無効化ファイル（.backup、.disabled拡張子）を削除
- 冗長なドキュメントファイル（color-themesからCHANGELOG.md、COLOR_VARIABLE_MAPPING.md）を削除

### 技術的変更
- 変更: `js/theme.js`: スティッキーサイドバー計算にWordPress管理バー高さ検出を追加（528-533行目、586-619行目）
- 変更: `header.php`: フラグ 'backbone_cache_cleared_20250111_v1' 付きの forceClearLegacyCache() 関数を追加
- 変更: `css/utilities.css`: ユーティリティスタイルを更新
- リネーム: `inc/designs/` → `inc/design-themes/`、`inc/typography/` → `inc/typography-themes/`

## [1.0.17] - 2025-11-11

### 追加
- 全CSS・JavaScriptファイルの個別ファイルベースキャッシュバスティング
- 一貫したバージョン管理のためのヘルパー関数 `backbone_get_file_version()` を `inc/theme-setup.php` に追加
- パフォーマンス向上のためMutationObserverにデバウンス機能（150ms）を `js/theme.js` に追加

### 修正
- スティッキーヘッダーパディング調整による不要な余白
- テーマ全体のすべてのハードコードバージョン番号（1.0.0、1.0.1、4.0.0）を削除

### 変更
- キャッシュバスティングが共有バージョン番号の代わりに個別ファイル更新時刻を使用
- 全enqueue関数で新ヘルパーを使用するようバージョン管理を更新
- MutationObserverがパディング更新前に高さ変更をチェック

### 技術的変更
- 変更: `inc/theme-setup.php`: backbone_get_file_version()ヘルパーを追加、全wp_enqueue_style/script呼び出しを更新
- 変更: `functions.php`: backbone_enqueue_responsive_typography()とbackbone_enqueue_front_page_sections()を更新
- 変更: `inc/customizer/index.php`: 全カスタマイザースクリプトを新ヘルパー使用に更新
- 変更: `inc/widget-working-solution.php`: ウィジェットエディタースクリプトバージョニングを更新
- 変更: `inc/customizer/custom-color-theme.php`: カスタマイザープレビュー/コントロールスクリプトを更新
- 変更: `inc/customizer/subdirectory-design-settings.php`: サブディレクトリカスタマイザースクリプトを更新
- 変更: `js/theme.js`: adjustStickyHeaderPadding()にデバウンスと高さ変更検出を追加

## [1.0.16] - 2025-11-10

### 修正
- アーカイブページネーションで2ページ目以降の投稿順序が正しくない問題（IDによるセカンダリソートキーを追加）
- アーカイブ設定でランダム投稿順序が機能しない問題（backbone_force_correct_post_orderに'rand'サポートを追加）

### 追加
- 統一/個別アーカイブ設定の条件付き表示（active_callback関数）
- 共通設定表示用の backbone_is_unified_archive_settings_enabled() 関数
- template_redirectで正しい投稿順序を強制する backbone_force_correct_post_order() 関数

### 改善
- アーカイブ設定セクションがカスタマイザーで微妙なボーダーで視覚的にグループ化
- プラグイン干渉を防ぐためクエリ実行優先度を9999に引き上げ

### 技術的変更
- 変更: `functions.php`: orderbyをIDセカンダリソートキー付き配列形式に変更
- 変更: `functions.php`: pre_get_posts優先度をデフォルトから9999に引き上げ
- 変更: `functions.php`: 正しい投稿順序を強制するtemplate_redirectフックを追加
- 変更: `inc/customizer/archive-settings.php`: 全アーカイブ設定コントロールにactive_callbackを追加
- 追加: `css/customizer-controls.css`: セクションボーダースタイリング
- 変更: `js/customizer-controls.js`: セクショングループ化ロジック
- 変更: `js/subdirectory-customizer-preview.js`: console.logデバッグ出力を削除

### ドキュメント
- CLAUDE.mdにGitHubリポジトリURLを追加

## [1.0.15] - 2025-11-10

### 修正
- スティッキーサイドバーがフッターを突き抜ける問題（正確な衝突検出のためマージン計算を追加）
- サイドバー位置ロジックの逆転（sidebar-rightで正しくSidebar-1が右側に配置）
- スクロール時にSidebar-1が消える問題（position:absolute切り替え時にGrid互換性を維持）

### 追加
- カスタマイザーのスクロール透明度設定でのリアルタイム不透明度パーセンテージ表示
- カスタマイザーでのサイドバー位置変更のライブプレビュー機能

### 変更
- スティッキーサイドバー、スティッキーヘッダー、ヘッダー自動非表示をデフォルトで有効化
- ドキュメント用語統一（「ヒーローイメージ」→「メインビジュアル（ヒーローイメージ）」）

### ドキュメント
- 公式子テーマリファレンス追加（https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child）
- 子テーマFAQとクイックスタートガイドを追加

## [1.0.14] - 2025-11-05

### 修正
- メインビジュアル（ヒーローイメージ）スタイル、配置、装飾設定が共通/個別モード設定を正しく反映するように
- 全幅メインビジュアル（ヒーローイメージ）CSSレイアウトを修正

### 追加
- メインビジュアル設定（ヒーローイメージ）カスタマイザー共通/個別モードラジオトグル
- カスタマイザーでのメインビジュアル（ヒーローイメージ）スタイル変更のライブプレビュー
- 動的ハンドラー用のsupportedPostTypesデータをJavaScriptに渡す

### 改善
- 全テンプレートで日付バッジサイズ（0.75em）と形式（Y/m/d）を統一
- アーカイブグリッド日付バッジがフォントサイズ二重縮小を適用しないように

## [1.0.13] - 2025-11-04

### 改善
- **404テンプレート構造**: 404.phpテンプレート構造を他のテンプレートと統一（標準article、entry-header、entry-contentクラスを使用）
- **404ページレイアウト**: 404ページコンテンツにmax-width: 800px制約を追加し可読性向上
- **404ページコンテンツ**: ハードコードされた提案（人気投稿、カテゴリー、複数ボタン）を削除し404ページを簡素化
- **管理画面UX**: カスタムフロントページモードがアクティブな場合、設定 > 表示設定に目立つ通知を追加
- **設定管理**: カスタムフロントページモード有効時にWordPress標準ホームページ表示設定を無効化しユーザー混乱を防止

### 追加
- **404ステータスコード**: 404.phpテンプレートに適切なHTTP 404ステータスヘッダーとno-cacheヘッダーを追加
- **管理通知システム**: 表示設定ページに情報バナーを表示する新しい backbone_reading_settings_notice() 関数
- **視覚的フィードバック**: カスタムモードがアクティブな場合、説明オーバーレイ付きでホームページ表示設定をグレーアウト
- **カスタマイザー説明**: フロントページモードに基づいて更新される動的セクション説明

### 技術的変更
- 変更: `404.php`: sectionをarticle、page-headerをentry-header、page-contentをentry-contentに変更
- 変更: `css/content.css`: .error-404クラスにmax-widthとmargin autoを追加
- 変更: `inc/admin-pages.php`: 無効状態用CSSを含む表示設定通知関数を追加
- 変更: `inc/customizer/front-page-settings.php`: カスタムモード通知用の動的説明を追加

## [1.0.12] - 2025-11-03

### 主な更新

**WordPressコア「色」セクションを削除**
- テーマセットアップから `add_theme_support('custom-background')` を削除
- カスタマイザーからWordPressコア色セクションを強制削除する `backbone_remove_colors_section()` 関数を追加
- テーマ固有のカラーテーマに焦点を当てたよりクリーンなカスタマイザーUI

**カラーテーマを完全リニューアル**
- 40+テーマを22種類のWCAG AAA準拠テーマ（11色×2パターン: ライト/ダーク）に置き換え
- 旧テーマは参照用に `inc/color-themes/old/` ディレクトリに保存
- ユニークなブランディングのため系外惑星と恒星にちなんだ新テーマ名:
  - 青: **Kepler-22b**（海洋系外惑星）
  - 赤: **Betelgeuse**（赤色超巨星）
  - 緑: **Luyten 726-8**（赤色矮星）
  - 黄: **WASP-12b**（超高温木星型惑星）
  - 紫: **Psi Draconis**（連星系）
  - オレンジ: **Algorab**（蛍光オレンジ）、**Algorab K0V**（黒ヘッダーバリアント）、**Arcturus**（オレンジ巨星）
  - グレー: **CoRoT-7b**（溶岩惑星）
  - 白: **Deneb**（白色超巨星）
  - 黒: **Sirius B**（白色矮星伴星）
  - クリーム: **Capella**（黄白色巨星）
- 全テーマが適切なコントラスト比で色覚多様性ユーザー向けに設計（WCAG AAA）
- 各テーマに日本語で包括的なアクセシビリティ説明を含む

**すべてのハードコードカラーを削除**
- すべてのハードコードカラー値（#555、#666、#fff等）をCSS変数に置き換え
- 影響ファイル:
  - `css/front-page-sections.css` - カード背景、テキスト、ボーダー、カテゴリーバッジ、アーカイブボタン
  - `css/components-header.css` - ヘッダーサブタイトルカラー
  - `css/components-footer.css` - フッターテキストとリンクカラー
  - `css/base.css` - コンテンツリンクスタイル
- 全22テーマで一貫性を保つためすべてテーマカラーシステム変数を使用

**視認性とアクセシビリティの改善**
- 全テーマでヘッダーサブタイトル視認性を修正
  - ダーク背景で白テキストを強制するインラインスタイルとCSSルールを追加
  - ファイル: `header.php`、`css/components-header.css`
- 全テーマでフッターテキスト可読性を修正
  - 全フッター要素を `!important` 付きの `var(--footer-link-color)` 使用に変更
  - フッターリンクが白テキストで一貫して表示
  - ファイル: `css/components-footer.css`、`footer.php`
- ダークテーマでカードテキスト視認性を修正
  - 投稿抜粋がハードコード#555の代わりに `var(--text-primary)` を使用
  - カテゴリーバッジが適切なコントラストのため `var(--header-link-color)` を使用
  - アーカイブリンクボタンが背景色の代わりに `var(--header-link-color)` を使用
  - ファイル: `css/front-page-sections.css`
- ヒーローイメージテキスト視認性を修正
  - ハードコード#ffffffから `var(--button-text-color)` に変更
  - ファイル: `css/front-page-sections.css`

**より良いUXのためリンクアンダーラインを追加**
- 改善されたアクセシビリティのため全リンクに微妙なアンダーライン（0.5px厚さ）
- 適用対象:
  - 3pxオフセットと0.9不透明度のフッターリンク
  - コンテンツリンク（`.entry-content a`）
  - 投稿タイトルリンク（`.post-title a`）
- リンクが明確に識別可能でありながらクリーンなデザインを維持
- ファイル: `css/components-footer.css`、`css/base.css`、`css/front-page-sections.css`

### 技術的改善

**カード背景**
- カード背景が `var(--background-color)` 経由でテーマカラーを適切に継承
- カードボーダーがテーマ一貫性のため `var(--border-color)` を使用

**ヘッダーとフッターの一貫性**
- ヘッダーとフッターテキストが全22テーマで一貫した視認性のため白（`var(--header-link-color)`、`var(--footer-link-color)`）を強制
- 両方が同一のプライマリカラー上の白パターンを使用

**テキスト装飾標準**
- すべてのテキスト装飾が細い線（0.5px厚さ）を使用
- 全アンダーライン要素で一貫したオフセット（3px）
- 微妙で邪魔にならない外観のため不透明度0.9

### 変更ファイル

**テーマ設定:**
- `inc/theme-setup.php` - custom-backgroundサポート削除、色セクション削除を追加
- `style.css` - バージョンを1.0.12にバンプ

**カラーテーマ（22の新ファイル）:**
- `inc/color-themes/tk-theme-kepler22b-light.json`
- `inc/color-themes/tk-theme-kepler22b-dark.json`
- `inc/color-themes/tk-theme-betelgeuse-light.json`
- `inc/color-themes/tk-theme-betelgeuse-dark.json`
- `inc/color-themes/tk-theme-luyten7268-light.json`
- `inc/color-themes/tk-theme-luyten7268-dark.json`
- `inc/color-themes/tk-theme-wasp12b-light.json`
- `inc/color-themes/tk-theme-wasp12b-dark.json`
- `inc/color-themes/tk-theme-psidraconis-light.json`
- `inc/color-themes/tk-theme-psidraconis-dark.json`
- `inc/color-themes/tk-theme-algorab-light.json`
- `inc/color-themes/tk-theme-algorab-dark.json`
- `inc/color-themes/tk-theme-algorabk0v-light.json`
- `inc/color-themes/tk-theme-algorabk0v-dark.json`
- `inc/color-themes/tk-theme-arcturus-light.json`
- `inc/color-themes/tk-theme-arcturus-dark.json`
- `inc/color-themes/tk-theme-corot7b-light.json`
- `inc/color-themes/tk-theme-corot7b-dark.json`
- `inc/color-themes/tk-theme-deneb-light.json`
- `inc/color-themes/tk-theme-deneb-dark.json`
- `inc/color-themes/tk-theme-siriusb-light.json`
- `inc/color-themes/tk-theme-siriusb-dark.json`
- `inc/color-themes/tk-theme-capella-light.json`
- `inc/color-themes/tk-theme-capella-dark.json`

**旧テーマ（40+ファイル移動）:**
- 全旧カラーテーマJSONファイルを `inc/color-themes/old/` ディレクトリに移動

**CSSファイル:**
- `css/components-header.css` - ヘッダーサブタイトル視認性修正
- `css/components-footer.css` - フッターテキストとリンク視認性、アンダーライン
- `css/front-page-sections.css` - ハードコードカラー削除、カード視認性修正
- `css/base.css` - リンクアンダーライン追加

**テンプレートファイル:**
- `header.php` - サブタイトル視認性用インラインスタイル追加

### ブラウザテスト

全変更をPlaywrightブラウザ自動化で検証:
- Kepler-22bライトテーマ: ヘッダーサブタイトル視認性確認
- Kepler-22bダークテーマ: カードテキスト可読性確認
- 全テーマでフッター視認性確認
- 全コンポーネントでリンクアンダーライン確認

## [1.0.11] - 2025-10-31

### 追加
- 個別ページカスタマイズ用カスタムヘッダーコードメタボックス
  - ページ/投稿ごとにheadセクションにカスタムコード（JSON-LD、メタタグ、スクリプト）を追加
  - バリデーションなしのシンプルなテキストエリアインターフェース
- 投稿タイプ別設定付き投稿メタ情報設定
  - 統一設定または投稿タイプ別（投稿、固定ページ、カスタム投稿タイプ）の個別設定
  - 日付、更新日、著者、カテゴリー、タグの表示制御
  - 展開ボタン付きタグ表示制限（デフォルト: 5タグ、残りは+N表示）
  - 全メタタイプで一貫したラベル形式

### 改善
- WCAG AA準拠コントラスト比によるメタバッジアクセシビリティ
  - 明るい背景は暗いテキスト（var(--text-primary)）を使用
  - 暗い背景は明るいテキスト（var(--background-color)）を使用
  - ハードコードカラーなし、すべてテーマカラーシステムから
- デザインシステム統合
  - デザインパターンのcontainers.border_radiusをメタバッジに適用
  - 装飾テーマからのバッジパディング（var(--badge-padding)）
- ファイルクリーンアップ
  - 全Zone.Identifierファイルを削除
  - 全41カラーテーマJSONから未使用のbadge_text_colorを削除

### 修正
- simple-blog-cardプラグインに影響するPHP XML拡張機能欠落エラー
  - DOMDocumentクラス未検出エラーを解決するためphp8.1-xmlパッケージをインストール
- カスタム投稿タイプ編集ページエラーを解決

## [1.0.10] - 2025-10-30

### 修正
- ERR_INCOMPLETE_CHUNKED_ENCODINGエラーを修正
  - load-styles.phpとload-scripts.php経由で大量ファイル結合時のチャンク転送エンコーディングエラーを解決
  - スクリプトとスタイル結合を無効化（CONCATENATE_SCRIPTSとCONCATENATE_STYLESをfalseに設定）
  - HTTP/2環境でのプロトコルエラーを防止

## [1.0.9] - 2025-10-28

### 修正
- フッターウィジェットmin-height: 200px問題を修正
- フッターウィジェットから不要なbackground-colorを削除
- レスポンシブCSSの!important宣言を削減
- WordPressカスタマイザーでのjQuery未定義エラーを修正
- キャッシュバスティング機能が適用されない問題を修正

### 追加
- フロントエンド/バックエンド別キャッシュバスティング設定
- フッターコピーライトテキストのカスタマイザー設定
- フッターテーマクレジット表示のトグル

### 変更
- ウィジェットパディング/マージンをブロックエディター優先に変更
- 柔軟なサイドバーウィジェット設定用CSS変数を使用

## [1.0.8] - 2025-10-26

### 追加
- フロントページセクション拡張
  - リストセクション: 3 → 5セクション
  - 個別記事セクション: 3 → 5セクション
  - ドラッグ&ドロップセクション順序制御
- リストセクション5種類のフィルタータイプ
  - カテゴリー、タグ、投稿タイプ、著者、日付（当月/先月/今年/去年）
- リストセクション用アーカイブリンク（オン/オフトグル）
- フロントページコンテンツmax-width設定（0-2000px、0で全幅）
- フォーム表示設定（カスタマイザー）
  - パディング、フォントサイズ、ボーダー、スペーシング、テキストエリア高さ、行間、最大幅
  - 全フォームに適用（Contact Form 7、カスタムフォーム等）
- アーカイブ統一/個別設定切り替え
  - カテゴリー、タグ、著者、日付、検索、カスタム投稿タイプの個別設定

### 変更
- 検索結果ページ: アーカイブページと同じグリッドレイアウトとバッジスタイルメタ情報
- サムネイル表示: `aspect-ratio: 16/9` から `max-height: 250px` に変更（空白削減）

### 改善
- カスタマイザー見出し表示（視認性向上）
- JavaScriptキャッシュバスティング

### 修正
- カスタマイザーCSS: リピーターコントロール表示問題を修正（`!important`追加）

### 技術詳細
- 新ファイル: 8ファイル（カスタムコントロール: 2、設定: 2、CSS: 1、JS: 1、テンプレート: 2）
- 更新ファイル: 12ファイル
- 合計: +2,013追加、-315削除

## [1.0.7] - 2025-10-25

### 追加
- カスタマイザーにアーカイブレイアウト設定
  - カテゴリーアーカイブレイアウト設定
  - タグアーカイブレイアウト設定
  - その他アーカイブレイアウト設定
  - 検索結果ページレイアウト設定
  - レガシーウィジェットインターフェース（カスタマイザー）とブロックウィジェットインターフェース（外観 > ウィジェット）間の相互ナビゲーションリンク
- フロントページとアーカイブページの並び替えオプション
  - 公開日順（新しい順）
  - 更新日順（新しい順）
  - ランダム順
- フロントページ投稿セクションの表示要素制御
  - 表示切り替え: サムネイル、公開日、更新日、カテゴリー、抜粋
- アーカイブページの表示要素制御
  - 表示切り替え: サムネイル、公開日、更新日、カテゴリー、抜粋
- CSS変数によるバッジスタイル日付表示
  - 公開日バッジと更新日バッジを異なるスタイルで
  - 並び替え設定に基づく動的バッジ順序（主要並び替え基準が最初に表示）

### 修正
- ウィジェットエディター互換性: ブロックウィジェットでのwp-editorスクリプトエンキューエラーを修正
  - wp-editor競合警告のエラー抑制を実装
  - カスタマイザー（レガシーウィジェット）と外観 > ウィジェット（ブロックウィジェット）の両方が適切に動作

### 変更
- ウィジェット管理: ウィジェット機能を単一の動作するソリューションファイルに統合
- 日付表示スタイリング: プレーンテキストからより良い可読性のためバッジスタイル形式に置き換え

### 削除
- 8つの不要な開発ウィジェット実装ファイルを削除
- 未使用のajaxディレクトリとsave-color-theme.phpファイルを削除
- テストアーカイブレイアウトファイルをクリーンアップ

### 技術詳細
- .gitignoreを更新して.claude/ディレクトリとCLAUDE.mdを除外
- ウィジェットソリューションをinc/widget-working-solution.phpに実装
- アーカイブ設定をinc/customizer/layout-settings.phpに追加
- 表示要素制御: inc/customizer/front-page-settings.php、archive-settings.php
- バッジスタイリング: css/content.css
- 動的バッジ順序: template-parts/sections/posts-list.php、archive.php
- アーカイブクエリ変更: functions.php（backbone_modify_archive_query）

## [1.0.6] - 2025-10-24

### 追加
- カスタマイザーにカスタムJavaScript機能
  - サイト全体カスタムJS追加
  - 投稿タイプ別JS（投稿、固定ページ、カスタム投稿タイプ）
  - 出力位置選択（ヘッダー/フッター）
- カスタマイザーにカスタムCSS機能
  - サイト全体カスタムCSS追加
  - 投稿タイプ別CSS（投稿、固定ページ、カスタム投稿タイプ）
  - 出力位置選択（ヘッダー/フッター）
  - WordPressデフォルトの追加CSSセクションを置き換え

### 改善
- カスタマイザーメニュー順序の論理的再編成
  - 流れ: 外観/デザイン → コンテンツ表示 → 詳細設定 → カスタムコード → 開発者
  - 関連項目を近くに配置

## [1.0.5] - 2025-10-21

### 修正
- カスタム投稿タイプアーカイブでのページネーション404エラーを修正
  - 全登録カスタム投稿タイプ用の動的リライトルールを追加
  - 単層ページネーション修正（例: /seo-note/page-2/）
  - 階層ページネーション修正（例: /seo-note/report/page-2/）
  - 汎用実装のためハードコードディレクトリ名を削除
- タイトルタグのハードコード「Archive」テキストを修正
  - __()関数を使用した翻訳可能文字列に変更

### 技術詳細
- 全カスタム投稿タイプを動的検出するようリライトルールを更新
- 投稿タイプ設定からの自動スラッグ検出を実装
- 自動フラッシュ用リライトルールバージョンをv18に増加

## [1.0.4] - 2025-10-20

### 追加
- 動的アイテム管理用カスタマイザーリピーターコントロール機能
  - ピックアップ投稿セクション: ドラッグ&ドロップ並び替え付き無制限投稿
  - サービスセクション: ドラッグ&ドロップ並び替え付き無制限サービスカード
- HTML編集機能付きテキストエリアのWYSIWYGサポート
  - フリーコンテンツエリア、説明、サービス説明で利用可能
  - 対応タグ: 段落、太字、改行、斜体、リンク、リスト
  - タグガイド表示とサンプルプレースホルダー
- カスタマイザーに視覚的セクション区切り
  - フロントページ設定に視覚的セパレーターを追加（4セクション）
  - より良い識別のための微妙な背景色付きアイコンベース見出し

### 改善
- 統一レイアウトオプション: 全セクションが一貫した2col/3col/4col/list形式を使用

### 変更
- ピックアップ投稿: 固定6アイテムから動的リピーターシステムに変更
- サービスカード: 固定6アイテムから動的リピーターシステムに変更

### 技術詳細
- 新ファイル: inc/customizer/class-repeater-control.php、class-wysiwyg-control.php、js/customizer-repeater.js、css/customizer.css
- 更新ファイル: inc/customizer/front-page-settings.php、template-parts/sections/pickup.php、services.php

## [1.0.3] - 2025-10-18

### 追加

- ヒーローイメージ（メインビジュアル）機能: ページと投稿用カスタマイズ可能ヒーローイメージ
  - 高さ、幅、位置、オーバーレイ、テキスト配置のカスタマイザー設定
  - モバイル/デスクトップブレークポイント付きレスポンシブサポート
  - ページ/投稿別ヒーローイメージカスタマイズ用メタボックス
  - 再利用可能ヒーローイメージ表示用テンプレートパーツシステム
- アーカイブグリッドレイアウト機能: カテゴリー/アーカイブページ用レスポンシブグリッドレイアウト
  - カスタマイザー経由での2/3/4カラムグリッド選択
  - レスポンシブブレークポイント（デスクトップ: 選択カラム、タブレット: 2カラム、モバイル: 1カラム）
  - カード要素用不透明度による微妙なボーダーデザイン
- カスタムページネーション形式: /page/2/ から /page-2/ 形式に変更
  - 新ページネーション形式用カスタムリライトルール
  - 後方互換性のための旧形式から新形式への301リダイレクト

### 改善

- アーカイブページレイアウト: 著者情報と「続きを読む」ボタンを削除
- アーカイブページスペーシング: コンパクトレイアウトのため要素間スペーシングを削減
- 投稿テンプレート: メタ情報と投稿ナビゲーションを削除
- 固定ページテンプレート: 投稿サムネイルをヒーローイメージシステムに置き換え

### 修正

- アーカイブページ: プラグイン関数kspb_display_breadcrumbs()の条件チェックを追加
- ページネーションリダイレクト問題: /dictionary/page-experience/への不正リダイレクトを修正

### 変更

- ページネーションURL構造: /page/2/ から /page-2/（より意味的でクリーンなURL）

### 技術詳細

- 新ファイル: 7ファイル（CSS: 2、カスタマイザー: 2、ユーティリティ: 1、メタボックス: 1、テンプレートパーツ: 1）
- 新ディレクトリ: inc/meta-boxes/、inc/template-parts/
- functions.php: リダイレクト処理、カスタムリライトルール、クエリ変数登録を追加

## [1.0.2] - 2025-10-13

### 追加

- サブディレクトリ設定: サブディレクトリルートパスに特定コンテンツを表示するページ/投稿選択機能を追加
- 各サブディレクトリ（1-10）の表示タイプ（なし/固定ページ/投稿）選択と特定ページ/投稿選択用カスタマイザーコントロール
- ページと投稿リスト取得用ヘルパー関数 `backbone_get_pages_choices()` と `backbone_get_posts_choices()`
- カスタマイザードロップダウンでの階層ページ表示サポート（親 > 子形式）
- 投稿リストの日本語日付形式サポート（Y/m/d）

### 改善

- URL構造を維持しながら異なるコンテンツを表示するクエリリライト実装
- URL変更なしで動的コンテンツ割り当てをサポートするサブディレクトリ機能強化

### 技術詳細

- 早期URL傍受とクエリ操作用 `parse_request` と `parse_query` フックを実装
- フック間で選択ページ/投稿IDを渡すグローバル変数
- 新サブディレクトリ表示オプションを含むよう設定削除ハンドラーを更新

## [1.0.1] - 2025-10-06

### 修正

- 空タイトルページのタイトルタグ生成改善
- アーカイブページタイトルフォーマット改善（「アーカイブ:」「カテゴリー:」等の冗長プレフィックスを削除）
- 各種ページタイプ（カテゴリー、タグ、著者、日付アーカイブ、検索結果、404ページ）のタイトルタグ処理を修正

### 改善

- より適切なタイトルタグ生成によるSEO最適化向上
- 日付ベースアーカイブの日本語日付形式サポート

### 変更

- `.gitignore` を更新して `uploads/` ディレクトリと開発ファイルを除外
- 開発ファイル（`cp.sh`、`readme.html`）をリポジトリから削除

## [1.0.0] - 2025-09-28

### 追加

- Backbone Theme for SEO + LLMO 初回リリース
- 必須テンプレートファイル付き基本テーマ構造（front-page、home、single、page、archive、search、404）
- 1〜3カラムレイアウトとフルワイドオプションをサポートする柔軟なレイアウトシステム
- カラー、レイアウト、タイポグラフィ、デザイン設定用WordPressカスタマイザー統合
- 内蔵SEO機能: メタディスクリプション抽出、タイトル最適化、SEOフレンドリーパーマリンク
- 検索エンジン互換性向上のためHTML5セマンティックマークアップ
- モバイルファーストアプローチによるレスポンシブデザイン
- ウィジェットエリア: サイドバーとフッター（複数カラム対応）
- テーマサポート: カスタムロゴ、カスタムメニュー、投稿サムネイル、HTML5マークアップ、自動フィードリンク
- 開発者機能: CSS/JSファイル用キャッシュバスティング機能
- ユーティリティ関数モジュール: コア、レイアウト、タイポグラフィ、カラー、デザイン、装飾ユーティリティ
- 固定ページSEO拡張: 抜粋フィールドとタグサポート
- 複数CSSモジュール: base、layouts、components、content、responsive、utilities
- JavaScriptカスタマイザーコントロール
- GPL v2以降ライセンス

### 技術詳細

- WordPress 5.0+互換
- PHP 7.2+互換
- 拡張性のためプラグイン優先アーキテクチャ
- 最小限の依存関係による軽量設計
- テーマドメイン: backbone-seo-llmo
