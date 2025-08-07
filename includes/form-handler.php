<?php
function pum_procesar_formulario_perfil($user) {
    // Sanitizar campos que vienen del formulario
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name  = sanitize_text_field($_POST['last_name']);
    $puesto     = sanitize_text_field($_POST['puesto']);
    $pais       = sanitize_text_field($_POST['pas']);
    $telefono   = sanitize_text_field($_POST['telefono']); // corregido el typo
    $empresa    = sanitize_text_field($_POST['empresa']);

    // Actualizar los datos básicos de WordPress
    wp_update_user([
        'ID'         => $user->ID,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        // 'display_name' => $display_name, // lo sacamos porque no viene del form
    ]);

    // Actualizar los merge fields en Mailchimp
    pum_actualizar_mailchimp_merge_fields(
        $user->user_email,
        $first_name,
        $last_name,
        $puesto,
        $pais,
        $telefono,
        $empresa
    );

    echo '<p style="color: green;">Perfil actualizado correctamente.</p>';
}

add_action('wp_ajax_pum_suscribirme_ajax', 'pum_suscribirme_ajax_handler');
add_action('wp_ajax_nopriv_pum_suscribirme_ajax', 'pum_suscribirme_ajax_handler');

function pum_suscribirme_ajax_handler() {
    if (!is_user_logged_in()) {
        wp_send_json(['success' => false, 'message' => 'Debes iniciar sesión para suscribirte.']);
    }

    $list_id = sanitize_text_field($_POST['list_id']);
    if (!$list_id) {
        wp_send_json(['success' => false, 'message' => 'Falta el ID de la lista.']);
    }

    $ok = pum_suscribir_usuario_a_lista($list_id);
    if ($ok) {
        wp_send_json(['success' => true, 'message' => 'Te suscribiste correctamente, revisá tu email.']);
    } else {
        wp_send_json(['success' => false, 'message' => 'Error al suscribirte.']);
    }
}
