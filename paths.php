<?php
//Endpoints/Files
define('ROOT_PATH', __DIR__);
define('MAIN_CSS', ROOT_PATH . '/pages/css/main.css');
define('HTML_HEADER', ROOT_PATH . '/components/html_header.php');

//api
define('LOGIN_AUTHOR_API', '/chapter-one/auth/login_author.php');
define('LOGOUT_AUTHOR_API', '/chapter-one/auth/logout_author.php');
define('LOGIN_USER_API', '/chapter-one/auth/login_user.php');
define('LOGOUT_USER_API', '/chapter-one/auth/logout_user.php');
define('NOVEL_API', '/chapter-one/api/novel.php');
define('GENRE_API', '/chapter-one/api/genre.php');
define('USER_API', '/chapter-one/api/user.php');
define('AUTHOR_API', '/chapter-one/api/author.php');
define('NOVEL_CHAPTER_API', '/chapter-one/api/novel_chapter.php');
define('RATING_API', '/chapter-one/api/rating.php');
define('LIBRARY_API', '/chapter-one/api/library.php');
define('REVIEW_API', '/chapter-one/api/review.php');

// UI Components
define('NAVBAR_COMPONENT', ROOT_PATH . '/components/navbar.php');
define('FOOTER_COMPONENT', ROOT_PATH . '/components/footer.php');

//Pages
define('LOGIN_PAGE', '/chapter-one/pages/login.php');
define('REGISTER_PAGE', '/chapter-one/pages/register.php');
define('AUTHOR_DASHBOARD_PAGE', '/chapter-one/pages/author/author_dashboard.php');
define('AUTHOR_NOVEL_VIEW_PAGE', '/chapter-one/pages/author/author_novel_view.php');
define('AUTHOR_EDIT_NOVEL_PAGE', '/chapter-one/pages/author/author_edit_novel.php');
define('AUTHOR_CREATE_NOVEL_PAGE', '/chapter-one/pages/author/author_create_novel.php');
define('AUTHOR_CREATE_CHAPTER_PAGE', '/chapter-one/pages/author/author_create_chapter.php');
define('USER_DASHBOARD_PAGE', '/chapter-one/pages/user/dashboard.php');
define('USER_READ_PAGE', '/chapter-one/pages/user/user_read_page.php');
define('USER_SEARCH_PAGE', '/chapter-one/pages/user/search.php');
define('USER_BROWSE_PAGE', '/chapter-one/pages/user/browse_book.php');
define('USER_LIBRARY_PAGE', '/chapter-one/pages/user/user_library.php');
?>