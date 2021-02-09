<!-- Création d'un raccourcisseur d'URL -->
<!-- Tout va se faire sur cette page -->

<?php
// S'il existe un shortcut
if (isset($_GET['q'])) {

    // variable
    $shortcut = htmlspecialchars($_GET['q']);

    // S'il s'agit bien d'un shortcut
    $bdd = new PDO(
        'mysql:host=localhost;
        dbname=bitly;charset=utf8',
        'root',
        ''
    );

    // On vérifie si le shortcut à déjà été utilisé
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    while ($result = $req->fetch()) {
        // Si shortcut non connu alors on l'informe !
        if ($result['x'] != 1) {
            header('location: ./?error=true&message=Adresse URL non connue !');
            exit();
        }
    }
    // Redirection si shortcut connu !
    $req = $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    while ($result = $req->fetch()) {

        header('location : '.$result['url']);
        exit();
    }
}

// Vérification de l'envoi du form
if (isset($_POST['url'])) {

    // Variable URL
    $url = $_POST['url'];

    // Verification si l'adresse envoyée est bien valide
    // ! Si filter var est true il deviendra false
    // lorsque notre URL n'existera pas
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        // Si lien pas valide
        header('location: ./?error=true&message=Adresse URL non valide');
        // Permet d'arrêter le script !
        // Surtout s'il y a plusieurs header
        exit();
    }
    // Création du shortcut à partir de l'url donnée
    $shortcut = crypt($url, rand());

    // On vérifie si l'URL a déjà été raccourcie
    $bdd = new PDO(
        'mysql:host=localhost;
        dbname=bitly;charset=utf8',
        'root',
        ''
    );
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
    $req->execute(array($url));

    while ($result = $req->fetch()) {

        if ($result['x'] != 0) {
            header('location:./?error=true&message=Adresse déjà raccourcie !');
            exit();
        }
    }

    // Si tout est OK envoi tout dans la BDD
    $req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES (?, ?)');
    $req->execute(array($url, $shortcut));

    header('location: ./?short=' . $shortcut);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type='text/css' href="design/default.css">
    <link rel="icon" type='img/png' href="pictures/favico.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raccourcisseur d'URL</title>
</head>

<body>
    <!-- PRESENTATION -->
    <section id='hello'>

        <!-- CONTAINER -->
        <div class='container'>

            <!-- HEADER -->
            <header>
                <img src='./pictures/logo.png' alt='logo' id='logo' />
            </header>

            <!-- VP -->
            <h1> Une URL longue raccourcissez là. </h1>
            <h2> Largement meilleur et plus court que les autres. </h2>

            <!-- FORMULAIRE -->
            <form method='post' action='./index.php'>
                <input type='url' name='url' placeholder='Coller votre URL ici !' />
                <input type='submit' value='Raccourcir !'>
            </form>

            <?php if (isset($_GET['error']) && isset($_GET['message'])) { ?>
                <div class='center'>
                    <div id="result">
                        <b><?php echo htmlspecialchars($_GET['message']) ?></b>
                    </div>
                </div>
            <?php } else if (isset($_GET['short'])) { ?>
                <div class='center'>
                    <div id="result">
                        <b> URL raccourcie : </b>
                        <!-- q= URL GET -->
                        http://localhost/projet_3/?q=<?php echo htmlspecialchars($_GET['short']) ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- BRANDS SECTION -->
    <section id='brands'>

        <!-- CONTAINER -->
        <div class='container'>

            <!-- TITLE -->
            <h3> Ces marques nous font confiance </h3>

            <!-- PICTURES -->
            <img src='./pictures/1.png' alt='1' class='picture' />
            <img src='./pictures/2.png' alt='2' class='picture' />
            <img src='./pictures/3.png' alt='3' class='picture' />
            <img src='./pictures/4.png' alt='4' class='picture' />
        </div>
    </section>

    <!-- FOOTER SECTION -->
    <footer>

        <!-- LOGO -->
        <img src='./pictures/logo2.png' alt='logo' id='logo' />

        <!-- COPYRIGHT -->
        <p>2021 @ Bitly // LOUIS Nicolas UDEMY => https://www.udemy.com/course/php-et-mysql-la-formation-ultime/ </p>

        <!-- URL -->
        <a href='#'>Contact</a> - <a href='#'> A propos </a>

    </footer>
</body>

</html>