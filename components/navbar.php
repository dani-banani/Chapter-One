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
    <div id="navbar-search-bar">
        <input type="text" placeholder="Search...">
        <a href=""><i class="fa-solid fa-magnifying-glass"></i></a>
    </div>
    <nav id="navbar-links">
        <ul>
            <?php switch ($userRole) {
                case 'author':
                    echo '<li><a href="' . AUTHOR_DASHBOARD_PAGE . '">Dashboard</a></li>';
                    echo '<li><a href="' . LOGIN_PAGE . '">Create Book</a></li>';
                    echo '<li><a href="' . LOGOUT_AUTHOR_ENDPOINT . '">Logout</a></li>';
                    break;
                case 'user':
                    echo '<li><a href="' . LOGIN_PAGE . '">Browse</a></li>';
                    echo '<li><a href="' . LOGIN_PAGE . '">Trending</a></li>';
                    echo '<li><a href="' . LOGIN_PAGE . '">My Library</a></li>';
                    echo '<li><a href="' . LOGOUT_AUTHOR_ENDPOINT . '">Logout</a></li>';
                    break;
                default:
                    echo '<li><a href="' . LOGIN_PAGE . '">Browse Books</a></li>';
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
