<?php
/**
 * Form CSS Output
 *
 * @package Backbone_SEO_LLMO
 */

if (!defined('ABSPATH')) {
    exit;
}

function backbone_output_form_css() {
    $padding_vertical = get_theme_mod('form_input_padding_vertical', 12);
    $padding_horizontal = get_theme_mod('form_input_padding_horizontal', 15);
    $font_size = get_theme_mod('form_input_font_size', 16);
    $border_width = get_theme_mod('form_input_border_width', 2);
    $margin_bottom = get_theme_mod('form_input_margin_bottom', 15);
    $textarea_min_height = get_theme_mod('form_textarea_min_height', 150);
    $line_height = get_theme_mod('form_input_line_height', 1.5);
    $max_width = get_theme_mod('form_input_max_width', 600);
    ?>
    <style id="backbone-form-custom-css">
        input[type="text"],
        input[type="email"],
        input[type="url"],
        input[type="tel"],
        input[type="number"],
        input[type="date"],
        input[type="password"],
        textarea,
        select {
            padding: <?php echo esc_attr($padding_vertical); ?>px <?php echo esc_attr($padding_horizontal); ?>px;
            font-size: <?php echo esc_attr($font_size); ?>px;
            line-height: <?php echo esc_attr($line_height); ?>;
            background-color: var(--form-background-color, #ffffff);
            border: <?php echo esc_attr($border_width); ?>px solid var(--border-color, #ddd);
            color: var(--text-primary, #333);
            transition: border-color 0.3s ease;
            margin-bottom: <?php echo esc_attr($margin_bottom); ?>px;
            width: 100%;
            <?php if ($max_width > 0) : ?>
            max-width: <?php echo esc_attr($max_width); ?>px;
            <?php endif; ?>
            box-sizing: border-box;
        }

        textarea {
            min-height: <?php echo esc_attr($textarea_min_height); ?>px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--form-focus-color, var(--accent-color, #0073aa));
        }

        input[type="submit"],
        input[type="button"],
        button[type="submit"] {
            width: auto;
            margin-bottom: 0;
        }
    </style>
    <?php
}

add_action('wp_head', 'backbone_output_form_css', 25);
