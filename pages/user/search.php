<?php
session_start();
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
            .libraryBtn,
            .fakeLibraryBtn {
                padding: 10px 30px;
                border-radius: 8px;
                border: 0px;
                cursor: pointer;
                text-decoration: none;
                border: 1px solid black;
            }

            .readBtn {
                background-color: #DB6D29;
                color: white;
                margin-right: 20px;
            }

            .libraryBtn,
            .fakeLibraryBtn {
                background-color: white;
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
</body>


<script>
    //API Paths
    const API = {
        novel: '<?php echo NOVEL_API ?>',
        genre: '<?php echo GENRE_API ?>',
        author: '<?php echo AUTHOR_API ?>',
        rating: '<?php echo RATING_API ?>',
        library: '<?php echo LIBRARY_API ?>',
    };
    //Get userID
    const userID = <?php echo json_encode(($userRole == 'user') ? $_SESSION['user_id'] : null); ?>;


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
            //Get book title
            const requestParam = `?nv_novel_title=${searchQuery}`;

            //call novel API to fetch books based on the title
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
                htmlHead = `
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
                                    </a> `

                if (!userID) {
                    buttonContent = `<a class="fakeLibraryBtn" onclick="togglePopup('popup-container')">+ Add to Library </a>`
                } else {
                    buttonContent = await generateLibraryButton(novel.nv_novel_id);
                }

                htmlTail = `</div></div></div> `;

                return htmlHead + buttonContent + htmlTail;
            }))).join('');
        } catch (ex) {
            box.innerHTML = ex.response?.data?.error || '<p>Error loading novels<p>';
        }
    }

    //Check if the user has the book in library
    async function generateLibraryButton(novelId) {
        //If user is not logged in, ignore
        if (!userID) {
            return;
        }

        try {
            const requestParam = `?nv_user_id=${userID}&nv_novel_id=${novelId}`;
            const res = await axios.get(API.library + requestParam);
            console.log(res.data);
            if (!res.data.length) {
                return `<a class="libraryBtn" onclick="addToLibrary('${novelId}')">+ Add to Library </a>`;
            } else {
                return `<a class="libraryBtn" onclick="removeFromLibrary('${novelId}')">Remove from Library</a>`;
            }

        } catch (ex) {
            errMessage = ex.response?.data?.error || 'Error Modifying library';
            console.log(errMessage);
        }
    }

    async function addToLibrary(novelId) {
        try {
            const requestParam = `?nv_novel_id=${novelId}`;
            console.log(API.library + requestParam);
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



    async function deleteFromLibrary(novelId) {
        try {
            //Get library id
            const requestParam = `?nv_user_id=${userID}&nv_novel_id=${novelId}`;
            const { data } = await axios.get(API.library + requestParam);
            const libraryID = data.nv_user_library_id;

            //Remove from library
            const res = await axios.delete(`${API.library}?nv_user_library_id=${libraryID}`);
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

    loadNovels()
</script>
</body>

</html>