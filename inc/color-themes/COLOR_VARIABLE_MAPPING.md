# カラーテーマ設定 - 変数名対応表

このドキュメントでは、JSONファイルで定義される色設定名と、実際にCSSで使用されるCSS変数名の対応関係を説明します。

**注意**: 2025年8月時点で、混乱を避けるためJSONキー名をCSS変数名に統一しました。

## 基本的な変数マッピング

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

## 変数の一対一対応

JSONキーとCSS変数名は完全に一対一で対応しており、余計な重複はありません。

## JSONファイルの基本構造

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

- **変数マッピング定義**: `inc/css-themes.php` の `seo_optimus_generate_css_from_theme_data()` 関数
- **テーマデータ読み込み**: `inc/customizer/color-utilities.php` の `seo_optimus_get_color_themes()` 関数
- **CSS出力**: `wp_head` アクションで動的にCSS変数として出力

## 使用方法

1. JSONファイルを `inc/color-themes/` フォルダに配置
2. ファイル名は `theme-id.json` 形式
3. カスタマイザーの「デザイン設定 > カラーテーマ」で選択可能になります

## 注意事項

- すべての色設定は16進数カラーコード（`#rrggbb`）で指定してください
- JSONファイルの構文エラーがあるとテーマが読み込まれません
- 「設定なし」を選択した場合、すべてのCSS変数は `initial` または `transparent` に設定されます