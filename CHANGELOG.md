# Changelog

All notable changes to Backbone Theme for SEO + LLMO will be documented in this file.

## [1.0.18] - 2025-11-11

### Added
- 20 new color themes (10 pairs dark/light): 55 Cancri e, GJ 1214b, HAT-P-7b, HD 189733b, HD 209458b, KELT-9b, PSR B1257+12, TOI-5205b, TrES-2b, WASP-76b
- 20 new design themes (artist names): Banksy, Basquiat, Calder, Chagall, Dalí, Duchamp, Escher, Gaudí, Haring, Hokusai, Kahlo, Kandinsky, Klimt, Kusama, Matisse, Miró, Mondrian, Mucha, Pollock, Rembrandt, Rothko, Warhol
- 10 new decoration themes: Velvet, Marble, Wood, Glass, Paper, Neon, Leather, Fabric, Concrete, Ceramic
- One-time cache clearing script in header.php for existing visitors (clears LocalStorage, SessionStorage, Service Worker caches)

### Fixed
- Sticky sidebar positioning issue when WordPress Admin Bar is present (added adminBarHeight calculation in js/theme.js)

### Changed
- Standardized directory naming with `-themes` suffix: `inc/designs/` → `inc/design-themes/`, `inc/typography/` → `inc/typography-themes/`
- Consolidated color-themes documentation into single README.md
- Updated design-utilities.php and typography-utilities.php to use new directory paths
- Adjusted all thick borders (2px+) to 1px with transparency for better readability
- Optimized shadow intensities across all new design themes

### Removed
- Deleted `inc/color-themes/old/` directory (45 legacy theme files)
- Removed backup and disabled files (.backup, .disabled extensions)
- Removed redundant documentation files (CHANGELOG.md, COLOR_VARIABLE_MAPPING.md from color-themes)

### Technical
- Modified `js/theme.js`: Added WordPress Admin Bar height detection in sticky sidebar calculations (lines 528-533, 586-619)
- Modified `header.php`: Added forceClearLegacyCache() function with flag 'backbone_cache_cleared_20250111_v1'
- Modified `css/utilities.css`: Updated utility styles
- Renamed directories: `inc/designs/` → `inc/design-themes/`, `inc/typography/` → `inc/typography-themes/`

## [1.0.17] - 2025-11-11

### Added
- Individual file-based cache busting for all CSS and JavaScript files
- Helper function `backbone_get_file_version()` in `inc/theme-setup.php` for consistent version management
- Debounce functionality (150ms) to MutationObserver in `js/theme.js` for better performance

### Fixed
- Unwanted whitespace caused by sticky header padding adjustments
- Removed all hardcoded version numbers (1.0.0, 1.0.1, 4.0.0) across the theme

### Changed
- Cache busting now uses individual file modification times instead of shared version numbers
- Updated version management across all enqueue functions to use new helper
- MutationObserver now checks for height changes before updating padding

### Technical
- Modified `inc/theme-setup.php`: Added backbone_get_file_version() helper, updated all wp_enqueue_style/script calls
- Modified `functions.php`: Updated backbone_enqueue_responsive_typography() and backbone_enqueue_front_page_sections()
- Modified `inc/customizer/index.php`: Updated all customizer scripts to use new helper
- Modified `inc/widget-working-solution.php`: Updated widget editor script versioning
- Modified `inc/customizer/custom-color-theme.php`: Updated customizer preview/controls scripts
- Modified `inc/customizer/subdirectory-design-settings.php`: Updated subdirectory customizer scripts
- Modified `js/theme.js`: Added debounce and height change detection to adjustStickyHeaderPadding()

## [1.0.16] - 2025-11-10

### Fixed
- Archive pagination displaying posts in wrong order on page 2+ (added secondary sort key with ID)
- Random post order not working in archive settings (added 'rand' support in backbone_force_correct_post_order)

### Added
- Conditional visibility for unified/individual archive settings (active_callback functions)
- backbone_is_unified_archive_settings_enabled() function for showing common settings
- backbone_force_correct_post_order() function to enforce correct post order at template_redirect

### Improved
- Archive settings sections now visually grouped with subtle borders in customizer
- Query execution priority increased to 9999 to prevent plugin interference

### Technical
- Modified `functions.php`: Changed orderby to array format with secondary ID sort key
- Modified `functions.php`: Increased pre_get_posts priority from default to 9999
- Modified `functions.php`: Added template_redirect hook to force correct post order
- Modified `inc/customizer/archive-settings.php`: Added active_callback to all archive setting controls
- Added `css/customizer-controls.css`: Section border styling
- Modified `js/customizer-controls.js`: Section grouping logic
- Modified `js/subdirectory-customizer-preview.js`: Removed console.log debug output

