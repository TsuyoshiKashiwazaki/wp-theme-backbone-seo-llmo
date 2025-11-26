# Backbone Theme for SEO + LLMO

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.26-orange.svg)](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo/releases)

![Backbone Theme Screenshot](screenshot.png)

A minimal WordPress base theme optimized for media sites with built-in SEO and LLMO support, designed for plugin-based extensibility.

> A lightweight foundation theme for efficient media operations, designed to work seamlessly with specialized plugins

## Overview

Backbone Theme for SEO + LLMO is a streamlined WordPress base theme specifically designed for media operations. It provides essential functionality while intentionally keeping advanced features modular through plugin architecture (such as SEO・LLMO・GEO・AIO optimization plugins). This design philosophy enables efficient management of media sites and blogs through the combination of theme and plugins.

### Key Features

- **Plugin-First Architecture** - Designed for extension via specialized plugins
- **Basic SEO Support** - Meta description extraction, title optimization
- **Flexible Layouts** - 1-3 column layouts and full-width support
- **Customizer Integration** - Basic color and design adjustments
- **Media-Optimized** - Tailored for blogs and media sites
- **Lightweight Design** - Minimal footprint with essential features only
- **Responsive** - Mobile-first design approach
- **Developer-Friendly** - Cache busting functionality included

## Quick Start

### Installation

1. Download the theme ZIP file
2. Navigate to WordPress Admin: **Appearance** → **Themes** → **Add New** → **Upload Theme**
3. Select the ZIP file and install
4. Activate the theme

### System Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Modern web browser with HTML5 support

## Theme Structure

### Template Files

- `front-page.php` - Front page template
- `home.php` - Blog home page template
- `single.php` - Single post template
- `page.php` - Page template
- `archive.php` - Archive template
- `search.php` - Search results template
- `404.php` - 404 error page template
- `comments.php` - Comments template
- `sidebar.php` - Sidebar template

### Directory Structure

```
/css/              - Stylesheet files (base, layouts, components, etc.)
/inc/              - PHP include files (feature modules)
/inc/utilities/    - Utility functions
/inc/customizer/   - Customizer settings
/js/               - JavaScript files (customizer related)
```

## Customizer Settings

Access the following settings via WordPress Customizer:

- **Color Settings** - Theme colors, background, text colors
- **Layout Settings** - Column count, sidebar position, container width
- **Typography Settings** - Font family, size, line height
- **Design Patterns** - Button styles, cards, and other design elements
- **Header/Footer Settings** - Logo, menus, widget areas

## Widget Areas

- Sidebar Widget Area
- Footer Widget Areas (multi-column support)

## SEO Features

### Built-in SEO Functionality

- **Meta Description Auto-extraction** - Extracts from excerpt or first 25 words of content
- **Title Tag Optimization** - Customizable separator, automatic title generation
- **Page SEO Extensions** - Excerpt field and tag support for pages
- **SEO-Friendly URLs** - Automatic permalink structure optimization
- **HTML5 Semantic Markup** - Search engine friendly structure
- **Responsive Design** - Mobile-first approach

### Plugin-Based Extensions

Advanced SEO features are intended to be added via specialized plugins such as "SEO・LLMO・GEO・AIO Optimization Plugin":

- Structured Data (JSON-LD, Schema.org)
- Open Graph tags (Facebook and other SNS)
- Twitter Card tags
- Canonical URL tags
- Advanced robots meta tag control

## Developer Features

### Cache Busting

The theme implements timestamp-based versioning for CSS and JavaScript files to resolve cache issues during development. This feature can be toggled on/off via the Customizer.

### Utility Functions

The theme includes the following utility modules:

- `core-utilities.php` - Core functionality helper functions
- `layout-utilities.php` - Layout-related functions
- `typography-utilities.php` - Typography functions
- `color-utilities.php` - Color processing functions
- `design-utilities.php` - Design pattern functions
- `decoration-utilities.php` - Decoration-related functions

## WordPress Theme Support

- Custom Logo
- Custom Menus (Main, Footer)
- Post Thumbnails
- HTML5 Markup
- Automatic Feed Links
- Title Tag Support
- Editor Styles
- Wide Alignment
- Responsive Embeds
- Page Excerpts and Tags

## Important Note

