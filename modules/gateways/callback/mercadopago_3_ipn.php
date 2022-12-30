<?php


include "../../../init.php";
include ROOTDIR . DIRECTORY_SEPARATOR . "includes/functions.php";
include ROOTDIR . DIRECTORY_SEPARATOR . "includes/gatewayfunctions.php";
include ROOTDIR . DIRECTORY_SEPARATOR . "includes/invoicefunctions.php";
$gatewayModule = "mercadopago_3";
$gateway = new WHMCS\Module\Gateway();
if (!$gateway->isActiveGateway($gatewayModule) || !$gateway->load($gatewayModule)) {
    WHMCS\Terminus::getInstance()->doDie("Module not Active");
}
$GATEWAY = $gateway->getParams();
require_once "../MercadoPago_Lib/mercadopago_config.php";
$chequeo = $_GET["chequeo"];
if (!empty($chequeo)) {
    BayresApp();
    $respuesta = "check mode";
} else {
    $respuesta = mpipn($gatewayModule, $GATEWAY);
}
header("HTTP/1.1 " . $respuesta);
exit("Callback " . $sitio . " by orlandi.dev");

?>