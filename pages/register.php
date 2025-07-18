<?php
require_once __DIR__ . '/../paths.php';
require_once HTML_HEADER;
?>

<link rel="stylesheet" href="/chapter-one/style/formStyle.css" type="text/css">
<style>
    .valid {
        color: green;
    }

    .valid:before {
        content: "✔";
        padding-right: 15px;
    }

    .invalid {
        color: red;
    }

    .invalid:before {
        content: "✘";
        padding-right: 15px;
    }

    #submitButton:disabled {
        background-color: rgb(121, 119, 114);
    }
</style>
</head>

<body>
    <main>
        <div id="form-wrapper">
            <h1 id="welcomeTitle">Create Your Account!</h1>
            <p id="welcomeText">Unlock the full potential of the platform now!</p>

            <div id="form-container">
                <form method="POST">
                    <label for="username">Username</label><br>
                    <input type="text" name="username" id="username">
                    <br><br><br><br>

                    <label for="email">Email</label><br>
                    <input type="text" name="email" id="email">
                    <br><br><br><br>

                    <label for="password">Password</label><br>
                    <input type="password" name="password" id="password">
                    <div id="requirementCheck">
                        <p class="invalid" id="letter">At least one uppercase letter</p>
                        <p class="invalid" id="numeral">At least one numeral (0-9)</p>
                        <p class="invalid" id="specialChar">At least one special character (Only #,@,$,%,&
                            allowed)</p>
                        <p class="invalid" id="charLength">Length between 8 and 12 characters</p>
                    </div>
                    <br><br>
                    <label for="confirm_pass">Confirm Password</label><br>
                    <input type="password" name="confirm_pass" id="confirm_pass">
                    <br><br><br><br>
                    <label>Register as</label><br><br>
                    <label><input type="radio" name='role' value="reader">Reader</label>
                    <label style="margin-left:30px;"><input type="radio" name='role' value="author">Author</label>
                    <br><br><br><br>

                    <button type="button" id="submitButton" disabled>Sign Up</button>
                </form>
                <p>Already have an account? <a href="<?php echo LOGIN_PAGE; ?>" style='color:blue'>Login Here</a></p>
            </div>
        </div>
    </main>
</body>


<script>

    //Get fields
    const passField = document.getElementById("password");
    const confirm_passField = document.getElementById("confirm_pass");
    const emailField = document.getElementById("email");
    const usernameField = document.getElementById("username");

    // Constantly check for state as fields are being input
    passField.onkeyup = updateButtonState;
    confirm_passField.onkeyup = updateButtonState;

    // When the user starts to type something inside the password field, validate the field
    function checkReq() {
        //Check requirements with global Regex
        const uppercaseCheck = /[A-Z]/g;
        const numeralCheck = /[\d]/g;
        const specialCharCheck = /[#@$%&]/g;
        const invaidSpecialCharCheck = /[^a-zA-Z\d#@$%&]/g;
        const lengthCheck = passField.value.length;

        //Get boolean value by matching the regex with password field value
        const hasUpperCase = uppercaseCheck.test(passField.value);
        const hasNumeral = numeralCheck.test(passField.value);
        const hasSpecialChar = specialCharCheck.test(passField.value);
        const hasInvalidChar = invaidSpecialCharCheck.test(passField.value);
        const isLong = lengthCheck >= 8 && lengthCheck <= 12;

        //Validate uppercase letter
        if (hasUpperCase) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
        }

        // Validate numbers
        if (hasNumeral) {
            numeral.classList.remove("invalid");
            numeral.classList.add("valid");
        } else {
            numeral.classList.remove("valid");
            numeral.classList.add("invalid");
        }

        // Validate special characters
        if (hasSpecialChar && !hasInvalidChar) {
            specialChar.classList.remove("invalid");
            specialChar.classList.add("valid");
        } else {
            specialChar.classList.remove("valid");
            specialChar.classList.add("invalid");
        }

        // Validate length
        if (isLong) {
            charLength.classList.remove("invalid");
            charLength.classList.add("valid");
        } else {
            charLength.classList.remove("valid");
            charLength.classList.add("invalid");
        }

        //Return boolean value
        return hasUpperCase && hasNumeral && hasSpecialChar && !hasInvalidChar && isLong;
    }

    //Check if the password is the same as confirm_pass field
    function confirmPass() {
        var password = document.getElementById("password");
        var confirm_pass = document.getElementById("confirm_pass");
        return password.value === confirm_pass.value;
    }

    //Function to update the button state(enabled/disabled)
    function updateButtonState() {
        var passedReq = checkReq();
        var confirmedPass = confirmPass();
        var submitBtn = document.getElementById("submitButton");
        submitBtn.disabled = !(passedReq && confirmedPass);
    }

    //Run query when button is clicked
    document.getElementById('submitButton').onclick = async (e) => {
        const roleField = document.querySelector('input[name="role"]:checked');
        console.log(roleField);
        if (!roleField) {
            alert('Please select a role!');
            return;
        }

        const selectedRole = roleField.value;

        try {
            switch (selectedRole) {
                case 'author':
                    const res = await axios.post('<?php echo AUTHOR_API; ?>', {
                        email: emailField.value,
                        username: usernameField.value,
                        password: passField.value,
                    });
                    if (res.data?.success) {
                        const loginRes = await axios.post('<?php echo LOGIN_AUTHOR_API; ?>', {
                            identifier: emailField.value,
                            password: passField.value,
                        });
                        if (loginRes.data?.success) {
                            window.location.href = '<?PHP echo AUTHOR_DASHBOARD_PAGE; ?>';
                        } else {
                            alert('Login failed: ' + (loginRes.data?.error || 'Unknown error'));
                        }
                    } else {
                        alert('Registration failed: ' + (res.data?.error || 'Unknown error'));
                    }
                    break;

                case 'reader':

                    const response = await axios.post('<?php echo USER_API; ?>', {
                        email: emailField.value,
                        username: usernameField.value,
                        password: passField.value,
                    });
                    console.log("EEER" + response.data);

                    if (response.data?.success) {
                        const loginRes = await axios.post('<?php echo LOGIN_USER_API; ?>', {
                            identifier: emailField.value,
                            password: passField.value,
                        });
                        if (loginRes.data?.success) {
                            window.location.href = '<?PHP echo USER_DASHBOARD_PAGE; ?>';
                        } else {
                            alert('Login failed: ' + (loginRes.data?.error || 'Unknown error'));
                        }
                    } else {
                        alert('Registration failed: ' + (res.data?.error || 'Unknown error'));
                    }

                    break;

                default:
                    alert("Error! Invalid Request.");
            }

        } catch (err) {
            if (err.response?.data?.error) {
                alert('Error: ' + err.response.data.error);
            } else {
                alert('Server error');
            }
        }
    }
</script>
</body>

</html>