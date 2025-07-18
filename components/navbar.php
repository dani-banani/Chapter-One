<?php
$userRole = "";

if (isset($_SESSION['user_id'])) {
    $userRole = 'user';
} elseif (isset($_SESSION['author_id'])) {
    $userRole = 'author';
} ?>

<header>
    <div id="navbar-title">
        <h1>Chapter One</h1>
    </div>
    <?php if ($userRole != 'author'): ?>
        <div id="navbar-search-bar">
            <input type="text" placeholder="Search...">
            <a href=""><i class="fa-solid fa-magnifying-glass"></i></a>
        </div>
    <?php endif ?>

    <nav id="navbar-links">
        <ul>
            <?php switch ($userRole) {
                case 'author':
                    echo '<li><a href="' . AUTHOR_DASHBOARD_PAGE . '">Dashboard</a></li>';
                    echo '<li><a href="' . AUTHOR_CREATE_NOVEL_PAGE . '">Create Novel</a></li>';
                    echo '<li><a href="' . LOGOUT_AUTHOR_API . '">Logout</a></li>';
                    break;
                case 'user':
                    echo '<li> 
                            <div id="dropdown">
                                <a id="dropdownBtn">Browse</a>
                                <div id="dropdown-content">
                                    <div class="genreGroup">
                                        <a href="browse_book.php">All</a>
                                        <a href="browse_book.php?nv_genre_id=1">Fantasy</a>
                                        <a href="browse_book.php?nv_genre_id=2">Science Fiction</a>
                                        <a href="browse_book.php?nv_genre_id=3">Romance</a>
                                    </div>
                                    <div class="genreGroup">
                                        <a href="browse_book.php?nv_genre_id=4">Mystery</a>
                                        <a href="browse_book.php?nv_genre_id=5">Thriller</a>
                                        <a href="browse_book.php?nv_genre_id=7">Horror</a>
                                        <a href="browse_book.php?nv_genre_id=8">Adventure</a>
                                    </div>
                                    <div class="genreGroup">
                                        <a href="browse_book.php?nv_genre_id=9">Drama</a>
                                        <a href="browse_book.php?nv_genre_id=10">Comedy</a>
                                    </div>
                                </div>
                            </div>
                        </li>';
                    echo '<li><a href="' . LOGIN_PAGE . '">Trending</a></li>';
                    echo '<li><a href="' . LOGIN_PAGE . '">My Library</a></li>';
                    echo '<li><a href="' . LOGOUT_AUTHOR_API . '">Logout</a></li>';
                    break;
                default:
                    echo '<li> 
                            <div id="dropdown">
                                <a id="dropdownBtn">Browse</a>
                                <div id="dropdown-content">
                                    <div class="genreGroup">
                                        <a href="browse_book.php">All</a>
                                        <a href="browse_book.php?nv_genre_id=1">Fantasy</a>
                                        <a href="browse_book.php?nv_genre_id=2">Science Fiction</a>
                                        <a href="browse_book.php?nv_genre_id=3">Romance</a>
                                    </div>
                                    <div class="genreGroup">
                                        <a href="browse_book.php?nv_genre_id=4">Mystery</a>
                                        <a href="browse_book.php?nv_genre_id=5">Thriller</a>
                                        <a href="browse_book.php?nv_genre_id=7">Horror</a>
                                        <a href="browse_book.php?nv_genre_id=8">Adventure</a>
                                    </div>
                                    <div class="genreGroup">
                                        <a href="browse_book.php?nv_genre_id=9">Drama</a>
                                        <a href="browse_book.php?nv_genre_id=10">Comedy</a>
                                    </div>
                                </div>
                            </div>
                        </li>';
                    echo '<li><a href="' . LOGIN_PAGE . '">View Trending</a></li>';
                    echo '<li><a href="' . LOGIN_PAGE . '">Login</a></li>';
                    break;
            }
            ?>
        </ul>
        <div id="navbar-profile-pic">
            <img src="https://picsum.photos/50" alt="Profile Picture">
        </div>
    </nav>
</header>