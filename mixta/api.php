<?php
// api.php - Endpoint REST que sirve JSON

header('Content-Type: application/json; charset=utf-8');
// Si vas a abrir los HTML con un server estático distinto, descomenta CORS:
// header('Access-Control-Allow-Origin: *');

function getData() {
    $json = '{
      "categorias": [
        {"slug": "cocinas", "nombre": "Cocinas"},
        {"slug": "banos", "nombre": "Baños"},
        {"slug": "electrohogar", "nombre": "Electrohogar"}
      ],
      "productos": [
       {"id": 1, "nombre": "Mueble Cocina Blanco", "precio": 799.99, "categoria_slug": "cocinas", "descripcion": "Cocina modular con acabado blanco mate.", "img": "https://www.murellicucine.com/wp-content/uploads/2019/10/Captura-de-pantalla-2019-10-10-a-las-13.37.39.png"},
        {"id": 2, "nombre": "Encimera Granito", "precio": 299.00, "categoria_slug": "cocinas", "descripcion": "Encimera resistente de granito natural.", "img": "https://storage.googleapis.com/catalog-pictures-carrefour-es/catalog/pictures/hd_510x_/8059019005195_1.jpg"},
        {"id": 3, "nombre": "Lavabo Suspendido", "precio": 159.50, "categoria_slug": "banos", "descripcion": "Lavabo con instalación suspendida y sifón oculto.", "img": "https://www.murellicucine.com/wp-content/uploads/2019/10/Captura-de-pantalla-2019-10-10-a-las-13.37.39.png"},
        {"id": 4, "nombre": "Mampara Ducha 120", "precio": 220.00, "categoria_slug": "banos", "descripcion": "Mampara corredera templada 6mm.", "img" : "https://www.murellicucine.com/wp-content/uploads/2019/10/Captura-de-pantalla-2019-10-10-a-las-13.37.39.png"},
        {"id": 5, "nombre": "Lavadora 8kg A+++", "precio": 399.90, "categoria_slug": "electrohogar", "descripcion": "Lavadora eficiente con 15 programas.", "img": "https://www.murellicucine.com/wp-content/uploads/2019/10/Captura-de-pantalla-2019-10-10-a-las-13.37.39.png"}
 
      ]
    }';
    return json_decode($json, true);
}

$data = getData();

$resource = $_GET['resource'] ?? 'categorias';

// Rutas simples:
// - /api.php?resource=categorias
// - /api.php?resource=productos[&categoria_slug=...]
// - /api.php?resource=producto&id=...
switch ($resource) {
    case 'categorias':
        echo json_encode($data['categorias'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        break;

    case 'productos':
        $categoria = $_GET['categoria_slug'] ?? null;
        $result = $data['productos'];
        if ($categoria) {
            $result = array_values(array_filter($result, function($p) use ($categoria) {
                return $p['categoria_slug'] === $categoria;
            }));
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        break;

    case 'producto':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $found = null;
        foreach ($data['productos'] as $p) {
            if ((int)$p['id'] === $id) {
                $found = $p;
                break;
            }
        }
        if ($found) {
            echo json_encode($found, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Producto no encontrado"]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Recurso no soportado"]);
}
