<?php
function pum_obtener_estado_mailchimp($email) {
    $api_key = get_option('pum_mailchimp_api_key');
    $listas = get_option('pum_mailchimp_lists', []);

    if (!$api_key || empty($listas)) return 'API Key o listas no configuradas';

    $data_center = substr($api_key, strpos($api_key, '-') + 1);
    $subscriber_hash = md5(strtolower($email));

    $resultados = [];

    foreach ($listas as $lista) {
        $list_id = $lista['id'];
        $nombre_lista = $lista['name'];

        $url = "https://$data_center.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

        $response = wp_remote_get($url, [
            'headers' => ['Authorization' => 'apikey ' . $api_key]
        ]);

        if (is_wp_error($response)) {
            $estado = 'Error al conectar';
        } else {
            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (!isset($body['status'])) {
                $estado = 'No suscripto';
            } else {
                switch ($body['status']) {
                    case 'subscribed':
                        $estado = 'Suscripto';
                        break;
                    case 'unsubscribed':
                        $estado = 'No suscripto';
                        break;
                    case 'pending':
                        $estado = 'Verificación pendiente';
                        break;
                    case 'pendiente':
                        $estado = 'Verificación pendiente';
                        break;
                    default:
                        $estado = 'No suscripto';
                }
            }
        }

        $resultados[] = [
            'list_id' => $list_id,
            'nombre'  => $nombre_lista,
            'estado'  => $estado
        ];
    }

    return $resultados; // Devolvemos un array con el estado en cada lista
}


function pum_obtener_datos_mailchimp($email) {
    $api_key = get_option('pum_mailchimp_api_key');
    $listas = get_option('pum_mailchimp_lists', []);

    if (!$api_key || empty($listas)) return null;

    $data_center = substr($api_key, strpos($api_key, '-') + 1);
    $subscriber_hash = md5(strtolower($email));

    $datos = [];

    foreach ($listas as $lista) {
        $list_id = $lista['id'];
        $nombre_lista = $lista['name'];

        $url = "https://$data_center.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

        $response = wp_remote_get($url, [
            'headers' => ['Authorization' => 'apikey ' . $api_key]
        ]);

        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $datos[] = [
                'list_id' => $list_id,
                'nombre'  => $nombre_lista,
                'datos'   => $body
            ];
             
        }
      
    }
  
    return $datos; // Devuelve array con datos por cada lista
}


function pum_actualizar_mailchimp_merge_fields($email, $fname, $lname, $puesto = '', $pais = '', $telefono = '', $empresa = '', ) {
    $api_key = get_option('pum_mailchimp_api_key');
    $listas = get_option('pum_mailchimp_lists', []);

    if (!$api_key || empty($listas)) return false;

    $data_center = substr($api_key, strpos($api_key, '-') + 1);
    $subscriber_hash = md5(strtolower($email));

    $body = json_encode([
        'merge_fields' => [
            'FNAME'   => $fname,
            'LNAME' => $lname,
            'COUNTRY'   => $pais,
            'COMPANY'  => $empresa,
            'JOB'   => $puesto,
            'PHONE'  => $telefono,
            'TELEPHONE'  => $telefono,
        ]
    ]);

    $resultado = true;

    foreach ($listas as $lista) {
        $list_id = $lista['id'];
        $url = "https://$data_center.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

        $response = wp_remote_request($url, [
            'method'  => 'PATCH',
            'headers' => [
                'Authorization' => 'apikey ' . $api_key,
                'Content-Type'  => 'application/json',
            ],
            'body' => $body
        ]);

        if (is_wp_error($response)) {
            $resultado = false; // si falla en una lista, marcamos como error
        }
    }

    return $resultado;
}

// function pum_suscribir_usuario_a_lista($list_id) {
//     if (!is_user_logged_in()) {
//         return false;
//     }

//     $user = wp_get_current_user();
//     $api_key = get_option('pum_mailchimp_api_key');

//     if (!$api_key || !$list_id) {
//         return false;
//     }

//     $data_center = substr($api_key, strpos($api_key, '-') + 1);
//     $email = $user->user_email;
//     $subscriber_hash = md5(strtolower($email));

