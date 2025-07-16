<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<style>
    main {
        padding: 40px 80px;
    }

    .container {
        padding: 20px;
        border-radius: 10px;
        box-shadow: 1px 1px 5px 1px rgba(0, 0, 0, 0.3);
        min-height: 400px;
        display: flex;
        flex-direction: row;
        gap: 20px;
        /* gap: 40px; */
    }

    .title-description-col {
        display: flex;
        flex-direction: column;
        flex: 4;

        #novel-description,
        #novel-title {
            padding: 20px;
            font-size: 16px;
            resize: none;
            border: none;
            border-radius: 10px;
            font-family: var(--font-family);
        }

        #novel-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        #novel-description {
            height: 100%;
        }

        #novel-title:focus,
        #novel-description:focus {
            outline: 1px solid black;
        }
    }

    .cover-genre-col {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        .gender-selection {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            width: 100%;

            .genre-dropdown {
                flex: 1;
                display: flex;
                justify-content: space-between;
                position: relative;
                border: 1px solid black;
                cursor: pointer;
                padding: 5px;
                border-radius: 5px;
                width: 100%;
                box-sizing: border-box;
            }

            .genre-dropdown-container {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                box-sizing: border-box;
                max-height: 150px;
                overflow: scroll;
                overflow-x: hidden;
                border: 1px solid black;
                background-color: white;

                .genre-option {
                    padding: 5px;
                    cursor: pointer;
                }

                .genre-option:hover {
                    background-color: var(--primary-color);
                }
            }

            .selected-genre-list-container {
                width: 100%;
                box-sizing: border-box;
                display: flex;
                gap: 10px;
                flex-direction: column;
                justify-content: left;
            }

            .selected-genre-list {
                list-style-type: none;
                margin: 0;
                padding: 0;
                width: 100%;
                border: 1px solid black;
                border-radius: 5px;
                padding: 5px 10px;
                box-sizing: border-box;

                li {
                    width: 100%;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px;
                    border-bottom: 1px solid black;

                    i {
                        color: var(--danger-color);
                    }
                }

                li:last-child {
                    border-bottom: none;
                }
            }
        }

        .create-novel-button {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-weight: bold;
            border: 1px solid black;
        }


    }
