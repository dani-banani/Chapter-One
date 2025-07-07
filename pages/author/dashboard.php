<?php
require_once '../../auth/author.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Author Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <style>
    #editor {
        height: 200px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <h2><?php echo "Welcome, Author #{$authorId}"; ?></h2>
    <a href="../../auth/logout_author.php" style="color:red">Logout</a>

    <h3>Create a New Novel</h3>
    <form id="create-form">
        <input type="text" id="title" placeholder="Novel Title" required><br><br>
        <div id="editor">
            <p>Start writing your novel description…</p>
        </div>
        <button type="submit">Create</button>
        <p id="create-err" style="color:red"></p>
    </form>
    <hr>
    <h3>Your Novels</h3>
    <div id="novel-list">Loading…</div>
    <script>
    const API = '../../api/novel.php';
    const ME = <?= $authorId ?>;
    const quill = new Quill('#editor', {
        theme: 'snow'
    });

    async function loadNovels() {
        const box = document.getElementById('novel-list');
        box.textContent = 'Loading…';
        try {
            const {
                data
            } = await axios.get(API);
            if (data.error) {
                box.textContent = data.error;
                return;
            }
            const mine = data.filter(nv => Number(nv.nv_novel_author_id) === ME);
            box.innerHTML = mine.length ?
                mine.map(nv => `
                <div style="border:1px solid #ccc;padding:10px;margin:10px">
                    <strong>${nv.nv_novel_title}</strong><br>
                    <div>${nv.nv_novel_description}</div>
                    <small>Published ${nv.nv_novel_publish_date}</small><br>
                    <button onclick="deleteNovel(${nv.nv_novel_id})">Delete</button>
                </div>`).join('') :
                '<p>No novels yet.</p>';
        } catch (ex) {
            box.textContent = ex.response?.data?.error || 'Error loading novels';
        }
    }

    document.getElementById('create-form').onsubmit = async e => {
        e.preventDefault();
        const title = document.getElementById('title').value.trim();
        const errEl = document.getElementById('create-err');
        errEl.textContent = '';
        const descHTML = quill.root.innerHTML.trim();
        if (!title || !descHTML) {
            errEl.textContent = 'Both fields required';
            return;
        }
        try {
            const res = await axios.post(API, {
                nv_novel_title: title,
                nv_novel_description: descHTML
            }, {
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            if (res.data.success) {
                document.getElementById('title').value = '';
                quill.setContents([]);
                loadNovels();
            } else {
                errEl.textContent = res.data.error || 'Error';
            }
        } catch (ex) {
            errEl.textContent = ex.response?.data?.error || 'Server error';
        }
    };
    async function deleteNovel(id) {
        if (!confirm('Delete this novel?')) return;
        try {
            await axios.delete(`${API}?id=${id}`);
            loadNovels();
        } catch (ex) {
            alert(ex.response?.data?.error || 'Delete failed');
        }
    }
    loadNovels();
    </script>
</body>

</html>