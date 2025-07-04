<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form id="login-form">
        <input type="text" id="identifier" placeholder="Email or Username" required><br>
        <input type="password" id="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    document.getElementById('login-form').onsubmit = async (e) => {
        e.preventDefault();
        const identifier = document.getElementById('identifier').value;
        const password = document.getElementById('password').value;
        try {
            const res = await axios.post('../auth/login_author.php', {
                identifier,
                password
            });
            if (res.data.success) {
                window.location.replace('author/dashboard.php');
            } else {
                alert(res.data.error || 'Login failed');
            }
        } catch (err) {
            console.error(err);
        }
    };
    </script>
</body>
</html>
