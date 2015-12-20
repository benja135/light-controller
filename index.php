<!--
*   Light Controller - Interface lampe connectée
*
*   Auteur: LACHERAY Benjamin
*   Date de création: 05/12/2015
*   Dernière modification: 19/12/2015
*
-->

<?php
    include('lib/hex2rgb.php');

    $timeout = 3;                   // Temps (s) d'attente de la réponse de la lampe avant le timeout
    $url = 'http://192.168.0.25/';  // Adresse de la lampe

    if (isset($_GET["e"])) {
        $url .= $_GET["e"];
    } elseif (isset($_GET["c"]) && isset($_GET["i"])) {
        $url .= 'c' . hex2rgb($_GET["c"]) . ',' . $_GET["i"];
    }
    
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);             // set the url
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // permet de ne pas afficher le résultat
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);    // timeout en secondes
    $output = curl_exec($ch);                       // execute
    curl_close($ch);                                // ferme la connexion
    
    $etat = 'images/2off.png';
    $couleur = '#FFFFFF';
    $intensity = '0';

    // Si la lampe a répondu (eRRRGGGBBBIII)
    if (strlen($output) == 13)
    {
        // On scan l'état de la lampe dans la réponse: "0" ou "1"
        if (substr($output, 0, 1) == '1') {
            $etat = 'images/2on.png';
        } elseif (substr($output, 0, 1) != '0') {
            $erreur .= "La lampe n'a pas communiqué son état actuel. ";
        }

        // Couleur de la lampe dans la réponse: "RRGGGBBB"
        $r = substr($output, 1, 3);
        $g = substr($output, 4, 3);
        $b = substr($output, 7, 3);
        $couleur = rgb2hex(array($r, $g, $b));

        // Intensité de l'éclairage
        $intensity = intval(substr($output, 10, 3));
        
    } elseif ($output == '') { // timeout dans la plupart des cas
        $erreur .= 'La lampe semble être injoignale (timeout). ';
    } else {
        $erreur .= "La lampe n'a pas communiqué les bonnes informations. ";
    }
?>

<!doctype html>

<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Light Controller</title>
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/bootstrap-slider/css/bootstrap-slider.css" rel="stylesheet">
    <script src="lib/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>

    <style type="text/css">
        body {
            background-color: #2E2E2E;
            color: white;
        }
        .contenu {
            padding-left: 10%;
            padding-right: 10%;
        }
        #colorPicker {
            padding: 0px;
            margin: 0px;
            height: 34px;
            width: 20%;
        }
    </style>

    <script type='text/javascript' src="lib/bootstrap-slider/js/bootstrap-slider.js"></script>
    <script type='text/javascript'>
        $(document).ready(function() {
            $("#idSlider").slider({
                tooltip_position:'up',
                formatter: function(value) {
                    if (value == 0) {
                        return "Éteint";
                    } else if (value == 100) {
                        return "Lumineux";
                    }
                    return 'Intensité: ' + value + '%';
                },
                ticks: [0, 50, 100],
                ticks_positions: [0, 50, 100],
                ticks_labels: ['Éteint', 'Moyen', 'Lumineux'],
                ticks_snap_bounds: 4
            });
        });
    </script>  

</head>

<body>

    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand text-center" href="index.php">Light Controller</a>
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
            <h2>Ma lampe</h2>
                <div class="controls">
                    <div class="btn-group">
                        <button type="submit" name="e" class="btn btn-danger" value="off">Éteindre</button>
                        <button type="submit" name="e" class="btn btn-success" value="on">Allumer</button>
                    </div>
                    <img src=<?php echo '"'.$etat.'"'; ?> class="" alt="lampe">
                </div>
            </div>


            <h2>Mes leds</h2>

            <input id="idSlider" name="i" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value=<?php echo '"'.$intensity.'"'; ?>/>
            <br><br>
            <input class="btn" id="colorPicker" type="color" name="c" value=<?php echo '"'.$couleur.'"'; ?>>
            <button type="submit" class="btn btn-primary">Appliquer</button>


        </form>

    </div>


</body>
</html>