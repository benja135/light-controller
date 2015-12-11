<!--
*   Light Controller - Interface lampe connectée
*
*   Auteur: LACHERAY Benjamin
*   Date de création: 05/12/2015
*   Dernière modification: 9/12/2015
*
-->

<?php
    include('lib/hex2rgb.php');

    $timeout = 8;                   // Temps (s) d'attente de la réponse de la lampe avant le timeout
    $url = 'http://192.168.0.25/';  // Adresse de la lampe

    if (isset($_GET["e"])) {
        $url .= $_GET["e"];
    } elseif (isset($_GET["c"])) {
        $url .= 'c' . hex2rgb($_GET["c"]);
    }
    
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);             // set the url
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // permet de ne pas afficher le résultat
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);    // timeout en secondes
    $output = curl_exec($ch);                       // execute
    curl_close($ch);                                // ferme la connexion

    
    // Si la lampe a répondu (eRRRGGGBBB)
    if ($output != '') 
    {
        // On scan l'état de la lampe dans la réponse: "0" ou "1"
        if (substr($output, 0, 1) == '1') {
            $etat = 'images/2on.png';
        } else {
            if (substr($output, 0, 1) != '0') {
                $erreur .= "La lampe n'a pas communiqué son état actuel. ";
            }
            $etat = 'images/2off.png';
        }

        // On scan la couleur de la lampe dans la réponse: "RRGGGBBB"
        if ($pos !== false) {                       // Si elle a communiqué sa couleur
            $r = substr($output, 1, 3);
            $g = substr($output, 4, 3);
            $b = substr($output, 7, 3);
            $couleur = rgb2hex(array($r, $g, $b));
        } else {
            $erreur .= "La lampe n'a pas communiqué sa couleur actuelle. ";
            $couleur = '#FFFFFF';
        }
    } else
    {
        $erreur .= 'La lampe semble être injoignale (timeout). ';
        $etat = 'images/2off.png';
        $couleur = '#FFFFFF';
    }
?>

<!doctype html>

<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Light Controller</title>
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="lib/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
    
    <style type="text/css">
        body {
            background-color: #2E2E2E;
            margin-top: 25px;
        }
        .contenu {
            padding-left: 10%;
            padding-right: 10%;
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand text-center" href="#">Light Controller</a>
            </div>
        </div>
    </nav>

    <div class="container contenu">

        <?php
            if (!empty($erreur)) {
                echo '<div class="alert alert-danger fade in">';
                echo '<a href="#" class="close" data-dismiss="alert">&times;</a>';
                echo '<strong>Erreur!</strong> '.$erreur.'</div>';
            }
        ?>

        <form method="get" role="form" action="index.php"> 

            <div class="control-group">
                <div class="controls">
                    <div class="btn-group">
                        <button type="submit" name="e" class="btn btn-danger" value="off">Éteindre</button>
                        <button type="submit" name="e" class="btn btn-success" value="on">Allumer</button>
                    </div>
                    <img src=<?php echo '"'.$etat.'"'; ?> class="" alt="lampe">
                </div>
            </div>
            

            <div class="control-group">
                <div class="controls">
                    <input type="color" class="btn btn-small" name="c" value=<?php echo '"'.$couleur.'"'; ?>>
                    <button type="submit" class="btn btn-primary">Appliquer</button>
                </div>
            </div>

        </form>

    </div>


</body>
</html>