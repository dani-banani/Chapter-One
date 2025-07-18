<?php
session_start();
require_once __DIR__ . '/../../paths.php';
require_once HTML_HEADER;
?>
<style>
    main {
        min-height: 100vh;
        margin-bottom: 100px;
    }

    header {
        position: fixed;
        top: 0px;
        z-index: 100;
    }

    .wrapper {
        margin-left: auto;
        margin-right: auto;
        max-width: 1000px;
        margin-top: 30px;
    }

    /* Slider Styles */
    .slider-container {
        margin-top: 70px;
        position: relative;
        background: white;
        overflow: hidden;
    }

    .slider-wrapper {
        overflow: hidden;
        position: relative;
    }

    .slider-track {
        display: flex;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .slide {
        min-width: 100%;
        background: linear-gradient(135deg, rgb(255, 161, 161) 0%, rgb(249, 181, 122) 100%);
        padding: 60px 40px;
        display: flex;
        align-items: center;
        gap: 40px;
        position: relative;
        height: 300px;
    }

    .slide-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .slide-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-colour);
        margin-bottom: 30px;
        text-align: center;
    }

    .book-showcase {
        display: flex;
        align-items: center;
        width: 700px;
        gap: 30px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #4a90e2;
    }


    .slider-novel-img,
    .novel-img {
        background-color: black;

        padding: 10px;
        border-radius: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .novel-img {
        height: 200px;
        width: 140px;
    }

    .slider-novel-img {
        height: 120px;
        width: 80px;
    }

    .book-info {
        flex: 1;
    }

    .book-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .book-author {
        color: #7f8c8d;
        font-size: 1.1rem;
        margin-bottom: 15px;
    }

    .book-description {
        color: #34495e;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .nav-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        width: 75px;
        height: 75px;
        border-radius: 50%;
        cursor: pointer;
        color: black;
        transition: all 0.3s ease;
        z-index: 10;
        font-size: 50px;
        margin: 0;
        padding-bottom: 10px;
        align-self: center
    }

    .nav-button:hover {
        background: white;
    }

    .prev-button {
        left: 100px;
    }

    .next-button {
        right: 100px;
    }





    #recentBookContainer,
    #trendingFreeBookContainer,
    #trendingPremBookContainer,
    #genreContainer {
        display: flex;
        flex-direction: row;
        column-gap: 50px;
    }

    #genreContainer {
        margin-top: 30px;
    }


    .novel-container {
        margin-top: 30px;

        .novel-details {
            margin-top: 10px;

        }
    }

    #genre-1 {
        width: 100%;
    }

    .genre-img {
        width: 100%;
        height: 250px;
        border-radius: 10px;
        background-size: cover;
        background-position: center;
    }

    .genre-col {
        flex: 0 0 calc(100% / 4);
        margin-right: 10px;
        box-sizing: border-box;
        position: relative;
        overflow: hidden;
        width: 180px;
        height: 250px;
    }

    .genre-text {
        position: absolute;
        bottom: -10px;
        left: 20px;
        font-size: 20px;
        color: white;
    }

    #genre-1:hover .genre-img,
    #genre-2:hover .genre-img,
    #genre-3:hover .genre-img,
    #genre-4:hover .genre-img,
    #genre-5:hover .genre-img {
        background-image: linear-gradient(to top, rgba(16, 11, 11, 0.5), rgba(255, 255, 255, 0)), url(../img/genre_placeholder.png) !important;
    }

    .carousel {
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: 10px 0;
        margin-top: 30px;
    }

    .carousel-viewport {
        overflow: hidden;
        width: 100%;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.4s ease-in-out;
    }

    .carousel-btn {
        background: none;
        border: none;
        font-size: 32px;
        cursor: pointer;
        border-radius: 50%;
        z-index: 2;
    }
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>

    <!-- Hero Slider -->
    <div class="slider-container">
        <div class="slider-wrapper">
            <div class="slider-track" id="sliderTrack">
                <div class="slide">
                    <div class="slide-content">
                        <h2 class="slide-title">Top Books of the Week</h2>
                        <div class="book-showcase">
                            <div class='slider-novel-img'>
                                <img src='../img/question.png' />
                            </div>
                            <div class="book-info">
                                <h3 class="book-title" id="slide-title1">Loading...</h3>
                                <p class="book-author" id="slide-author1">By Author Name</p>
                                <p class="book-description" id="slide-desc1">Lorem ipsum dolor sit amet consectetur
                                    adipiscing elit. Sit
                                    amet consectetur adipiscing elit quisque faucibus ex sapien vitae pellentesque.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide-content">
                        <h2 class="slide-title">Top Books of the Week</h2>
                        <div class="book-showcase">
                            <div class='slider-novel-img'>
                                <img src='../img/question.png' />
                            </div>
                            <div class="book-info">
                                <h3 class="book-title" id="slide-title2">Featured Selection</h3>
                                <p class="book-author" id="slide-author2">By Author Name</p>
                                <p class="book-description" id="slide-desc3">Lorem ipsum dolor sit amet consectetur
                                    adipiscing elit. Sit
                                    amet consectetur adipiscing elit quisque faucibus ex sapien vitae pellentesque.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="slide">
                    <div class="slide-content">
                        <h2 class="slide-title">Top Books of the Week</h2>
                        <div class="book-showcase">
                            <div class='slider-novel-img'>
                                <img src='../img/question.png' alt="book placeholder" />
                            </div>
                            <div class="book-info">
                                <h3 class="book-title" id="slide-title3">Latest Release</h3>
                                <p class="book-author" id="slide-author3">By Author Name</p>
                                <p class="book-description" id="slide-desc3">Lorem ipsum dolor sit amet consectetur
                                    adipiscing elit. Sit
                                    amet consectetur adipiscing elit quisque faucibus ex sapien vitae pellentesque.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="nav-button prev-button" id="prevButton">‹</button>
        <button class="nav-button next-button" id="nextButton">›</button>
    </div>

    <main>
        <section id="recentBookSection" class="wrapper">
            <h1>New books fresh off the stove</h1>
            <div id="recentBookContainer">

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>
                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

            </div>
            <hr>
        </section>

        <section id="trendingFreeBookSection" class="wrapper">
            <h1>All-Time Best Free Book Picks</h1>
            <div id="trendingFreeBookContainer">

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>
            </div>
            <hr>
        </section>

        <section id="trendingPremBookSection" class="wrapper">
            <h1>Premium Books for Premium Experience</h1>
            <div id="trendingPremBookContainer">

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>

                <div class="novel-container">
                    <div class='novel-img'>
                        <img src='../img/question.png' alt="book placeholder" />
                    </div>
                    <div class="novel-details">
                        <h3>Book Title</h3>
                        <p>Book Genre</p>
                    </div>
                </div>
            </div>
            <hr>
        </section>

        <section id="genreSection" class="wrapper">
            <h1>Explore various genres</h1>

            <div class="carousel">
                <div class="carousel-viewport">
                    <div class="carousel-track" id="genreTrack">

                        <div class="genre-col" id="genre-1">
                            <a href='<?php echo USER_BROWSE_PAGE . "?nv_genre_id=8" ?>'>
                                <div class="genre-img"
                                    style="background-image: linear-gradient(rgba(16, 11, 11, 0.5), rgba(13, 9, 9, 0.768)), url(../img/genre_placeholder.png);">
                                </div>
                                <p class="genre-text">Adventure</p>
                            </a>
                        </div>

                        <div class="genre-col" id="genre-2">
                            <a href="<?php echo USER_BROWSE_PAGE . "?nv_genre_id=4" ?>">
                                <div class="genre-img"
                                    style="background-image: linear-gradient(rgba(16, 11, 11, 0.5), rgba(13, 9, 9, 0.768)), url(../img/genre_placeholder.png);">
                                </div>
                                <p class="genre-text">Mystery</p>
                            </a>
                        </div>

                        <div class="genre-col" id="genre-3">
                            <a href="<?php echo USER_BROWSE_PAGE . "?nv_genre_id=1" ?>">
                                <div class="genre-img"
                                    style="background-image: linear-gradient(rgba(16, 11, 11, 0.5), rgba(13, 9, 9, 0.768)), url(../img/genre_placeholder.png);">
                                </div>
                                <p class="genre-text">Fantasy</p>
                            </a>
                        </div>
                        <div class="genre-col" id="genre-4">
                            <a href="<?php echo USER_BROWSE_PAGE . "?nv_genre_id=2" ?>">
                                <div class="genre-img"
                                    style="background-image: linear-gradient(rgba(16, 11, 11, 0.5), rgba(13, 9, 9, 0.768)), url(../img/genre_placeholder.png);">
                                </div>
                                <p class="genre-text">Science Fiction</p>
                            </a>
                        </div>
                        <div class="genre-col" id="genre-5">
                            <a href="<?php echo USER_BROWSE_PAGE . "?nv_genre_id=3" ?>">
                                <div class="genre-img"
                                    style="background-image: linear-gradient(rgba(16, 11, 11, 0.5), rgba(13, 9, 9, 0.768)), url(../img/genre_placeholder.png);">
                                </div>
                                <p class="genre-text">Romance</p>
                            </a>
                        </div>
                        <div class="genre-col" id="genre-6">
                            <a href="<?php echo USER_BROWSE_PAGE . "?nv_genre_id=7" ?>">
                                <div class="genre-img"
                                    style="background-image: linear-gradient(rgba(16, 11, 11, 0.5), rgba(13, 9, 9, 0.768)), url(../img/genre_placeholder.png);">
                                </div>
                                <p class="genre-text">Horror</p>
                            </a>
                        </div>
                    </div>
                </div>
                <div
                    style="display:flex;flex-direction:column;gap:20px;margin-left:20px;justify-content:center;align-items:center">
                    <button class="carousel-btn left" onclick="moveSlide(-1)">&#8249;</button>
                    <button class="carousel-btn right" onclick="moveSlide(1)">&#8250;</button>
                </div>
            </div>
        </section>
    </main>

    <?php require_once FOOTER_COMPONENT; ?>
    <script>

        let currentIndex = 0;

        function moveSlide(direction) {
            const track = document.getElementById('genreTrack');
            const items = track.querySelectorAll('.genre-col');
            const visibleCount = 3;

            const maxIndex = items.length - visibleCount;
            currentIndex += direction;

            if (currentIndex < 0) currentIndex = 0;
            if (currentIndex > maxIndex) currentIndex = maxIndex;

            const cardWidth = items[0].offsetWidth + 20; // card + padding
            track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        }
        //Initialise containers
        const recentBook = document.getElementById('recentBookContainer');
        const trendingFreeBook = document.getElementById('trendingFreeBookContainer');
        const trendingPremBook = document.getElementById('trendingPremBookContainer');
        const genre = document.getElementById('genreContainer');

        //API Paths
        const API = {
            novel: '<?php echo NOVEL_API ?>',
            author: '<?php echo AUTHOR_API ?>',
        };

        class BookSlider {
            constructor() {
                this.currentSlide = 0;
                this.totalSlides = 3;
                this.sliderTrack = document.getElementById('sliderTrack');
                this.prevButton = document.getElementById('prevButton');
                this.nextButton = document.getElementById('nextButton');
                this.autoSlideInterval = null;

                this.init();
            }

            init() {
                this.prevButton.addEventListener('click', () => this.prevSlide());
                this.nextButton.addEventListener('click', () => this.nextSlide());

                // Touch/swipe support
                this.addTouchSupport();

                // Auto-slide functionality
                this.startAutoSlide();

                // Pause auto-slide on hover
                this.sliderTrack.addEventListener('mouseenter', () => this.stopAutoSlide());
                this.sliderTrack.addEventListener('mouseleave', () => this.startAutoSlide());
            }

            updateSlider() {
                const translateX = -this.currentSlide * 106;
                this.sliderTrack.style.transform = `translateX(${translateX}%)`;
            }

            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                this.updateSlider();
            }

            prevSlide() {
                this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                this.updateSlider();
            }

            addTouchSupport() {
                let startX = 0;
                let endX = 0;

                this.sliderTrack.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                });

                this.sliderTrack.addEventListener('touchmove', (e) => {
                    endX = e.touches[0].clientX;
                });

                this.sliderTrack.addEventListener('touchend', () => {
                    const threshold = 50;
                    const diff = startX - endX;

                    if (Math.abs(diff) > threshold) {
                        if (diff > 0) {
                            this.nextSlide();
                        } else {
                            this.prevSlide();
                        }
                    }
                });
            }

            startAutoSlide() {
                this.stopAutoSlide();
                this.autoSlideInterval = setInterval(() => {
                    this.nextSlide();
                }, 5000);
            }

            stopAutoSlide() {
                if (this.autoSlideInterval) {
                    clearInterval(this.autoSlideInterval);
                    this.autoSlideInterval = null;
                }
            }
        }


        // Function to fetch novels from API based on filter
        async function fetchNovel($filter) {
            try {
                const { data } = await axios.get(API.novel + $filter);
                return data;
            } catch (err) {
                if (err.response?.data?.error) {
                    alert('Error: ' + err.response.data.error);
                } else {
                    alert('Server error');
                }
            }
        }

        //Get newly released books
        async function populateNewNovel() {
            const filter = "?"
        }


        // Initialize slider when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new BookSlider();
        });
    </script>
</body>

</html>