<?php
require_once __DIR__ . '/../../paths.php';
require_once HTML_HEADER;
?>
<style>
    .wrapper {
        margin-left: auto;
        margin-right: auto;
        width: 1250px;
        margin-top: 30px;
    }

    main {
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 30px;
    }

    aside {
        width: 250px;
    }

    #btn_wrapper {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        background-color: #007bff;
        border-radius: 6px;
        width: 210px;
    }

    .lead_option {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 2px 8px;
        margin: 5px;
        border-radius: 6px;
        cursor: pointer;
        color: white;
    }

    .male {
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
    }

    .female {
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    .lead_option input[type="radio"] {
        display: none;
    }

    .lead_option:has(input:checked) {
        background-color: #ffffff;
        border-color: #007bff;
        color: black;
        font-weight: bold;
        transition: background-color 0.2s, border-color 0.2s;
    }

    section {
        width: 700px;
    }

    #novel-list-container {
        display: flex;
        flex-direction: row;
        overflow: hidden;
    }

    #novel-list {
        display: grid;
        grid-template-columns: repeat(2, 300px);
        list-style-type: none;
        row-gap: 30px;
        column-gap: 30px;

        .novel-container {
            display: grid;
            grid-template-columns: 90px auto;
            flex-direction: row;
            column-gap: 20px;

            .novel-img {
                grid-column: 1/2;
                background-color: black;
                height: 98px;
                width: 70px;
                padding: 10px;
                border-radius: 12px;
                display: flex;
                justify-content: center;
                align-items: center;
            }


            .novel-details {
                grid-column: 2/3;

                .novel-title {
                    font-size: 18px;
                    font-weight: bold;
                    margin: 0 auto;
                }

                .novel-title a {
                    text-decoration: none;
                }

                .novel-description {
                    margin: 0 auto;
                }

                .novel-view-count {
                    font-size: 13px;
                }

                .novel-stats {
                    display: flex;
                    gap: 20px;
                    align-items: center;
                }
            }
        }

    }

    #filter-genre {
        display: grid;
        grid-template-columns: repeat(2, auto);
        row-gap: 10px;
        column-gap: 20px;
        margin-top: 20px;
    }

    .genre-button {
        border-radius: 8px;
        width: max-content;
        height: 30px;
        background-color: transparent;
        padding: 5px 10px;
        align-content: center;
    }

    .genre-button:hover {
        background-color: blue;
        color: white;
    }

    .isSelected {
        background: blue;
        color: white;
    }

    #genreTitle {
        font-size: 24px;
        margin-bottom: 5px;
    }

    #genreIntro {
        margin: 0px;
        margin-bottom: 15px;
    }
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>

    <div class="wrapper">
        <main>
            <aside>
                <h3 style="font-size:28px;font-weight:700;">Genre of Novels</h3>

                <!-- Filters -->
                <div id="filter-genre">
                    <a class="genre-button" href="browse_book.php?all">All</a>
                </div>

            </aside>

            <section>
                <div>
                    <h3 id="genreTitle"></h3>
                    <p id="genreIntro"></p>
                    <hr>
                </div>
                <div id="novel-list-container">
                    <ul id="novel-list">Loading…</ul>
                </div>
            </section>
        </main>
    </div>


    <script>
        //API Paths

        const API = {
            novel: '<?php echo NOVEL_API ?>',
            genre: '<?php echo GENRE_API ?>',
            author: '<?php echo AUTHOR_API ?>',
            rating: '<?php echo RATING_API ?>',
        };
        let genreList = [];

        //Get current URL
        const params = new URLSearchParams(window.location.search);
        //Get genre ID from request param
        const genreId = params.get('nv_genre_id');

        // Function to load all genres
        async function loadGenres() {
            try {
                const res = await axios.get(API.genre + '?list=1');
                //Check if response data is an array
                if (Array.isArray(res.data)) {
                    //store in genreList
                    genreList = res.data;
                    const filterSelect = document.getElementById('filter-genre');
                    //For each element in genreList, add the option name as well as the value
                    filterSelect.innerHTML += genreList.map(genre => {
                        return `<a class="genre-button" href="browse_book.php?nv_genre_id=${genre.nv_genre_id}">${genre.nv_genre_name}</a>`;

                    }).join('');
                }
            } catch (e) {
                console.error('Failed to load genres', e);
            }
        }


        async function loadGenreMap() {
            const res = await axios.get(API.genre + '?list=1');
            return Object.fromEntries(res.data.map(g => [g.nv_genre_id, g.nv_genre_name]));
        }

        async function loadAllMappings() {
            const res = await axios.get(API.genre + '?all');
            return res.data;
        }

        function setGenreIntro(genre) {
            const genreTitle = document.getElementById('genreTitle');
            const genreIntro = document.getElementById('genreIntro');

            let genreDescription = '';
            switch (genre.toLowerCase()) {
                case 'adventure':
                    genreDescription = 'Adventure novels take readers on thrilling journeys, often featuring courageous protagonists who explore uncharted lands, overcome dangerous obstacles, and uncover hidden secrets. These stories are packed with action, excitement, and a sense of discovery.';
                    break;
                case 'comedy':
                    genreDescription = 'Comedy novels aim to entertain and amuse, using humor, witty dialogue, and clever situations. The characters often find themselves in absurd or exaggerated scenarios, offering a light-hearted and feel-good reading experience.';
                    break;
                case 'drama':
                    genreDescription = 'Drama novels focus on emotional depth and character development. These stories often revolve around real-life issues, personal conflicts, and moral dilemmas, drawing readers into powerful and thought-provoking narratives.';
                    break;
                case 'fantasy':
                    genreDescription = 'Fantasy novels transport readers to imaginative worlds filled with magic, mythical creatures, and epic quests. The genre often involves heroic battles between good and evil, where the impossible becomes reality.';
                    break;
                case 'historical':
                    genreDescription = 'Historical novels are set in the past and immerse readers in the customs, politics, and lifestyles of earlier eras. They may include real historical figures and events, blending fact with fiction to bring history to life.';
                    break;
                case 'horror':
                    genreDescription = 'Horror novels are crafted to evoke fear, suspense, and unease. These stories often involve supernatural elements, psychological terror, or monstrous creatures that challenge the characters’ sanity and survival.';
                    break;
                case 'mystery':
                    genreDescription = 'Mystery novels revolve around solving a crime or unraveling a secret. The protagonist—often a detective or amateur sleuth—pieces together clues and follows twists and turns to uncover the truth behind a puzzling situation.';
                    break;
                case 'romance':
                    genreDescription = 'Romance novels center around love and relationships, capturing the emotional journey of characters as they navigate attraction, heartbreak, and passion. These stories often highlight the power of connection and emotional growth.';
                    break;
                case 'science fiction':
                    genreDescription = 'Science fiction novels explore futuristic settings, advanced technology, space travel, or parallel worlds. The genre often poses philosophical questions about humanity, society, and the consequences of scientific progress.';
                    break;
                case 'thriller':
                    genreDescription = 'Thriller novels are fast-paced and suspenseful, filled with danger, intrigue, and unexpected twists. They often involve high-stakes scenarios where the protagonist must race against time to uncover the truth or stop a catastrophe.';
                    break;
                default:
                    genreDescription = 'Explore captivating stories across various genres—from heartfelt dramas to pulse-pounding adventures. Each novel offers a unique experience to spark your imagination and emotions.';
                    break;
            }

            genreTitle.innerHTML = genre;
            genreIntro.innerHTML = genreDescription;
        }



        async function loadNovels() {
            //Declare container 
            const box = document.getElementById('novel-list');
            try {
                //Fetch all books and genre
                const genreMapping = await loadAllMappings();
                //Fetch all genre id, map to assoc array [{genre_id => genre_value}], and then to object {genre_id : genre_value}
                const genreMap = await loadGenreMap();

                let novels = [];
                //Handle for all request 
                if (genreId == null) {

                    novels = await axios.get(API.novel);
                    //Change webpage title
                    document.title = "All Genre";

                } else {
                    //Get genre request, and append to the Request Param
                    const requestParam = '?genre_id=' + genreId;

                    //call novel API to fetch books based on the genre_id with the status of published
                    novels = await axios.get(API.novel + requestParam);

                    //Set genre introduction
                    setGenreIntro(genreMap[genreId]);
                    //Change webpage title
                    document.title = genreMap[genreId];
                }
                const data = novels.data;

                if (!data.length) {
                    box.innerHTML = '<p>No novels found for selected genre.</p>';
                    return;
                }

                box.innerHTML = (await Promise.all(data.map(async (novel) => {
                    // Fetch average rating of the book
                    const requestParam = "?nv_novel_id=" + novel.nv_novel_id;
                    const ratingResponse = await axios.get(API.rating + requestParam);
                    const ratingData = ratingResponse.data;
                    const avgRating = ratingData.length ? ratingData[0].average_rating : "N/A";

                    // Limit description length
                    let modifiedDesc = novel.nv_novel_description;
                    if (modifiedDesc.length > 20) {
                        modifiedDesc = modifiedDesc.substring(0, 20) + '...';
                    }
                    return `
                            <li>
                                <div class='novel-container'>
                                    <div class='novel-img'>
                                        <img src='../img/question.png' />
                                    </div>
                                    <div class='novel-details'>
                                        <h3 class='novel-title'><a href='book_details.php?nv_novel_id=${novel.nv_novel_id}'>${novel.nv_novel_title}</a></h3>
                                        <div class='novel-description'>${novel.nv_novel_description}</div>
                                        <div class='novel-stats'>
                                            <p class='novel-view-count'><i class='fa-solid fa-eye'></i>&nbsp; ${novel.nv_novel_view_count}</p>
                                            <p class='novel-view-count'><i class='fa-solid fa-star'></i>&nbsp; ${avgRating}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        `;
                }))).join('');
            } catch (ex) {
                box.textContent = ex.response?.data?.error || 'Error loading novels';
            }
        }

        function escapeHtml(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;').replace(/\r?\n/g, '<br>');
        }

        function setupGenreButtonToggle() {
            const genreButtons = document.querySelectorAll('.genre-button');

            genreButtons.forEach(button => {
                const url = new URL(button.href);
                const buttonGenreId = url.searchParams.get('nv_genre_id');

                if ((genreId == null && url.href.includes('browse_book.php?all')) ||
                    (genreId != null && genreId === buttonGenreId)) {
                    button.classList.add('isSelected');
                }
            });
        }


        loadGenres().then(setupGenreButtonToggle);
        loadNovels();
    </script>
</body>

</html>