<?php
require_once __DIR__ . '/../paths.php';
require_once HTML_HEADER;
?>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<script src="https://kit.fontawesome.com/598075423d.js" crossorigin="anonymous"></script>
<title>Chapter One</title>
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
        grid-template-columns: repeat(2, 80px);
        row-gap: 10px;
        column-gap: 20px;
        margin-top: 20px;
    }

    .genre-button {
        border-radius: 8px;
        width: 100%;
        height: 40px;
        background-color: transparent;
    }

    .genre-button:hover {
        background-color: blue;
        color: white;
    }
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>

    <div class="wrapper">
        <main>
            <aside>
                <h3 style="font-size:20px;font-weight:700;">Genre of Novels</h3>
                <div id="btn_wrapper">
                    <label class="lead_option male">
                        <input type="radio" name="lead" value="male" checked>
                        Male Lead
                    </label>

                    <label class="lead_option female">
                        <input type="radio" name="lead" value="female">
                        Female Lead
                    </label>
                </div>

                <!-- Filters -->
                <div id="filter-genre">
                    <button class="genre-button">All</button>
                </div>

            </aside>

            <section>
                <h3>Books</h3>
                <hr>
                <div id="novel-list-container">
                    <ul id="novel-list">Loading…</ul>
                </div>
            </section>
        </main>
    </div>


    <script>
        const API = {
            novel: '../api/novel.php',
            genre: '../api/genre.php',
            author: '../api/author.php',
            rating: '../api/rating.php',
        };
        let genreList = [];
        document.querySelectorAll('input[name="lead"]').forEach(radio => {
            radio.addEventListener('change', function () {
                console.log(`Selected lead: ${this.value}`);
                // You can filter novels or update UI here
            });
        });


        async function loadGenres2() {
            try {
                const res = await axios.get(API.genre + '?list=1');
                //Check if response data is an array
                if (Array.isArray(res.data)) {
                    //store in genreList
                    genreList = res.data;
                    console.log(genreList);
                    const filterSelect = document.getElementById('filter-genre');

                    //For each element in genreList, add the option name as well as the value
                    for (const g of genreList) {
                        const opt = new Option(g.nv_genre_name, g.nv_genre_id);
                        filterSelect.add(opt);
                    }
                }
                console.log("Success");
            } catch (e) {
                console.error('Failed to load genres', e);
            }
        }

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
                        //TODO Fetch Author name
                        console.log(genre.nv_genre_id);
                        return `<button class="genre-button" onclick="loadNovels('${genre.nv_genre_id}')">${genre.nv_genre_name}</button>`;

                    }).join('');
                }
                console.log("Success");
            } catch (e) {
                console.error('Failed to load genres', e);
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

        async function loadNovels(genreId) {
            //Declare container 
            const box = document.getElementById('novel-list');
            try {
                //Fetch all books and genre
                const genreMapping = await loadAllMappings();
                //Fetch all genre id, map to assoc array [{genre_id => genre_value}], and then to object {genre_id : genre_value}
                const genreMap = await loadGenreMap();

                //Get genre request, and append to the Request Param
                const requestParam = '?genre_id=' + genreId;

                //call genre API to fetch books based on the genre_id
                const { data } = await axios.get(API.genre + requestParam);

                if (!data.length) {
                    box.innerHTML = '<p>No novels found for selected genre.</p>';
                    return;
                }

                box.innerHTML = (await Promise.all(data.map(async (novel) => {
                    console.log(novel.nv_novel_title);

                    // Fetch average rating of the book
                    const requestParam = "?nv_novel_id=" + novel.nv_novel_id; // Added '=' after the key
                    const ratingResponse = await axios.get(API.rating + requestParam); // fixed variable name
                    const ratingData = ratingResponse.data;
                    console.log(ratingData);
                    const avgRating = ratingData.length ? ratingData[0].average_rating : "N/A";
                    // Limit description length
                    let modifiedDesc = novel.nv_novel_description;
                    if (modifiedDesc.length > 20) {
                        modifiedDesc = modifiedDesc.substring(0, 20) + '...';
                    }
                    console.log(novel);
                    console.log(modifiedDesc);
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

        loadGenres();
        // loadNovels();
    </script>
</body>

</html>