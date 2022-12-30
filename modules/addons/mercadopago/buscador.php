<?php

include "../../../init.php";
include ROOTDIR . DIRECTORY_SEPARATOR . "includes/functions.php";
include ROOTDIR . DIRECTORY_SEPARATOR . "includes/gatewayfunctions.php";
include ROOTDIR . DIRECTORY_SEPARATOR . "includes/invoicefunctions.php";
require_once ROOTDIR . DIRECTORY_SEPARATOR . "modules/gateways/MercadoPago_Lib/mercadopago_config.php";
$gatewayModule = "mercadopago_1";
$gateway = new WHMCS\Module\Gateway();
if (!$gateway->isActiveGateway($gatewayModule) || !$gateway->load($gatewayModule)) {
    WHMCS\Terminus::getInstance()->doDie("Module not Active");
}
$GATEWAY = $gateway->getParams();
$licencia_valida = ORLANDI_Verificar_Licencia();
if ($licencia_valida[0]) {
    echo "Licencia validada<br>";
    $token = $GATEWAY["bh_Access_Token"];
    $userid = substr(strrchr($token, "-"), 1);
    $adminUsername = $GATEWAY["useradmin"];
    $ch = curl_init("https://api.mercadopago.com/v1/payments/search?operation_type=regular_payment&status=approved&begin_date=NOW-3DAYS&end_date=NOW&range=date_last_updated&sort=date_last_updated&criteria=desc");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "x-integrator-id: dev_ea52525a0a6e11eb98420242ac130004", "Authorization: Bearer " . $token]);
    $result = curl_exec($ch);
    curl_close($ch);
    $datosdelpago = json_decode($result, true);
    $resultados = $datosdelpago["results"];
    foreach ($resultados as $pago) {
        $mp_transaccion = $pago["id"];
        $comision = $pago["fee_details"][0]["amount"];
        $cobrado = $pago["transaction_details"]["total_paid_amount"];
        $nrofactura = $pago["external_reference"];
        $status = $pago["status"];
        echo "ID: " . $mp_transaccion . " Status: " . $status . " FC: " . $nrofactura . "<br>";
        if ($status == "approved") {
            $command = "GetTransactions";
            $postData = ["transid" => $mp_transaccion];
            $arr_transacciones = localAPI($command, $postData, $adminUsername);
            if ($arr_transacciones["totalresults"] == 0 && !empty($nrofactura)) {
                $moneda_de_cobro = $pago["currency_id"];
                $command = "GetInvoice";
                $postData = ["invoiceid" => $nrofactura];
                $arr_datos_factura = localAPI($command, $postData, $adminUsername);
                if ($arr_datos_factura["result"] == "success") {
                    $datos_factura = print_r($arr_datos_factura, true);
                    $usuario_id = $arr_datos_factura["userid"];
                    $balance = $arr_datos_factura["balance"];
                    if ($GATEWAY["bh_comportamiento"] != "normal") {
                        $importe_pagado = $balance;
                    } else {
                        $importe_pagado = $pago["transaction_details"]["total_paid_amount"];
                        $command = "GetClientsDetails";
                        $postData = ["clientid" => $usuario_id];
                        $arr_datos_cliente = localAPI($command, $postData, $adminUsername);
                        $moneda_code_usuario = $arr_datos_cliente["currency_code"];
                        if ($moneda_de_cobro != $moneda_code_usuario) {
                            $command = "GetCurrencies";
                            $postData = [];
                            $arr_listademonedas = localAPI($command, $postData, $adminUsername);
                            $monedero = $arr_listademonedas["currencies"]["currency"];
                            foreach ($monedero as $datosmoneda) {
                                $moneda_code = $datosmoneda["code"];
                                $monedasporcode[$moneda_code] = $datosmoneda["rate"];
                            }
                            $tasadeconversion = $monedasporcode[$moneda_code_usuario];
                            $ximporte_pagado = round($importe_pagado * $tasadeconversion, 2);
                            $xcomision = round($comision * $tasadeconversion, 2);
                            $importe_pagado = $ximporte_pagado;
                            $comision = $xcomision;
                            $conversionlog = traduccion($idioma, "mpconfig_73") . ": " . $moneda_code_usuario . "\r\n                                " . traduccion($idioma, "mpconfig_74") . ": " . $importe_pagado . "\r\n                                " . traduccion($idioma, "mpconfig_75") . ": " . $comision;
                        }
                    }
                    $command = "AddTransaction";
                    $postData = ["paymentmethod" => "mercadopago_1", "invoiceid" => $nrofactura, "transid" => $mp_transaccion, "amountin" => $importe_pagado, "fees" => $comision];
                    $results = localAPI($command, $postData, $adminUsername);
                    $texto_log = "\r\n                        " . traduccion($idioma, "mpconfig_56") . ": " . $nrofactura . "\r\n                        " . traduccion($idioma, "mpconfig_57") . ": " . $GATEWAY["name"] . "\r\n                        " . traduccion($idioma, "mpconfig_58") . ": " . $mp_transaccion . "\r\n                        " . traduccion($idioma, "mpconfig_61") . ": " . $pago["date_approved"] . "\r\n                        " . traduccion($idioma, "mpconfig_62") . ": " . $pago["payment_type_id"] . " - " . $pago["payment_method_id"] . "\r\n                        " . traduccion($idioma, "mpconfig_63") . ": " . $pago["currency_id"] . "\r\n                        " . traduccion($idioma, "mpconfig_64") . ": " . $pago["transaction_details"]["total_paid_amount"] . "\r\n                        " . traduccion($idioma, "mpconfig_65") . ": " . $pago["transaction_details"]["net_received_amount"] . "\r\n                        " . $conversionlog;
                    logTransaction($GATEWAY["name"], $texto_log, traduccion($idioma, "mpconfig_66") . " [" . $nrofactura . "]");
                    $resultado = Illuminate\Database\Capsule\Manager::table("tbltransaction_history")->where("transaction_id", "=", $mp_transaccion)->delete();
                }
            }
        }
    }
} else {
    echo "ERROR: " . $licencia_valida["mensaje"];
}

?>