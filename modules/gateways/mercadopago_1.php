<?php

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function MercadoPago_1_config()
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $modulo = "mercadopago_1";
    $nombre = "MercadoPago 1";
    $salida = mpconfig($modulo, $nombre);
    return $salida;
}
function MercadoPago_1_link($params)
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $salida = mplink($params);
    return $salida;
}

?>