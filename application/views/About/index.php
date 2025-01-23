<!DOCTYPE html>
<html lang="en">
    <meta charset="UTF-8">
    <title>About Us</title> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="style.css">   
    <style>
.carousel-item img {
    width: 300px;
    height: 200px;
    object-fit: cover;}
.carousel {
    height: 400px !important;
}
.carousel-item {
    width: 400px !important;
    height: 400px !important;
}
.carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style> 
</head>
<body style="background: linear-gradient(rgb(10, 50, 120), rgb(70, 130, 180))">
  <!-- Rest of the content remains the same -->
  <header>
    <nav class="Boxx" style="background-color: transparent; display: flex;">
      <div>
        <img src="<?= base_url('assets/Asset Svg/Logo Alternative One Color - Yellow - Horizontal.svg') ?>" alt="University amikom yogyakarta Logo" height="40" class="rumah" style="position: relative; left: 2rem; top: 1rem;">
      </div>
      <div style="position: relative; left: 65rem; color: black;">
        <ul class="list1">
          <li><a href="<?= base_url() ?>">Home</a></li>
          <li><a href="<?= base_url('pmb') ?>">PMB</a></li>
          <li><a href="<?= base_url('contact') ?>">Contact</a></li>
          <li><a href="<?= base_url('about') ?>">About Us</a></li>
        </ul>
      </div>
    </nav>
  </header>

  <div style="position: relative; left: 3rem; top: 11rem;">
    <h4 style="color: rgb(255, 255, 255);"><b>Biodata</b></h4>
    <h5 style="color: rgb(255, 255, 255);">Web Developer</h5>
    <h5 style="color: yellow;"><b>"Amikom Creative Economy Park"</b></h5>
    <h5 style="color: rgb(255, 255, 255);"><b>23 mai 2024 s/d 30 Juli 2024</b></h5>
  </div>

  <div style="position: relative; left: 12rem; bottom: 10rem;">
    <div class="carousel" style="position: relative; left: 3rem;">
      <?php foreach($slides as $slide): ?>
        <div class="carousel-item">
          <img src="<?= base_url($slide) ?>" alt="Slide" style="border-radius: 10px;">
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>