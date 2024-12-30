<div class="flex-1 ml-64 min-h-screen bg-gradient-to-b from-purple-900 to-purple-400">
    <!-- Main Content -->
    <div class="p-8">
        <!-- Biodata Section -->
        <div class="mb-12">
            <h1 class="text-3xl font-bold text-white mb-4">Biodata</h1>
            <h2 class="text-xl text-white mb-2">Web Developer</h2>
            <h2 class="text-xl font-bold text-yellow-400 mb-2">
                "Sistem Penggajian Karyawan"
            </h2>
            <h2 class="text-xl font-bold text-white">
                23 mei 2024 s/d 30 Juli 2024
            </h2>
        </div>
        <div class="max-w-4xl mx-auto">
            <div class="relative overflow-hidden">
                <div id="carousel" class="flex transition-transform duration-500 ease-in-out">
                    <div class="flex-none w-full px-4">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="<?= base_url('assets/img/carousel/1.png') ?>" 
                                 alt="Team Member 1" 
                                 class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Developer 1</h3>
                                <p class="text-gray-600">Full Stack Developer</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex-none w-full px-4">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="<?= base_url('assets/img/carousel/1.png') ?>" 
                                 alt="Team Member 2" 
                                 class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Developer 2</h3>
                                <p class="text-gray-600">UI/UX Designer</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex-none w-full px-4">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="<?= base_url('assets/img/carousel/1.png') ?>" 
                                 alt="Team Member 3" 
                                 class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Developer 3</h3>
                                <p class="text-gray-600">Backend Developer</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <button onclick="moveCarousel(-1)" 
                        class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button onclick="moveCarousel(1)" 
                        class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('#carousel > div');
const totalSlides = slides.length;

function moveCarousel(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    document.getElementById('carousel').style.transform = `translateX(-${currentSlide * 100}%)`;
}

// Auto play
setInterval(() => moveCarousel(1), 4500);
</script>