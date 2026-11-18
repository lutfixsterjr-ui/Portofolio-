<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); $h = esc_url(home_url('/')); ?>
<div class="floor"></div>
<div id="particles"></div>
<div class="cur-dot"></div><div class="cur-ring"></div>

<header>
  <a href="<?php echo $h; ?>#home" class="logo">LH<span>//</span>DBA</a>
  <button class="burger" id="burger" aria-label="Menu"><i></i><i></i></button>
  <nav id="nav">
    <a href="<?php echo $h; ?>#about">Tentang</a><a href="<?php echo $h; ?>#skills">Skill</a><a href="<?php echo $h; ?>#projects">Proyek</a><a href="<?php echo $h; ?>#education">Pendidikan</a><a href="<?php echo $h; ?>#contact">Kontak</a>
  </nav>
</header>
