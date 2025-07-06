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
        <p id="error-message" style="color: red;"></p>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    document.getElementById('login-form').onsubmit = async (e) => {
        e.preventDefault();
        const identifier = document.getElementById('identifier').value;
        const password = document.getElementById('password').value;
        const errorBox = document.getElementById('error-message');
        errorBox.textContent = '';
        try {
            const res = await axios.post('../auth/login_author.php', {
                identifier,
                password
            }, {
                headers: { 'Content-Type': 'application/json' }
            });

            if (res.data.success) {
                window.location.replace('author/dashboard.php');
            }
        } catch (err) {
            const msg = err.response?.data?.error || 'Login failed';
            errorBox.textContent = msg;
        }
    };
    </script>
</body>
</html>
