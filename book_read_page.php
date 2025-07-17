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
        width: 1000px;
        margin-top: 30px;
    }

    #navbar-title {
        flex: 0.4;
    }

    #breadcrumbs {
        flex: 0.6;

        a,
        p {
            display: inline;
            font-size: 18px;
            font-weight: bold;
        }
    }

    #navbar-links ul {
        align-items: center;
    }

    #libraryBtn {
        padding: 10px 20px;
        border-radius: 8px;
        border: 0px;
        cursor: pointer;
        background-color: #DB6D29;
        color: white;
        text-decoration: none;
    }

    body {
        background-color: gray;
    }

    main {
        background-color: white;
        width: 100%;
        display: block;
    }


    section {
        width: 900px;
    }

    #intro {
        display: flex;
        flex-direction: column;
        justify-items: center;
    }

    #intro p {
        text-align: center;

    }

    #novel-img {
        background-color: black;
        height: 300px;
        width: 220px;
        padding: 10px;
        border-radius: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 50px auto;
    }

    .deco {
        width: 300px;
        height: max-content;
        position: relative;
        text-align: center;
        margin: 20px auto;
        opacity: 0.5;
    }

    .deco::after {
        content: '';
        width: 100%;
        height: 2px;
        background: black;
        position: absolute;
        bottom: 7px;
        left: 220px;
    }

    .deco::before {
        content: '';
        width: 100%;
        height: 2px;
        background: black;
        position: absolute;
        bottom: 7px;
        right: 220px;
    }

    .chapterContainer {
        width: 700px;
        margin: auto;

        .chapterTitle {
            font-size: 30px;
            font-weight: bold;
        }

        .chapterContent {
            line-height: 1.5;
            text-align: justify;
            margin: auto;
        }
    }


    .flexwrap {
        display: flex;
        flex-direction: row;
        column-gap: 50px;
    }

    #utilities {
        flex: 0.5;
        top: 100px;
        position: sticky;
        align-self: flex-start;
        background-color: #DB6D29;
        padding: 30px 10px;
        border-radius: 12px;

        #tools {
            display: flex;
            gap: 40px;
            flex-direction: column;
            font-size: 20px;
        }
    }

    #anchor {
        height: 1px;
    }

    #chapterMenu {
        position: absolute;
        top: 0px;
        right: 40px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        padding: 10px;
        z-index: 1000;
        width: 200px;
        max-height: 300px;
        overflow-y: auto;
    }

    #chapterMenu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #chapterMenu ul li {
        padding: 8px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        color: black;
    }

    #chapterMenu ul li:hover {
        background-color: #f0f0f0;
    }

    .hidden {
        display: none;
    }
</style>
</head>

