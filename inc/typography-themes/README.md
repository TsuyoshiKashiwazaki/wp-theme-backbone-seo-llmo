# タイポグラフィパターン管理システム

このディレクトリは、WordPressテーマのタイポグラフィパターンを管理するためのシステムです。

## 🎯 概要

- **動的パターン管理**: JSONファイルでタイポグラフィパターンを定義
- **簡単な追加・削除**: ファイルを追加・削除するだけでパターンが反映
- **Googleフォント自動読み込み**: 使用するフォントが自動で読み込まれる
- **カスタマイザー自動更新**: 新しいパターンがカスタマイザーに自動表示

## 📁 ファイル構造

```
inc/typography/
├── README.md                    # このファイル
├── tk-typo.json                # T.Kタイポ・ベーシック
├── tk-typo-sans.json           # T.Kタイポ・サンセリフ
├── tk-typo-serif.json          # T.Kタイポ・セリフ
├── tk-typo-rounded.json        # T.Kタイポ・ラウンド
├── tk-typo-mono.json           # T.Kタイポ・モノ
├── tk-typo-gothic.json         # T.Kタイポ・ゴシック
├── tk-typo-mincho.json         # T.Kタイポ・明朝
├── tk-typo-light.json          # T.Kタイポ・ライト
├── tk-typo-bold.json           # T.Kタイポ・ボールド
├── tk-typo-modern.json         # T.Kタイポ・モダン
└── sample-new-typo.json        # サンプル（削除可能）
```

## 🔧 JSONファイル形式

各タイポグラフィパターンは以下の形式で定義します：

```json
{
  "name": "T.Kタイポ・サンプル",
  "id": "tk-typo-sample",
  "description": "パターンの説明",
  "google_fonts": [
    "Noto+Sans+JP:wght@300;400;500;600;700;800;900"
  ],
  "font_family": "Noto Sans JP, sans-serif",
  "entry_content": {
    "font_size": "1rem",
    "line_height": "1.8",
    "letter_spacing": "0.02em",
    "font_weight": "400"
  },
  "headings": {
    "font_weight": "600",
    "letter_spacing": "0.01em",
    "margin_top": "2.5em",
    "margin_bottom": "1em"
  },
  "paragraphs": {
    "margin_bottom": "1.5em"
  }
}
```

## 📝 フィールド説明

### 必須フィールド
- **name**: カスタマイザーに表示される名前
- **id**: CSS クラス名に使用される一意のID
- **font_family**: CSSで使用するフォントファミリー

### オプションフィールド
- **description**: パターンの説明（管理用）
- **google_fonts**: Googleフォントの読み込み用URL配列
- **entry_content**: 本文コンテンツのスタイル
- **headings**: 見出し（h1-h6）のスタイル
- **paragraphs**: 段落（p）のスタイル

## ➕ 新しいパターンの追加方法

1. このディレクトリに新しいJSONファイルを作成
2. 上記の形式に従ってパターンを定義
3. ファイルを保存
4. WordPressカスタマイザーで新しいパターンが利用可能になります

## ❌ パターンの削除方法

1. 削除したいJSONファイルを削除
2. WordPressカスタマイザーから自動的に削除されます

## ✏️ パターンの編集方法

1. 該当するJSONファイルを編集
2. ファイルを保存
3. 変更が自動的に反映されます

## 🎨 Googleフォントの追加方法

1. [Google Fonts](https://fonts.google.com/) で使用したいフォントを選択
2. URLパラメータ形式（例：`Roboto:wght@300;400;700`）を`google_fonts`配列に追加
3. `font_family`フィールドにCSSフォントファミリーを設定

## 🔍 使用例

### 新しいタイポグラフィパターン「T.Kタイポ・エレガント」を追加する場合：

```json
{
  "name": "T.Kタイポ・エレガント",
  "id": "tk-typo-elegant",
  "description": "エレガントなセリフフォントを使用したタイポグラフィ",
  "google_fonts": [
    "Playfair+Display:wght@400;500;600;700"
  ],
  "font_family": "Playfair Display, serif",
  "entry_content": {
    "font_size": "1.1rem",
    "line_height": "1.9",
    "letter_spacing": "0.01em",
    "font_weight": "400"
  },
  "headings": {
    "font_weight": "600",
    "letter_spacing": "0.02em",
    "margin_top": "3em",
    "margin_bottom": "1.2em"
  },
  "paragraphs": {
    "margin_bottom": "1.8em"
  }
}
```

## ⚠️ 注意事項

- IDは他のパターンと重複しないよう注意してください
- JSONファイルの構文エラーがあるとパターンが読み込まれません
- Googleフォントは表示速度に影響するため、必要最小限に抑えることを推奨します
- パターンを削除する前に、サイトで使用されていないことを確認してください

## 🔧 トラブルシューティング

### パターンがカスタマイザーに表示されない場合
1. JSONファイルの構文が正しいか確認
2. `name`と`id`フィールドが設定されているか確認
3. ファイルの権限が正しく設定されているか確認

### フォントが読み込まれない場合
1. `google_fonts`配列の形式が正しいか確認
2. Googleフォントサービスが利用可能か確認
3. ネットワーク接続を確認
