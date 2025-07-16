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

        h4 {
            margin: 0;
            padding: 0;
        }
    }

    .novel-overview {
        display: flex;
        gap: 40px;

        .novel-cover {
            aspect-ratio: 3/4;
            height: 100%;

            img {
                border-radius: 10px;
                object-fit: cover;
                width: 100%;
                height: 100%;
                box-shadow: 1px 1px 5px 1px rgba(0, 0, 0, 0.3);
            }
        }

        .novel-data {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: start;
            flex-direction: column;

            h2,
            h3,
            p {
                margin: 0;
                padding: 0;
            }
        }
    }

    .novel-stats {
        height: 100px;
        border: 1px solid black;
        display: flex;
        justify-content: space-around;
    }

    .novel-chapters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;

        button {
            display: flex;
            gap: 10px;
        }
    }

    .chapter-title {
        justify-content: flex-start;
    }

    .chapter-actions {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;

        .chapter-actions-icon-container {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        button {
            padding: 5px;
            width: 80%;
        }
    }
</style>
<script>
    const novelId = new URLSearchParams(window.location.search).get('novel_id');
    async function loadNovel() {
        const novel = await axios.get('<?php echo NOVEL_API; ?>', {
            params: {
                nv_novel_id: novelId
            }
        });
        return novel.data[0];
    }

    async function loadNovelGenre() {
        const novelGenres = await axios.get(`<?php echo GENRE_API; ?>?novel_id=${novelId}`);
        const genreList = novelGenres.data.map(genre => genre.nv_genre_name);
        return genreList.join(',');
    }
    
    document.addEventListener('DOMContentLoaded', async () => {
        const currentNovel = await loadNovel();
        const currentNovelGenre = await loadNovelGenre();

        const publishDate = document.getElementById('novel-publish-date');
        const title = document.getElementById('novel-title');
        const author = document.getElementById('novel-author');
        const novelGenre = document.getElementById('novel-genre');

        publishDate.innerHTML = currentNovel.nv_publish_date;
        title.innerHTML = currentNovel.nv_novel_title;
        author.innerHTML = currentNovel.nv_author_name;
        novelGenre.innerHTML = currentNovelGenre;

        console.log(currentNovel);
    });
</script>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>
    <main>
        <h1>Book Name</h1>
        <div class="container">
            <div class="novel-overview">
                <div class="novel-cover">
                    <img src="https://picsum.photos/200/300" alt="">
                </div>
                <div class="novel-data">
                    <div>
                        <h2 id="novel-title">Book Name</h2>
                        <h3 id="novel-author">By Author Name</h3>
                        <p id="novel-publish-date">Published Date</p>
                        <p>Description Lorem ipsum dolor sit amet consectetur, adipisicing elit. Porro, quisquam Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur deserunt iusto totam, est aut incidunt culpa illo quis nulla nisi?Lorem, ipsum dolor sit amet consectetur adipisicing elit. Fugit perferendis corrupti a ratione error recusandae at nisi, iure sequi enim reiciendis laboriosam quia illo amet molestias labore. Enim sint eius inventore accusamus quod fugiat dolores voluptatibus tenetur deserunt consequuntur at minima ipsum laboriosam veniam officiis asperiores quidem explicabo, eaque amet quia dignissimos quaerat? Placeat deserunt necessitatibus consequuntur voluptate aut repudiandae sit dolore ducimus quaerat corrupti distinctio possimus, repellendus aspernatur maiores consequatur facilis dicta velit. Officia nobis sint ipsa itaque sit.lorem100 Lorem ipsum dolor sit amet consectetur adipisicing elit. Non ipsa, voluptatum minima in unde animi itaque officiis distinctio mollitia quo, eligendi, quasi provident omnis maiores! Ipsam, impedit animi repellendus, excepturi architecto incidunt quam, consequatur dolor ratione accusamus ullam. Totam harum excepturi modi vel. Quo omnis ipsum nisi est similique? Quas.</p>
                    </div>
                    <p id="novel-genre">Genre: Lorem, ipsum dolor.</p>
                </div>
                <div class="novel-action">
                    <button>Delete Novel</button>
                    <button>View Reviews</button>
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
                <div class="novel-chapters-header">
                    <h2>Book Chapters</h2>
                    <button><i class="fa-solid fa-plus"></i>Add New Chapter</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th><h5>Chapters</h5></th>
                            <th><h5>Title</h5></th>
                            <th><h5>Status</h5></th>
                            <th><h5>Actions</h5></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="chapter-number">1</td>
                            <td class="chapter-title">Lorem ipsum dolor sit amet consectetur, adipisicing elit.</td>
                            <td class="chapter-status">Published/Editing/Pending Approval</td>
                            <td class="chapter-actions">
                                <div class="chapter-actions-icon-container">
                                    <a href=""><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href=""><i class="fa-solid fa-trash"></i></a>
                                </div>
                                <button>Publish</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="chapter-number">1</td>
                            <td class="chapter-title">Lorem ipsum dolor sit amet consectetur, adipisicing elit.</td>
                            <td class="chapter-status">Published/Editing/Pending Approval</td>
                            <td class="chapter-actions">
                                <div class="chapter-actions-icon-container">
                                    <a href=""><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href=""><i class="fa-solid fa-trash"></i></a>
                                </div>
                                <button>Publish</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="chapter-number">1</td>
                            <td class="chapter-title">Lorem ipsum dolor sit amet consectetur, adipisicing elit.</td>
                            <td class="chapter-status">Published/Editing/Pending Approval</td>
                            <td class="chapter-actions">
                                <div class="chapter-actions-icon-container">
                                    <a href=""><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href=""><i class="fa-solid fa-trash"></i></a>
                                </div>
                                <button>Publish</button>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </main>
</body>