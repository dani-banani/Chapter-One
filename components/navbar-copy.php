<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/chapter-one/style/style.css" type="text/css">
    <link rel="stylesheet" href="/chapter-one/style/navbar.css" type="text/css">
    <style>
        #dropdown {
            position: relative;
        }


        #dropdown-content {
            display: none;
            background-color: rgb(0, 0, 0);
            width: max-content;
            padding: 10px;
            border-radius: 16px;

            a {
                color: white;
                font-size: 15px;
                padding: 10px 45px;
                border-radius: 8px;
            }

            a:hover {
                background-color: rgb(143, 143, 255);
            }
        }

        #dropdownBtn {
            padding: 20px;
        }

        #dropdown:hover #dropdown-content {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            gap: 10px;
            position: absolute;
            top: 30px;
            right: 0;

            .genreGroup {
                display: flex;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <header>
        <div id="navbar-title">
            <h1>Chapter One</h1>
        </div>
        <div id="navbar-search-bar">
            <input type="text" placeholder="Search...">
            <a href=""><i class="fa-solid fa-magnifying-glass"></i></a>
        </div>
        <nav id="navbar-links">
            <ul>
                <li>
                    <div id="dropdown">
                        <a id="dropdownBtn">Dropdown</a>
                        <div id="dropdown-content">
                            <div class="genreGroup">
                                <a href="pages/browse_book.php">All</a>
                                <a href="pages/browse_book.php?nv_genre_id=1">Fantasy</a>
                                <a href="pages/browse_book.php?nv_genre_id=2">Science Fiction</a>
                                <a href="pages/browse_book.php?nv_genre_id=3">Romance</a>
                            </div>
                            <div class="genreGroup">
                                <a href="pages/browse_book.php?nv_genre_id=4">Mystery</a>
                                <a href="pages/browse_book.php?nv_genre_id=5">Thriller</a>
                                <a href="pages/browse_book.php?nv_genre_id=7">Horror</a>
                                <a href="pages/browse_book.php?nv_genre_id=8">Adventure</a>
                            </div>
                            <div class="genreGroup">
                                <a href="pages/browse_book.php?nv_genre_id=9">Drama</a>
                                <a href="pages/browse_book.php?nv_genre_id=10">Comedy</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="">Trending</a></li>
                <li><a href="">My Library</a></li>
                <li><a href="">Logout</a></li>
            </ul>
            <div id="navbar-profile-pic">
                <img src="https://picsum.photos/50" alt="Profile Picture">
            </div>
        </nav>

    </header>
</body>

</html>