</style>
<script>
    async function loadGenreOptions() {
        const genreOptions = await axios.get('<?php echo GENRE_API; ?>?list');
        const genreDropdown = document.querySelector('.genre-dropdown');
        const genreDropdownContainer = document.querySelector('.genre-dropdown-container');
        for (const genre of genreOptions.data) {
            const genreOption = document.createElement('div');
            genreOption.classList.add('genre-option');
            genreOption.id = genre.nv_genre_id;
            genreOption.innerHTML = genre.nv_genre_name;
            genreDropdownContainer.appendChild(genreOption);
        }
    }

    async function createNovel(novelTitle, novelDescription, selectedGenreIds) {
        const novelCreationResponse = await axios.post('<?php echo NOVEL_API; ?>', {
            nv_novel_title: novelTitle,
            nv_novel_description: novelDescription,
            nv_author_id: <?php echo $_SESSION['author_id']; ?>,
        });

        if (novelCreationResponse.status != 200){
            alert('Failed to create novel');
            return false;
        }

        for(const genreId of selectedGenreIds){
            const genreMappingResponse = await axios.post('<?php echo GENRE_API; ?>', {
                nv_novel_id: novelCreationResponse.data.id,
                nv_genre_id: genreId
            });

            if (genreMappingResponse.status != 200) {
                alert('Failed to map genre to novel');
                return false;
            }
        }

        return true;
    }


    let selectedGenres = [];

    function appendGenreToList(genreId, genreName) {
        const selectedGenreList = document.querySelector('.selected-genre-list');
        if (selectedGenres.length == 0) {
            selectedGenreList.innerHTML = '';
        }

        const genreItem = document.createElement('li');
        genreItem.appendChild(document.createElement('span')).textContent = genreName;
        genreItem.appendChild(document.createElement('i')).classList.add('fa-solid', 'fa-xmark');
        genreItem.style.cursor = 'pointer';

        selectedGenreList.appendChild(genreItem);
        selectedGenres.push(genreId);

        genreItem.addEventListener('click', (e) => {
            e.stopPropagation();
            genreItem.remove();
            selectedGenres.pop(genreId);

            if (selectedGenres.length == 0) {
                selectedGenreList.innerHTML = '<li>None</li>';
            }
        });
    }

    function enableGenderDropdown() {
        const genreDropdown = document.querySelector('.genre-dropdown');
        const genreDropdownContainer = document.querySelector('.genre-dropdown-container');
        const selectedGenreText = document.querySelector('.selected-genre-text');

        genreDropdown.addEventListener('click', () => {
            if (genreDropdownContainer.style.display == 'block') {
                genreDropdownContainer.style.display = 'none';
                genreDropdown.style.borderRadius = '5px';
                return;
            }

            genreDropdownContainer.style.display = 'block';
            genreDropdown.style.borderRadius = '5px 5px 0 0';
        });

        genreDropdownContainer.addEventListener('click', (e) => {
            e.stopPropagation();
            const selectedGenre = e.target.closest('.genre-option');

            selectedGenreText.textContent = selectedGenre.textContent;
            genreDropdownContainer.style.display = 'none';
            genreDropdown.style.borderRadius = '5px';

            if (selectedGenres.includes(selectedGenre.id)) {
                return;
            }

            appendGenreToList(selectedGenre.id, selectedGenre.textContent);
        });
    }

    function enableCreateNovelButton() {
        let isCreatingNovel = false;
        const createNovelButton = document.querySelector('.create-novel-button');
        const novelTitle = document.querySelector('#novel-title');
        const novelDescription = document.querySelector('#novel-description');

        createNovelButton.addEventListener('click', async () => {
            if (isCreatingNovel) {
                return;
            }

            if (novelTitle.value == '' || novelDescription.value == '') {
                alert('The title and description are required');
                return;
            }

            isCreatingNovel = true;
            const isNovelCreated = await createNovel(novelTitle.value, novelDescription.value, selectedGenres);
            if (isNovelCreated) {
                window.location.href = '<?php echo REAL_AUTHOR_DASHBOARD_PAGE; ?>';
            }
            isCreatingNovel = false;
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await loadGenreOptions();
        enableGenderDropdown();
        enableCreateNovelButton();
    });
</script>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>
    <main>
        <div class="title">

            <h1>Create New Novel</h1>
        </div>
        <div class="container">
            <div class="title-description-col">
                <input type="text" placeholder="Novel Title" id="novel-title">
                <textarea placeholder="Start writing your novel description..." name="novel-description" id="novel-description" rows="10"></textarea>
            </div>
            <hr>
            <div class="cover-genre-col">
                <!-- <div class="novel-cover-img">
                        <img src="https://picsum.photos/200/300" alt="">
                    </div> -->
                <div class="gender-selection">
                    <h3>Add Book Genre</h3>
                    <div class="genre-dropdown">
                        <div class="selected-genre-text">Select Genre</div>
                        <i class="fa-solid fa-chevron-down"></i>
                        <div class="genre-dropdown-container">

                        </div>
                    </div>
                    <div class="selected-genre-list-container">
                        <h5>Selected Genres:</h5>

                        <ul class="selected-genre-list">
                            <li>None</li>
                        </ul>
                    </div>

                    <!-- <select name="genre" id="genre-dropdown">
                                <option selected disabled value="0">Select Genre</option>
                            </select> -->
                    <!-- <button class="add-genre-button"><i class="fa-solid fa-plus"></i>Add Genre</button> -->
                </div>
                <button class="create-novel-button">
                    Create Novel
                </button>
            </div>

        </div>

    </main>
</body>

</html>