//     $country    = get_user_meta($user->ID, 'country', true);
//     $company    = get_user_meta($user->ID, 'company', true);
//     $job_title  = get_user_meta($user->ID, 'job_title', true);
//     $source     = get_user_meta($user->ID, 'source', true);
//     $phone      = get_user_meta($user->ID, 'phone', true);



//      $merge_fields = [
//     //     'FIRST_NAME' => $user->first_name,
//     //     'LAST_NAME'  => $user->last_name,
//     //     // agregá más si querés
//        'FNAME'    => $user->first_name,
//         'LNAME'    => $user->last_name,
//         'COUNTRY'  => $country,
//         'COMPANY'  => $company,
//         'JOBTITLE' => $job_title,
//         'SOURCE'   => $source,
//         'PHONE'    => $phone,

//      ];

//     $body = json_encode([
//         'email_address' => $email,
//         'status_if_new' => 'pending', // doble opt-in
//         'status'        => 'pending',
//         'merge_fields'  => $merge_fields,
//     ]);

//     $url = "https://$data_center.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

//     $response = wp_remote_request($url, [
//         'method'  => 'PUT',
//         'headers' => [
//             'Authorization' => 'apikey ' . $api_key,
//             'Content-Type'  => 'application/json',
//         ],
//         'body' => $body,
//     ]);

//     if (is_wp_error($response)) {
//         error_log('Error al suscribirse a Mailchimp: ' . $response->get_error_message());
//         return false;
//     }

//     $code = wp_remote_retrieve_response_code($response);
//     return ($code == 200 || $code == 201);
// }


function pum_suscribir_usuario_a_lista($list_id) {
    if (!is_user_logged_in()) {
        return false;
    }

    $user = wp_get_current_user();
    $api_key = get_option('pum_mailchimp_api_key');
    if (!$api_key || !$list_id) return false;

    $data_center = substr($api_key, strpos($api_key, '-') + 1);
    $email = $user->user_email;
    $subscriber_hash = md5(strtolower($email));

    // Obtenemos datos existentes de Mailchimp para no perder info
    $datos = pum_obtener_datos_mailchimp($email);
    $merge_fields_existentes = [];
    if (!empty($datos) && isset($datos[0]['datos']['merge_fields'])) {
        $merge_fields_existentes = $datos[0]['datos']['merge_fields'];
    }

    // Función para obtener valor del campo, chequeando primero Mailchimp, luego WP
    function campo_valor_simple($campo, $merge_fields_existentes, $user) {
        if (!empty($merge_fields_existentes[$campo])) {
            return $merge_fields_existentes[$campo];
        }
        switch ($campo) {
            case 'FNAME':
                return $user->first_name;
            case 'LNAME':
                return $user->last_name;
            case 'COUNTRY':
                return get_user_meta($user->ID, 'country', true);
            case 'COMPANY':
                return get_user_meta($user->ID, 'company', true);
            case 'JOB':
                return get_user_meta($user->ID, 'job_title', true);
            case 'PHONE':
            case 'TELEPHONE':
                // En  WP se usa'phone' para ambos
                return get_user_meta($user->ID, 'phone', true);
            default:
                return '';
        }
    }

    $campos_mailchimp = ['FNAME', 'LNAME', 'COUNTRY', 'COMPANY', 'JOB', 'PHONE', 'TELEPHONE'];

    $merge_fields = [];
    foreach ($campos_mailchimp as $campo) {
        $merge_fields[$campo] = campo_valor_simple($campo, $merge_fields_existentes, $user);
    }

    $body = json_encode([
        'email_address' => $email,
        'status_if_new' => 'pending', // doble opt-in
        'status'        => 'pending',
        'merge_fields'  => $merge_fields,
    ]);

    $url = "https://$data_center.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

    $response = wp_remote_request($url, [
        'method'  => 'PUT',
        'headers' => [
            'Authorization' => 'apikey ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => $body,
    ]);

    if (is_wp_error($response)) {
        error_log('Error al suscribirse a Mailchimp: ' . $response->get_error_message());
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    return ($code == 200 || $code == 201);
}
