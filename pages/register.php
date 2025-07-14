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
</style>
</head>

<body>
    <?php require_once NAVBAR_COMPONENT; ?>

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
                    <br><br>

                    <label for="agreement" style="font-size:14px;"><input type="checkbox" name="agreement" value="true"
                            id="agreement"> I agree
                        to the
                        the <a href="#">Terms and Condition</a> and <a href="#">Privacy Policy</a></label><br><br><br>
                    <input type="submit" value="Sign Up" id="submitButton">
                </form>


                <p>Already have an account? <a href="<?php echo LOGIN_PAGE; ?>">Login Here</a></p>
            </div>
        </div>
    </main>
</body>


<script>
    //Get Fields
    passField = document.getElementById("password");
    confirm_passField = document.getElementById("confirm_pass");

    //Constantly check for state as fields are being input
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
        try {
            e.preventDefault();
            var emailField = document.getElementById("email");
            var usernameField = document.getElementById("username");
            const res = await axios.post('../api/author.php', {
                email: emailField.value,
                username: usernameField.value,
                password: passField.value,
            });
            console.log(res.data);
            alert(JSON.stringify(res.data));
        } catch (err) {
            if (err.response?.data?.error) {
                alert('Error: ' + err.response.data.error);
            } else {
                alert('Server error');
            }
        }
        alert(res);
    };
</script>
</body>

</html>