<?php
if (!defined('ABSPATH')) exit;
define('LH_VER', '1.0.0');

/* ---------- Default isi (bisa diubah di Appearance > Customize > Portofolio Lutfi) ---------- */
function lh_fields() {
  return [
    'lh_name'  => ['Nama', 'Lutfi Hidayatullah'],
    'lh_role'  => ['Peran / profesi', 'Database Administrator'],
    'lh_intro' => ['Intro (hero)', 'A fifth-semester Information Systems student with a fondness for thoughtful interfaces, clean data, and the quiet craft of building things that work beautifully.', 'sanitize_textarea_field', 'textarea'],
    'lh_about1'=> ['Tentang saya - paragraf 1', 'Halo, saya Lutfi Hidayatullah, seorang mahasiswa yang sedang menempuh pendidikan di Universitas Pamulang Serang pada program studi Sistem Informasi. Saya memiliki ketertarikan mendalam pada pengelolaan sistem basis data dan pengembangan web menggunakan teknologi seperti HTML, CSS, JavaScript, PHP, dan Laravel.', 'sanitize_textarea_field', 'textarea'],
    'lh_about2'=> ['Tentang saya - paragraf 2', 'Tujuan karier saya adalah menjadi seorang Database Administrator yang andal dalam merancang, mengamankan, dan mengoptimalkan kinerja basis data untuk mendukung kebutuhan bisnis.', 'sanitize_textarea_field', 'textarea'],
    'lh_school'=> ['Pendidikan - kampus', 'Universitas Pamulang Serang'],
    'lh_major' => ['Pendidikan - jurusan', 'S-1 Sistem Informasi'],
    'lh_email' => ['Email', 'lutfixster.j.r@gmail.com', 'sanitize_email'],
    'lh_wa'    => ['Nomor WhatsApp (format 62...)', '6283895408282'],
    'lh_ig'    => ['URL Instagram', 'https://www.instagram.com/luthfi_hii', 'esc_url_raw'],
    'lh_gh'    => ['URL GitHub', 'https://github.com/lutfixsterjr-ui', 'esc_url_raw'],
  ];
}
function lh($k) { $f = lh_fields(); return get_theme_mod($k, $f[$k][1]); }
function lh_photo() { return get_theme_mod('lh_photo') ?: get_template_directory_uri() . '/assets/img/foto.jpg'; }

add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['script', 'style']);
});

/* ---------- CSS & JS (motion.dev + anime.js) ---------- */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('lh-fonts', 'https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Rajdhani:wght@400;500;600&display=swap', [], null);
  wp_enqueue_style('lh-style', get_stylesheet_uri(), ['lh-fonts'], LH_VER);
  wp_enqueue_script('lh-motion', 'https://cdn.jsdelivr.net/npm/motion@11.18.2/dist/motion.js', [], null, true);
  wp_enqueue_script('lh-anime', 'https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js', [], null, true);
  wp_enqueue_script('lh-script', get_template_directory_uri() . '/assets/js/script.js', ['lh-motion', 'lh-anime'], LH_VER, true);
});

/* ---------- Customizer ---------- */
add_action('customize_register', function ($c) {
  $c->add_section('lh', ['title' => 'Portofolio Lutfi', 'priority' => 30]);
  foreach (lh_fields() as $k => $v) {
    $c->add_setting($k, ['default' => $v[1], 'sanitize_callback' => $v[2] ?? 'sanitize_text_field']);
    $c->add_control($k, ['label' => $v[0], 'section' => 'lh', 'type' => $v[3] ?? 'text']);
  }
  $c->add_setting('lh_photo', ['default' => '', 'sanitize_callback' => 'esc_url_raw']);
  $c->add_control(new WP_Customize_Image_Control($c, 'lh_photo', ['label' => 'Foto name tag (kosongkan = foto bawaan)', 'section' => 'lh']));
});

