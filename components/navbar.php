<?php
$userRole = "";
require_once __DIR__ . '/../paths.php';


if (isset($_SESSION['user_id'])) {
    $userRole = 'user';
} elseif (isset($_SESSION['author_id'])) {
    $userRole = 'author';
} ?>

<header>
    <div id="navbar-title">
        <?php if ($userRole != 'author'): ?>
            <a id="homeBtn" href="<?php echo USER_DASHBOARD_PAGE ?>">
                <div id="website-logo">
                    <img src="../pages/../img/footer-book-img.png" height="60px" width="60px" alt="book img">
                    <p id='navbar-title'>Chapter One</p>
                </div>
            </a>
        <?php else: ?>
            <div id="footer-logo">
                <img src="../pages/../img/footer-book-img.png" height="60px" width="60px" alt="book img">
                <p id='footer-title'>Chapter One</p>
            </div>
        <?php endif ?>
    </div>
    <?php if ($userRole != 'author'): ?>
        <form action="search.php" method="POST" id="navbar-search-bar">
            <input type="text" name='searchQuery' placeholder="Search...">
            <button type="submit" id="searchSubmitBtn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    <?php endif ?>

    <nav id="navbar-links">
        <ul>
            <?php switch ($userRole) {
                case 'author':
                    echo '<li><a href="' . AUTHOR_DASHBOARD_PAGE . '">Dashboard</a></li>';
                    echo '<li><a href="' . AUTHOR_CREATE_NOVEL_PAGE . '">Create Novel</a></li>';
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
                    echo '<li><a href="">My Library</a></li>';
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
                    echo '<li><a href="' . LOGIN_PAGE . '">Login</a></li>';
                    break;
            }
            ?>
        </ul>
        <?php if ($userRole == 'author' || $userRole == 'user'): ?>
            <div id="profile-dropdown">
                <div id="navbar-profile-pic">
                    <img src="https://picsum.photos/50" alt="Profile Picture">
                </div>
                <div id="dropdown-content">
                    <a href="<?php echo $link = ($userRole == 'user') ? LOGOUT_USER_API : LOGOUT_AUTHOR_API ?>">Logout</a>
                </div>
            </div>
        <?php endif ?>
    </nav>
</header>