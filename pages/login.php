<?php
require_once __DIR__ . '/../paths.php';
require_once HTML_HEADER;
?>

<link rel="stylesheet" href="/chapter-one/style/formStyle.css" type="text/css">
<title>Chapter One</title>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>
    <main>
        <div id="form-wrapper">
            <h1 id="welcomeTitle">Welcome</h1>
            <p id="welcomeText">Sign in to your account using the registered email and password</p>

            <div id="login-form">
                <form action="" method="POST">
                    <label for="email">Username or Email</label><br>
                    <input type="text" name="email" id="identifier">
                    <div class="errorMessage" id="emailError"></div><br><br><br><br>

                    <label for="password">Password</label><br>
                    <input type="password" name="password" id="password">
                    <div class="errorMessage" id="passwordError"></div><br><br>

                    <!-- Remember me part can be implemented using $_COOKIE instead of $_SESSION -->
                    <label for="remember"><input type="checkbox" name="remember" value="true"> Remember
                        me</label>
                    <br><br><br>
                    <p id="error-message"></p>
                    <input type="submit" value="Sign in">
                </form>
                <p>Don't have an account? &nbsp;<a href="<?php echo REGISTER_PAGE; ?>">Click here</a></p>
            </div>
        </div>
    </main>
</body>


<script>
    document.getElementById('login-form').onsubmit = async (e) => {
        e.preventDefault();
        const identifier = document.getElementById('identifier').value;
        const password = document.getElementById('password').value;
        const errorBox = document.getElementById('error-message');
        errorBox.textContent = '';
        console.log('Attempting login with:', {
            identifier,
            password
        });

        try {
            const res = await axios.post('../auth/login_author.php', {
                identifier,
                password
            });
            console.log('Login response:', res);
            if (res.data.success) {
                window.location.href = '<?php echo AUTHOR_DASHBOARD_PAGE; ?>';
            }
        } catch (err) {
            const msg = err.response?.data?.error || 'Login failed';
            errorBox.textContent = msg;
        }
    };
</script>
</body>

</html>