
<?php
/*
Template Name: Perfil de Usuario Personalizado
*/
get_header();

$current_user = wp_get_current_user();

// Estas variables deben ser pasadas desde el shortcode o controlador que carga esta plantilla.
// Por ejemplo, podrías definirlas antes de incluir este template.
if (!isset($estados_mailchimp)) {
    $estados_mailchimp = []; // Por defecto vacío
}
if (!isset($pais_mailchimp)) {
    $pais_mailchimp = '';
}
if (!isset($empresa_mailchimp)) {
    $empresa_mailchimp = '';
}
if (!isset($puesto_mailchimp)) {
    $puesto_mailchimp = '';
}
if (!isset($telefono_mailchimp)) {
    $telefono_mailchimp = '';
}
?>

<div class="perfil-container">
    <div class="header-profile">
        <div class="section-header-profile">
          <h1 class="title">Perfil</h1>
          <span class="line"></span>
          <p><strong><?php echo esc_html($current_user->user_email); ?></strong></p>
          <p><strong><?php echo esc_html($estado[0]); ?></strong><p>
          <?php
           if (!empty($estados_mailchimp)) {
            echo '<p><strong>' . esc_html($estados_mailchimp[0]['estado']) . '</strong><p>';
            } else {
              echo '<p><strong>No hay datos de suscripción</strong><p>';
            }
          ?>
        </div>
        <button class="button-close" type="button" onclick="window.location.href='<?php echo wp_logout_url(home_url()); ?>';">Cerrar sesión</button>
   </div>

    <div class="tabs">
        <ul class="tab-nav">
            <li class="active" data-tab="perfil">Mis datos</li>
            <li data-tab="newsletter">Newsletters</li>
            <li data-tab="favoritas">Notas Guardadas</li>
        </ul>

        <div class="tab-content active" id="perfil">
            <div class="perfil-usuario-mailchimp">
                <form class="datos-profile" method="post">
                    <?php wp_nonce_field('actualizar_perfil', 'pum_nonce'); ?>
                    <div class="fila-profile">
                        <p><label>Nombre:</label><br>
                        <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" required></p>

                        <p><label>Apellido:</label><br>
                        <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" required></p>

                        <p><label>País:</label><br>
                        <input type="text" name="pas" value="<?php echo esc_attr($pais_mailchimp); ?>"></p>
                    </div>

                    <div class="fila-profile">
                        <p><label>Empresa:</label><br>
                        <input type="text" name="empresa" value="<?php echo esc_attr($empresa_mailchimp); ?>"></p>

                        <p><label>Puesto:</label><br>
                        <input type="text" name="puesto" value="<?php echo esc_attr($puesto_mailchimp); ?>"></p>

                        <p><label>Teléfono:</label><br>
                        <input type="text" name="telefono" value="<?php echo esc_attr($telefono_mailchimp); ?>"></p>
                    </div>

                    <p><input class="button-save" type="submit" value="Guardar cambios"></p>
                </form>
            </div>
        </div>

        <div class="tab-content" id="favoritas">
            <h2>Notas favoritas</h2>
            <p>(Sección en construcción)</p>
        </div>

       <div class="tab-content" id="newsletter">
    <h2>Newsletters</h2>
    <div class="newsletter-list">
   <?php foreach ($estados_mailchimp as $estado): ?>
  <div class="newsletter-card">
    <img src="https://newsletter.edairynews.com/es/banners/header_nuevo_news.jpg" alt="<?php echo esc_attr($estado['nombre']); ?>">
    <p><?php echo esc_html($estado['nombre']); ?></p>

   <?php if ($estado['estado'] == 'Suscripto'): ?>
    <button class="button-suscripto" type="button" disabled>Suscripto</button>

<?php elseif ($estado['estado'] == 'Verificación pendiente' ): ?>
    <button class="button-suscripto pending" type="button" disabled>Pendiente de verificación</button>

<?php else: ?>
    <button 
        type="button" 
        class="btn-suscribirme button-suscripto" 
        data-list-id="<?php echo esc_attr($estado['list_id']); ?>">
        Suscribirme
    </button>
<?php endif; ?>


    <div id="suscripcion-msg-<?php echo esc_attr($estado['list_id']); ?>"></div>
  </div>
<?php endforeach; ?>

    </div>
</div>

    </div>
</div>

<?php get_footer(); ?>

