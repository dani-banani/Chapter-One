<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>
<style>
    body {
        display: flex;
        flex-direction: column;
    }

    main {
        padding: 40px 80px;
        flex: 1
    }

    .container {
        padding: 20px;
        border-radius: 10px;
        box-shadow: 1px 1px 5px 1px rgba(0, 0, 0, 0.3);
        min-height: 400px;
        display: flex;
        flex-direction: row;
        gap: 20px;
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

        button {
            justify-content: center;
            width: 100%;
        }

        .edit-novel-button {
            display: none;
        }
    }
</style>
<script>
    let genreOptions = [];
    let selectedGenreIds = [];

    let defaultNovelGenreIds = []
    let defaultGenreTitle;
    let defaultGenreDescription;

    async function loadGenreOptions() {
        const genreMapping = await axios.get('<?php echo GENRE_API; ?>?list');
        const genreDropdown = document.querySelector('.genre-dropdown');
        const genreDropdownContainer = document.querySelector('.genre-dropdown-container');
        genreOptions = genreMapping.data;
        for (const genre of genreOptions) {
            const genreOption = document.createElement('div');
            genreOption.classList.add('genre-option');
            genreOption.id = genre.nv_genre_id;
            genreOption.innerHTML = genre.nv_genre_name;
            genreDropdownContainer.appendChild(genreOption);
        }
    }

    async function loadNovel(novelId) {
        const novel = await axios.get('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        });
        defaultGenreTitle = novel.data[0].nv_novel_title;
        defaultGenreDescription = novel.data[0].nv_novel_description;

        const title = document.getElementById('novel-title');
        const novelDescription = document.getElementById('novel-description');
        title.value = defaultGenreTitle;
        novelDescription.value = defaultGenreDescription;

        const novelGenres = await axios.get(`<?php echo GENRE_API; ?>?novel_id=${novelId}`);
        defaultNovelGenreIds = novelGenres.data.map((genre) => genre.nv_genre_id);

        const selectedGenreList = document.querySelector('.selected-genre-list');

        if (defaultNovelGenreIds.length == 0) {
            selectedGenreList.innerHTML = '<li>None</li>';
        } else {
            selectedGenreList.innerHTML = '';
        }

        defaultNovelGenreIds.forEach((id) => appendGenreToList(id, genreOptions.find((option) => option.nv_genre_id == id).nv_genre_name));
    }

    async function createNovel(novelTitle, novelDescription) {
        const novelCreationResponse = await axios.post('<?php echo NOVEL_API; ?>', {
            nv_novel_title: novelTitle,
            nv_novel_description: novelDescription,
        });

        if (novelCreationResponse.status != 200) {
            alert('Failed to create novel');
            return false;
        }

        for (const genreId of selectedGenreIds) {
            const genreMappingResponse = await axios.post('<?php echo GENRE_API; ?>', {
                nv_novel_id: novelCreationResponse.data.id,
                nv_genre_id: genreId
            });

            if (genreMappingResponse.status != 200) {
                alert('Failed to map genre to novel');
                return false;
            }
        }

        return novelCreationResponse.data.id;
    }

    async function editNovel(novelId, novelTitle, novelDescription) {
        const editNovelResponse = await axios.put('<?php echo NOVEL_API; ?>', {
            nv_novel_id: novelId,
            nv_novel_title: novelTitle,
            nv_novel_description: novelDescription,
        });

        if (editNovelResponse.status != 200) {
            alert('Failed to edit novel');
            return false;
        }

        const removeGenreNovelMapping = await axios.delete('<?php echo GENRE_API; ?>', {
            params: {
                novel_id: novelId
            }
        });

        if (removeGenreNovelMapping.status != 200) {
            alert('Failed to remove genre from novel');
            return false;
        }

        for (const genreId of selectedGenreIds) {
            const addGenreNovelMapping = await axios.post('<?php echo GENRE_API; ?>', {
                nv_novel_id: novelId,
                nv_genre_id: genreId
            });

            if (addGenreNovelMapping.status != 200) {
                alert('Failed to map genre to novel');
                return false;
            }
        }

        return true;
    }

    function appendGenreToList(genreId, genreName) {
        if (selectedGenreIds.includes(parseInt(genreId))) {
            return;
        }

        const selectedGenreList = document.querySelector('.selected-genre-list');

        if (selectedGenreIds.length == 0) {
            selectedGenreList.innerHTML = '';
        }

        const genreItem = document.createElement('li');
        genreItem.appendChild(document.createElement('span')).textContent = genreName;
        genreItem.appendChild(document.createElement('i')).classList.add('fa-solid', 'fa-xmark');
        genreItem.style.cursor = 'pointer';

        selectedGenreList.appendChild(genreItem);
        selectedGenreIds.push(parseInt(genreId));

        genreItem.addEventListener('click', (e) => {
            e.stopPropagation();
            genreItem.remove();
            const removedGenreIndex = selectedGenreIds.indexOf(parseInt(genreId));
            selectedGenreIds.splice(removedGenreIndex, 1);

            if (selectedGenreIds.length == 0) {
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

            if (selectedGenreIds.includes(selectedGenre.id)) {
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
                alert('The title and description are required!');
                return;
            }

            isCreatingNovel = true;
            const createdNovelId = await createNovel(novelTitle.value, novelDescription.value);
            if (createdNovelId) {
                alert("Novel succesfully created!")
                window.location.href = '<?php echo AUTHOR_NOVEL_VIEW_PAGE; ?>?novel_id=' + createdNovelId;
            }
            isCreatingNovel = false;
        });
    }

    async function enableEditNovelButton(novelId) {
        let isEditingNovel = false;
        const editNovelButton = document.querySelector('.edit-novel-button');
        const novelTitle = document.querySelector('#novel-title');
        const novelDescription = document.querySelector('#novel-description');

        editNovelButton.addEventListener('click', async () => {
            if (isEditingNovel) {
                return;
            }

            if (novelTitle.value == '' || novelDescription.value == '') {
                alert('The title and description are required!');
                return;
            }

            if (!changesAreMade(novelTitle.value, novelDescription.value)) {
                alert('No changes are made!');
                return;
            }

            console.log(changesAreMade(novelTitle.value, novelDescription.value));

            isEditingNovel = true;
            const isNovelEdited = await editNovel(novelId, novelTitle.value, novelDescription.value);
            if (isNovelEdited) {
                alert("Novel succesfully edited!")
                window.location.href = '<?php echo AUTHOR_NOVEL_VIEW_PAGE; ?>?novel_id=' + novelId;
            }
            isEditingNovel = false;
        });
    }

    function loadEditView(novelId) {
        const pageTitle = document.querySelector('.title h1');
        pageTitle.innerHTML = 'Edit Novel';

        const genreSelectionTitle = document.querySelector('.gender-selection h3');
        genreSelectionTitle.innerHTML = 'Edit Book Genre';

        const defaultGenreSelector = document.querySelector('.selected-genre-text');
        defaultGenreSelector.innerHTML = 'Add Genre';

        const createNovelButton = document.querySelector('.create-novel-button');
        createNovelButton.style.display = 'none';
        const editNovelButton = document.querySelector('.edit-novel-button');
        editNovelButton.style.display = 'block';

        loadNovel(novelId);
        enableEditNovelButton(novelId);
    }

    function changesAreMade(novelTitle, novelDescription) {
        if (defaultGenreTitle != novelTitle) {
            return true;
        }
        if (defaultGenreDescription != novelDescription) {
            return true;
        }

        if (defaultNovelGenreIds.length != selectedGenreIds.length) {
            return true;
        }

        for (const genreId of selectedGenreIds) {
            if (defaultNovelGenreIds.includes(genreId)) {
                continue;
            }
            return true;
        }

        return false;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        const novelId = new URLSearchParams(window.location.search).get('novel_id');
        if (novelId) {
            loadEditView(novelId);
        }

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
            <h1>Create Your New Novel</h1>
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
                </div>
                <button class="create-novel-button">
                    Create Novel
                </button>
                <button class="edit-novel-button">
                    Edit Novel
                </button>
            </div>
        </div>
    </main>
    <?php require_once FOOTER_COMPONENT; ?>
</body>

</html>