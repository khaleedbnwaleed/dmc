<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Danmodi Students Care</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- Swiper CSS -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>

</head>
<body class="bg-white text-gray-800 font-sans">

  <!-- Header -->
  <header class="bg-white border-b shadow py-4 px-6 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center gap-3">
        <img src="logo.png" width="60" height="60" class="rounded-md" alt="Logo">
        <div>
          <h1 class="font-bold text-green-700 text-lg sm:text-xl">DANMODI STUDENTS CARE</h1>
          <p class="text-xs text-gray-500">Jigawa State Initiative</p>
        </div>
      </div>
      <nav class="hidden md:flex gap-6 text-sm font-medium">
        <a href="#about" class="hover:text-green-600">About</a>
        <a href="#programs" class="hover:text-green-600">Programs</a>
        <a href="#impact" class="hover:text-green-600">Impact</a>
        <a href="#contact" class="hover:text-green-600">Contact</a>
      </nav>
      <div class="flex gap-2 text-sm">
        <a href="signup.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"><i class="fas fa-user-plus mr-1"></i>Apply</a>
        <a href="login.php" class="border px-4 py-2 rounded hover:bg-gray-100 transition"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="bg-gradient-to-br from-green-50 to-blue-50 py-20 text-center px-4">
    <div class="container mx-auto">
      <h2 class="text-4xl sm:text-5xl font-bold mb-4">Empowering <span class="text-green-600">Jigawa Students</span></h2>
      <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">Supporting underprivileged students across Jigawa with scholarships, admission help, and mentorship.</p>
      <div class="flex flex-wrap justify-center gap-4">
        <a href="signup.php" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition"><i class="fas fa-graduation-cap mr-2"></i>Apply Now</a>
        <a href="#about" class="border px-6 py-2 rounded-md hover:bg-gray-100 transition"><i class="fas fa-book-open mr-2"></i>Learn More</a>
      </div>
      
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-20 bg-white px-4">
    <div class="container mx-auto text-center">
      <h2 class="text-3xl font-bold mb-6">About Danmodi Students Care</h2>
      <p class="text-lg text-gray-600 max-w-3xl mx-auto">
        An educational support initiative under the Office of the SSA on Student Affairs to the Jigawa State Governor, aimed at uplifting underprivileged students through scholarships, mentorship, and access to opportunities.
      </p>
  <!-- Swiper -->
  <div class="swiper mySwiper max-w-5xl mx-auto rounded-lg overflow-hidden">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <img src="images/1.jpg" alt="Gallery Image 1" class="w-full h-80 object-cover">
      </div>
      <div class="swiper-slide">
        <img src="images/2.jpg" alt="Gallery Image 2" class="w-full h-80 object-cover">
      </div>
      <div class="swiper-slide">
        <img src="images/3.jpg" alt="Gallery Image 3" class="w-full h-80 object-cover">
      </div>
      <div class="swiper-slide">
        <img src="images/4.jpg" alt="Gallery Image 4" class="w-full h-80 object-cover">
      </div>
      <div class="swiper-slide">
        <img src="images/5.jpg" alt="Gallery Image 5" class="w-full h-80 object-cover">
      </div>
    </div>
    <!-- Swiper Navigation -->
    <div class="swiper-button-next text-green-600"></div>
    <div class="swiper-button-prev text-green-600"></div>
    <div class="swiper-pagination mt-4"></div>
  </div>
</section>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  const swiper = new Swiper(".mySwiper", {
    loop: true,
    spaceBetween: 20,
    slidesPerView: 1,
    autoplay: {
      delay: 4000,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
</script>

  <!-- Programs Section -->
  <section id="programs" class="py-20 bg-gray-50 px-4">
    <div class="container mx-auto text-center">
      <h2 class="text-3xl font-bold mb-10">Our Core Programs</h2>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
          <i class="fas fa-graduation-cap text-green-600 text-3xl mb-2"></i>
          <h3 class="font-semibold text-lg">Scholarships</h3>
          <p class="text-gray-600 text-sm mt-2">Covers tuition and living costs for various academic levels.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
          <i class="fas fa-users text-blue-600 text-3xl mb-2"></i>
          <h3 class="font-semibold text-lg">Admission Help</h3>
          <p class="text-gray-600 text-sm mt-2">Application guidance and support for higher institutions.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
          <i class="fas fa-heart text-purple-600 text-3xl mb-2"></i>
          <h3 class="font-semibold text-lg">Special Quotas</h3>
          <p class="text-gray-600 text-sm mt-2">Support for vulnerable and priority groups.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
          <i class="fas fa-book-open text-orange-600 text-3xl mb-2"></i>
          <h3 class="font-semibold text-lg">Mentorship</h3>
          <p class="text-gray-600 text-sm mt-2">Monitoring, tools, and academic support.</p>
        </div>
      </div>
      
    </div>
  </section>

  <!-- Impact Section -->
  <section id="impact" class="py-20 bg-white px-4">
    <div class="container mx-auto text-center">
      <h2 class="text-3xl font-bold mb-4">Impact Dashboard</h2>
      <p class="text-gray-600 text-lg mb-10">Real-time indicators of progress across all LGAs</p>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-green-100 rounded-lg p-6 shadow text-center">
          <div class="text-4xl font-bold text-green-700">2,847</div>
          <div class="text-gray-700 mt-1">Total Beneficiaries</div>
        </div>
        <div class="bg-blue-100 rounded-lg p-6 shadow text-center">
          <div class="text-4xl font-bold text-blue-700">27</div>
          <div class="text-gray-700 mt-1">LGAs Covered</div>
        </div>
        <div class="bg-purple-100 rounded-lg p-6 shadow text-center">
          <div class="text-4xl font-bold text-purple-700">₦2.8B</div>
          <div class="text-gray-700 mt-1">Funds Disbursed</div>
        </div>
        <div class="bg-orange-100 rounded-lg p-6 shadow text-center">
          <div class="text-4xl font-bold text-orange-600">89%</div>
          <div class="text-gray-700 mt-1">Success Rate</div>
        </div>
      </div>
      
    </div>
  </section>
  
  <!-- Contact Section -->
  <section id="contact" class="py-20 bg-gray-50 px-4">
    <div class="container mx-auto text-center">
      <h2 class="text-2xl font-bold mb-8">Contact Us</h2>
      <div class="grid gap-6 md:grid-cols-3 text-center">
        <div>
          <i class="fas fa-map-marker-alt text-green-600 text-xl mb-2"></i>
          <p class="text-sm text-gray-600">SSA Student Affairs Office, Dutse, Jigawa</p>
        </div>
        <div>
          <i class="fas fa-globe text-blue-600 text-xl mb-2"></i>
          <a href="https://danmodistudentscare.com.ng" class="text-sm text-blue-600 hover:underline">danmodistudentscare.com.ng</a>
        </div>
        <div>
          <i class="fas fa-phone text-purple-600 text-xl mb-2"></i>
          <p class="text-sm text-gray-600">Follow us for updates and inquiries</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Footer -->
  <footer class="bg-gray-900 text-gray-400 py-6 text-center text-sm">
    &copy; 2025 Danmodi Students Care - Jigawa State Government. Alyaum Technology All rights reserved.
  </footer>

</body>
</html>
