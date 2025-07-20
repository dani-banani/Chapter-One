<?php
require_once __DIR__ . '/../../paths.php';
require_once '../../auth/user.php';
require_once HTML_HEADER;
?>
<style>
    main {
        min-height: 81vh;
    }

    .wrapper {
        margin-left: auto;
        margin-right: auto;
        max-width: 1000px;
        margin-top: 30px;
    }

    #libraryContainer {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 50px;
        margin: 50px auto;
    }


    .novel-container {
        display: flex;
        flex-direction: column;
        width: 110px;
        row-gap: 20px;

        .novel-img {
            background-color: black;
            height: 150px;
            width: 100%;
            padding: 10px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .novel-details {

            .novel-title {
                font-size: 18px;
                font-weight: bold;
                margin: 0;
            }

            .novel-title a {
                text-decoration: none;
            }
        }
    }

    #libraryBackground {
        background-color: #fceec6ff;
        padding: 30px;
    }
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>

    <main>
        <div id="libraryBackground">
            <div class="wrapper">
                <h1 id="libraryTitle">Library</h1>
            </div>
        </div>
        <div class="wrapper">
            <div id="libraryContainer">

                <div class='novel-container'>

                    <div class='novel-img'>
                        <img src='../img/question.png' />
                    </div>

                    <div class='novel-details'>
                        <h3 class='novel-title'><a>Loading....</a>
                        </h3>
                    </div>

                </div>

            </div>
        </div>
    </main>
    <?php require_once FOOTER_COMPONENT; ?>


    <script>

        //API Paths
        const API = {
            novel: '<?php echo NOVEL_API ?>',
            library: '<?php echo LIBRARY_API ?>',
        };


        async function loadLibrary() {
            //Declare container 
            const box = document.getElementById('libraryContainer');
            const libraryContainer = document.getElementById('libraryContainer');

            //Change webpage title
            document.title = "Library";
            //Get userID 
            const userID = <?php echo json_encode(($userRole == 'user') ? $_SESSION['user_id'] : null); ?>;

            if (!userID) {
                window.location.href = '<?php echo LOGIN_PAGE ?>';
                return;
            }

            try {
                const { data } = await axios.get(`${API.library}?nv_user_id=${userID}`);

                if (!Array.isArray(data) || data.length === 0) {
                    libraryContainer.style.justifyContent = 'center';
                    box.innerHTML = `
                                    <div style='display:flex;flex-direction:column;justify-content:center;'>
                                        <img src='../img/crying-book.png' alt='crying book emoji' height='200px' width='200px' style='margin:50px auto;'>
                                        <h2 style='margin:auto;padding:0px;width:fit-content;text-align:center'>No book in library, let's start adding them!</h2>
                                    </div>`;
                    return;
                } else {

                    box.innerHTML = (await Promise.all(data.map(async (libraryNovel) => {
                        // Fetch book cover and title 
                        const novels = await axios.get(`${API.novel}?nv_novel_id=${libraryNovel.nv_novel_id}`);
                        const novel = novels.data;


                        // Limit title length
                        let limitedTitle = novel[0].nv_novel_title;
                        console.log(novel.nv_novel_title);
                        if (limitedTitle.length > 30) {
                            limitedTitle = limitedTitle.substring(0, 30) + '...';
                        }
                        return `
                        <div class='novel-container'>
                            <div class='novel-img'>
                                <img src='../img/question.png' />
                            </div>

                            <div class='novel-details'>
                                <h3 class='novel-title'>
                                    <a href='book_details.php?nv_novel_id=${novel[0].nv_novel_id}'>
                                        ${limitedTitle}
                                    </a>
                                </h3>
                            </div>
                    </div>
                        `;

                    }))).join('');
                }
            } catch (ex) {
                console.log(ex);
                box.innerHTML = ex.response?.data?.error || '<p>Error loading library<p>';
            }

        }

        loadLibrary()
    </script>
</body>

</html>