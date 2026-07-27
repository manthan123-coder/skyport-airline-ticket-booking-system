<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .login-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-image: url(angela-compagnone-c0AdVgdusLM-unsplash.jpg);
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
 
}



/* Login box */
.form-wrapper {
  width: 100%;
  max-width: 380px;
  /* PERFECT login width */
  padding: 30px;
  /* background: #ffffff; */
  border: 1px solid #ddd;
  border-radius: 18px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
  text-align: center;
  background-image: url(angela-compagnone-c0AdVgdusLM-unsplash.jpg);
}

/* Inputs */
.form-input {
  width: 100%;
  padding: 12px 14px;
  margin-bottom: 15px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 15px;
  background-color: rgb(207, 218, 222);

}

/* Title */
.form-wrapper h1 {
  margin-bottom: 20px;
  font-size: 26px;
}

.input-group {
  position: relative;
  width: 100%;
  margin-bottom: 15px;
}

.input-icon {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #6c757d;
  pointer-events: visible;
}

/* allow click on eye icon */
.toggle-password {
  pointer-events: auto;
  cursor: pointer;
}

/* space for icon */
.form-input {
  padding-right: 42px;
}


.login-btn {
display: flex;
justify-content: center;
width: 100%;
margin-top: 20px;
clear: both;
color: #4fb4c8;
box-shadow: #6c757d;
}

.login-btn a {
display: inline-block;

background: #2087e8;
color:white;
padding: 6px 18px;
font-weight: bold;
border: black;
border-radius: 5px;
text-decoration: none;
font-weight: none;
/* transition: all 0.6s ease; */
transition: 0.8s;
}

.login-btn a:hover {
background: #ffffff;
color: #000101;
border: 2px solid #050606;
}

    </style>

</head>
<body>
    <div class="login-page">
        <div class="form-wrapper">
            <form action="">

                <h1>Log in</h1>

                <div class="input-group">
                    <input type="text" class="form-input" placeholder="User Name" required="User">
                    <span class="input-icon">
                        <i class="bi bi-person-fill"></i>
                    </span>
                </div>

                <div class="input-group">
                    <input type="password" id="password" class="form-input" placeholder="Password" required="password">
                    <span class="input-icon toggle-password">
                        <i class="bi bi-eye-fill"></i>
                    </span>
                </div>
<div class="login-btn">

    <a href="index.php" class="btn btn-primary w-30 mt-3">Login</a>
</div>

            </form>
        </div>
    </div>

    <script>
        document.querySelector(".toggle-password").addEventListener("click", function () {
            const pass = document.getElementById("password");
            const icon = this.querySelector("i");

            if (pass.type === "password") {
                pass.type = "text";
                icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
            } else {
                pass.type = "password";
                icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
            }
        });

    </script>
</body>

</html>
<?php
include("include/header.php");


?>

