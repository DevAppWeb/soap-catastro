<?php
if (!empty($_POST)) {

    $cliente = new SoapClient(
            "http://ovc.catastro.meh.es/ovcservweb/OVCSWLocalizacionRC/OVCCallejero.asmx?WSDL"
    );

    $provincia = filter_input(INPUT_POST, 'provincia', FILTER_SANITIZE_STRING);

//Obtengo la lista de divisas posibles
// $lista_divisas = $cliente->GetCurrencies()->GetCurrenciesResult->string;
    $lista_municipios_xml = $cliente->ObtenerMunicipios($provincia)->any;

    $lista_municipios = new SimpleXMLElement($lista_municipios_xml);

    $lista_municipios_oo = json_decode(json_encode($lista_municipios));

    if (isset($lista_municipios_oo->lerr)) {
        $errorProvincia = true;
    } else {
        $municipios = array_map(fn($x) => $x->nm, $lista_municipios_oo->municipiero->muni);
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>soap catastro</title>
        <meta name="viewport" content="width=device-width">
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
    </head>
    <body>
        <h1>Enter text:</h1>
        <div class="capaform">
            <form name="Select_Words" class="form-font"
                  action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="form-section">
                    <div class="input-section">
                        <label for="provincia">Provincia:</label> 
                        <input type="text" name="provincia" id="provincia" value="<?= ($provincia ?? '') ?>" />
                    </div>
                    <div class="submit-section">
                        <input class="submit" type="submit" 
                               value="Send" name="button" />  
                    </div>
                </div>
            </form>
            <?php if (isset($errorProvincia)): ?>
                <p><?= "Provincia Incorrecta" ?></p>
            <?php endif ?>
            <?php if (isset($municipios)): ?>
                <div class="info">
                    <?php foreach ($municipios as $municipio): ?>
                        <p><?= $municipio ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>
    </body>
</html>