/* ---------- Custom post type: Proyek ---------- */
add_action('init', function () {
  register_post_type('lh_project', [
    'labels' => ['name' => 'Proyek', 'singular_name' => 'Proyek', 'add_new_item' => 'Tambah Proyek', 'edit_item' => 'Edit Proyek'],
    'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'menu_icon' => 'dashicons-portfolio', 'menu_position' => 5,
    'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
  ]);
});

add_action('add_meta_boxes', function () {
  add_meta_box('lh_meta', 'Detail Proyek', function ($p) {
    wp_nonce_field('lh_meta', 'lh_nonce');
    foreach (['lh_demo' => 'Link Demo', 'lh_github' => 'Link GitHub'] as $k => $l) {
      printf('<p><label>%s<br><input type="url" name="%s" value="%s" style="width:100%%"></label></p>', esc_html($l), esc_attr($k), esc_attr(get_post_meta($p->ID, $k, true)));
    }
    printf('<p><label><input type="checkbox" name="lh_mobile" value="1" %s> Screenshot tampilan mobile (portrait)</label></p>', checked(get_post_meta($p->ID, 'lh_mobile', true), '1', false));
    echo '<p><em>Screenshot: unggah gambar lewat Add Media / blok Gambar di dalam proyek ini. Semua gambar yang diunggah di sini tampil di slider geser. Urutan proyek diatur lewat kolom "Order" (Page Attributes).</em></p>';
  }, 'lh_project');
});

add_action('save_post_lh_project', function ($id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!isset($_POST['lh_nonce']) || !wp_verify_nonce($_POST['lh_nonce'], 'lh_meta') || !current_user_can('edit_post', $id)) return;
  update_post_meta($id, 'lh_demo', esc_url_raw(wp_unslash($_POST['lh_demo'] ?? '')));
  update_post_meta($id, 'lh_github', esc_url_raw(wp_unslash($_POST['lh_github'] ?? '')));
  update_post_meta($id, 'lh_mobile', empty($_POST['lh_mobile']) ? '' : '1');
});

function lh_shots($id) {
  $out = [];
  $imgs = get_children(['post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order ID', 'order' => 'ASC']);
  foreach ($imgs as $a) $out[] = wp_get_attachment_image_url($a->ID, 'large');
  if (!$out && has_post_thumbnail($id)) $out[] = get_the_post_thumbnail_url($id, 'large');
  return $out;
}

/* ---------- Isi awal: 2 proyek + screenshot, dibuat sekali saat tema diaktifkan ---------- */
add_action('after_switch_theme', function () {
  if (get_option('lh_seeded')) return;
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';
  require_once ABSPATH . 'wp-admin/includes/image.php';
  $d = get_template_directory() . '/assets/img/';
  $seed = [
    ['Web Showroom Turismo', 'Website showroom mobil dengan katalog produk dan tampilan modern.', ['turismo-1', 'turismo-2', 'turismo-3'], 'https://lutfixsterjr-ui.github.io/web-sorum-mobil/', 0, 1],
    ['Mini Soccer Booking', 'Aplikasi mobile untuk pemesanan lapangan mini soccer.', ['soccer-1', 'soccer-2'], '', 1, 2],
  ];
  foreach ($seed as [$t, $x, $imgs, $demo, $mob, $order]) {
    $id = wp_insert_post(['post_type' => 'lh_project', 'post_title' => $t, 'post_excerpt' => $x, 'post_content' => $x, 'post_status' => 'publish', 'menu_order' => $order]);
    if (!$id || is_wp_error($id)) continue;
    update_post_meta($id, 'lh_demo', $demo);
    update_post_meta($id, 'lh_github', lh('lh_gh'));
    update_post_meta($id, 'lh_mobile', $mob ? '1' : '');
    foreach ($imgs as $n) {
      $tmp = wp_tempnam($n);
      if (!copy($d . $n . '.jpg', $tmp)) continue;
      $aid = media_handle_sideload(['name' => $n . '.jpg', 'tmp_name' => $tmp], $id);
      if (is_wp_error($aid)) @unlink($tmp);
    }
  }
  update_option('lh_seeded', 1);
});
