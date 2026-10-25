<?php
// Almacena la localidad seleccionada por el usuario
$localidad = "";
$localidadesPermitidas = [
    "Kennedy",
    "Rafael Uribe",
    "Tunjuelito",
    "Usaquen",
    "Antonio Nariño",
    "Chapinero",
    "Santa Fe",
    "San Cristobal",
    "Usme",
    "Bosa",
    "Fontibon",
    "Engativa",
    "Barrios Unidos",
    "Teusaquillo",
    "Los Martires",
    "Puente Aranda",
    "La Candelaria",
    "Ciudad Bolivar",
    "Suba",
    "Sumapaz"
];
$url = "https://serviciosgis.catastrobogota.gov.co/arcgis/rest/services/Mapa_Referencia/Mapa_Referencia/MapServer/8/query";

// Obtiene la localidad enviada mediante el formulario GET
if (isset($_GET["localidad"])) {
   $localidad = $_GET["localidad"];
}
// Define los filtros y campos solicitados al servicio ArcGIS
$parametros = [
    "where" => "LOCALIDAD = '$localidad'",
    "outFields" => "NOMBRE,DIRECCION_,LATITUD,LONGITUD",
    "returnGeometry" => "true",
    "outSR" => "4326",
    "resultRecordCount" => "10",
    "f" => "geojson"
];

if ($localidad != "" && in_array($localidad, $localidadesPermitidas, true)) {
// Construye la URL de consulta a partir de los parámetros
    $urlConsulta = $url . "?" . http_build_query($parametros);
// Realiza la petición HTTP y obtiene la respuesta del servicio
    $respuesta = @file_get_contents($urlConsulta);

    if ($respuesta === false) {
        $mensaje = "No fue posible conectar con la API.";
    } else {
// Convierte la respuesta JSON en un arreglo asociativo de PHP        
        $datos = json_decode($respuesta, true);
// Extrae los registros geográficos de la propiedad features
        $paraderos = $datos["features"] ?? [];
        $mensaje = "La API respondió correctamente.";
    }

} elseif ($localidad != "") {
    $mensaje = "La localidad seleccionada no es válida.";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta de paraderos SITP</title>
</head>

<body>

    <h1>Consulta de paraderos SITP</h1>

    <form method="GET">

        <label for="localidad">Selecciona una localidad:</label>
        
        <select name="localidad" id="localidad">

            <option value="">Selecciona una opcion</option>
            <option value="Kennedy">Kennedy</option>
            <option value="Rafael Uribe">Rafael Uribe</option>
            <option value="Tunjuelito">Tunjuelito</option>
            <option value="Usaquen">Usaquen</option>
            <option value="Antonio Nariño">Antonio Nariño</option>
            <option value="Chapinero">Chapinero</option>
            <option value="Santa Fe">Santa Fe</option>
            <option value="San Cristobal">San Cristobal</option>
            <option value="Usme">Usme</option>
            <option value="Bosa">Bosa</option>
            <option value="Fontibon">Fontibon</option>
            <option value="Engativa">Engativa</option>
            <option value="Barrios Unidos">Barrios Unidos</option>
            <option value="Teusaquillo">Teusaquillo</option>
            <option value="Los Martires">Los Martires</option>
            <option value="Puente Aranda">Puente Aranda</option>
            <option value="La Candelaria">La Candelaria</option>
            <option value="Ciudad Bolivar">Ciudad Bolivar</option>
            <option value="Suba">Suba</option>
            <option value="Sumapaz">Sumapaz</option>

        </select>

        <button type="submit">Consultar paraderos</button>

    </form>
    
    <?php

    if (isset($mensaje)) {
        echo "<p>$mensaje</p>";
    }

    ?>

    <?php if (!empty($paraderos)): ?>

        <h2>Paraderos encontrados</h2>

        <table border="1">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Latitud</th>
                    <th>Longitud</th>
                </tr>
            </thead>
            <tbody>
<!-- Recorre los paraderos y genera una fila por cada registro -->
                <?php foreach ($paraderos as $paradero): ?>

                    <tr>
                        <td><?= htmlspecialchars($paradero["properties"]["NOMBRE"] ?? "Sin nombre") ?></td>

                        <td><?= htmlspecialchars($paradero["properties"]["DIRECCION_"] ?? "Sin dirección") ?></td>

                        <td><?= htmlspecialchars((string) ($paradero["properties"]["LATITUD"] ?? "No disponible")) ?></td>

                        <td><?= htmlspecialchars((string) ($paradero["properties"]["LONGITUD"] ?? "No disponible")) ?></td>
                    </tr>

                <?php endforeach; ?>


            </tbody>
        </table>

    <?php elseif ($localidad != "" && isset($respuesta) && $respuesta !== false): ?>

        <p>No se encontraron paraderos para la localidad seleccionada.</p>

    <?php endif; ?>

    <?php

    if ($localidad != "") {
        echo "<p>Localidad seleccionada: $localidad</p>";
    }

    ?>

</body>

</html>