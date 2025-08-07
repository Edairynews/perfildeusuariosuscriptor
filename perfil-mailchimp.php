<?php
/*
Plugin Name: Perfil de Usuario con Mailchimp
Description: Muestra un perfil personalizado para suscriptores, permite editar datos personales y sincroniza con Mailchimp.
Version: 1.2
Author: Ivana Kowalczuk
*/

defined('ABSPATH') || exit;

// Cargar archivos del plugin
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/mailchimp-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/enqueue.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';
require_once plugin_dir_path(__FILE__) . 'admin/menu.php';
require_once plugin_dir_path(__FILE__) . 'admin/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';

