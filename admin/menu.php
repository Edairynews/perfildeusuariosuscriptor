<?php

add_action('admin_menu', function() {
    add_options_page('Perfil de Usuario Mailchimp', 'Perfil Mailchimp', 'manage_options', 'perfil-mailchimp', 'pum_config_page');
});

function pum_config_page() {
    $lists = get_option('pum_mailchimp_lists', []);
    ?>
    <div class="wrap">
        <h1>Configuración del Plugin Perfil Mailchimp</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('pum_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th>API Key de Mailchimp</th>
                    <td>
                        <input type="text" name="pum_mailchimp_api_key" value="<?php echo esc_attr(get_option('pum_mailchimp_api_key')); ?>" size="50">
                    </td>
                </tr>
            </table>

            <h2>Listas de Mailchimp</h2>
            <table id="mailchimp-lists" class="form-table">
                <?php foreach ($lists as $index => $list): ?>
                <tr>
                    <td>
                        <input type="text" name="pum_mailchimp_lists[<?php echo $index; ?>][id]" placeholder="List ID" value="<?php echo esc_attr($list['id']); ?>" size="30">
                        <input type="text" name="pum_mailchimp_lists[<?php echo $index; ?>][name]" placeholder="Nombre de la lista" value="<?php echo esc_attr($list['name']); ?>" size="30">
                        <button type="button" class="remove-list button">Quitar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p>
                <button type="button" id="add-list" class="button">Agregar lista</button>
            </p>

            <?php submit_button(); ?>
        </form>
    </div>

    
    <?php
}







/*add_action('admin_menu', function() {
    add_options_page('Perfil de Usuario Mailchimp', 'Perfil Mailchimp', 'manage_options', 'perfil-mailchimp', 'pum_config_page');
});



function pum_config_page() {
    ?>
    <div class="wrap">
        <h1>Configuración del Plugin Perfil Mailchimp</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('pum_settings');
            do_settings_sections('pum_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th>API Key de Mailchimp</th>
                    <td><input type="text" name="pum_mailchimp_api_key" value="<?php echo esc_attr(get_option('pum_mailchimp_api_key')); ?>" size="50"></td>
                </tr>
                <tr>
                    <th>List ID</th>
                    <td><input type="text" name="pum_mailchimp_list_id" value="<?php echo esc_attr(get_option('pum_mailchimp_list_id')); ?>" size="50"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}*/