<body>
    <header>
        <div id="navbar-title">
            <h1>Chapter One</h1>
        </div>
        <div id="breadcrumbs">
            <a id="novel-title">Loading....</a>
            <p style="font-size:24px;margin: 0 5px;">/</p>
            <p id="chapter-title">Loading....</p>
        </div>
        <nav id="navbar-links">
            <ul>
                <li><a>Library</a></li>
                <li> <a id="libraryBtn">+ Add to Library</a></li>
            </ul>
            <div id="navbar-profile-pic">
                <img src="https://picsum.photos/50" alt="Profile Picture">
            </div>
        </nav>
    </header>

    <div class="wrapper flexwrap">
        <main>
            <div id="chapter">
                <div id="intro">
                    <div id='novel-img'>
                        <img src='img/question.png' />
                    </div>
                    <p id="section-title">Loading....</p>
                    <p stle="font-size:9x;color:gray;">Author: <span id="novel-author">Author</span></p>

                    <p class="deco">Have Fun Reading!</p>
                </div>
            </div>
            <div id="top-anchor"></div>
            <div id="content">
                <div class="chapterContainer"></div>
                <div id="specificChapter"></div>
            </div>
            <div id="bottom-anchor"></div>
        </main>

        <aside id="utilities">
            <div id="tools">
                <a id="chapterMenuBtn"><i class="fa-solid fa-list"></i></a>
                <a><i class="fa-solid fa-gear"></i></a>
            </div>
            <div id="chapterMenu" class="hidden">
                <ul id="chapterList"></ul>
            </div>
        </aside>
    </div>


    <script>
        const API = {
            novel: '../api/novel.php',
            genre: '../api/genre.php',
            author: '../api/author.php',
            rating: '../api/rating.php',
            novel_chapter: '../api/novel_chapter.php',
        };

        const contentBox = document.getElementById('content');


        //Get current URL
        const params = new URLSearchParams(window.location.search);

        //Get novel ID from request param
        const novelId = params.get('nv_novel_id');

        async function loadNovelContent() {
            try {
                //Get current URL
                const params = new URLSearchParams(window.location.search);

                //Get novel ID from request param
                const novelId = params.get('nv_novel_id');
            } catch (ex) {

            }
        }

        async function loadNovelDetail() {
            try {
                //Declare containers
                const title = document.getElementById('novel-title');
                const title2 = document.getElementById('section-title');
                const authorName = document.getElementById('novel-author');

                //Fetch novel information
                const { data } = await axios.get(`${API.novel}?nv_novel_id=${novelId}`);
                const novel = data[0];

                //Fetch author information
                const res = await axios.get(`${API.author}?id=${novel.nv_author_id}`);
                const author = res.data

                //Update container value 
                title.innerHTML = novel.nv_novel_title;
                title2.innerHTML = novel.nv_novel_title;
                authorName.innerHTML = author.nv_author_username;


            } catch (ex) {
                errMessage = ex.response?.data?.error || 'Error loading novels';
                console.log(errMessage);
            }
        }

        async function getNovelChapters() {
            //Get container for chapter
            const box = document.getElementById('chapterArea');

            //Get all chapters of a novel using novel id
            const filter = 'nv_novel_id=' + novelId;
            const { data } = await axios.get(`${API.novel_chapter}?${filter}`);
            console.log(data.length);
            box.innerHTML = data.map(chapter => {
                console.log(chapter);
            });
        }

        //Function to get the total count of chapters
        async function getChapterCount() {
            try {
                //Get all of the chapters of the novel
                const filter = 'nv_novel_id=' + novelId;
                const count = await axios.get(`${API.novel_chapter}?${filter}`);
                return count.data.length;
            } catch (error) {
                console.error('Error loading chapters', error);
            }
        }

        //Set current and last chapter
        let currChapter = 1;
        let lastChapter;

        //Function to initialise
        async function init() {
            lastChapter = await getChapterCount();

            //Populate first chapter on load
            const chapter = await fetchChapter(currChapter);
            const title = chapter[0].nv_novel_chapter_title;
            const content = chapter[0].nv_novel_chapter_content;
            contentBox.innerHTML = `<div class='chapterContainer'><p class='chapterTitle'>${title}</p><p class=\"text\">${content}</p><p class="deco">Have Fun Reading!</p></div>`;
        }


        //Function to load specific chapter, for the table of contents
        async function loadSpecificChapter(target_chapter) {
            //Clear existing content
            document.querySelectorAll('.chapterContainer').forEach(content => content.remove());
            const specificBox = document.getElementById('specificChapter');

            if (target_chapter > lastChapter || target_chapter <= 0) return console.log("ERROR!");
            const chapter = await fetchChapter(target_chapter);
            const title = chapter[0].nv_novel_chapter_title;
            const content = chapter[0].nv_novel_chapter_content;

            specificBox.innerHTML = `<div class='chapterContainer' id="chapter-${target_chapter}"><p class='chapterTitle'>${title}</p><p class=\"text\">${content}</p><p class="deco">Have Fun Reading!</p></div>`;
            document.getElementById(`chapter-${target_chapter}`).scrollIntoView({ behavior: 'smooth' });
            currChapter = target_chapter;
        }


        async function fetchChapter(chapter_num) {
            //Get specific chapters with chapter number and novel id 
            const filter = '?nv_novel_chapter_number=' + chapter_num;
            const { data } = await axios.get(`${API.novel_chapter}${filter}`);
            const chapter = data;
            return chapter;
        }


        //Function to load next or previous chapter
        async function populateChapter(isNext) {
            const chapter = isNext ? await fetchChapter(currChapter + 1) : await fetchChapter(currChapter - 1);
            const title = chapter[0].nv_novel_chapter_title;
            const content = chapter[0].nv_novel_chapter_content;
            return `<div class='chapterContainer'><p class='chapterTitle'>${title}</p><p class=\"text\">${content}</p><p class="deco">Have Fun Reading!</p></div>`;
        }


        //Declare anchors
        const topAnchor = document.getElementById('top-anchor');
        const bottomAnchor = document.getElementById('bottom-anchor');

        const observerOptions = {
            //Use viewport as container for detection
            root: null,
            //Trigger as soon as page is loaded
            rootMargin: '0px',
            //Trigger when 50% of the element is visible
            threshold: 0.1,
        }

        const observer = new IntersectionObserver(async (entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue;

                if (entry.target.id === 'top-anchor') {
                    if (currChapter <= 1) {
                        observer.unobserve(topAnchor); // Stop observing if first chapter
                        continue;
                    }

                    const prevChapter = await populateChapter(false);
                    if (prevChapter) {
                        contentBox.insertAdjacentHTML('afterbegin', prevChapter);
                        currChapter--; // Only decrement if loaded
                    }
                }

                if (entry.target.id === 'bottom-anchor') {
                    console.log(currChapter);
                    if (currChapter >= lastChapter) {
                        observer.unobserve(bottomAnchor); // Stop observing if last chapter
                        continue;
                    }

                    const nextChapter = await populateChapter(true);
                    if (nextChapter) {
                        contentBox.insertAdjacentHTML('beforeend', nextChapter);
                        currChapter++; // Only increment if loaded
                    }
                }
            }
        }, observerOptions);


        observer.observe(topAnchor);
        observer.observe(bottomAnchor);



        //Declare menu for tools
        const chapterMenuBtn = document.getElementById('chapterMenuBtn');
        const chapterMenu = document.getElementById('chapterMenu');
        const chapterList = document.getElementById('chapterList');

        // Toggle menu visibility
        chapterMenuBtn.addEventListener('click', () => {
            chapterMenu.classList.toggle('hidden');
        });

        // Hide menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!chapterMenu.contains(event.target) && !chapterMenuBtn.contains(event.target)) {
                chapterMenu.classList.add('hidden');
            }
        });


        // Load chapters into menu
        async function loadChaptersToMenu() {
            let counter = 1;
            try {
                //Get all of the chapters of the novel
                const filter = 'nv_novel_id=' + novelId;
                const { data } = await axios.get(`${API.novel_chapter}?${filter}`);
                chapterList.innerHTML = ''; // Clear any existing
                console.log(data);
                //For each array in data, create a list with anchor 
                data.forEach(chapter => {

                    const li = document.createElement('li');
                    const link = document.createElement('a');

                    link.onclick = () => loadSpecificChapter(chapter.nv_novel_chapter_number);
                    link.innerHTML = "Chapter " + counter;
                    link.style.textDecoration = 'none';

                    li.appendChild(link);
                    chapterList.appendChild(li);
                    counter++;
                });
            } catch (error) {
                console.error('Error loading chapters', error);
            }
        }

        init();
        loadChaptersToMenu();
        loadNovelDetail();
    </script>
</body>

</html>