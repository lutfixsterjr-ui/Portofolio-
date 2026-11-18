<?php
get_header();
$icon = 'https://cdn.jsdelivr.net/npm/devicon@2.16.0/icons/%1$s/%1$s-original.svg';
$skills = [
  ['HTML · CSS · JS', 'Front-end', ['html5', 'css3', 'javascript']],
  ['PHP · Laravel', 'Back-end', ['php', 'laravel']],
  ['Flutter · Dart', 'Mobile', ['flutter', 'dart']],
  ['MySQL', 'Database', ['mysql']],
  ['Git · GitHub', 'Version control', ['git', 'github']],
];
$wa = preg_replace('/\D/', '', lh('lh_wa'));
$social = [
  ['gmail', 'Email', 'mailto:' . lh('lh_email')],
  ['whatsapp', 'WhatsApp', 'https://wa.me/' . $wa],
  ['instagram', 'Instagram', lh('lh_ig')],
  ['github', 'GitHub', lh('lh_gh')],
];
?>
<section id="home" class="hero">
  <div class="hang-wrap" id="wrap">
    <div id="hang" class="hang">
      <div class="strap"></div>
      <div class="clip"></div>
      <div class="badge" id="badge">
        <div class="slot"></div>
        <div class="photo"><img src="<?php echo esc_url(lh_photo()); ?>" alt="<?php echo esc_attr(lh('lh_name')); ?>" draggable="false"></div>
        <h3><?php echo esc_html(strtoupper(lh('lh_name'))); ?></h3>
        <p><?php echo esc_html(strtoupper(lh('lh_role'))); ?></p>
        <div class="barcode"></div>
        <small>ID // SI-SEM5 // UNPAM</small>
        <i class="scan"></i>
      </div>
    </div>
    <span class="hint">tarik &amp; lepas name tag-nya</span>
  </div>
  <div class="hero-text">
    <p class="tag rv">&gt; HELLO, WORLD_</p>
    <h1 id="name"><?php echo esc_html(lh('lh_name')); ?></h1>
    <h2 class="rv"><?php echo esc_html(lh('lh_role')); ?></h2>
    <p class="rv lead"><?php echo esc_html(lh('lh_intro')); ?></p>
    <div class="btns rv">
      <a href="#projects" class="btn">Lihat Portofolio</a>
      <a href="#contact" class="btn ghost">Hubungi Saya</a>
    </div>
  </div>
</section>

<section id="about">
  <h2 class="title rv">01 // Tentang Saya</h2>
  <div class="panel tilt rv">
    <p><?php echo esc_html(lh('lh_about1')); ?></p>
    <p><?php echo esc_html(lh('lh_about2')); ?></p>
  </div>
</section>

<section id="skills">
  <h2 class="title rv">02 // Keahlian</h2>
  <div class="grid skills">
    <?php foreach ($skills as [$t, $s, $ics]) : ?>
      <div class="card tilt rv">
        <div class="icons">
          <?php foreach ($ics as $n) : ?><img src="<?php echo esc_url(sprintf($icon, $n)); ?>" alt="<?php echo esc_attr($n); ?>" loading="lazy"<?php echo $n === 'github' ? ' class="inv"' : ''; ?>><?php endforeach; ?>
        </div>
        <b><?php echo esc_html($t); ?></b><span><?php echo esc_html($s); ?></span>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section id="projects">
  <h2 class="title rv">03 // Proyek</h2>
  <div class="grid projects">
    <?php
    $q = new WP_Query(['post_type' => 'lh_project', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC']);
    while ($q->have_posts()) : $q->the_post();
      $id = get_the_ID(); $shots = lh_shots($id);
      $demo = get_post_meta($id, 'lh_demo', true); $gh = get_post_meta($id, 'lh_github', true);
      $mob = get_post_meta($id, 'lh_mobile', true) ? ' mobile' : '';
    ?>
      <article class="proj tilt rv<?php echo $mob; ?>">
        <?php if ($shots) : ?>
          <div class="slider" data-slider>
            <?php foreach ($shots as $u) : ?><img src="<?php echo esc_url($u); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" draggable="false"><?php endforeach; ?>
          </div>
          <div class="dots"></div>
          <?php if (count($shots) > 1) : ?><p class="swipe">&larr; geser screenshot &rarr;</p><?php endif; ?>
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
        <p><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php if ($demo) : ?><a class="btn small" href="<?php echo esc_url($demo); ?>" target="_blank" rel="noopener">Lihat Demo</a><?php endif; ?>
        <?php if ($gh) : ?><a class="btn small ghost" href="<?php echo esc_url($gh); ?>" target="_blank" rel="noopener">GitHub</a><?php endif; ?>
      </article>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</section>

<section id="education">
  <h2 class="title rv">04 // Pendidikan</h2>
  <div class="panel tilt rv edu">
    <b><?php echo esc_html(lh('lh_school')); ?></b>
    <span><?php echo esc_html(lh('lh_major')); ?></span>
  </div>
</section>

<section id="contact">
  <h2 class="title rv">05 // Kontak</h2>
  <div class="socials">
    <?php foreach ($social as [$i, $n, $u]) : ?>
      <a class="ico tilt rv" href="<?php echo esc_url($u); ?>"<?php echo $i === 'gmail' ? '' : ' target="_blank" rel="noopener"'; ?> aria-label="<?php echo esc_attr($n); ?>" title="<?php echo esc_attr($n); ?>"><img src="<?php echo esc_url('https://cdn.jsdelivr.net/npm/simple-icons@11.14.0/icons/' . $i . '.svg'); ?>" alt="<?php echo esc_attr($n); ?>"></a>
    <?php endforeach; ?>
  </div>
</section>
<?php get_footer();
