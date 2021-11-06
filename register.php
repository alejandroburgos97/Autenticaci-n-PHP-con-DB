<?php
require_once "config.php";
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "El nombre de usuario contiene caracteres no válidos";
    } else {
        $sql = "SELECT id FROM usuario WHERE user = ?";
        echo ("Aqui");

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = trim($_POST["username"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Este nombre de usuario ya ha sido tomado";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Algo salió mal, por favor intente de nuevo.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    if (empty(trim($_POST["password"]))) {
        $password_err = "Ingrese su contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Su contraseña debe tener al menos 6 caracteres";
    } else {
        $password = trim($_POST["password"]);
    }
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor confirme su contraseña.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO usuario (user, pw) VALUES (?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            echo ($username);
            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php");
            } else {
                echo "Oops! Algo salió mal, por favor intente de nuevo.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="vh-100 d-flex justify-content-center align-items-center">
        <div class="card" style="width: 25rem;">
            <div class="card-body">
                <h2>Registrarse</h2>
                <p>Por favor ingrese sus datos.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Usuario</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Enviar">
                    </div>
                    <p>Ya tiene una cuenta? <a href="login.php">Ingrese aquí</a>.</p>
                </form>

            </div>
        </div>
    </div>
</body>

</html>