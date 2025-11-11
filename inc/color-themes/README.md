# Color Themes

カラーテーマのJSON設定ファイルディレクトリ。

## Overview

このディレクトリには、Backbone Theme for SEO + LLMOで使用できるカラーテーマのJSON設定ファイルが格納されています。現在24個のWCAG AAA準拠カラーテーマ（12種類 x 明暗）を系外惑星・恒星名で提供しています。

## Features

- **24個のプリセットテーマ**: 系外惑星・恒星名を採用（ケプラー22b、ベテルギウス、アルゴラブなど）
- **WCAG AAA準拠**: すべてのテーマでアクセシビリティ基準を満たす
- **色覚多様性対応**: 多様な色覚特性に配慮した配色
- **明暗バリエーション**: 各テーマで明暗2種類（-dark / -light）を用意

## カラーテーマ設定 - 変数名対応表

JSONファイルで定義される色設定名と、実際にCSSで使用されるCSS変数名の対応関係を説明します。

**現在の仕様**: JSONキー（アンダースコア形式）とCSS変数名（ハイフン形式）は名前構造が統一されており、`primary_color` → `--primary-color` のように機械的に変換されます。

### 基本的な変数マッピング

| JSONキー | CSS変数名 | 説明 |
|----------|-----------|------|
| `primary_color` | `--primary-color` | プライマリカラー（メインの色） |
| `secondary_color` | `--secondary-color` | セカンダリカラー（補助色） |
| `accent_color` | `--accent-color` | アクセントカラー（強調色） |
| `background_color` | `--background-color` | メイン背景色 |
| `background_secondary` | `--background-secondary` | サブ背景色 |
| `text_primary` | `--text-primary` | メインテキスト色 |
| `text_secondary` | `--text-secondary` | サブテキスト色 |
| `text_light` | `--text-light` | ライトテキスト色 |
| `link_color` | `--link-color` | リンク色 |
| `link_hover_color` | `--link-hover-color` | リンクホバー色 |
| `header_link_color` | `--header-link-color` | ヘッダーリンク色 |
| `header_link_hover_color` | `--header-link-hover-color` | ヘッダーリンクホバー色 |
| `footer_link_color` | `--footer-link-color` | フッターリンク色 |
| `footer_link_hover_color` | `--footer-link-hover-color` | フッターリンクホバー色 |
| `border_color` | `--border-color` | ボーダー色 |
| `button_background_color` | `--button-background-color` | ボタン背景色 |
| `button_text_color` | `--button-text-color` | ボタンテキスト色 |
| `button_hover_background_color` | `--button-hover-background-color` | ボタンホバー背景色 |
| `form_background_color` | `--form-background-color` | フォーム背景色 |
| `form_focus_color` | `--form-focus-color` | フォームフォーカス色 |
| `search_button_color` | `--search-button-color` | 検索ボタン色 |

### 変数の一対一対応

JSONキーとCSS変数名は完全に一対一で対応しており、余計な重複はありません。

### JSONファイルの基本構造

```json
{
    "name": "テーマ名",
    "id": "theme-id",
    "description": "テーマの説明",
    "colors": {
        "primary_color": "#000000",
        "secondary_color": "#ffffff",
        "accent_color": "#dd9933",
        "background_color": "#ffffff",
        "background_secondary": "#f8f9fa",
        "text_primary": "#333333",
        "text_secondary": "#666666",
        "text_light": "#999999",
        "link_color": "#0073aa",
        "link_hover_color": "#005a87",
        "header_link_color": "#333333",
        "header_link_hover_color": "#0073aa",
        "footer_link_color": "#333333",
        "footer_link_hover_color": "#0073aa",
        "border_color": "#e1e1e1",
        "button_background_color": "#0073aa",
        "button_text_color": "#ffffff",
        "button_hover_background_color": "#005a87",
        "form_background_color": "#ffffff",
        "form_focus_color": "#0073aa",
        "search_button_color": "#0073aa"
    }
}
```

## 実装場所

- **CSS生成関数**: `inc/css-themes.php` の `backbone_generate_css_from_theme_data()` 関数
- **テーマ読み込み関数**: `inc/customizer/color-utilities.php` の `backbone_get_color_themes()` 関数
- **CSS変数出力**: `inc/css-themes.php` の `backbone_dynamic_color_theme_output()` 関数（`wp_head`フックで実行）

## 使用方法

1. JSONファイルを `inc/color-themes/` フォルダに配置
2. ファイル名形式: `tk-theme-{name}-{dark|light}.json`
   - 例: `tk-theme-kepler22b-dark.json`, `tk-theme-kepler22b-light.json`
3. カスタマイザーの「デザイン設定 > カラーテーマ」で自動的に選択可能になります

## 注意事項

- すべての色設定は16進数カラーコード（`#rrggbb`）で指定してください
- JSONファイルの構文エラーがあるとテーマが読み込まれません
- 「設定なし」を選択した場合、すべてのCSS変数は `initial` または `transparent` に設定されます

---

## 現在のカラーテーマ一覧

24個のカラーテーマ（12種類 x 明暗）:

1. **Kepler-22b** (ケプラー22b) - 青系
2. **Betelgeuse** (ベテルギウス) - 赤系
3. **Luyten 726** (ルイテン726) - 緑系
4. **WASP-12b** (ワスプトゥエルブ) - 黄系
5. **Psi Draconis** (プサイドラコニス) - 紫系
6. **Algorab** (アルゴラブ) - 蛍光オレンジ系
7. **Algorab K0V** (アルゴラブK0V) - オレンジ+黒ヘッダー系
8. **Arcturus** (アークトゥルス) - 橙系
9. **CoRoT-7b** (コロート7b) - 灰系
10. **Deneb** (デネブ) - 白系
11. **Sirius B** (シリウスB) - 黒系
12. **Capella** (カペラ) - クリーム系

各テーマに`-dark`（ダーク）と`-light`（ライト）バリエーションがあります。

---

## Changelog (Color Themes)

カラーテーマ固有の変更履歴。テーマ全体のChangelogは `/CHANGELOG.md` を参照してください。

### [1.0.12] - 2025-11-03

#### 追加
- 新24カラーテーマ（12種類 x 明暗）を系外惑星・恒星名で作成
- 全テーマWCAG AAA準拠、色覚多様性対応

#### 技術的改善
- ハードコードされた色を全てCSS変数化
- カラーテーマのJSON構造を標準化
