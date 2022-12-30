<?php

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function MercadoPago_5_config()
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $modulo = "mercadopago_5";
    $nombre = "MercadoPago 5";
    $salida = mpconfig($modulo, $nombre);
    return $salida;
}
function MercadoPago_5_link($params)
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $salida = mplink($params);
    return $salida;
}

?>