### Documentation
- Added GitHub repository URL to CLAUDE.md

## [1.0.15] - 2025-11-10

### Fixed
- Sticky sidebar penetrating through footer (added margin calculations for accurate collision detection)
- Sidebar position logic reversed (sidebar-right now correctly places Sidebar-1 on the right side)
- Sidebar-1 disappearing on scroll (preserved Grid layout properties when switching to position:absolute)

### Added
- Real-time opacity percentage display for scroll transparency setting in customizer
- Live preview functionality for sidebar position changes in customizer

### Changed
- Sticky sidebar, sticky header, and auto-hide header are now enabled by default
- Documentation terminology unified ("ヒーローイメージ" → "メインビジュアル（ヒーローイメージ）")

### Documentation
- Added official child theme reference (https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child)
- Added child theme FAQ and quick start guide

## [1.0.14] - 2025-11-05

### Fixed
- Main visual (hero image) style, alignment, and decoration settings now properly respect common/individual mode setting
- Fullwidth main visual (hero image) CSS layout corrected

### Added
- Main visual settings (hero image) customizer common/individual mode radio toggle
- Live preview for main visual (hero image) style changes in customizer
- supportedPostTypes data passed to JavaScript for dynamic handlers

### Improved
- Unified date badge size (0.75em) and format (Y/m/d) across all templates
- Archive grid date badge no longer applies double font-size reduction

## [1.0.13] - 2025-11-04

### Improved
- **404 Template Structure**: Unified 404.php template structure with other templates using standard article, entry-header, and entry-content classes for consistency
- **404 Page Layout**: Added max-width: 800px constraint to 404 page content for improved readability
- **404 Page Content**: Simplified 404 page by removing hardcoded suggestions (popular posts, categories, multiple buttons) for cleaner design
- **Admin UX**: Added prominent notice in Settings > Reading when custom front page mode is active
- **Settings Management**: Disabled WordPress standard homepage display settings when custom front page mode is enabled to prevent user confusion

### Added
- **404 Status Code**: Added proper HTTP 404 status header and no-cache headers to 404.php template
- **Admin Notice System**: New backbone_reading_settings_notice() function displays info banner on reading settings page
- **Visual Feedback**: Grayed out homepage display settings with explanation overlay when custom mode is active
- **Customizer Description**: Dynamic section description that updates based on front page mode

### Technical
- Modified `404.php`: Changed section to article, page-header to entry-header, page-content to entry-content
- Modified `css/content.css`: Added max-width and margin auto to .error-404 class
- Modified `inc/admin-pages.php`: Added reading settings notice function with CSS for disabled state
- Modified `inc/customizer/front-page-settings.php`: Added dynamic description for custom mode notice

## [1.0.12] - 2025-11-03

### Major Updates

**Removed WordPress Core "Colors" Section**
- Removed `add_theme_support('custom-background')` from theme setup
- Added `backbone_remove_colors_section()` function to force remove WordPress Core colors section from customizer
- Cleaner customizer UI focused on theme-specific color themes

**Completely Redesigned Color Themes**
- Replaced 40+ themes with 22 WCAG AAA compliant themes (11 colors × 2 patterns: light/dark)
- Old themes preserved in `inc/color-themes/old/` directory for reference
- New themes named after exoplanets and stars for unique branding:
  - Blue: **Kepler-22b** (ocean world exoplanet)
  - Red: **Betelgeuse** (red supergiant star)
  - Green: **Luyten 726-8** (red dwarf star)
  - Yellow: **WASP-12b** (scorching hot Jupiter)
  - Purple: **Psi Draconis** (binary star system)
  - Orange: **Algorab** (fluorescent orange), **Algorab K0V** (black header variant), **Arcturus** (orange giant star)
  - Grey: **CoRoT-7b** (lava planet)
  - White: **Deneb** (white supergiant star)
  - Black: **Sirius B** (white dwarf companion)
  - Cream: **Capella** (yellow-white giant star)
- All themes designed for color-blind users with proper contrast ratios (WCAG AAA)
- Each theme includes comprehensive accessibility descriptions in Japanese

