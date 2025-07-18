<?php

//Endpoints/Files
define('ROOT_PATH', __DIR__);
define('MAIN_CSS',ROOT_PATH . '/pages/css/main.css');
define('HTML_HEADER',ROOT_PATH . '/components/html_header.php');

//api
define('LOGOUT_AUTHOR_API','/chapter-one/auth/logout_author.php');
define('NOVEL_API','/chapter-one/api/novel.php');
define('GENRE_API','/chapter-one/api/genre.php');
define('NOVEL_CHAPTER_API','/chapter-one/api/novel_chapter.php');

// UI Components
define('NAVBAR_COMPONENT',ROOT_PATH . '/components/navbar.php');

//Pages
define('LOGIN_PAGE','/chapter-one/pages/login.php');
define('REGISTER_PAGE','/chapter-one/pages/register.php');
define('AUTHOR_DASHBOARD_PAGE','/chapter-one/pages/author/author_dashboard.php');
define('AUTHOR_NOVEL_VIEW_PAGE','/chapter-one/pages/author/author_novel_view.php');
define('AUTHOR_EDIT_NOVEL_PAGE','/chapter-one/pages/author/author_edit_novel.php');
define('AUTHOR_CREATE_NOVEL_PAGE','/chapter-one/pages/author/author_create_novel.php');
define('AUTHOR_CREATE_CHAPTER_PAGE','/chapter-one/pages/author/author_create_chapter.php');