**Plugin Integration**: This theme is designed as a minimal base theme. Advanced features such as SEO enhancements, LLMO (Large Language Model Optimization), and GEO (Geographic Optimization) are intended to be added via specialized plugins like "SEO・LLMO・GEO・AIO Optimization Plugin".

## Child Theme

**Official Child Theme Sample Available**

A sample child theme is available for safe customization:

🔗 **[Backbone SEO LLMO Child Theme](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child)**

### Why Use a Child Theme?

- ✅ **Safe Updates**: Parent theme updates won't overwrite your customizations
- ✅ **Easy Customization**: Only modify the files you need
- ✅ **Reversible**: Deactivate the child theme to return to the original
- ✅ **Learning-Friendly**: Study parent theme code while customizing

### Quick Start

1. Download the child theme from [GitHub](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child)
2. Install both parent and child themes
3. Activate the **child theme** (not the parent)
4. Customize via `style.css`, `functions.php`, or template files

See the [child theme documentation](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo-child) for detailed instructions.

## Changelog

### [1.0.26] - 2025-11-26
- **Added**: Extended individual layout settings to all post types (Posts, Custom Post Types)
- **Changed**: Unified "Full Width" label with Customizer terminology
- **Changed**: Updated meta box definition for broader compatibility

### [1.0.25] - 2025-11-25
- **Fixed**: Vertical submenu (3rd level+) disappearing when hovering on parent items
- **Fixed**: Added hover region extension and JavaScript handling for vertical layout stability

### [1.0.24] - 2025-11-24
- **Added**: SEO meta tags enable/disable settings in customizer (Meta Description / Meta Keywords)
- **Improved**: Meta description automatic generation for all page types (home, archives, search, 404)
- **Improved**: Meta keywords automatic extraction from tags, categories, and content
- **Added**: Helper function for intelligent keyword extraction with stop-word filtering

### [1.0.23] - 2025-11-23
- **Fixed**: Customizer jQuery dependency errors causing preview infinite loop
- **Fixed**: Script duplicate registration in customizer controls
- **Improved**: Inline script dependency management using `wp_add_inline_script`
- **Improved**: Proper script loading order for customizer preview and controls

### [1.0.22] - 2025-11-18
- **Added**: Mobile menu breakpoint customizer setting (Always visible / Mobile only ≤767px / Tablet and below ≤1279px)
- **Added**: Navigation CSS dynamic output module for responsive menu control
- **Added**: `.active` class support for plugin-based hamburger menu functionality
- **Improved**: Plugin-first architecture - theme controls breakpoints, plugins handle hamburger UI
- **Changed**: Menu visibility control delegated to customizer instead of hardcoded CSS
- **Fixed**: File permissions issue with navigation CSS module

### [1.0.21] - 2025-11-17
- **Added**: Navigation menu customizer settings integrated into WordPress native menu panel
- **Added**: Deep hierarchy submenu display direction setting (vertical/horizontal)
- **Changed**: Default submenu direction from horizontal to vertical (stair-step indentation)
- **Improved**: Post meta settings with dynamic visibility control (unified vs individual mode)
- **Improved**: Navigation CSS with better hover effects and panel design
- **Fixed**: Submenu visibility in full-width layouts

### [1.0.20] - 2025-11-14
- **Added**: Custom author URL setting feature

### [1.0.19] - 2025-11-13
- **Changed**: Removed "Content Max Width" from Front Page Settings, added "Single Column Max Width" to Layout Settings
- **Fixed**: Resolved contradiction between Layout Settings and Front Page Settings
- **Improved**: Content max width now only applies to single-column layouts, not affecting 2-column or 3-column layouts

**Current Version:** 1.0.25 (2025-11-25)

For complete version history and detailed changes, see [CHANGELOG.md](CHANGELOG.md).

## License

This theme is licensed under GPL v2 or later.
License: https://www.gnu.org/licenses/gpl-2.0.html

## Support & Development

**Developer**: Tsuyoshi Kashiwazaki
**Website**: https://www.tsuyoshikashiwazaki.jp/profile/
**Support**: For questions or bug reports regarding this theme, please contact via the developer website.

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues on GitHub.

---

<div align="center">

**Keywords**: WordPress theme, base theme, SEO, LLMO, media optimization, plugin-extensible, lightweight, responsive, customizer, blog theme

Made by [Tsuyoshi Kashiwazaki](https://github.com/TsuyoshiKashiwazaki)

</div>
