<?php
    session_start();

    require 'connect.php';

    $conn = mysqli_connect($host, $user, $pass, $db) or die("Błąd połączenia z bazą!");

    function poprawnyEmail($email) 
    {
        return preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
    }

    function poprawneHaslo($haslo) 
    {
        return preg_match('/^(?=.*[0-9])(?=.*[\W_]).{8,}$/', $haslo);
    }

    if (isset($_GET['form'])) 
    {
        $biezacyFormularz = $_GET['form'];
    } 
    else {
        $biezacyFormularz = 'rejestracja';
    }

    if ($biezacyFormularz === 'rejestracja' && isset($_POST['rejestracja'])) 
    {
        $nazwa = $_POST['uzytkownik'];
        $email = $_POST['email'];
        $haslo = hash('sha256', $_POST['haslo']);

        if (!poprawnyEmail($email)) 
        {
            echo "<p>Błędny format adresu e-mail.</p>";
        } 
        elseif (!poprawneHaslo($_POST['haslo'])) 
        {
            echo "<p>Hasło musi mieć co najmniej 8 znaków, zawierać przynajmniej jedną wielką literę, jedną małą literę i jedną cyfrę.</p>";
        } 
        else {
            $sql = "INSERT INTO user_data (username, email, password) VALUES ('$nazwa', '$email', '$haslo')";
            $result = mysqli_query($conn, $sql) or die("Błąd w zapytaniu!");

            if ($result) 
            {
                echo "<p>Rejestracja zakończona pomyślnie!</p>";
            } 
            else 
            {
                echo "<p>Błąd podczas rejestracji.</p>";
            }
        }
    } 
    elseif ($biezacyFormularz === 'logowanie' && isset($_POST['logowanie'])) 
    {
        $nazwa = $_POST['uzytkownik'];
        $haslo = $_POST['haslo'];

        $sql = "SELECT * FROM user_data WHERE username = '$nazwa'";
        $result = mysqli_query($conn, $sql) or die("Błąd w zapytaniu!");

        if ($result) 
        {
            $uzytkownik = mysqli_fetch_row($result);

            if ($uzytkownik && hash('sha256', $haslo) === $uzytkownik[3]) 
            {
                $_SESSION['id_uzytkownika'] = $uzytkownik[0];
                echo "<p>Zalogowano pomyślnie!</p>";
            } 
            else 
            {
                echo "<p>Błędny login lub hasło.</p>";
            }
        } 
        else 
        {
            echo "<p>Błąd podczas logowania</p>";
        }
    }

    session_destroy();

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rejestracja i logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="?form=rejestracja">Rejestracja</a>
        <a href="?form=logowanie">Logowanie</a>
    </nav>
    <?php
    if ($biezacyFormularz === 'rejestracja') 
    {
    ?>
        <form method="post" action="login.php?form=rejestracja">
            <h2>Rejestracja</h2>
            <label>Nazwa użytkownika: <input type="text" name="uzytkownik" required></label><br>
            <label>Adres e-mail: <input type="text" name="email" required></label><br>
            <label>Hasło: <input type="password" name="haslo" required></label><br>
            <input type="submit" name="rejestracja" value="Zarejestruj">
        </form>
    <?php
    } 
    elseif ($biezacyFormularz === 'logowanie') 
    {
    ?>
        <form method="post" action="login.php?form=logowanie">
            <h2>Logowanie</h2>
            <label>Nazwa użytkownika: <input type="text" name="uzytkownik" required></label><br>
            <label>Hasło: <input type="password" name="haslo" required></label><br>
            <input type="submit" name="logowanie" value="Zaloguj">
        </form>
    <?php
    }
    ?>
</body>
</html>
