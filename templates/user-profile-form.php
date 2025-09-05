<?php
/*
Template Name: Perfil de Usuario Personalizado
*/
get_header();

$current_user = wp_get_current_user();

$imagenes_newsletters = [
    '9ddedfe3ae' => 'https://newsletter.edairynews.com/es/banners/Español.png',
    '3466066aae' => 'https://newsletter.edairynews.com/en/banners/ingles.webp',
    '401686583d' => 'https://newsletter.edairynews.com/en/banners/Mexico.webp',
    'd67ce261a5' => 'https://newsletter.edairynews.com/en/banners/India.jpeg',
    'b2c8022dbd' => 'https://newsletter.edairynews.com/en/banners/Portugues.jpeg',
];
$descripcion = [
    '9ddedfe3ae' => 'Infórmate   más y mejor con el resumen de noticias lácteas más importantes del mercado lácteo en español',
    '3466066aae' => 'Stay more and better informed with the top dairy news summary from the global dairy market in English.',
    '401686583d' => 'Infórmate más y mejor con el resumen de noticias lácteas más importantes del mercado lácteo mexicano   ',
    'd67ce261a5' => 'Stay more and better informed with the top dairy news summary from the Indian dairy market.',
    'b2c8022dbd' => 'Fique mais e melhor informado com o resumo das notícias lácteas mais importantes do mercado lácteo brasileiro..',
];
$subtitulo = [
    '9ddedfe3ae' => '5 Minutos de Noticias Lácteas en Español',
    '3466066aae' => '5 Minutes of Dairy News in English',
    '401686583d' => '5 Minutos de Noticias Lácteas en México',
    'd67ce261a5' => '5 Minutes of Dairy News in India',
    'b2c8022dbd' => '5 Minutos de Noticias Lácteas em Português',
];


// Variables de fallback
if (!isset($estados_mailchimp)) $estados_mailchimp = [];
if (!isset($pais_mailchimp)) $pais_mailchimp = '';
if (!isset($empresa_mailchimp)) $empresa_mailchimp = '';
if (!isset($puesto_mailchimp)) $puesto_mailchimp = '';
if (!isset($telefono_mailchimp)) $telefono_mailchimp = '';
?>

<div class="perfil-container">
  <div class="header-profile">
    <div class="section-header-profile">
      <h1 class="title">Perfil</h1>
      <span class="line"></span>
      <p><strong><?php echo esc_html($current_user->user_email); ?></strong></p>
      <?php
      if (!empty($estados_mailchimp)) {
        echo '<p><strong>' . esc_html($estados_mailchimp[0]['estado']) . '</strong></p>';
      } else {
        echo '<p><strong>No hay datos de suscripción</strong></p>';
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
            <input type="text" name="pais" value="<?php echo esc_attr($pais_mailchimp); ?>"></p>
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
 
      <div class="newsletter-list">
        <?php foreach ($estados_mailchimp as $estado): 
          $list_id = $estado['list_id'];
          $img = $imagenes_newsletters[$list_id] ?? 'https://via.placeholder.com/400x200';
        ?>
          <div class="newsletter-card">
            <img class="newsletter-img" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($estado['nombre']); ?>">

            <div class="newsletter-body">
                <p class="newsletter-subtitle" style="font-weight:bold; font-size:16px; color:#263D8C; text-align:left">
                <?php echo esc_html($subtitulo[$list_id] ?? ''); ?>
            </p>
            <p class="newsletter-desc"><?php echo esc_html($descripcion[$list_id] ?? 'Descripción no disponible'); ?></p>


              <hr class="newsletter-divider">

              <?php if ($estado['estado'] === 'Suscripto'): ?>
                <p class="estado suscripto">✔ Suscripto</p>
              <?php elseif ($estado['estado'] === 'Verificación pendiente'): ?>
                <p class="estado pendiente">🕛 Pendiente de verificación</p>
              <?php else: ?>
                 <button 
                  type="button" 
                  class="btn-suscribirme"
                  data-list-id="<?php echo esc_attr($list_id); ?>">
                  📧 Suscribirme
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
