<?php

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function MercadoPago_2_config()
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $modulo = "mercadopago_2";
    $nombre = "MercadoPago 2";
    $salida = mpconfig($modulo, $nombre);
    return $salida;
}
function MercadoPago_2_link($params)
{
    require_once "MercadoPago_Lib/mercadopago_config.php";
    $salida = mplink($params);
    return $salida;
}

?>