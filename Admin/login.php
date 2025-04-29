<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
        }
        .myform {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        }
        .logo img {
            height: 80px;
            width: auto;
            margin-bottom: 20px;
        }
        .login-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-primary {
            border-radius: 50px;
        }
        .google.btn {
            background-color: #db4a39;
            color: white;
            border-radius: 50px;
            text-align: center;
            width: 100%;
        }
        .google.btn:hover {
            background-color: #c23321;
            color: white;
        }
        .login-or {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }
        .hr-or {
            height: 1px;
            margin: 0;
            background-color: #ccc;
        }
        .span-or {
            background: white;
            padding: 0 10px;
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <div class="myform text-center">
            <div class="logo">
                <a href="index.html" class="logo-light">
                    <span class="logo-lg">
                        <img src="../assets/images/logo_cssc.png" alt="logo">
                    </span>
                </a>
            </div>
            <form action="" method="post" name="login">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password">
                </div>
                <div class="form-group">
                    <p class="text-center mb-1">By signing up you accept our <a href="#">Terms Of Use</a></p>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>

                <div class="login-or">
                    <hr class="hr-or">
                    <span class="span-or">or</span>
                </div>

                <div class="form-group">
                    <a href="javascript:void();" class="google btn"><i class="fa fa-google-plus"></i> Signup using Google</a>
                </div>
                <div class="form-group">
                    <p class="text-center mb-0">Don't have an account? <a href="#">Sign up here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
        }
        .myform {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        }
        .logo img {
            height: 80px;
            width: auto;
            margin-bottom: 20px;
        }
        .login-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-primary {
            border-radius: 50px;
        }
        .google.btn {
            background-color: #db4a39;
            color: white;
            border-radius: 50px;
            text-align: center;
            width: 100%;
        }
        .google.btn:hover {
            background-color: #c23321;
            color: white;
        }
        .login-or {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }
        .hr-or {
            height: 1px;
            margin: 0;
            background-color: #ccc;
        }
        .span-or {
            background: white;
            padding: 0 10px;
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <div class="myform text-center">
            <div class="logo">
                <a href="index.html" class="logo-light">
                    <span class="logo-lg">
                        <img src="../assets/images/logo_cssc.png" alt="logo">
                    </span>
                </a>
            </div>
            <form action="" method="post" name="login">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password">
                </div>
                <div class="form-group">
                    <p class="text-center mb-1">By signing up you accept our <a href="#">Terms Of Use</a></p>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>

                <div class="login-or">
                    <hr class="hr-or">
                    <span class="span-or">or</span>
                </div>

                <div class="form-group">
                    <a href="javascript:void();" class="google btn"><i class="fa fa-google-plus"></i> Signup using Google</a>
                </div>
                <div class="form-group">
                    <p class="text-center mb-0">Don't have an account? <a href="#">Sign up here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


