<?php
require_once __DIR__ . '/../../paths.php';
require_once HTML_HEADER;
?>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: Arial, sans-serif;
    }

    .slider {
        position: relative;
        width: 100%;
        max-width: 1000px;
        height: 400px;
        margin: 40px auto;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .slides {
        display: flex;
        transition: transform 0.5s ease-in-out;
        width: 100%;
        height: 100%;
    }

    .slide {
        min-width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .slide-content {
        position: absolute;
        bottom: 30px;
        left: 30px;
        color: white;
        background: rgba(0, 0, 0, 0.5);
        padding: 20px;
        border-radius: 8px;
    }

    .nav-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.4);
        border: none;
        color: white;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 18px;
        border-radius: 50%;
        z-index: 10;
    }

    .nav-button:hover {
        background: rgba(0, 0, 0, 0.7);
    }

    .prev {
        left: 10px;
    }

    .next {
        right: 10px;
    }

    .dots {
        text-align: center;
        position: absolute;
        bottom: 15px;
        width: 100%;
    }

    .dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        background: #bbb;
        border-radius: 50%;
        margin: 0 5px;
        cursor: pointer;
    }

    .dot.active {
        background: #333;
    }
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>

    <div class="slider" id="heroSlider">
        <div class="slides" id="slides">
            <div class="slide" style="background-image: url('https://source.unsplash.com/featured/?city');">
                <div class="slide-content">
                    <h2>City Life</h2>
                    <p>Discover the pulse of modern cities.</p>
                </div>
            </div>
            <div class="slide" style="background-image: url('https://source.unsplash.com/featured/?nature');">
                <div class="slide-content">
                    <h2>Nature</h2>
                    <p>Reconnect with the natural world.</p>
                </div>
            </div>
            <div class="slide" style="background-image: url('https://source.unsplash.com/featured/?technology');">
                <div class="slide-content">
                    <h2>Innovation</h2>
                    <p>Explore the future of technology.</p>
                </div>
            </div>
        </div>

        <button class="nav-button prev" onclick="prevSlide()">❮</button>
        <button class="nav-button next" onclick="nextSlide()">❯</button>

        <div class="dots" id="dots"></div>
    </div>

    <script>
        const slides = document.querySelectorAll(".slide");
        const slidesContainer = document.getElementById("slides");
        const dotsContainer = document.getElementById("dots");
        let currentIndex = 0;
        let slideInterval = setInterval(nextSlide, 5000);

        function showSlide(index) {
            currentIndex = (index + slides.length) % slides.length;
            slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;

            document.querySelectorAll(".dot").forEach((dot, i) => {
                dot.classList.toggle("active", i === currentIndex);
            });
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        function goToSlide(index) {
            showSlide(index);
        }

        function createDots() {
            slides.forEach((_, i) => {
                const dot = document.createElement("span");
                dot.className = "dot" + (i === 0 ? " active" : "");
                dot.addEventListener("click", () => {
                    goToSlide(i);
                    resetInterval();
                });
                dotsContainer.appendChild(dot);
            });
        }

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000);
        }

        createDots();
        showSlide(0);
    </script>
</body>

</html>