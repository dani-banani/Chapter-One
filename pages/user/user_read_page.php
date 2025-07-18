<?php
require_once HTML_HEADER;
?>
<style>
    header {
        height: 60px;
        justify-content: space-between;
    }

    .wrapper {
        margin-left: auto;
        margin-right: auto;
        width: 1000px;
        margin-top: 30px;
    }

    #navbar-title {
        flex: 0.4;
    }

    #return {
        flex: 1;
        text-align: center;

        a {
            display: inline;
            font-size: 18px;
            font-weight: bold;
        }
    }

    #navbar-links ul {
        align-items: center;
        padding: 0px;
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
        margin-top: 30px;
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
        margin-top: 50px;
        margin-bottom: 10px;
    }

    #novel-title,
    #novel-author {
        font-size: 18px;
        font-weight: bold;
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

    #content {
        min-height: 100vh;
    }
</style>
</head>

<body>
    <header style="position:fixed;top:0;z-index:999;">
        <div id="navbar-title">
            <h1>Chapter One</h1>
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

            <div id="intro">
                <div id='novel-img'>
                    <img src='img/question.png' />
                </div>
                <p id="novel-title">Loading....</p>
                <p stle="font-size:9x;color:gray;">Author: <span id="novel-author">Author</span></p>
                <p class="deco">Have Fun Reading!</p>
            </div>


            <div id="top-anchor"></div>
            <div id="content"></div>
            <div id="bottom-anchor"></div>
        </main>

        <aside id="utilities">
            <div id="tools">
                <a id="chapterMenuBtn"><i class="fa-solid fa-list"></i></a>
                <a><i class="fa-solid fa-gear"></i></a>
                <a id="returnBtn"><i class="fas fa-sign-out-alt"></i></a>
            </div>
            <div id="chapterMenu" class="hidden">
                <h1>Table of Content</h1>
                <ul id="chapterList"></ul>
            </div>
        </aside>
    </div>

    <script>
        const API = {
            novel: '/chapter-one/api/novel.php',
            genre: '/chapter-one//api/genre.php',
            author: '/chapter-one/api/author.php',
            rating: '/chapter-one/api/rating.php',
            novel_chapter: '/chapter-one/api/novel_chapter.php',
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
                const returnBtn = document.getElementById('returnBtn');
                const title = document.getElementById('novel-title');
                const authorName = document.getElementById('novel-author');

                //Fetch novel information
                const { data } = await axios.get(`${API.novel}?nv_novel_id=${novelId}`);
                const novel = data[0];

                //Fetch author information
                const res = await axios.get(`${API.author}?id=${novel.nv_author_id}`);
                const author = res.data

                //Update container value 
                title.innerHTML = novel.nv_novel_title;
                authorName.innerHTML = author.nv_author_username;

                //Change webpage title name
                document.title = novel.nv_novel_title;

                //Set returnBtn href to go to book details page
                returnBtn.href = `book_details.php?nv_novel_id=${novelId}`;

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
        let isLoadingChapter = false;

        //Function to initialise
        async function init() {
            lastChapter = await getChapterCount();
            //Populate first chapter on load
            const chapter = await fetchChapter(currChapter);
            const title = chapter[0].nv_novel_chapter_title;
            const chapterNum = chapter[0].nv_novel_chapter_number;
            const content = chapter[0].nv_novel_chapter_content;
            contentBox.innerHTML = `<div class='chapterContainer' id="chapter-${chapterNum}">
                <p class='chapterTitle'>${title}</p>
                <p class=\"text\">${content} Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                </p>
                <p class="deco">Have Fun Reading!</p></div>`;
        }

        //Function to load specific chapter, for the table of contents
        async function loadSpecificChapter(target_chapter) {
            if (isLoadingChapter) return; // Prevent multiple simultaneous loads
            isLoadingChapter = true;

            try {
                //Hide Chapter intro section
                (target_chapter != 1) ? document.getElementById('intro').style.display = 'none' : document.getElementById('intro').style.display = 'block';

                //Clear existing content
                document.querySelectorAll('.chapterContainer').forEach(content => content.remove());

                if (target_chapter > lastChapter || target_chapter <= 0) {
                    console.log("ERROR!");
                    return;
                }

                const chapter = await fetchChapter(target_chapter);
                const title = chapter[0].nv_novel_chapter_title;
                const content = chapter[0].nv_novel_chapter_content;

                const htmlContent = `<div class='chapterContainer' id="chapter-${target_chapter}">
                    <p class='chapterTitle'>${title}</p>
                    <p class="text">${content}Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                    Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                    Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                    Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                    Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                    Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                    Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.
                    </p>
                <p class="deco">Have Fun Reading!</p></div>`;

                contentBox.insertAdjacentHTML('beforeend', htmlContent);


                // Update currChapter AFTER successful load
                currChapter = target_chapter;

                // Reset observer state based on new current chapter
                resetObserverState();

            } catch (error) {
                console.error('Error loading chapter:', error);
            } finally {
                isLoadingChapter = false;
            }
        }


        async function fetchChapter(chapter_num) {
            //Get specific chapters with chapter number and novel id 
            const filter = '?nv_novel_chapter_number=' + chapter_num;
            const { data } = await axios.get(`${API.novel_chapter}${filter}`);
            const chapter = data;

            //Reveal the chapter heading when the chapter number is 1
            if (chapter_num == 1) document.getElementById('intro').style.display = 'block';
            return chapter;
        }


        //Function to load next or previous chapter
        async function populateChapter(isNext) {
            const chapter = isNext ? await fetchChapter(currChapter + 1) : await fetchChapter(currChapter - 1);
            const title = chapter[0].nv_novel_chapter_title;
            const chapterNum = chapter[0].nv_novel_chapter_number;
            const content = chapter[0].nv_novel_chapter_content;
            return `<div class='chapterContainer' id="chapter-${chapterNum}">
                <p class='chapterTitle'>${title}</p>
                <p class=\"text\">${content}Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.

                Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.</p>
                <p class="deco">Have Fun Reading!</p></div>`;
        }


        //Declare anchors
        const topAnchor = document.getElementById('top-anchor');
        const bottomAnchor = document.getElementById('bottom-anchor');

        const observerOptions = {
            //Use viewport as container for detection
            root: null,
            //Trigger as soon as page is loaded
            rootMargin: '30px',
            //Trigger when 50% of the element is visible
            threshold: 0.1,
        }

        // Function to reset observer state based on current chapter
        function resetObserverState() {
            // Re-observe anchors based on current chapter position
            if (currChapter > 1) {
                observer.observe(topAnchor);
            } else {
                observer.unobserve(topAnchor);
            }

            if (currChapter < lastChapter) {
                observer.observe(bottomAnchor);
            } else {
                observer.unobserve(bottomAnchor);
            }
        }

        const observer = new IntersectionObserver(async (entries) => {
            const chapterTitle = document.getElementById('chapter-title');
            chapterTitlte = currChapter;
            if (isLoadingChapter) return; // Don't trigger if we're already loading

            for (const entry of entries) {
                if (!entry.isIntersecting) continue;

                //Observe when scrolling up
                if (entry.target.id === 'top-anchor') {
                    if (currChapter <= 1) {
                        observer.unobserve(topAnchor);
                        continue;
                    } else if (document.getElementById(`chapter-${currChapter - 1}`)) {




                        console.log(currChapter);
                        currChapter = currChapter - 1;
                        continue;
                    }

                    isLoadingChapter = true;
                    try {
                        const prevChapterByOne = await populateChapter(false);
                        if (prevChapterByOne) {
                            // Append content...
                            contentBox.insertAdjacentHTML('afterbegin', prevChapterByOne);
                            //Set window scroll slightly below for smoother transition
                            window.scrollBy(0, 150);
                            //Reduce current chapter by 1
                            currChapter--;
                            // Update observer state
                            if (currChapter <= 1) {
                                observer.unobserve(topAnchor);
                                //Increase current chapter by 1 to prevent going to 0
                                currChapter++;
                            }
                        }
                    } finally {
                        isLoadingChapter = false;
                    }
                }

                //Observer when scrolling down
                if (entry.target.id === 'bottom-anchor') {
                    /*
                        - Perform two checks
                            i) Check if the current chapter is bigger or equals to the last chapter, stop observer from triggering more
                            ii) If the next chapter already exist in page (previous chapter after user scrolls up) then increment by 1 and ignore, used to fix a bug where when user select chapter below and then scroll up,
                                the currChapter will be changed and then populate the same chapter again
                    */
                    if (currChapter >= lastChapter) {
                        observer.unobserve(bottomAnchor);
                        continue;

                    } else if (document.getElementById(`chapter-${currChapter + 1}`)) {
                        console.log(currChapter);
                        currChapter = currChapter + 1;
                        continue;
                    }

                    isLoadingChapter = true;
                    try {
                        const nextChapter = await populateChapter(true);
                        if (nextChapter) {
                            window.curr
                            contentBox.insertAdjacentHTML('beforeend', nextChapter);
                            //Set window scroll slightly above for smoother transition
                            window.scrollBy(0, -50);
                            currChapter++;

                            // Update observer state
                            if (currChapter >= lastChapter) {
                                observer.unobserve(bottomAnchor);
                            }
                        }
                    } finally {
                        isLoadingChapter = false;
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

        (async function main() {
            await init(); // wait until init is fully complete
            loadChaptersToMenu();
            loadNovelDetail();

            // Then load specific chapter if param exists
            const params = new URLSearchParams(window.location.search);
            const chapterParam = params.get('nv_novel_chapter_number');

            if (chapterParam && !isNaN(chapterParam)) {
                currChapter = parseInt(chapterParam);
                await loadSpecificChapter(currChapter);
            }
        })();
    </script>
</body>

</html>