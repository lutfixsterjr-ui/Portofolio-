<?php get_header(); ?>
<section class="wp-page">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <h1 class="title"><?php the_title(); ?></h1>
    <div class="panel"><?php the_content(); ?></div>
  <?php endwhile; else : ?>
    <h1 class="title">404 // Tidak ditemukan</h1>
    <div class="panel"><a href="<?php echo esc_url(home_url('/')); ?>">Kembali ke beranda</a></div>
  <?php endif; ?>
</section>
<?php get_footer();
