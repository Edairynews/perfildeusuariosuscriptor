<?php
// add_action('admin_init', function() {
//     register_setting('pum_settings', 'pum_mailchimp_api_key');
//     register_setting('pum_settings', 'pum_mailchimp_list_id');
// });


add_action('admin_init', function() {
    register_setting('pum_settings', 'pum_mailchimp_api_key');
    register_setting('pum_settings', 'pum_mailchimp_lists'); // NUEVA opción para todas las listas
});
