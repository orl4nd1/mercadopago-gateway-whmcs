<?php

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function MercadoPago_8_config()
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $modulo = "mercadopago_8";
    $nombre = "MercadoPago 8";
    $salida = mpconfig($modulo, $nombre);
    return $salida;
}
function MercadoPago_8_link($params)
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $salida = mplink($params);
    return $salida;
}

?>