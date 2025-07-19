<?php
require_once __DIR__ . '/../../paths.php';
session_start();
require_once HTML_HEADER;
?>
<title>Chapter One</title>
<style>
    .wrapper {
        margin-left: auto;
        margin-right: auto;
        max-width: 1250px;
        margin-top: 30px;
    }

    #novel-container {
        padding: 20px;
        margin: auto;
        background-color: rgb(145, 203, 255);

        .novel-wrapper {
            width: 900px;
            display: grid;
            grid-template-columns: 250px auto;
            column-gap: 70px;

            #novel-img {
                grid-column: 1/2;
                background-color: black;
                height: 300px;
                width: 220px;
                padding: 10px;
                border-radius: 12px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            #novel-details {
                display: flex;
                flex-direction: column;
                justify-content: space-between;

                #novel-title {
                    margin: 0;
                }

                #novel-author {
                    color: rgb(204, 0, 255);
                    font-size: 18px;
                    font-weight: bold;
                }
            }

            #readBtn,
            #libraryBtn,
            #fakeLibraryBtn {
                padding: 10px 30px;
                border-radius: 8px;
                border: 0px;
                cursor: pointer;
                text-decoration: none;
            }

            #readBtn {
                background-color: #DB6D29;
                color: white;
                margin-right: 20px;
            }

            #libraryBtn,
            #fakeLibraryBtn {
                background-color: white;
            }

        }
    }

    section {
        width: 100%;
    }


    main {
        min-height: 100vh;
    }

    #aboutBtn,
    #chapterBtn {
        border: 0px;
        background-color: transparent;
        font-size: 32px;
        cursor: pointer;
    }

    #btnSection button:hover {
        text-decoration: underline;
        text-decoration-color: #DB6D29;
    }

    .separator {
        font-size: 32px;
        margin: 0 20px;
        font-weight: bold;
    }

    #aboutSection {
        margin-top: 30px;


        #sypnosis {
            font-size: 24px;
            font-weight: bold;
        }
    }

    #novel-sypnosis {
        font-size: 18px;
        text-align: justify;
        line-height: 1.5;
    }

    #chapterSection {
        display: none;

    }


    .chapter-container {
        padding: 20px 10px;
        margin: 50px 10px;
        height: 100px;
        word-break: break-word;
        overflow: hidden;

        .chapter-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }

        .chapter-content {
            padding: 10px;
        }

        .chapter-content p {
            font-size: 16px;
            margin-bottom: 0px;
            line-height: 1.5;
        }

    }

    .focused {
        text-decoration: underline;
        text-decoration-color: #DB6D29;
    }

    .chapter-container:nth-child(even) {
        background-color: #fff1e9ff;
        border-radius: 8px;
    }

    #chapter-count {
        font-size: 18px;
        font-weight: 500;
    }

    .review-wrapper {
        display: flex;
        flex-direction: row;
        gap: 50px;
        margin-bottom: 70px;
    }

    .rating-review-section {
        margin-top: 50px;
    }

    #reviewForm {
        display: none;
    }

    .review {
        max-width: 300px;
        max-height: 300px;
    }

    .emptyBtnStyle {
        background-color: transparent;
    }

    .emptyBtnStyle:hover {
        background-color: transparent;
        color: red;
    }


    .review-container {
        display: grid;
        grid-template-columns: 100px auto;
        grid-template-rows: auto;
        margin-bottom: 50px;
    }

    .reviewRating {
        display: inline;
        width: 10px;
        height: 10px;
        margin-right: 5px;
    }


    .userProfile {
        display: flex;
        flex-direction: column;
        justify-self: center;
        text-align: center;
    }

    .review_pfp {
        width: 60px;
        height: 60px;
        border-radius: 50%;
    }

    .user-review {
        max-width: 700px;
        text-align: justify;
        line-height: 1.4;
    }
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>


    <div id="novel-container">
        <div class="wrapper novel-wrapper">
            <div id='novel-img'>
                <img src='../img/question.png' />
            </div>
            <div id='novel-details'>
                <div id="detail-container">
                    <h1 id="novel-title">Loading...</h1>
                    <p stle="font-size:9x;color:gray;">Author: <span id="novel-author">Author</span></p>
                </div>
                <div id="button-container">
                    <a id="readBtn" href="">
                        Read Now
                    </a>
                    <?php if ($userRole != 'user'): ?>
                        <a id="fakeLibraryBtn" onclick="togglePopup('popup-container')">
                            + Add to Library
                        </a>
                    <?php else: ?>
                        <a id="libraryBtn">
                            + Add to Library
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <main class="wrapper">
        <section id="btnSection" style="display: flex;flex-direction: row;">
            <button id="aboutBtn" onclick="sectionSelection('about')" class="focused">About</button>
            <span class="separator">|</span>
            <button id="chapterBtn" onclick="sectionSelection('chapter')">Chapters</button>
        </section>

        <section id="aboutSection">
            <p id="sypnosis">Sypnosis</p>
            <div id="novel-sypnosis">Loading...</div>
            <hr>
            <!-- Rating and Review Section -->
            <div class="rating-review-section">
                <div class="review-wrapper">
                    <h1>Ratings and Review Section</h1>
                    <!-- If the user is not logged in, display login prompt popup -->
                    <?php if ($userRole != 'user'): ?>
                        <button class='BtnStyle' id='reviewBtn' onclick="togglePopup('popup-container')">Add a
                            Review</button>
                        <!-- Else if the user does not have a review yet, prompt the user to share a review -->
                    <?php else: ?>
                        <button class='BtnStyle' id='reviewBtn' onclick="toggleReviewBtn('reviewForm','reviewBtn')">Add a
                            Review</button>
                    <?php endif ?>
                </div>


                <!-- Review form -->
                <form id="reviewForm" method="POST" style="margin:20px auto">
                    <label for="rating">Rating:</label>
                    <label><input type="radio" name="rating" value="1">1 star</label>
                    <label><input type="radio" name="rating" value="2">2 star</label>
                    <label><input type="radio" name="rating" value="3">3 star</label>
                    <label><input type="radio" name="rating" value="4">4 star</label>
                    <label><input type="radio" name="rating" value="5">5 star</label><br><br>

                    <label for="review">Review</label><br>
                    <textarea class="review" name="review" maxlength="1500"></textarea><br><br>

                    <div class="review-wrapper">
                        <button class="BtnStyle" type="submit" value="" style="padding:10px 20px;">Submit
                            Review</button>
                        <button id='cancel_reviewBtn' class='emptyBtnStyle'
                            onclick="toggleReviewBtn('reviewBtn','reviewForm')">Cancel</button>
                    </div>
                    <hr>
                </form>

                <div id="reviewList">
                    <div class='review-container'>
                        <div class='userProfile'>
                            <img class='review_pfp' alt='default pfp' src='https://picsum.photos/50'>
                            <p id='review_username'>Loading...</p>
                        </div>

                        <div class='userComment'>
                            <div style='display:flex;justify-content:space-between'>
                                <!-- Fetch rating star -->
                                <div>
                                    <?php for ($i = 0; $i < 5; $i++) {
                                        if ($i < 5) {
                                            echo "<img class='reviewRating' src= '../img/filled_star.png'>";
                                        } else {
                                            echo "<img class='reviewRating' src= '../img/empty_star.png'>";
                                        }
                                    }
                                    ?>
                                </div>
                                <p style='display:inline;margin:0 0;'>2025-5-43</p>
                            </div>
                            <p class='user-review'>Loading...</p>
                        </div>
                    </div>
                </div>
        </section>

        <section id="chapterSection">
            <p id="chapter-count">Loading....</p>
            <hr>
            <div id="chapterArea">Loading...</div>
        </section>
    </main>

    <!-- Login prompt container -->
    <div id="popup-container" class="overlay-container">
        <div class="popup_wrapper">
            <h1 class="popup_title">Before we proceed..</h1>
            <button class="popup_closeBtn" onclick="togglePopup('popup-container')"><img src="../img/close_Icon.png"
                    width="20px" height="20px"></button>
            <h3 style="margin-bottom:70px;">Please Login to Continue</h3>
            <a style="padding:10px 70px;background-color:black;border-radius:12px;color:white;text-decoration:none;"
                href="<?php echo LOGIN_PAGE ?>">Login Now!</a>
            <p style="font-size:14px;">Are you new here? <a style="font-size:14px;color:blue;"
                    href="<?php echo REGISTER_PAGE ?>">Register now!</a></p>
        </div>
    </div>

    <?php require_once FOOTER_COMPONENT; ?>

    <script>
        //API Paths
        const API = {
            novel: '<?php echo NOVEL_API ?>',
            genre: '<?php echo GENRE_API ?>',
            author: '<?php echo AUTHOR_API ?>',
            rating: '<?php echo RATING_API ?>',
            review: '<?php echo REVIEW_API; ?>',
            library: '<?php echo LIBRARY_API ?>',
            novel_chapter: '<?php echo NOVEL_CHAPTER_API ?>',
        };

        let genreList = [];

        //Get current URL
        const params = new URLSearchParams(window.location.search);

        //Get novel ID from request param
        const novelId = params.get('nv_novel_id');
        //Add link to "Read" button
        const readBtn = document.getElementById('readBtn');
        readBtn.href = "user_read_page.php?nv_novel_id=" + novelId;

        //Get userID
        const userID = <?php echo json_encode(($userRole == 'user') ? $_SESSION['user_id'] : null); ?>;

        //Function to handle add review request
        document.getElementById('reviewForm').onsubmit = async (e) => {
            e.preventDefault();

            // Get rating
            const ratingInput = document.querySelector('input[name="rating"]:checked');
            const rating = parseInt(ratingInput.value);
            const reviewText = document.querySelector('textarea[name="review"]').value.trim();

            if (!ratingInput) {
                alert('Please select a rating.');
                return;
            }



            try {
                const res = await axios.post(API.review, {
                    nv_novel_id: novelId,
                    nv_review_rating: rating,
                    nv_review_comment: reviewText,
                });

                if (res.data.success) {
                    alert('Review submitted successfully!');
                    e.target.reset();
                } else {
                    const err = res.data.error || 'Failed to submit review.';
                    console.log(err);
                }

            } catch (err) {
                const errorMsg = err.response?.data?.error || 'Submission failed';
                console.log(errorMsg);
            }
        };

        async function loadReviews(novelId) {
            const container = document.getElementById('reviewList');
            container.innerHTML = '<p>Loading reviews...</p>';

            try {
                const res = await axios.get(`${API.review}?nv_novel_id=${novelId}`);
                const reviews = res.data;


                if (!reviews.length) {
                    container.innerHTML = '<p>No reviews yet.</p>';
                    return;
                }

                container.innerHTML = reviews.map(review => {
                    const stars = Array.from({ length: 5 }, (_, i) => {
                        const starType = i < review.nv_review_rating ? 'filled_star.png' : 'empty_star.png';
                        return `<img class="reviewRating" src="../img/${starType}" alt="${i + 1} star">`;
                    }).join('');

                    return `
                        <div class='review-container'>
                            <div class='userProfile'>
                                <img class='review_pfp' alt='default pfp' src='https://picsum.photos/50'>
                                <p class='review_username'>Username</p>
                            </div>

                            <div class='userComment'>
                                <div style='display:flex;justify-content:space-between'>
                                    <div>${stars}</div>
                                    <p style='display:inline;margin:0 0;'>${new Date(review.nv_review_created_at).toLocaleDateString()}</p>
                                </div>
                                <p class='user-review'>${review.nv_review_comment}</p>
                                <hr>
                            </div>
                        </div>
                    `;
                }).join('');

            } catch (err) {
                container.innerHTML = '<p>Error loading reviews.</p>';
                console.error(err);
            }
        }

        //Check if the user has the book in library
        async function modifyLibraryButton() {
            //If user is not logged in, ignore
            if (!userID) {
                return;
            }

            try {
                const libraryBtn = document.getElementById('libraryBtn');
                const requestParam = `?nv_user_id=${userID}&nv_novel_id=${novelId}`;
                const res = await axios.get(API.library + requestParam);
                if (!res.data.length) {
                    libraryBtn.onclick = () => addToLibrary();
                } else {
                    libraryBtn.innerHTML = "Remove from Library";
                    libraryBtn.onclick = () => deleteFromLibrary();
                }

            } catch (ex) {
                errMessage = ex.response?.data?.error || 'Error Modifying library';
                console.log(errMessage);
            }
        }

        async function addToLibrary() {
            try {
                const requestParam = `?nv_novel_id=${novelId}`;
                const res = await axios.post(API.library, {
                    nv_novel_id: novelId,
                    nv_user_id: userID,
                });

                //Reload page to get changes
                window.location.reload();
            } catch (ex) {
                errMessage = ex.response?.data?.error || 'Error Adding to library';
                console.log(errMessage);
            }
        }



        async function deleteFromLibrary() {
            try {
                //Remove from library
                const requestParam = `?nv_user_id=${userID}&nv_novel_id=${novelId}`;
                const { data } = await axios.delete(API.library + requestParam);
                const libraryID = data.nv_user_library_id;

                //Reload page to get changes
                window.location.reload();
            } catch (ex) {
                errMessage = ex.response?.data?.error || 'Error Adding to library';
                console.log(errMessage);
            }
        }


        // Toggle popup functions
        function togglePopup(containerID) {
            const overlay = document.getElementById(containerID);
            overlay.classList.toggle('show');
        }


        function toggleReviewBtn(showID, hideID) {
            document.getElementById(hideID).style.display = 'none';
            document.getElementById(showID).style.display = 'block';
        }


        function sectionSelection(section) {
            //Get button element for styling
            const aboutBtn = document.getElementById('aboutBtn');
            const chapterBtn = document.getElementById('chapterBtn');

            //Get section 
            const about = document.getElementById('aboutSection');
            const chapter = document.getElementById('chapterSection');

            //If section is 'about', hide chapter and focus about button, else do the opposite
            if (section === 'about') {
                about.style.display = 'block';
                chapter.style.display = 'none';
                aboutBtn.classList.add("focused");
                chapterBtn.classList.remove("focused");
            } else if (section === 'chapter') {
                about.style.display = 'none';
                chapter.style.display = 'block';
                chapterBtn.classList.add("focused");
                aboutBtn.classList.remove("focused");
            }
        }

        async function loadGenreMap() {
            const res = await axios.get(API.genre + '?list=1');
            return Object.fromEntries(res.data.map(g => [g.nv_genre_id, g.nv_genre_name]));
        }

        async function loadAllMappings() {
            const res = await axios.get(API.genre + '?all=1');
            return res.data;
        }


        async function loadNovelDetail() {
            try {
                //Declare containers
                const title = document.getElementById('novel-title');

                const authorName = document.getElementById('novel-author');
                const sypnosis = document.getElementById('novel-sypnosis');

                //Fetch novel information
                const { data } = await axios.get(`${API.novel}?nv_novel_id=${novelId}`);
                const novel = data[0];
                //Fetch author information
                const res = await axios.get(`${API.author}?id=${novel.nv_author_id}`);
                const author = res.data

                //Update container value 
                title.innerHTML = novel.nv_novel_title;
                authorName.innerHTML = author.nv_author_username;
                sypnosis.innerHTML = novel.nv_novel_description;

                //Change webpage title name
                document.title = novel.nv_novel_title;

            } catch (ex) {
                errMessage = ex.response?.data?.error || 'Error loading novels';
                console.log(errMessage);
            }
        }

        async function getNovelChapters() {



            //Get container for chapter
            const box = document.getElementById('chapterArea');
            const chapterCount = document.getElementById('chapter-count');


            //Get all chapters of a novel using novel id
            const filter = 'nv_novel_id=' + novelId;
            const { data } = await axios.get(`${API.novel_chapter}?${filter}&nv_novel_chapter_status=published`);

            //Set chapter count
            chapterCount.innerHTML = data.length + " Published Chapters";

            //For each chapters in the novel, populate div section
            if (data.length > 1) {
                let counter = 1;
                box.innerHTML = data.map(chapter => (
                    `<div class='chapter-container'>
                    <a class='chapter-title' href='user_read_page.php?nv_novel_id=${chapter.nv_novel_id}&nv_novel_chapter_number=${chapter.nv_novel_chapter_number}'>Chapter ${counter++ + ": " + chapter.nv_novel_chapter_title}</a>
                    <div class='chapter-content'>${chapter.nv_novel_chapter_content}</div>
                </div>`

                )).join('');
            } else {
                box.innerHTML = `<div class='chapter-container' style=''>
                                    <h1 style='text-align:center;'>This Novel has not published any chapters yet. Please come back later.</h1>
                                </div>`
            }
        }
        loadReviews(novelId);
        modifyLibraryButton();
        loadNovelDetail();
        getNovelChapters();
    </script>
</body>

</html>