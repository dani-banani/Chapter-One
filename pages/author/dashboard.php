<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>
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
    <?php require_once NAVBAR_COMPONENT; ?>

    <h2><?php echo "Welcome, Author #{$authorId}"; ?></h2>

    <!-- Create/Edit Novel Form -->
    <h3 id="form-title">Create a New Novel</h3>
    <form id="create-form">
        <input type="hidden" id="novel-id">
        <input type="text" id="title" placeholder="Novel Title" required><br><br>
        <div id="editor">
            <p>Start writing your novel description…</p>
        </div>
        <select id="genre-select">
            <option disabled selected value="">Select a genre</option>
        </select>
        <button type="button" onclick="addGenre()">+ Add Genre</button>
        <ul id="selected-genres" style="list-style:none; padding-left:0; margin-top:10px;"></ul>
        <button type="submit" id="submit-btn">Create</button>
        <button type="button" id="cancel-edit" style="display:none" onclick="cancelEdit()">Cancel</button>
        <p id="create-err" style="color:red"></p>
    </form>
    <hr>

    <!-- Filters -->
    <h3>Your Novels</h3>
    <label for="filter-genre">Filter by Genre:</label>
    <select id="filter-genre" onchange="loadNovels()">
        <option value="">-- All Genres --</option>
    </select>
    <div id="novel-list">Loading…</div>

    <!-- Review Section -->
    <hr>
    <h3>Reviews on Your Novels</h3>
    <div id="review-list">Loading reviews…</div>

    <!-- Library Section -->
    <hr>
    <h3>User Library Interactions</h3>
    <div id="library-list">Loading library usage…</div>

    <script>
        const API = {
            novel: '../../api/novel.php',
            genre: '../../api/genre.php',
            review: '../../api/review.php',
            library: '../../api/user_library.php'
        };
        const ME = <?= $authorId ?>;
        const quill = new Quill('#editor', { theme: 'snow' });
        let isEditing = false;
        let genreList = [];
        let selectedGenreIds = new Set();

        async function loadNovels() {
            const box = document.getElementById('novel-list');
            box.textContent = 'Loading…';
            const filterGenreId = document.getElementById('filter-genre').value;
            const params = new URLSearchParams({ nv_author_id: ME });
            if (filterGenreId) params.append('genre_id', filterGenreId);
            try {
                const { data } = await axios.get(`${API.novel}?${params.toString()}`);
                const genreMap = await loadGenreMap();
                const genreMapping = await loadAllMappings();
                box.innerHTML = data.length ? data.map(nv => {
                    const novelGenres = genreMapping.filter(m => m.nv_novel_id == nv.nv_novel_id);
                    const genreNames = novelGenres.map(m => genreMap[m.nv_genre_id]).join(', ');
                    return `
                <div style="border:1px solid #ccc;padding:10px;margin:10px">
                    <strong>${nv.nv_novel_title}</strong><br>
                    <div>${nv.nv_novel_description}</div>
                    <small>Published ${nv.nv_novel_publish_date}</small><br>
                    <em>Genres: ${genreNames || 'None'}</em><br>
                    <button onclick="editNovel(${nv.nv_novel_id}, \`${escapeHtml(nv.nv_novel_title)}\`, \`${escapeHtml(nv.nv_novel_description)}\`)">Edit</button>
                    <button onclick="deleteNovel(${nv.nv_novel_id})">Delete</button>
                </div>`;
                }).join('') : '<p>No novels found for selected genre.</p>';
                loadReviews(data.map(n => n.nv_novel_id));
                loadLibrary(data.map(n => n.nv_novel_id));
            } catch (ex) {
                box.textContent = ex.response?.data?.error || 'Error loading novels';
            }
        }

        async function loadReviews(novelIds) {
            const box = document.getElementById('review-list');
            box.textContent = 'Loading…';
            try {
                const res = await axios.get(`${API.review}?novel_ids=${novelIds.join(',')}`);
                const reviews = res.data;
                box.innerHTML = reviews.length ? reviews.map(r => `
            <div style="border:1px solid #aaa; padding:10px; margin:10px">
                <strong>Novel ID:</strong> ${r.nv_novel_id}<br>
                <strong>User #${r.nv_user_id}</strong>: ${r.nv_review_comment}<br>
                <em>Rating:</em> ${r.nv_review_rating} ★ | <em>Likes:</em> ${r.nv_review_likes}
            </div>`).join('') : '<p>No reviews found.</p>';
            } catch (ex) {
                box.textContent = ex.response?.data?.error || 'Failed to load reviews';
            }
        }

        async function loadLibrary(novelIds) {
            const box = document.getElementById('library-list');
            box.textContent = 'Loading…';
            try {
                const res = await axios.get(`${API.library}?novel_ids=${novelIds.join(',')}`);
                const library = res.data;
                box.innerHTML = library.length ? library.map(l => `
            <div style="border:1px solid #aaa; padding:10px; margin:10px">
                <strong>User #${l.nv_user_id}</strong> is currently reading <strong>Novel #${l.nv_novel_id}</strong>
                <em> at Chapter ${l.nv_current_chapter || 'N/A'}</em>
            </div>`).join('') : '<p>No users in library for your novels.</p>';
            } catch (ex) {
                box.textContent = ex.response?.data?.error || 'Failed to load library';
            }
        }

        async function loadGenres() {
            try {
                const res = await axios.get(API.genre + '?list=1');
                if (Array.isArray(res.data)) {
                    genreList = res.data;
                    const select = document.getElementById('genre-select');
                    const filterSelect = document.getElementById('filter-genre');
                    select.innerHTML = '<option disabled selected value="">Select a genre</option>';
                    filterSelect.innerHTML = '<option value="">-- All Genres --</option>';
                    for (const g of genreList) {
                        const opt1 = new Option(g.nv_genre_name, g.nv_genre_id);
                        const opt2 = new Option(g.nv_genre_name, g.nv_genre_id);
                        select.add(opt1);
                        filterSelect.add(opt2);
                    }
                }
            } catch (e) {
                console.error('Failed to load genres', e);
            }
        }

        async function loadGenreMap() {
            const res = await axios.get(API.genre + '?list=1');
            return Object.fromEntries(res.data.map(g => [g.nv_genre_id, g.nv_genre_name]));
        }

        async function loadAllMappings() {
            const res = await axios.get(API.genre + '?all=1');
            return res.data;
        }

        function escapeHtml(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;').replace(/\r?\n/g, '<br>');
        }

        function editNovel(id, title, descHtml) {
            isEditing = true;
            document.getElementById('form-title').textContent = 'Edit Novel';
            document.getElementById('submit-btn').textContent = 'Update';
            document.getElementById('cancel-edit').style.display = 'inline';
            document.getElementById('novel-id').value = id;
            document.getElementById('title').value = title;
            quill.root.innerHTML = descHtml;
            selectedGenreIds.clear();
            document.getElementById('selected-genres').innerHTML = '';
            axios.get(`${API.genre}?novel_id=${id}`).then(res => {
                for (const g of res.data) {
                    selectedGenreIds.add(parseInt(g.nv_genre_id));
                    const li = document.createElement('li');
                    li.textContent = g.nv_genre_name;
                    li.dataset.id = g.nv_genre_id;
                    li.style.cursor = 'pointer';
                    li.onclick = () => { selectedGenreIds.delete(parseInt(g.nv_genre_id)); li.remove(); };
                    document.getElementById('selected-genres').appendChild(li);
                }
            });
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
            selectedGenreIds.clear();
            document.getElementById('selected-genres').innerHTML = '';
        }

        document.getElementById('create-form').onsubmit = async e => {
            e.preventDefault();
            const id = document.getElementById('novel-id').value;
            const title = document.getElementById('title').value.trim();
            const descHTML = quill.root.innerHTML.trim();
            const errEl = document.getElementById('create-err');
            errEl.textContent = '';
            if (!title || !descHTML) return errEl.textContent = 'Both fields required';
            const payload = { nv_novel_title: title, nv_novel_description: descHTML };
            try {
                if (isEditing) {
                    payload.nv_novel_id = id;
                    const res = await axios.put(API.novel, payload, { headers: { 'Content-Type': 'application/json' } });
                    if (res.data.success) {
                        await syncGenres(id);
                        cancelEdit();
                        loadNovels();
                    } else errEl.textContent = res.data.error || 'Update failed';
                } else {
                    const res = await axios.post(API.novel, payload, { headers: { 'Content-Type': 'application/json' } });
                    if (res.data.success) {
                        await syncGenres(res.data.id);
                        document.getElementById('title').value = '';
                        quill.setContents([]);
                        loadNovels();
                    } else errEl.textContent = res.data.error || res.data;
                }
            } catch (ex) {
                errEl.textContent = ex.response?.data?.error || 'Server error';
            }
        };

        function addGenre() {
            const select = document.getElementById('genre-select');
            const id = parseInt(select.value);
            if (!id || selectedGenreIds.has(id)) return;
            selectedGenreIds.add(id);
            const genre = genreList.find(g => g.nv_genre_id == id);
            const li = document.createElement('li');
            li.textContent = genre.nv_genre_name;
            li.dataset.id = id;
            li.style.cursor = 'pointer';
            li.onclick = () => { selectedGenreIds.delete(id); li.remove(); };
            document.getElementById('selected-genres').appendChild(li);
        }

        async function syncGenres(novelId) {
            try {
                await axios.delete(`${API.genre}?novel_id=${novelId}`);
                for (const genre_id of selectedGenreIds) {
                    await axios.post(API.genre, { nv_novel_id: novelId, nv_genre_id: genre_id }, {
                        headers: { 'Content-Type': 'application/json' }
                    });
                }
            } catch (err) {
                console.error('Failed to sync genres', err);
            }
        }

        async function deleteNovel(id) {
            if (!confirm('Delete this novel?')) return;
            try {
                await axios.delete(API.novel, { params: { nv_novel_id: id } });
                loadNovels();
            } catch (ex) {
                alert(ex.response?.data?.error || 'Delete failed');
            }
        }

        loadGenres();
        loadNovels();
    </script>
</body>

</html>