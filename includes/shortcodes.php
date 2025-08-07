<?php

function mostrar_perfil_usuario_mailchimp() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver tu perfil.</p>';
    }

    $user = wp_get_current_user();
    if (!in_array('subscriber', $user->roles)) {
        return '<p>Solo los suscriptores pueden acceder a esta página.</p>';
    }

    // Procesar formulario si se envió
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pum_nonce']) && wp_verify_nonce($_POST['pum_nonce'], 'actualizar_perfil')) {
        pum_procesar_formulario_perfil($user);
    }

    // Obtener datos actuales de Mailchimp (todas las listas)
    $datos_mailchimp = pum_obtener_datos_mailchimp($user->user_email);
    // Obtener estado de suscripción en cada lista
    $estados_mailchimp = pum_obtener_estado_mailchimp($user->user_email);

    // Tomamos los merge_fields de la primera lista (podés ajustar si querés múltiples)
    $primer_lista_datos = $datos_mailchimp[0]['datos'] ?? [];
    $merge_fields = $primer_lista_datos['merge_fields'] ?? [];


    // Variables para la plantilla
    $pais_mailchimp      = $merge_fields['PAS'] ?? '';
    $empresa_mailchimp   = $merge_fields['EMPRESA'] ?? '';
    $puesto_mailchimp    = $merge_fields['PUESTO'] ?? '';
    $telefono_mailchimp  = $merge_fields['TELFONO'] ?? '';
    // Variables que el template usará:
    // $user, $estados_mailchimp, $puesto_mailchimp, $pais_mailchimp, $telefono_mailchimp, $empresa_mailchimp

    ob_start();

    // IMPORTANTE: Pasamos las variables al scope del template incluyendo $user y $estados_mailchimp
    include plugin_dir_path(__FILE__) . '../templates/user-profile-form.php';

    return ob_get_clean();
}
add_shortcode('user_profile', 'mostrar_perfil_usuario_mailchimp');



