# Changelog

All notable changes to Backbone Theme for SEO + LLMO will be documented in this file.

## [1.0.9] - 2025-10-28

### Fixed
- フッターウィジェットのmin-height: 200px問題を修正
- フッターウィジェットの不要なbackground-colorを削除
- レスポンシブCSSの!important宣言を削減
- WordPress CustomizerでのjQuery未定義エラーを修正
- キャッシュバスティング機能が適用されない問題を修正

### Added
- フロントエンド/バックエンド別々のキャッシュバスティング設定
- フッター著作権表示のカスタマイザー設定
- フッターテーマクレジット表示のオン/オフ切り替え

### Changed
- ウィジェットのパディング/マージンをブロックエディタ優先に変更
- サイドバーウィジェットにCSS変数を使用した柔軟な設定

## [1.0.8] - 2025-10-26

### Added
- Front page section expansion
  - List sections: 3 → 5 sections
  - Individual article sections: 3 → 5 sections
  - Drag & drop section ordering control
- List section 5 filter types
  - Category, tag, post type, author, date (current month/last month/current year/last year)
- Archive links for list sections (on/off toggle)
- Front page content max-width setting (0-2000px, 0 for full width)
- Form display settings (Customizer)
  - Padding, font size, border, spacing, textarea height, line height, max width
  - Applied to all forms (Contact Form 7, custom forms, etc.)
- Archive unified/individual settings switch
  - Individual settings for category, tag, author, date, search, custom post types

### Changed
- Search results page: Same grid layout and badge-style meta info as archive pages
- Thumbnail display: Changed from `aspect-ratio: 16/9` to `max-height: 250px` (reduced empty space)

### Improved
- Customizer heading display (better visibility)
- JavaScript cache busting

### Fixed
- Customizer CSS: Fixed repeater control display issues (`!important` added)

### Technical Details
- New files: 8 files (custom controls: 2, settings: 2, CSS: 1, JS: 1, templates: 2)
- Updated files: 12 files
- Total: +2,013 additions, -315 deletions

## [1.0.7] - 2025-10-25

### Added
- Archive layout settings in Customizer
  - Category archive layout configuration
  - Tag archive layout configuration
  - Other archive layout configuration
  - Search results page layout configuration
  - Mutual navigation links between legacy widget interface (Customizer) and block widget interface (Appearance > Widgets)
- Sorting options for front page and archive pages
  - Publication date order (newest first)
  - Modified date order (newest first)
  - Random order
- Display element controls for front page posts section
  - Toggle visibility: thumbnail, publication date, modified date, category, excerpt
- Display element controls for archive pages
  - Toggle visibility: thumbnail, publication date, modified date, category, excerpt
- Badge-style date display with CSS variables
  - Publication date badge and modified date badge with distinct styling
  - Dynamic badge order based on sort setting (primary sort criterion appears first)

### Fixed
- Widget editor compatibility: Fixed wp-editor script enqueueing error with block widgets
  - Implemented error suppression for wp-editor conflict warnings
  - Ensured both Customizer (legacy widgets) and Appearance > Widgets (block widgets) work properly

### Changed
- Widget management: Consolidated widget functionality into single working solution file
- Date display styling: Replaced plain text with badge-style format for better readability

### Removed
- Deleted 8 unnecessary development widget implementation files
- Deleted unused ajax directory and save-color-theme.php file
- Cleaned up test archive layout file

### Technical Details
- Updated .gitignore to exclude .claude/ directory and CLAUDE.md
- Widget solution implemented in inc/widget-working-solution.php
- Archive settings added to inc/customizer/layout-settings.php
- Display element controls: inc/customizer/front-page-settings.php, archive-settings.php
- Badge styling: css/content.css
- Dynamic badge ordering: template-parts/sections/posts-list.php, archive.php
- Archive query modification: functions.php (backbone_modify_archive_query)

## [1.0.6] - 2025-10-24

### Added
- Custom JavaScript functionality in Customizer
  - Site-wide custom JS addition
  - Post type specific JS (posts, pages, custom post types)
  - Output position selection (header/footer)
- Custom CSS functionality in Customizer
  - Site-wide custom CSS addition
  - Post type specific CSS (posts, pages, custom post types)
  - Output position selection (header/footer)
  - Replaced WordPress default Additional CSS section

### Improved
- Logical reorganization of Customizer menu order
  - Flow: Appearance/Design → Content Display → Advanced Settings → Custom Code → Developer
  - Related items placed close together

## [1.0.5] - 2025-10-21

### Fixed
- Fixed pagination 404 errors on custom post type archives
  - Added dynamic rewrite rules for all registered custom post types
  - Fixed single-level pagination (e.g., /seo-note/page-2/)
  - Fixed hierarchical pagination (e.g., /seo-note/report/page-2/)
  - Removed hardcoded directory names for generic implementation
- Fixed hardcoded "Archive" text in title tags
  - Changed to translatable string using __() function

### Technical Details
- Updated rewrite rules to dynamically detect all custom post types
- Implemented automatic slug detection from post type configuration
- Increased rewrite rules version to v18 for automatic flush

## [1.0.4] - 2025-10-20

