<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    main {
        padding: 40px 80px;
        flex: 1;
    }

    body{
        display: flex;
        flex-direction: column;
        min-height: 100vh;
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
            max-height: 420px;
            overflow-y: scroll;
            word-break: break-all !important;
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
            button{
                width: 100%;
                text-align: center;
            }
        }
    }
</style>
<script>
    let quill;
    async function loadNovelDetails(novelId,novelChapterNumber) {
        const novel = await axios.get('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        });
        
        const novelTitle = document.querySelector('.novel-title');
        const novelChapter = document.querySelector('.novel-chapter');

        novelTitle.innerHTML = novel.data[0].nv_novel_title;
        novelChapter.innerHTML = novelChapterNumber;
    }

    async function loadChapterDetail(novelId,novelChapterNumber){
        const chapter = await axios.get('<?php echo NOVEL_CHAPTER_API; ?>', {
            params: {
                nv_novel_id: novelId,
                nv_novel_chapter_number: novelChapterNumber
            }
        });

        const chapterTitle = document.querySelector('#chapter-title');

        chapterTitle.value = chapter.data[0].nv_novel_chapter_title;
        quill.clipboard.dangerouslyPasteHTML(0,chapter.data[0].nv_novel_chapter_content);
    }
        

    async function saveChapter(novelId, novelChapterTitle, novelChapterContent,novelChapterNumber) {
        try {
            console.log(novelChapterTitle)
            console.log(novelChapterContent)
            const saveChapterDraft = await axios.put('<?php echo NOVEL_CHAPTER_API; ?>', {
                nv_novel_id: novelId,
                nv_novel_chapter_number: novelChapterNumber,
                nv_novel_chapter_title: novelChapterTitle,
                nv_novel_chapter_content: novelChapterContent,
                nv_novel_chapter_status: "draft",
            });

            if (saveChapterDraft.status != 200) {
                return false;
            }

            return true;
        } catch (error) {
            console.log(error);
            return false;
        }
    }

    function enableSaveDraftButton(novelId,novelChapterNumber) {
        const saveDraftButton = document.querySelector('.save-draft-button');
        saveDraftButton.addEventListener('click', async () => {
            const novelChapterTitle = document.querySelector('#chapter-title');
            const novelChapterContent = quill.getSemanticHTML();

            const isChapterSaved = await saveChapter(novelId, novelChapterTitle.value, novelChapterContent,novelChapterNumber);
            if (!isChapterSaved) {
                alert('Failed to save draft');
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
            theme: 'snow',
        });

        quill.on('text-change', (delta, oldDelta) => {
            const wordCountSpan = document.querySelector('.word-count');
            wordCountSpan.innerHTML = quill.getText().trim().length;
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        const novelId = new URLSearchParams(window.location.search).get('novel_id');
        const novelChapterNumber = new URLSearchParams(window.location.search).get('chapter_number');

        if (!novelId || !novelChapterNumber) {
            window.location.href = '<?php echo AUTHOR_DASHBOARD_PAGE; ?>';
        }

        enableChapterContentEditor();
        loadNovelDetails(novelId,novelChapterNumber);
        loadChapterDetail(novelId,novelChapterNumber);
        enableSaveDraftButton(novelId,novelChapterNumber);
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
    <?php require_once FOOTER_COMPONENT; ?>
</body>

</html>