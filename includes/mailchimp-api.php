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

function get_mailchimp_list_name_by_id($list_id) {
    $lists = get_option('pum_mailchimp_lists');

    if (!is_array($lists)) {
        return null;
    }

    foreach ($lists as $list) {
        if (isset($list['id']) && $list['id'] === $list_id) {
            return $list['name'];
        }
    }

    return null;
}

function registrar_usuario_remoto2($email, $nombre_completo, $pais, $wp_remoto) {
    
    $user_app = 'Dev eDairy News';
	$pass_app = $wp_remoto['app_pass'];

	$url= $wp_remoto['url'];

	$nombre_completo= $userdata['user_login'];
	$email = $userdata['user_email'];
	$wp_generate_password  = wp_generate_password(12, true, true);
	$nombre_completo = $userdata['first_name'];
    $nombre_completo = $userdata['last_name'];

	$response = wp_remote_post( $url, array(
		'body'    => $data,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( $user_app . ':' . $pass_app ),
		),
	) );

	if (is_wp_error($response)) {
		error_log(print_r($response->get_error_message(),true));
		return;
	}

	$response = json_decode($response['body']??'{}',true);

	if ( isset($response['code']) ) {
		error_log(print_r($response,true));
		return;
	}

	if ( isset($response['id']) ) {
		update_user_meta( $user_id, 'id_user_remote_site', $response['id'] );
	}
}

function registrar_usuario_remoto($email, $nombre_completo, $pais, $wp_remoto) {

    $username = sanitize_user(explode('@', $email)[0] . rand(100, 999), true);
    $password = wp_generate_password(12, true, true);

    $body = [
        'username' => $username,
        'email'    => $email,
        'name'     => $nombre_completo,
        'password' => $password
        // 'meta'     => [
        //     'country' => $pais
        // ]
    ];

    $auth = base64_encode($wp_remoto['usuario'] . ':' . $wp_remoto['app_pass']);

    $response = wp_remote_post($wp_remoto['url'], [
        'headers' => [
            'Authorization' => 'Basic ' . $auth,
            'Content-Type'  => 'application/json',
        ],
        'body'    => json_encode($body),
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        error_log('❌ Error de conexión al registrar usuario remoto: ' . $response->get_error_message());
        return false;
    }

    $status_code   = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    // 📌 DEBUG COMPLETO
    error_log("📡 Registro remoto en {$wp_remoto['url']} → HTTP $status_code");
    error_log("📩 Respuesta: " . $response_body);

    if ($status_code === 201 || $status_code === 200) {
        return true;
    } else {
        return false;
    }
}

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
     if ($code == 200 || $code == 201) {

        // Buscamos el nombre de la lista según el id
        $list_name = get_mailchimp_list_name_by_id($list_id);

        global $mapa_wp_remotos_por_nombre;

        if ($list_name && isset($mapa_wp_remotos_por_nombre[$list_name])) {
            $wp_remoto = $mapa_wp_remotos_por_nombre[$list_name];

            $nombre_completo = trim($merge_fields['FNAME'] . ' ' . $merge_fields['LNAME']);
            $pais = $merge_fields['COUNTRY'];

            registrar_usuario_remoto($email, $nombre_completo, $pais, $wp_remoto);
        }
        return true;
    }

}