### Added
- Customizer repeater control feature for dynamic item management
  - Pickup posts section: Unlimited posts with drag-and-drop sorting
  - Services section: Unlimited service cards with drag-and-drop sorting
- WYSIWYG support for text areas with HTML editing capabilities
  - Available in free content area, descriptions, and service descriptions
  - Supported tags: paragraph, bold, line break, italic, link, list
  - Tag guide display and sample placeholders
- Visual section dividers in customizer
  - Added visual separators in front page settings (4 sections)
  - Icon-based headings with subtle background colors for better identification

### Improved
- Unified layout options: All sections now use consistent 2col/3col/4col/list formats

### Changed
- Pickup posts: Changed from fixed 6 items to dynamic repeater system
- Service cards: Changed from fixed 6 items to dynamic repeater system

### Technical Details
- New files: inc/customizer/class-repeater-control.php, class-wysiwyg-control.php, js/customizer-repeater.js, css/customizer.css
- Updated files: inc/customizer/front-page-settings.php, template-parts/sections/pickup.php, services.php

## [1.0.3] - 2025-10-18

### Added

- Hero Image (Main Visual) feature: Customizable hero images for pages and posts
  - Customizer settings for height, width, position, overlay, and text alignment
  - Responsive support with mobile/desktop breakpoints
  - Meta box for per-page/post hero image customization
  - Template part system for reusable hero image display
- Archive Grid Layout feature: Responsive grid layout for category/archive pages
  - 2/3/4 column grid selection via Customizer
  - Responsive breakpoints (desktop: selected columns, tablet: 2 columns, mobile: 1 column)
  - Subtle border design using opacity for card elements
- Custom Pagination format: Changed from /page/2/ to /page-2/ format
  - Custom rewrite rules for new pagination format
  - 301 redirect from old format to new format for backward compatibility

### Improved

- Archive page layout: Removed author information and "Read more" button
- Archive page spacing: Reduced spacing between elements for compact layout
- Single post template: Removed meta information and post navigation
- Page template: Replaced post thumbnail with hero image system

### Fixed

- Archive page: Added conditional check for plugin function kspb_display_breadcrumbs()
- Pagination redirect issue: Fixed incorrect redirects to /dictionary/page-experience/

### Changed

- Pagination URL structure: /page/2/ to /page-2/ (more semantic and cleaner URLs)

### Technical Details

- New files: 7 files (CSS: 2, Customizer: 2, Utilities: 1, Meta boxes: 1, Template parts: 1)
- New directories: inc/meta-boxes/, inc/template-parts/
- Functions.php: Added redirect handling, custom rewrite rules, query variable registration

## [1.0.2] - 2025-10-13

### Added

- Subdirectory settings: Added page/post selection functionality to display specific content at subdirectory root paths
- Customizer controls for selecting display type (none/page/post) and choosing specific pages/posts for each subdirectory (1-10)
- Helper functions `backbone_get_pages_choices()` and `backbone_get_posts_choices()` for retrieving page and post lists
- Support for hierarchical page display in customizer dropdowns (Parent > Child format)
- Japanese date format support (Y/m/d) for post listings in customizer

### Improved

- Query rewriting implementation to display different content while maintaining subdirectory URL structure
- Enhanced subdirectory functionality to support dynamic content assignment without URL changes

### Technical Details

- Implemented `parse_request` and `parse_query` hooks for early URL interception and query manipulation
- Global variables for passing selected page/post IDs between hooks
- Settings deletion handlers updated to include new subdirectory display options

## [1.0.1] - 2025-10-06

### Fixed

- Improved title tag generation for pages with empty titles
- Enhanced archive page title formatting (removed redundant prefixes like "Archive:", "Category:", etc.)
- Fixed title tag handling for various page types (category, tag, author, date archives, search results, 404 pages)

### Improved

- Better SEO optimization through more appropriate title tag generation
- Japanese date format support for date-based archives

### Changed

- Updated `.gitignore` to exclude `uploads/` directory and development files
- Removed development files (`cp.sh`, `readme.html`) from repository

## [1.0.0] - 2025-09-28

### Added

- Initial release of Backbone Theme for SEO + LLMO
- Basic theme structure with essential template files (front-page, home, single, page, archive, search, 404)
- Flexible layout system supporting 1-3 column layouts and full-width option
- WordPress Customizer integration for color, layout, typography, and design settings
- Built-in SEO features: meta description extraction, title optimization, SEO-friendly permalinks
- HTML5 semantic markup for better search engine compatibility
- Responsive design with mobile-first approach
- Widget areas: sidebar and footer (multi-column support)
- Theme support: custom logo, custom menus, post thumbnails, HTML5 markup, automatic feed links
- Developer features: cache busting functionality for CSS/JS files
- Utility function modules: core, layout, typography, color, design, and decoration utilities
- Page SEO extensions: excerpt field and tag support for pages
- Multiple CSS modules: base, layouts, components, content, responsive, utilities
- JavaScript customizer controls
- GPL v2 or later license

### Technical Details

- WordPress 5.0+ compatible
- PHP 7.2+ compatible
- Plugin-first architecture for extensibility
- Lightweight design with minimal dependencies
- Theme domain: backbone-seo-llmo
