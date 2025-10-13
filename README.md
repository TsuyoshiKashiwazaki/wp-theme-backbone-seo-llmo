# 🚀 Backbone Theme for SEO + LLMO

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.2--dev-orange.svg)](https://github.com/TsuyoshiKashiwazaki/wp-theme-backbone-seo-llmo/releases)

![Backbone Theme Screenshot](screenshot.png)

A minimal WordPress base theme optimized for media sites with built-in SEO and LLMO support, designed for plugin-based extensibility.

> 🎯 **A lightweight foundation theme for efficient media operations, designed to work seamlessly with specialized plugins**

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

## 🚀 Quick Start

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

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

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

**🔍 Keywords**: WordPress theme, base theme, SEO, LLMO, media optimization, plugin-extensible, lightweight, responsive, customizer, blog theme

Made with ❤️ by [Tsuyoshi Kashiwazaki](https://github.com/TsuyoshiKashiwazaki)

</div>
