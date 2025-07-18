<?php
require_once __DIR__ . '/../../paths.php';
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
            #libraryBtn {
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

            #libraryBtn {
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

    #chapterSection {
        display: none;

    }


    .chapter-container {
        padding: 20px 10px;
        margin: 50px 10px;
        height: 100px;

        .chapter-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }

        .chapter-content p {
            font-size: 18px;
            margin-bottom: 0px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* number of lines to show */
            line-clamp: 2;
            -webkit-box-orient: vertical;
        }

    }

    .focused {
        text-decoration: underline;
        text-decoration-color: #DB6D29;
    }

    .chapter-container:nth-child(even) {
        background-color: #fff1e9ff;
        border-radius: 8px;
        padding: 10px;
    }

    #chapter-count {
        font-size: 18px;
        font-weight: 500;
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
                    <a id="libraryBtn">
                        + Add to Library
                    </a>
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
        </section>

        <section id="chapterSection">
            <p id="chapter-count">Loading....</p>
            <hr>
            <div id="chapterArea">Loading...</div>
        </section>
    </main>


    <?php require_once FOOTER_COMPONENT; ?>

    <script>
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


        //API Paths
        const API = {
            novel: '<?php echo NOVEL_API ?>',
            genre: '<?php echo GENRE_API ?>',
            author: '<?php echo AUTHOR_API ?>',
            rating: '<?php echo RATING_API ?>',
            novel_chapter: '<?php echo NOVEL_CHAPTER_API ?>',
        };

        let genreList = [];

        //Get current URL
        const params = new URLSearchParams(window.location.search);

        //Get novel ID from request param
        const novelId = params.get('nv_novel_id');
        //Add link to "Read" anchor
        const readBtn = document.getElementById('readBtn');
        readBtn.href = "user_read_page.php?nv_novel_id=" + novelId;

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
                console.log(novel);
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
            console.log(`HELLLO${API.novel_chapter}?${filter}`);
            console.log(data);
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

        loadNovelDetail();
        getNovelChapters();
    </script>
</body>

</html>