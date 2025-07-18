<?php
require_once __DIR__ . '/../../paths.php';
require_once HTML_HEADER;
?>
<style>
    .wrapper {
        margin-left: auto;
        margin-right: auto;
        max-width: 1000px;
        margin-top: 30px;
    }

    #searchContainer {
        display: flex;
        gap: 50px;
        margin-top: 50px;
    }


    .novel-container {
        display: grid;
        grid-template-columns: 140px auto;
        flex-direction: row;
        column-gap: 20px;

        .novel-img {
            grid-column: 1/2;
            background-color: black;
            height: 150px;
            width: 110px;
            padding: 10px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .novel-details {
            grid-column: 2/3;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;

            .novel-title {
                font-size: 18px;
                font-weight: bold;
                margin: 0;
            }

            .novel-title a {
                text-decoration: none;
            }

            #button-container {
                margin-top: 40px;
            }

            .readBtn,
            .libraryBtn {
                padding: 10px 15px;
                border-radius: 8px;
                border: 0px;
                cursor: pointer;
                text-decoration: none;
                font-size: 13px;
            }

            .readBtn {
                background-color: #DB6D29;
                color: white;
                margin-right: 20px;
            }

            .libraryBtn {
                background-color: white;
                border: 1px solid #DB6D29;
            }

            .novel-description {
                margin: 0;
            }
        }
    }
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>

    <main>
        <div class="wrapper">
            <h1 id="searchResult">Loading....</h1>
            <hr>
            <div id="searchContainer">

                <div class='novel-container'>
                </div>

            </div>
        </div>
    </main>
</body>


<script>
    //API Paths
    const API = {
        novel: '<?php echo NOVEL_API ?>',
        genre: '<?php echo GENRE_API ?>',
        author: '<?php echo AUTHOR_API ?>',
        rating: '<?php echo RATING_API ?>',
    };

    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['searchQuery'])): ?>
        const searchQuery = <?php echo json_encode($_POST['searchQuery']); ?>;
    <?php else: ?>
        const searchQuery = null;
    <?php endif; ?>

    async function loadNovels() {
        //Declare container 
        const box = document.getElementById('searchContainer');
        const resultCount = document.getElementById('searchResult');
        //Change webpage title
        document.title = searchQuery;

        try {
            // //Fetch all books and genre
            // const genreMapping = await loadAllMappings();
            // //Fetch all genre id, map to assoc array [{genre_id => genre_value}], and then to object {genre_id : genre_value}
            // const genreMap = await loadGenreMap();

            //Get genre request, and append to the Request Param
            const requestParam = `?nv_novel_title=${searchQuery}`;

            //call novel API to fetch books based on the genre_id with the status of published
            console.log("Query" + API.novel + requestParam)
            const { data } = await axios.get(API.novel + requestParam);
            console.log(data);

            //Add result count to UI
            resultCount.innerHTML = `${data.length} Results related to <i>${searchQuery}<i>`;
            if (!Array.isArray(data) || data.length === 0) {
                //Add align-items:center to parent
                box.style.justifyContent = 'center';

                box.innerHTML = `
                <div style='display:flex;flex-direction:column;justify-content:center;'>
                <img src='../img/crying-book.png' alt='crying book emoji' height='200px' width='200px' style='margin:50px auto;'>
                    <h2 style='margin:auto;padding:0px;width:fit-content;text-align:center'>Please ensure that you're typing in the correct title</h2>
                </div>
                `;
                return;
            }

            box.innerHTML = (await Promise.all(data.map(async (novel) => {
                // Fetch average rating of the book
                const requestParam = "?nv_novel_id=" + novel.nv_novel_id;
                const ratingResponse = await axios.get(API.rating + requestParam);
                const ratingData = ratingResponse.data;
                console.log(ratingData);
                const avgRating = ratingData.length ? ratingData[0].average_rating : "N/A";

                // Limit description length
                let modifiedDesc = novel.nv_novel_description;
                if (modifiedDesc.length > 50) {
                    modifiedDesc = modifiedDesc.substring(0, 50) + '...';
                }
                return `
                        <div class='novel-container'>
                            <div class='novel-img'>
                                <img src='../img/question.png' />
                            </div>

                            <div class='novel-details'>
                                <h3 class='novel-title'>
                                    <a href='book_details.php?nv_novel_id=${novel.nv_novel_id}'>
                                        ${novel.nv_novel_title}
                                    </a>
                                </h3>

                                <div class='novel-description'>
                                    ${modifiedDesc}
                                </div>

                                <div id="button-container">
                                    <a class="readBtn" href="user_read_page.php?nv_novel_id=${novel.nv_novel_id}&nv_novel_chapter_number=1">
                                        Read Now
                                    </a>
                                    <a class="libraryBtn" data-novel-id="${novel.nv_novel_id}">
                                        + Add to Library
                                    </a>
                                </div>
                            </div>
                        </div>
                        `;

            }))).join('');
        } catch (ex) {
            box.innerHTML = ex.response?.data?.error || '<p>Error loading novels<p>';
        }

    }

    loadNovels()
</script>
</body>

</html>