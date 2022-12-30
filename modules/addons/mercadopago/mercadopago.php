<?php


require_once "../modules/gateways/MercadoPago_Lib/mercadopago_config.php";
if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function mercadopago_config()
{
    $idioma = chequeo_idioma();
    if (empty($idioma)) {
        $idioma = "ar";
    }
    $msgversion = chequeoversion();
    $output = obtenervalidacion();
    return ["name" => "MercadoPago", "description" => traduccion($idioma, "mercadopago_config_1"), "author" => "Orlandi", "language" => "spanish", "version" => "17", "fields" => ["licencia" => ["FriendlyName" => traduccion($idioma, "mercadopago_config_2"), "Type" => "text", "Size" => "25", "Description" => "&nbsp;&nbsp;" . $msgversion . "<br>" . $output], "verificador" => ["FriendlyName" => traduccion($idioma, "mercadopago_config_3"), "Type" => "textarea", "Rows" => "6", "Cols" => "60", "Description" => traduccion($idioma, "mercadopago_config_4")], "idioma" => ["FriendlyName" => traduccion($idioma, "mercadopago_config_5"), "Type" => "dropdown", "Options" => ["ar" => "Español", "br" => "Português", "us" => "English"], "Default" => "ar", "Description" => traduccion($idioma, "mercadopago_config_6")]]];
}
function mercadopago_activate()
{
    $idioma = chequeo_idioma();
    try {
        WHMCS\Database\Capsule::schema()->create("whmcs_mercadopago", function ($table) {
            $table->increments("id");
            $table->string("transaccion")->unique();
            $table->dateTime("momento");
            $table->string("gateway");
        });
    } catch (Exception $e) {
        echo "Imposible crear la tabla: " . $e->getMessage();
    }
    return ["status" => "success", "description" => traduccion($idioma, "mercadopago_activate_1")];
}
function mercadopago_deactivate()
{
    $idioma = chequeo_idioma();
    WHMCS\Database\Capsule::schema()->dropIfExists("whmcs_mercadopago");
    return ["status" => "success", "description" => traduccion($idioma, "mercadopago_deactivate_1")];
}
function mercadopago_output($vars)
{
    echo "Próximamente...<br><pre>" . print_r($vars, true) . "</pre>";
}

?>