<?php
    // Merci à c.bavota : http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/

    // Convertit une couleur #hexa en couleur RGB
    function hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);
        return implode(",", $rgb); // returns the rgb values separated by commas
    }

    // Convertit une couleur RGB en #hexa
    function rgb2hex($rgb) {
       $hex = "#";
       $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
       $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
       $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
       return $hex; // returns the hex value including the number sign (#)
    }

?>