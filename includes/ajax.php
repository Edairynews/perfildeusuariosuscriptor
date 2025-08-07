<?php
add_action('wp_ajax_pum_suscribirme', 'pum_ajax_suscribirme');
function pum_ajax_suscribirme() {
    if (!is_user_logged_in()) wp_send_json_error(['message' => 'Debés iniciar sesión']);

    $user = wp_get_current_user();
    $list_id = sanitize_text_field($_POST['list_id']);

    // Llamá acá a una función que mande el opt-in a Mailchimp
    $ok = pum_suscribir_usuario_a_lista($user->user_email, $list_id);
    if ($ok) {
        wp_send_json_success(['message' => 'Te suscribiste correctamente, revisá tu mail para confirmar.']);
    } else {
        wp_send_json_error(['message' => 'Error al suscribirte']);
    }
}
