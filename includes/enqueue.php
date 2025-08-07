<?php
defined('ABSPATH') || exit;

function pum_enqueue_assets() {
    global $post;
    if (isset($post) && has_shortcode($post->post_content, 'user_profile')) {
        wp_enqueue_style(
            'pum-style',
            plugin_dir_url(__DIR__) . 'assets/css/style.css',
            [],
            '1.0'
        );

        wp_enqueue_script(
            'pum-tabs',
            plugin_dir_url(__DIR__) . 'assets/js/tabs.js',
            [],
            '1.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'pum_enqueue_assets');

function pum_enqueue_admin_assets($hook) {
    // Ajusta el slug según el que usaste en add_options_page()
    if ($hook === 'settings_page_perfil-mailchimp') {
        wp_enqueue_script(
            'pum-admin-tabs',
            plugin_dir_url(__DIR__) . 'assets/js/tabs.js',
            [],
            '1.0',
            true
        );
        // Si querés, también podés agregar un CSS para el admin:
        // wp_enqueue_style(
        //     'pum-admin-style',
        //     plugin_dir_url(__DIR__) . 'assets/css/admin-style.css',
        //     [],
        //     '1.0'
        // );
    }
}
add_action('admin_enqueue_scripts', 'pum_enqueue_admin_assets');