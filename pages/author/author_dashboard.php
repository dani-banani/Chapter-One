<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>

<link rel="stylesheet" href="/chapter-one/style/table.css" type="text/css">
<style>
    body{
        display: flex;
        flex-direction: column;
    }

    main {
        padding: 40px 80px;
        flex: 1
    }

    .empty-table-row{
    display: flex;
    justify-content: center;
    align-items: center;
}   

    .container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .author-info {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background-color: var(--primary-color);
        border-radius: 10px;
        box-sizing: border-box;
    }

    /* table */
    tr {
        grid-template-columns: auto 100px 100px 200px 200px;
    }

    .novel-cover-and-title {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 20px;
        height: 100px;
    }

    .novel-operations {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;

        button {
            justify-content: center;
            width: 100%;
            padding: 5px;
        }

        .delete-novel-button {
            background-color: var(--danger-color);
        }

        .delete-novel-button:hover {
            background-color: var(--danger-color-hover);
        }
    }
</style>
<script>
    async function loadAuthor() {
        const author = await axios.get('<?php echo AUTHOR_API; ?>?id=<?php echo $_SESSION['author_id']; ?>', {});

        const authorName = document.getElementById('author-name');
        const authorWritingSinceStat = document.getElementById('author-writing-since');

        authorName.innerHTML = `Welcome, ${author.data.nv_author_username}`;
        authorWritingSinceStat.innerHTML = author.data.nv_author_created_date;
    }

    async function loadNovels() {
        const tableBody = document.querySelector('tbody');
        tableBody.innerHTML = '';
        let authorNovels = await axios.get('<?php echo NOVEL_API; ?>', {
            params: {
                nv_author_id: <?php echo $_SESSION['author_id']; ?>
            }
        });

        const authorBookCountStat = document.getElementById('author-book-count');
        const authorViewCountStat = document.getElementById('author-view-count');

        let authorBookCount = 0;
        let authorViewCount = 0;

        if (authorNovels.data.length == 0) {
            tableBody.innerHTML = `
                <tr class="empty-table-row">
                    <td colspan="5">
                        <div>
                            <i>No novels published yet. Add a new novel to get started.</i>
                        </div>
                    </td>
                </tr>
            `;
        }

        for (const novel of authorNovels.data) {
            tableBody.innerHTML += `
                <tr id="${novel.nv_novel_id}">
                <td class="novel-cover-and-title">
                    <div class="novel-cover">
                        <img src="https://picsum.photos/200/300" alt="">
                    </div>
                    <div class="novel-title">
                        <p>${novel.nv_novel_title}</p>
                    </div>
                </td>
                <td class="novel-status">
                    <p>${novel.nv_novel_status}</p>
                </td>
                <td class="novel-view-count">${novel.nv_novel_view_count}</td>
                <td class="novel-publish-date">${novel.nv_novel_publish_date}</td>
                <td class="novel-operations">
                    <button onclick="window.location.href='<?php echo AUTHOR_NOVEL_VIEW_PAGE; ?>?novel_id=${novel.nv_novel_id}'" class="view-novel-button">
                        View Novel
                    </button>
                    <button data-novel-id="${novel.nv_novel_id}" data-novel-title="${novel.nv_novel_title}" class="delete-novel-button">
                        Delete Novel
                    </button>
                    </td>
                </tr>
            `;
            authorBookCount += 1;
            authorViewCount += novel.nv_novel_view_count;
        }

        authorBookCountStat.innerHTML = authorBookCount;
        authorViewCountStat.innerHTML = authorViewCount;
    }

    async function deleteNovel(novelId) {
        const novelDeletionResponse = await axios.delete('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        })

        if (novelDeletionResponse.status != 200) {
            alert('Failed to delete novel');
            return false;
        }

        return true;
    }

    function enableDeleteNovelButtons() {
        const deleteButtons = document.querySelectorAll('.delete-novel-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                const novelId = e.currentTarget.dataset.novelId;
                const novelTitle = e.currentTarget.dataset.novelTitle;
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
                await loadNovels();
            });
        });
    }

    function enableCreateNovelButton() {
        const createNovelButton = document.getElementById('create-novel-button');
        createNovelButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = '<?php echo AUTHOR_CREATE_NOVEL_PAGE; ?>';
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await loadAuthor();
        await loadNovels();
        enableDeleteNovelButtons();
        enableCreateNovelButton();
    });
</script>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>
    <main>
        <div class="title">
            <h1 id="author-name"></h1>
        </div>
        <div class="container">
            <div class="author-info">
                <div class="author-info-col">
                    <h5>Books Written:</h5>
                    <span id="author-book-count"></span>

                </div>
                <div class="author-info-col">
                    <h5>Total Views:</h5>
                    <span id="author-view-count"></span>
                </div>

                <div class="author-info-col">
                    <h5>Member Since:</h5>
                    <span id="author-writing-since"></span>
                </div>

            </div>
            <div class="author-novels">
                <div class="table-title-row">
                    <h2>Published Novels</h2>
                    <button id="create-novel-button" on><i class="fa-solid fa-plus"></i>Create New Novel</button>
                </div>
                <table>
                    <thead>
                        <tr class="table-header">
                            <th>
                                <h5>Novel</h5>
                            </th>
                            <th>
                                <h5>Status</h5>
                            </th>
                            <th>
                                <h5>Views</h5>
                            </th>
                            <th>
                                <h5>Publish Date</h5>
                            </th>
                            <th>
                                <h5>Operations</h5>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>


    </main>
    <?php require_once FOOTER_COMPONENT; ?>
</body>

</html>