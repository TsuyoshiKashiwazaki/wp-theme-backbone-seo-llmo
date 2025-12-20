# Backbone Theme for SEO + LLMO

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.30-orange.svg)](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo/releases)

![Backbone Theme Screenshot](screenshot.png)

メディアサイト向けに最適化された軽量WordPressベーステーマ。SEO・LLMO対応機能を内蔵し、プラグインによる拡張を前提とした設計。

> 専用プラグインとの連携で効率的なメディア運用を実現する軽量ベーステーマ

## 概要

Backbone Theme for SEO + LLMO は、メディア運用に特化したシンプルなWordPressベーステーマです。基本的なSEO対応とレイアウト機能を提供し、高度な機能は専用プラグイン（SEO・LLMO・GEO・AIO最適化プラグイン）での拡張を前提とした設計です。テーマとプラグインの組み合わせにより、効率的なメディアサイト・ブログ運用を実現します。

### 主な機能

- **プラグイン優先設計** - 専用プラグインによる拡張を前提とした設計
- **基本SEO対応** - メタディスクリプション抽出、タイトル最適化
- **柔軟なレイアウト** - 1〜3カラムレイアウト、フルワイド対応
- **カスタマイザー連携** - 基本的なカラー・デザイン調整
- **メディア最適化** - ブログ・メディアサイト向けに最適化
- **軽量設計** - 必要最小限の機能のみ搭載
- **レスポンシブ** - モバイルファースト設計
- **開発者向け機能** - キャッシュバスティング機能搭載

## クイックスタート

### インストール

1. テーマのZIPファイルをダウンロード
2. WordPress管理画面: **外観** → **テーマ** → **新規追加** → **テーマのアップロード**
3. ZIPファイルを選択してインストール
4. テーマを有効化

### 動作環境

- WordPress 5.0 以上
- PHP 7.2 以上
- HTML5対応のモダンブラウザ

## テーマ構造

### テンプレートファイル

- `front-page.php` - フロントページテンプレート
- `home.php` - ブログホームページテンプレート
- `single.php` - 投稿詳細テンプレート
- `page.php` - 固定ページテンプレート
- `archive.php` - アーカイブテンプレート
- `search.php` - 検索結果テンプレート
- `404.php` - 404エラーページテンプレート
- `comments.php` - コメントテンプレート
- `sidebar.php` - サイドバーテンプレート

### ディレクトリ構造

```
/css/              - スタイルシート（base, layouts, components等）
/inc/              - PHPインクルードファイル（機能モジュール）
/inc/utilities/    - ユーティリティ関数
/inc/customizer/   - カスタマイザー設定
/js/               - JavaScriptファイル（カスタマイザー関連）
```

## カスタマイザー設定

WordPressカスタマイザーから以下の設定にアクセスできます:

- **カラー設定** - テーマカラー、背景色、テキストカラー
- **レイアウト設定** - カラム数、サイドバー位置、コンテナ幅
- **タイポグラフィ設定** - フォント、サイズ、行間
- **デザインパターン** - ボタンスタイル、カードなどのデザイン要素
- **ヘッダー/フッター設定** - ロゴ、メニュー、ウィジェットエリア

## ウィジェットエリア

- サイドバーウィジェットエリア
- フッターウィジェットエリア（複数カラム対応）

## SEO機能

### 内蔵SEO機能

- **メタディスクリプション自動抽出** - 抜粋または本文の最初の25語から抽出
- **タイトルタグ最適化** - カスタマイズ可能な区切り文字、自動タイトル生成
- **固定ページSEO拡張** - 抜粋フィールドとタグのサポート
- **SEOフレンドリーURL** - パーマリンク構造の自動最適化
- **HTML5セマンティックマークアップ** - 検索エンジンフレンドリーな構造
- **レスポンシブデザイン** - モバイルファースト対応

### プラグインによる拡張

高度なSEO機能は「SEO・LLMO・GEO・AIO最適化プラグイン」などの専用プラグインで追加することを想定:

- 構造化データ（JSON-LD、Schema.org）
- Open Graphタグ（Facebook等のSNS）
- Twitter Cardタグ
- Canonical URLタグ
- 高度なrobotsメタタグ制御

## 開発者向け機能

### キャッシュバスティング

開発時のキャッシュ問題を解決するため、CSSとJavaScriptファイルにタイムスタンプベースのバージョニングを実装。カスタマイザーでオン/オフの切り替えが可能。

### ユーティリティ関数

以下のユーティリティモジュールを搭載:

- `core-utilities.php` - コア機能ヘルパー関数
- `layout-utilities.php` - レイアウト関連関数
- `typography-utilities.php` - タイポグラフィ関数
- `color-utilities.php` - カラー処理関数
- `design-utilities.php` - デザインパターン関数
- `decoration-utilities.php` - 装飾関連関数

## WordPressテーマサポート

- カスタムロゴ
- カスタムメニュー（メイン、フッター）
- アイキャッチ画像
- HTML5マークアップ
- 自動フィードリンク
- タイトルタグサポート
- エディタースタイル
- ワイドアライメント
- レスポンシブ埋め込み
- 固定ページの抜粋とタグ

## 重要事項

**プラグイン連携**: このテーマは必要最小限のベーステーマとして設計されています。SEO強化、LLMO（大規模言語モデル最適化）、GEO（生成エンジン最適化）などの高度な機能は、「SEO・LLMO・GEO・AIO最適化プラグイン」などの専用プラグインで追加することを想定しています。

## 子テーマ

**公式子テーマサンプルを提供**

安全なカスタマイズのためのサンプル子テーマを用意しています:

**[Backbone SEO LLMO Child Theme](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child)**

### 子テーマを使う理由

- **安全なアップデート**: 親テーマの更新でカスタマイズが上書きされない
- **簡単なカスタマイズ**: 必要なファイルだけを変更
- **元に戻せる**: 子テーマを無効化すればオリジナルに戻る
- **学習に最適**: カスタマイズしながら親テーマのコードを学習

### クイックスタート

1. [GitHub](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child)から子テーマをダウンロード
2. 親テーマと子テーマの両方をインストール
3. **子テーマ**を有効化（親テーマではなく）
4. `style.css`、`functions.php`、またはテンプレートファイルでカスタマイズ

詳細は[子テーマドキュメント](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child)を参照。

## 更新履歴

詳細な変更履歴は [CHANGELOG.md](CHANGELOG.md) を参照してください。

## ライセンス

このテーマは GPL v2 以降でライセンスされています。
ライセンス: https://www.gnu.org/licenses/gpl-2.0.html

## サポート・開発

**開発者**: 柏崎 剛（Tsuyoshi Kashiwazaki）
**ウェブサイト**: https://www.tsuyoshikashiwazaki.jp/profile/
**サポート**: このテーマに関するご質問やバグ報告は、開発者ウェブサイトからお問い合わせください。

## コントリビューション

コントリビューションを歓迎します！GitHubでプルリクエストやイシューをお気軽にお寄せください。

---

<div align="center">

**キーワード**: WordPressテーマ, ベーステーマ, SEO, LLMO, メディア最適化, プラグイン拡張, 軽量, レスポンシブ, カスタマイザー, ブログテーマ

Made by [Tsuyoshi Kashiwazaki](https://github.com/TsuyoshiKashiwazaki)

</div>
