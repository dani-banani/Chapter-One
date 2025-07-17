<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>
<link rel="stylesheet" href="/chapter-one/style/table.css" type="text/css">
<style>
    main {
        padding: 40px 80px;
    }

    tr {
        grid-template-columns: 100px auto 200px 200px;
    }

    .container {
        width: 100%;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 1px 1px 5px 1px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .novel-overview {
        display: flex;
        gap: 20px;

        .novel-data {
            flex: 4;
            display: flex;
            justify-content: space-between;
            align-items: start;
            flex-direction: column;

            #novel-title {
                margin: 0 0 10px 0;
            }

            #novel-publish-date {
                margin: 0;
                font-style: italic;
                color: gray;
            }
        }

        .novel-actions {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;

            button {
                width: 50%;
            }

            .delete-novel-button {
                background-color: var(--danger-color);
            }

            .delete-novel-button:hover {
                background-color: var(--danger-color-hover);
            }
        }
    }

    .novel-stats {
        height: 100px;
        border: 1px solid black;
        display: flex;
        justify-content: space-around;
    }

    .novel-chapters {
        .novel-chapters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 0 20px 0;
        }

        .chapter-title {
            justify-content: flex-start;
        }

        .chapter-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;

            .chapter-actions-icon-container {
                display: flex;
                gap: 20px;
                justify-content: center;
            }

            button {
                padding: 5px;
                justify-content: center;
                width: 100%;
            }
        }
    }
