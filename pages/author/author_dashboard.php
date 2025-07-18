<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;


?>

<link rel="stylesheet" href="/chapter-one/style/table.css" type="text/css">
<style>
    main {
        padding: 40px 80px;
    }

    .author-info {
        background-color: red;
        width: 100%;
        height: 100px;
    }


    /* table */
    .author-novels-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;

        button {
            display: flex;
            gap: 10px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-weight: bold;
            border: 1px solid black;
        }
    }

    tr {
        grid-template-columns: auto 100px 200px 100px 200px;
    }

    .novel-cover-and-title {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 20px;
        height: 150px;
    }

    .novel-operations {
        gap: 20px;
    }
</style>
<script>
    async function loadNovels() {
        const authorNovels = await axios.get('<?php echo NOVEL_API; ?>', {
            params: {
                // nv_author_id: <?php echo $_SESSION['author_id']; ?>
                nv_author_id: 6
            }
        });
        const tableBody = document.querySelector('tbody');
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
                <td class="novel-publish-date">${novel.nv_publish_date}</td>
                <td class="novel-rating">idk</td>
                <td class="novel-operations">
                    <a href="<?php echo AUTHOR_EDIT_NOVEL_PAGE; ?>?novel_id=${novel.nv_novel_id}">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a class="delete-novel" data-novel-id="${novel.nv_novel_id}" data-novel-title="${novel.nv_novel_title}">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            `;
        }
    }

    async function deleteNovel(novelId, novelName) {
        const deleteBook = await confirm(`Are you sure you want to delete the following novel?\n\"${novelName}\"`);
        if (!deleteBook) return;

        const novelDeletionResponse = await axios.delete('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        })

        if (novelDeletionResponse.status != 200) {
            alert('Failed to delete novel');
            return;
        }

        window.location.reload();
    }

    function enableDeleteNovelButtons() {
        const deleteButtons = document.querySelectorAll('.delete-novel');
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const novelId = e.currentTarget.dataset.novelId;
                const novelTitle = e.currentTarget.dataset.novelTitle;
                deleteNovel(novelId, novelTitle);
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
            <h1>Welcome, <?php echo $authorUsername; ?></h1>
        </div>
        <div class="author-info">

        </div>
        <div class="author-novels">
            <div class="author-novels-header">
                <h2>Published Novels</h2>
                <button id="create-novel-button"><i class="fa-solid fa-plus"></i>Create New Novel</button>
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
                            <h5>Last Edited</h5>
                        </th>
                        <th>
                            <h5>Rating</h5>
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

    </main>
</body>

</html>