**Removed All Hardcoded Colors**
- Replaced all hardcoded color values (#555, #666, #fff, etc.) with CSS variables
- Affected files:
  - `css/front-page-sections.css` - Card backgrounds, text, borders, category badges, archive buttons
  - `css/components-header.css` - Header subtitle colors
  - `css/components-footer.css` - Footer text and link colors
  - `css/base.css` - Content link styles
- Everything now uses theme color system variables for consistency across all 22 themes

**Improved Visibility and Accessibility**
- Fixed header subtitle visibility on all themes
  - Added inline styles and CSS rules to force white text on dark backgrounds
  - File: `header.php`, `css/components-header.css`
- Fixed footer text readability on all themes
  - Changed all footer elements to use `var(--footer-link-color)` with `!important`
  - Footer links now consistently visible with white text
  - Files: `css/components-footer.css`, `footer.php`
- Fixed card text visibility in dark themes
  - Post excerpts now use `var(--text-primary)` instead of hardcoded #555
  - Category badges use `var(--header-link-color)` for proper contrast
  - Archive link buttons use `var(--header-link-color)` instead of background color
  - File: `css/front-page-sections.css`
- Fixed hero image text visibility
  - Changed from hardcoded #ffffff to `var(--button-text-color)`
  - File: `css/front-page-sections.css`

**Added Link Underlines for Better UX**
- All links now have subtle underlines (0.5px thickness) for improved accessibility
- Applied to:
  - Footer links with 3px offset and 0.9 opacity
  - Content links (`.entry-content a`)
  - Post title links (`.post-title a`)
- Maintains clean design while ensuring links are clearly identifiable
- Files: `css/components-footer.css`, `css/base.css`, `css/front-page-sections.css`

### Technical Improvements

**Card Backgrounds**
- Card backgrounds now properly inherit theme colors via `var(--background-color)`
- Card borders use `var(--border-color)` for theme consistency

**Header and Footer Consistency**
- Header and footer text forced to white (`var(--header-link-color)`, `var(--footer-link-color)`) for consistent visibility across all 22 themes
- Both use identical white-on-primary-color pattern

**Text Decoration Standards**
- All text decorations use thin lines (0.5px thickness)
- Consistent offset (3px) across all underlined elements
- Opacity 0.9 for subtle, non-intrusive appearance

### Files Changed

**Theme Configuration:**
- `inc/theme-setup.php` - Removed custom-background support, added colors section removal
- `style.css` - Version bump to 1.0.12

**Color Themes (22 new files):**
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

**Old Themes (40+ files moved):**
- All previous color theme JSON files moved to `inc/color-themes/old/` directory

**CSS Files:**
- `css/components-header.css` - Header subtitle visibility fixes
- `css/components-footer.css` - Footer text and link visibility, underlines
- `css/front-page-sections.css` - Removed hardcoded colors, card visibility fixes
- `css/base.css` - Added link underlines

**Template Files:**
- `header.php` - Added inline style for subtitle visibility

### Browser Testing

All changes verified with Playwright browser automation:
- Kepler-22b light theme: Header subtitle visibility confirmed
- Kepler-22b dark theme: Card text readability confirmed
- Footer visibility across all themes confirmed
- Link underlines verified across all components

## [1.0.11] - 2025-10-31

### Added
- Custom header code meta box for individual page customization
  - Add custom code (JSON-LD, meta tags, scripts) to head section per page/post
  - Simple textarea interface without validation
- Post meta information settings with per-post-type configuration
  - Unified settings or individual settings per post type (post, page, custom post types)
  - Display controls for date, modified date, author, category, tags
  - Tag display limit with expand button (default: 5 tags, shows +N for remaining)
  - Consistent label format across all meta types

### Improved
- Meta badge accessibility with WCAG AA compliant contrast ratios
  - Light backgrounds use dark text (var(--text-primary))
  - Dark backgrounds use light text (var(--background-color))
  - No hardcoded colors, all from theme color system
- Design system integration
  - Applied containers.border_radius from design patterns to meta badges
  - Badge padding from decoration themes (var(--badge-padding))
- File cleanup
  - Removed all Zone.Identifier files
  - Removed unused badge_text_color from all 41 color theme JSONs

### Fixed
- PHP XML extension missing error affecting simple-blog-card plugin
  - Installed php8.1-xml package to resolve DOMDocument class not found error
- Custom post type edit page errors resolved

## [1.0.10] - 2025-10-30

### Fixed
- Fixed ERR_INCOMPLETE_CHUNKED_ENCODING error
  - Resolved chunked transfer encoding errors when concatenating large numbers of files via load-styles.php and load-scripts.php
  - Disabled script and style concatenation (set CONCATENATE_SCRIPTS and CONCATENATE_STYLES to false)
  - Prevented protocol errors in HTTP/2 environments

## [1.0.9] - 2025-10-28

### Fixed
- Fixed footer widget min-height: 200px issue
- Removed unnecessary background-color from footer widgets
- Reduced !important declarations in responsive CSS
- Fixed jQuery undefined error in WordPress Customizer
- Fixed cache busting functionality not being applied

### Added
- Separate cache busting settings for frontend/backend
- Customizer setting for footer copyright text
- Toggle for footer theme credit display

### Changed
- Changed widget padding/margin to prioritize block editor
- Used CSS variables for flexible sidebar widget settings

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
