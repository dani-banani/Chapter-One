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

    <h3 id="form-title">Create a New Novel</h3>
    <form id="create-form">
        <input type="hidden" id="novel-id">
        <input type="text" id="title" placeholder="Novel Title" required><br><br>
        <div id="editor">
            <p>Start writing your novel description…</p>
        </div>
        <button type="submit" id="submit-btn">Create</button>
        <button type="button" id="cancel-edit" style="display:none" onclick="cancelEdit()">Cancel</button>
        <p id="create-err" style="color:red"></p>
    </form>
    <hr>
    <h3>Your Novels</h3>
    <div id="novel-list">Loading…</div>

    <script>
        const API = '../../api/novel.php';
        const ME = <?= $authorId ?>;
        const quill = new Quill('#editor', { theme: 'snow' });
        let isEditing = false;

        async function loadNovels() {
            const box = document.getElementById('novel-list');
            box.textContent = 'Loading…';
            try {
                const { data } = await axios.get(`${API}?nv_novel_author_id=${ME}`);
                if (data.error) {
                    box.textContent = data.error;
                    return;
                }
                box.innerHTML = data.length ?
                    data.map(nv => `
                    <div style="border:1px solid #ccc;padding:10px;margin:10px">
                        <strong>${nv.nv_novel_title}</strong><br>
                        <div>${nv.nv_novel_description}</div>
                        <small>Published ${nv.nv_novel_publish_date}</small><br>
                        <button onclick="editNovel(${nv.nv_novel_id}, \`${escapeHtml(nv.nv_novel_title)}\`, \`${escapeHtml(nv.nv_novel_description)}\`)">Edit</button>
                        <button onclick="deleteNovel(${nv.nv_novel_id})">Delete</button>
                    </div>`).join('') :
                    '<p>No novels yet.</p>';
            } catch (ex) {
                box.textContent = ex.response?.data?.error || 'Error loading novels';
            }
        }

        function escapeHtml(str) {
            return str.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/\r?\n/g, '<br>');
        }

        function editNovel(id, title, descHtml) {
            isEditing = true;
            document.getElementById('form-title').textContent = 'Edit Novel';
            document.getElementById('submit-btn').textContent = 'Update';
            document.getElementById('cancel-edit').style.display = 'inline';
            document.getElementById('novel-id').value = id;
            document.getElementById('title').value = title;
            quill.root.innerHTML = descHtml;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function cancelEdit() {
            isEditing = false;
            document.getElementById('form-title').textContent = 'Create a New Novel';
            document.getElementById('submit-btn').textContent = 'Create';
            document.getElementById('cancel-edit').style.display = 'none';
            document.getElementById('novel-id').value = '';
            document.getElementById('title').value = '';
            quill.setContents([]);
        }

        document.getElementById('create-form').onsubmit = async e => {
            e.preventDefault();
            const id = document.getElementById('novel-id').value;
            const title = document.getElementById('title').value.trim();
            const descHTML = quill.root.innerHTML.trim();
            const errEl = document.getElementById('create-err');
            errEl.textContent = '';
            if (!title || !descHTML) {
                errEl.textContent = 'Both fields required';
                return;
            }
            const payload = {
                nv_novel_title: title,
                nv_novel_description: descHTML
            };
            try {
                if (isEditing) {
                    payload.nv_novel_id = id;
                    const res = await axios.put(API, payload, {
                        headers: { 'Content-Type': 'application/json' }
                    });
                    if (res.data.success) {
                        cancelEdit();
                        loadNovels();
                    } else {
                        errEl.textContent = res.data.error || 'Update failed';
                    }
                } else {
                    const res = await axios.post(API, payload, {
                        headers: { 'Content-Type': 'application/json' }
                    });
                    if (res.data.success) {
                        document.getElementById('title').value = '';
                        quill.setContents([]);
                        loadNovels();
                    } else {
                        errEl.textContent = res.data.error || 'Create failed';
                    }
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