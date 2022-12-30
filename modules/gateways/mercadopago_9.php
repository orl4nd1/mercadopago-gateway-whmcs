<?php

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function MercadoPago_9_config()
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $modulo = "mercadopago_9";
    $nombre = "MercadoPago 9";
    $salida = mpconfig($modulo, $nombre);
    return $salida;
}
function MercadoPago_9_link($params)
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $salida = mplink($params);
    return $salida;
}

?>