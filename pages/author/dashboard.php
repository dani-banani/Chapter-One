<?php
require_once '../../auth/author.php';
require_once HTML_HEADER;
?>
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
    <h3 id="form-title">Create a New Novel</h3>
    <form id="create-form">
        <input type="hidden" id="novel-id">
        <input type="text" id="title" placeholder="Novel Title" required><br><br>
        <div id="editor">
            <p>Start writing your novel description…</p>
        </div>
        <br>
        <select id="genre-select">
            <option disabled selected value="">Select a genre</option>
        </select>
        <button type="button" onclick="addGenre()">+ Add Genre</button>

        <ul id="selected-genres" style="list-style:none; padding-left:0; margin-top:10px;"></ul>
        <input type="hidden" id="selected-genre-ids">

        <button type="submit" id="submit-btn">Create</button>
        <button type="button" id="cancel-edit" style="display:none" onclick="cancelEdit()">Cancel</button>
        <p id="create-err" style="color:red"></p>
    </form>
    <hr>

    <h3>Your Novels</h3>
    <label for="filter-genre">Filter by Genre:</label>
    <select id="filter-genre" onchange="loadNovels()">
        <option value="">-- All Genres --</option>
    </select>
    <div id="novel-list">Loading…</div>
    <script>
        const API = '../../api/novel.php';
        const GENRE_API = '../../api/genre.php';
        const ME = <?= $authorId ?>;
        const quill = new Quill('#editor', {
            theme: 'snow'
        });
        let isEditing = false;
        let genreList = [];
        let selectedGenreIds = new Set();
        let filterGenreId = '';
        async function loadNovels() {
            const box = document.getElementById('novel-list');
            box.textContent = 'Loading…';
            const genreDropdown = document.getElementById('filter-genre');
            filterGenreId = genreDropdown.value;
            const params = new URLSearchParams({
                nv_author_id: ME
            });

            if (filterGenreId) params.append('genre_id', filterGenreId);
            
            try {
                const {
                    data
                } = await axios.get(`${API}?${params.toString()}`);

                console.log(data);
                const genreMap = await loadGenreMap();
                const genreMapping = await loadAllMappings();
                box.innerHTML = data.length ?
                    data.map(nv => {
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
                    }).join('') :
                    '<p>No novels found for selected genre.</p>';
            } catch (ex) {
                box.textContent = ex.response?.data?.error || 'Error loading novels';
            }
        }

        async function loadGenreMap() {
            const res = await axios.get(GENRE_API + '?list=1');
            const map = {};
            for (const g of res.data) {
                map[g.nv_genre_id] = g.nv_genre_name;
            }
            return map;
        }
        async function loadAllMappings() {
            const res = await axios.get(GENRE_API + '?all=1');
            return res.data;
        }
        async function loadGenres() {
            try {
                const res = await axios.get(GENRE_API + '?list=1');
                if (Array.isArray(res.data)) {
                    genreList = res.data;
                    const select = document.getElementById('genre-select');
                    select.innerHTML = '<option disabled selected value="">Select a genre</option>';
                    const filterSelect = document.getElementById('filter-genre');
                    filterSelect.innerHTML = '<option value="">-- All Genres --</option>';
                    for (const genre of genreList) {
                        const opt1 = document.createElement('option');
                        opt1.value = genre.nv_genre_id;
                        opt1.textContent = genre.nv_genre_name;
                        select.appendChild(opt1);
                        const opt2 = opt1.cloneNode(true);
                        filterSelect.appendChild(opt2);
                    }
                }
            } catch (err) {
                console.error('Failed to load genres');
            }
        }
        
        document.getElementById('filter-genre').addEventListener('change', e => {
            const genreId = parseInt(e.target.value);
            loadNovels(genreId || null);
        });

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
            selectedGenreIds.clear();
            document.getElementById('selected-genres').innerHTML = '';
            axios.get(`${GENRE_API}?novel_id=${id}`).then(res => {
                if (Array.isArray(res.data)) {
                    for (const g of res.data) {
                        selectedGenreIds.add(parseInt(g.nv_genre_id));
                        const li = document.createElement('li');
                        li.textContent = g.nv_genre_name;
                        li.dataset.id = g.nv_genre_id;
                        li.style.cursor = 'pointer';
                        li.onclick = () => {
                            selectedGenreIds.delete(parseInt(g.nv_genre_id));
                            li.remove();
                        };
                        document.getElementById('selected-genres').appendChild(li);
                    }
                }
            });
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
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
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                    if (res.data.success) {
                        await syncGenres(id);
                        cancelEdit();
                        loadNovels();
                    } else {
                        errEl.textContent = res.data.error || 'Update failed';
                    }
                } else {
                    const res = await axios.post(API, payload, {
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                    if (res.data.success) {
                        await syncGenres(res.data.id);
                        document.getElementById('title').value = '';
                        quill.setContents([]);
                        loadNovels();
                    } else {
                        errEl.textContent = res.data.error || res.data;
                    }
                }
            } catch (ex) {
                errEl.textContent = ex.response?.data?.error || 'Server error';
            }
        };

        async function deleteNovel(id) {
            if (!confirm('Delete this novel?')) return;
            try {
                await axios.delete(API, {
                    params: {
                        nv_novel_id: id
                    }
                });
                loadNovels();
            } catch (ex) {
                alert(ex.response?.data?.error || 'Delete failed');
            }
        }

        function addGenre() {
            const select = document.getElementById('genre-select');
            const id = parseInt(select.value);
            if (!id || selectedGenreIds.has(id)) return;
            selectedGenreIds.add(id);
            const genre = genreList.find(g => g.nv_genre_id == id);
            const ul = document.getElementById('selected-genres');
            const li = document.createElement('li');
            li.textContent = genre.nv_genre_name;
            li.dataset.id = id;
            li.style.cursor = 'pointer';
            li.onclick = () => {
                selectedGenreIds.delete(id);
                li.remove();
            };
            ul.appendChild(li);
        }

        async function syncGenres(novelId) {
            try {
                await axios.delete(`${GENRE_API}?novel_id=${novelId}`);
                for (const genre_id of selectedGenreIds) {
                    await axios.post(GENRE_API, {
                        nv_novel_id: novelId,
                        nv_genre_id: genre_id
                    }, {
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                }
            } catch (err) {
                console.error('Failed to sync genres', err);
            }
        }

        loadNovels();
        loadGenres();
    </script>
</body>

</html>