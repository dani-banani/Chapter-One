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

                    <label>Login as</label><br><br>
                    <label><input type="radio" name='role' value="reader">Reader</label>
                    <label style="margin-left:30px;"><input type="radio" name='role' value="author">Author</label>
                    <br><br><br><br>


                    <!-- Remember me part can be implemented using $_COOKIE instead of $_SESSION -->
                    <label for="remember"><input type="checkbox" name="remember" value="true"> Remember
                        me</label>
                    <br><br><br>
                    <p id="error-message"></p>
                    <input type="submit" value="Sign in" id="submitButton">
                </form>
                <p>Don't have an account? &nbsp;<a style='color:blue;' href="<?php echo REGISTER_PAGE; ?>">Click
                        here</a></p>
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
            const roleField = document.querySelector('input[name="role"]:checked');
            const selectedRole = roleField.value;

            if (!roleField) {
                alert('Please select a role!');
                return;
            }

            switch (selectedRole) {
                case 'author':
                    const res = await axios.post('../auth/login_author.php', {
                        identifier,
                        password
                    });
                    console.log('Login response:', res);
                    if (res.data.success) {
                        window.location.href = '<?php echo AUTHOR_DASHBOARD_PAGE; ?>';
                    }
                    break;
                case 'reader':
                    const response = await axios.post('<?php echo LOGIN_USER_API; ?>', {
                        identifier,
                        password
                    });
                    console.log('Login response:', response);
                    if (response.data.success) {
                        window.location.href = '<?PHP echo USER_DASHBOARD_PAGE; ?>';
                    }


                    break;

                default:
                    alert("Error! Invalid Request.");
            }

        } catch (err) {
            const msg = err.response?.data?.error || 'Login failed';
            errorBox.textContent = msg;
        }
    };
</script>
</body>

</html>