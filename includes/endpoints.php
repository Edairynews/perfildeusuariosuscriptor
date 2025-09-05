<?php
// Seguridad: bloqueo acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Verificación de permisos (solo admins con Application Password válido)
function perfil_verificar_permiso( $request ) {
    $user = wp_get_current_user();

    // Validar que haya un usuario logueado por auth (incluye Basic Auth / App Passwords)
    if ( ! $user || ! $user->exists() ) {
        return new WP_Error('rest_forbidden', 'No autorizado', ['status' => 401]);
    }

    // Solo admins pueden crear usuarios
    if ( ! $user->has_cap('create_users') ) {
        return new WP_Error('rest_forbidden', 'No tenés permisos para crear usuarios', ['status' => 403]);
    }

    return true;
}

// Función para crear usuario
function perfil_crear_usuario( $request ) {
    $params = $request->get_json_params();

    if ( empty($params['username']) || empty($params['email']) || empty($params['password']) ) {
        return new WP_Error('missing_data', 'Faltan datos obligatorios', ['status' => 400]);
    }

    // Crear usuario
    $user_id = wp_create_user(
        sanitize_user($params['username']),
        $params['password'],
        sanitize_email($params['email'])
    );

    if ( is_wp_error($user_id) ) {
        return $user_id;
    }

    // Asignar rol por defecto (ej: suscriptor)
    $user = new WP_User($user_id);
    $user->set_role('subscriber');

    return [
        'success' => true,
        'user_id' => $user_id,
    ];
}

// Registro del endpoint
add_action('rest_api_init', function () {
    register_rest_route('perfil/v1', '/crearusuario', [
        'methods'             => 'POST',
        'callback'            => 'perfil_crear_usuario',
        'permission_callback' => 'perfil_verificar_permiso',
    ]);
});

// Evitar que plugins de seguridad bloqueen el endpoint
add_filter('rest_authentication_errors', function( $result ) {
    if ( true === $result || is_wp_error($result) ) {
        return $result;
    }

    $route = $_SERVER['REQUEST_URI'] ?? '';
    if ( strpos($route, '/wp-json/perfil/v1/crearusuario') !== false ) {
        return true;
    }

    return $result;
});