</style>
<script>
    let novelTitle;
    async function loadNovel(novelId) {
        const novel = await axios.get('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        });

        const currentNovel = novel.data[0];
        const novelGenres = await axios.get(`<?php echo GENRE_API; ?>?novel_id=${novelId}`);

        novelTitle = currentNovel.nv_novel_title;
        const publishDate = document.getElementById('novel-publish-date');
        const title = document.getElementById('novel-title');
        const novelDescription = document.getElementById('novel-description');
        const novelGenre = document.getElementById('novel-genre');

        const novelGenreText = novelGenres.data.map((genre) => genre.nv_genre_name).join(', ')

        title.innerHTML = currentNovel.nv_novel_title;
        publishDate.innerHTML = `Published on: ${currentNovel.nv_novel_publish_date}`;
        novelDescription.innerHTML = currentNovel.nv_novel_description;
        novelGenre.innerHTML = novelGenreText == "" ? "No Genre" : novelGenreText;
    }

    async function deleteNovel(novelId) {
        const deleteNovelResponse = await axios.delete('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        });

        if (deleteNovelResponse.status != 200) {
            alert('Failed to delete novel');
            return false;
        }

        return true;
    }

    async function loadNovelChapters(novelId) {
        const tableBody = document.querySelector('tbody');
        tableBody.innerHTML = '';

        const chapters = await axios.get('<?php echo NOVEL_CHAPTER_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        });

        for (const chapter of chapters.data) {
            const isChapterPublished = chapter.nv_novel_chapter_status == 'published';
            const chapterStatus = isChapterPublished ? 'Published' : 'Draft';

            tableBody.innerHTML += `
            <tr class="chapter-item" data-chapter-number="${chapter.nv_novel_chapter_number}" data-novel-id="${chapter.nv_novel_id}" data-chapter-published-status="${isChapterPublished}">
                <td class="chapter-number">${chapter.nv_novel_chapter_number}</td>
                <td class="chapter-title">${chapter.nv_novel_chapter_title}</td>
                <td class="chapter-status">${chapterStatus}</td>
                <td class="chapter-actions">
                    <div class="chapter-actions-icon-container">
                        <a href="#" class="edit-chapter-link">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="#" class="delete-chapter-link">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                    <button class="publish-chapter-button">${isChapterPublished ? 'Unpublish' : 'Publish'}</button>
                </td>
            </tr>
        `;
        }

        enableChapterActionButtons(novelId);
    }

    async function updateChapterStatus(chapterNumber, novelId, status) {
        const publishChapterResponse = await axios.put('<?php echo NOVEL_CHAPTER_API; ?>', {
            nv_novel_chapter_number: chapterNumber,
            nv_novel_id: novelId,
            nv_novel_chapter_status: status
        });

        if (publishChapterResponse.status != 200) {
            alert('Failed to publish chapter');
            return false;
        }

        return true;
    }

    async function deleteChapter(chapterNumber, novelId) {
        const deleteChapterResponse = await axios.delete('<?php echo NOVEL_CHAPTER_API; ?>', {
            params: {
                nv_novel_chapter_number: chapterNumber,
                nv_novel_id: novelId
            }
        });

        if (deleteChapterResponse.status != 200) {
            alert('Failed to delete chapter');
            return false;
        }

        return true;
    }

    function enableAddChapterButton(novelId) {
        const addChapterButton = document.querySelector('.add-chapter-button');
        addChapterButton.addEventListener('click', () => {
            window.location.href = '<?php echo AUTHOR_ADD_CHAPTER_PAGE; ?>?novel_id=' + novelId;
        });
    }

    function enableChapterActionButtons() {
        const novelChapters = document.querySelectorAll('.chapter-item');

        for (const novelChapter of novelChapters) {
            const publishChapterButton = novelChapter.querySelector('.publish-chapter-button');
            publishChapterButton.addEventListener('click', async (e) => {
                e.preventDefault();
                const chapterNumber = novelChapter.dataset.chapterNumber;
                const novelId = novelChapter.dataset.novelId;
                const isChapterPublished = novelChapter.dataset.chapterPublishedStatus == "true";

                const isChapterUpdated = await updateChapterStatus(chapterNumber, novelId, isChapterPublished ? 'draft' : 'published');
                if (!isChapterUpdated) {
                    alert('Failed to update chapter status');
                    return;
                }

                alert(`Chapter ${isChapterPublished ? 'unpublished' : 'published'} successfully`);
                loadNovelChapters(novelId);
            });

            const deleteChapterButton = novelChapter.querySelector('.delete-chapter-link');
            deleteChapterButton.addEventListener('click', async (e) => {
                e.preventDefault();
                const chapterNumber = novelChapter.dataset.chapterNumber;
                const novelId = novelChapter.dataset.novelId;

                const isChapterDeleted = await deleteChapter(chapterNumber, novelId);
                if (!isChapterDeleted) {
                    alert('Failed to delete chapter');
                    return;
                }

                alert('Chapter deleted successfully');
                loadNovelChapters(novelId);
            });
        }
    }

    function enableNovelActionButtons(novelId) {
        const editNovelButton = document.querySelector('.edit-novel-button');
        editNovelButton.addEventListener('click', () => {
            window.location.href = '<?php echo AUTHOR_CREATE_NOVEL_PAGE; ?>?novel_id=' + novelId;
        });

        const deleteNovelButton = document.querySelector('.delete-novel-button');
        deleteNovelButton.addEventListener('click', async () => {
            const userConfirmation = confirm(`Are you sure you want to delete the following novel?\n"${novelTitle}"\nThis action cannot be undone.`);

            if (!userConfirmation) {
                return;
            }

            const isNovelDeleted = await deleteNovel(novelId);
            if (!isNovelDeleted) {
                alert('Failed to delete novel');
                return;
            }

            alert('Novel deleted successfully');
            window.location.href = '<?php echo AUTHOR_DASHBOARD_PAGE; ?>';
        });
    }


    document.addEventListener('DOMContentLoaded', async () => {
        const novelId = new URLSearchParams(window.location.search).get('novel_id');
        await loadNovel(novelId);
        await loadNovelChapters(novelId);
        enableAddChapterButton(novelId);
        enableNovelActionButtons(novelId);
    });
</script>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>
    <main>
        <div class="title">
            <h1>Your Novel</h1>
        </div>
        <div class="container">
            <div class="novel-overview">
                <div class="novel-cover">
                    <img src="https://picsum.photos/200/300" alt="">
                </div>
                <div class="novel-data">
                    <div>
                        <h1 id="novel-title">Book Name</h1>
                        <p id="novel-publish-date">Published Date</p>
                        <p id="novel-description">Book Description
                    </div>
                    <p id="novel-genre">Genre</p>
                </div>
                <div class="novel-actions">
                    <button class="edit-novel-button"><i class="fa-solid fa-pen-to-square"></i>Edit Novel</button>
                    <button class="delete-novel-button"><i class="fa-solid fa-trash"></i>Delete Novel</button>
                </div>
            </div>
            <div class="novel-stats">
                <h4>Status</h4>
                <h4>Novel Tier</h4>
                <h4>Chapters</h4>
                <h4>Pages</h4>
                <h4>Rating</h4>
                <h4>Views</h4>
            </div>
            <div class="novel-chapters">
                <div class="table-title-row">
                    <h2>Book Chapters</h2>
                    <button class="add-chapter-button"><i class="fa-solid fa-plus"></i>Add New Chapter</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>
                                <h5>Chapters</h5>
                            </th>
                            <th>
                                <h5>Title</h5>
                            </th>
                            <th>
                                <h5>Status</h5>
                            </th>
                            <th>
                                <h5>Actions</h5>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>