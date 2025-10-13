# Changelog

All notable changes to Backbone Theme for SEO + LLMO will be documented in this file.

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
