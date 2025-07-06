<?php
require_once '../../auth/author.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Author Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h2><?php echo "Welcome, Author # ".$authorId; ?></h2>
    <a href="../../auth/logout_author.php" style="color:red">Logout</a>
    <h3>Create a New Novel</h3>
    <form id="create-form">
        <input type="text" id="title" placeholder="Novel Title" required><br>
        <textarea id="description"  placeholder="Novel Description" required></textarea><br>
        <button type="submit">Create</button>
        <p id="create-err" style="color:red"></p>
    </form>
    <hr>
    <h3>Your Novels</h3>
    <div id="novel-list">Loading…</div>
<script>
const API = '../../api/novel.php';
const ME  = <?= $authorId ?>;

async function loadNovels() {
    const box = document.getElementById('novel-list');
    box.textContent = 'Loading…';
    try {
        const { data } = await axios.get(API);
        if (data.error) { box.textContent = data.error; return; }
        const mine = data.filter(nv => Number(nv.nv_novel_author_id) === ME);
        box.innerHTML = mine.length
            ? mine.map(nv => `
                <div style="border:1px solid #ccc;padding:10px;margin:10px">
                    <strong>${nv.nv_novel_title}</strong><br>
                    ${nv.nv_novel_description}<br>
                    <small>Published ${nv.nv_novel_publish_date}</small><br>
                    <button onclick="deleteNovel(${nv.nv_novel_id})">Delete</button>
                </div>`).join('')
            : '<p>No novels yet.</p>';
    } catch { box.textContent = 'Failed to load novels'; }
}

document.getElementById('create-form').onsubmit = async e => {
    e.preventDefault();
    const title = document.getElementById('title').value.trim();
    const desc  = document.getElementById('description').value.trim();
    const errEl = document.getElementById('create-err');
    errEl.textContent = '';
    if (!title || !desc) { errEl.textContent = 'Both fields required'; return; }
        const res = await axios.post(API, {
            nv_novel_title: title,
            nv_novel_description: desc
        }, { headers:{ 'Content-Type':'application/json' }});
        if (res.data.success) {
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
            loadNovels();
        } else errEl.textContent = res.data.error || 'Error';
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
