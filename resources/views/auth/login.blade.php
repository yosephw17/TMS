<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Login page for the clinic management system">
    <meta name="author" content="">
    <title>Login - Team Up Management</title>
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <!-- Custom styles for this template -->
    <link href="css/styles.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Custom styling -->
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            background-color: #eef2f7;
            font-family: Arial, sans-serif;
        }

        .d-flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 1rem;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 12px;
            max-width: 420px;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #033841;
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem 1rem;
            text-align: center;
        }

        .card-header img {
            max-width: 100px;
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0;
        }

        .card-body {
            padding: 2rem;
        }

        .form-floating label {
            color: #555;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #033841;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .footer {
            text-align: center;
            font-size: 0.85rem;
            color: #888;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="d-flex-center">
        <div class="card shadow-lg">
            <div class="card-header">
                <!-- Logo section -->
                <img src="{{ asset('assets/img/logo.png') }}" alt="Company Logo" class="company-logo" />
                <h3 class="text-center">TeamUp
                    Management Login</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Email field -->
                    <div class="form-floating mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <label for="email">Email Address</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!-- Password field -->
                    <div class="form-floating mb-3">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password" required
                            autocomplete="current-password">
                        <label for="password">Password</label>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!-- Submit button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                </form>
                <div class="footer mt-4">
                    &copy; 2024 TeamUp Management. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <!-- Custom scripts -->
    <script src="js/scripts.js"></script>
</body>

</html>
