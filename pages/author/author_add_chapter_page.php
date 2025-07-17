<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
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
    }

    .title-content-col {
        display: flex;
        flex-direction: column;
        flex: 4;

        #chapter-title {
            padding: 20px 0;
            font-size: 16px;
            resize: none;
            border: none;
            font-family: var(--font-family);
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        #chapter-title:focus {
            outline: none;
        }

        .ql-toolbar {
            border-radius: 10px 10px 0 0;
            max-width: 100%;
        }

        #chapter-content {
            height: 100%;
            font-size: 16px;
            border-radius: 0 0 10px 10px;
            max-width: 100%;
            max-height: 420px;
            overflow-y: scroll;
            overflow-wrap: anywhere;
        }
    }

    .stats-button-col {
        flex: 1;
        padding: 20px 20px 20px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        .stats-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 10px;
        }

        .button-container {
            width: 100%;
        }

        button {
            justify-content: center;
            width: 100%;
        }
    }
</style>
<script>
    let quill;
    async function loadNovel(novelId) {
        const novel = await axios.get('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        });
        const novelTitle = document.querySelector('.novel-title');
        const novelChapter = document.querySelector('.novel-chapter');

        novelTitle.innerHTML = novel.data[0].nv_novel_title;
        // novelChapter.innerHTML = novel.data[0].nv_novel_chapter;
    }

    async function saveChapter(novelId, novelChapterTitle, novelChapterContent) {
        try {

            console.log(novelChapterTitle)
            console.log(novelChapterContent)
            const saveChapterDraft = await axios.post('<?php echo NOVEL_CHAPTER_API; ?>', {
                nv_novel_id: novelId,
                nv_novel_chapter_title: novelChapterTitle,
                nv_novel_chapter_content: novelChapterContent,
            });

            console.log(saveChapterDraft);


            if (saveChapterDraft.status != 200) {
                alert('Failed to save chapter');
                return false;
            }

            return true;
        } catch (error) {
            console.log(error);
            return false;
        }
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

    function enablePublishChapterButton() {
        const publishChapterButton = document.querySelector('.publish-chapter-button');
        publishChapterButton.addEventListener('click', async () => {
            console.log('publish chapter');
        });
    }

    function enableSaveDraftButton(novelId) {
        const saveDraftButton = document.querySelector('.save-draft-button');
        saveDraftButton.addEventListener('click', async () => {
            const novelChapterTitle = document.querySelector('#chapter-title');
            const novelChapterContent = quill.getSemanticHTML();

            const isChapterSaved = await saveChapter(novelId, novelChapterTitle.value, novelChapterContent);
            if (!isChapterSaved) {
                alert('Failed to save chapter');
                return;
            }

            alert('Chapter saved successfully');
            window.location.href = '<?php echo AUTHOR_NOVEL_VIEW_PAGE; ?>?novel_id=' + novelId;
        });
    }

    function enableChapterContentEditor() {
        quill = new Quill('#chapter-content', {
            modules: {
                toolbar: true
            },
            placeholder: 'Start writing your story...',
            theme: 'snow'
        });

        quill.on('text-change', (delta, oldDelta) => {
            const wordCountSpan = document.querySelector('.word-count');
            wordCountSpan.innerHTML = quill.getText().trim().length;
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        const novelId = new URLSearchParams(window.location.search).get('novel_id');
        if (!novelId) {
            window.location.href = '<?php echo AUTHOR_DASHBOARD_PAGE; ?>';
        }

        enableChapterContentEditor();
        enableSaveDraftButton(novelId);
        loadNovel(novelId);
        // const novelId = new URLSearchParams(window.location.search).get('novel_id');
        // if (novelId) {
        //     loadEditView(novelId);
        // }

        // await loadGenreOptions();
        // enableGenderDropdown();
        // enableCreateNovelButton();
    });
</script>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>
    <main>
        <div class="title">
            <h1>Write Your New Chapter</h1>
        </div>
        <div class="container">
            <div class="title-content-col">
                <input type="text" placeholder="Chapter Title" id="chapter-title">
                <div id="chapter-content"></div>
            </div>
            <hr>
            <div class="stats-button-col">
                <div class="stats-container">
                    <h3>Currently Writing:</h3>
                    <div>
                        <h5>Book Title:</h5>
                        <span class="novel-title">The stories of the sev.</span>
                    </div>
                    <div>
                        <h5>Book Chapter:</h5>
                        <span class="novel-chapter">Chapter 1</span>
                    </div>
                    <div>
                        <h5>Word Count:</h5>
                        <span class="word-count">0</span>
                    </div>

                </div>
                <div class="button-container">
                    <button class="save-draft-button">
                        Save Draft
                    </button>
                </div>

            </div>
        </div>
    </main>
</body>

</html>