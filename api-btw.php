<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');




include_once 'config.php';

// Função para retornar resposta padrão
function response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
}

// Função para verificar se o limite de acessos foi atingido
function verificaLimiteAcessos($id_empresa) {
    global $pdo;
    
    // Verificar o limite de acessos na empresa
    $sql = "SELECT qtd_acessos FROM empresa WHERE id = :id_empresa";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_empresa' => $id_empresa]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($empresa) {
        // Verificar a quantidade de máquinas cadastradas para a empresa
        $sql = "SELECT COUNT(*) FROM maquinas WHERE id_empresa = :id_empresa";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_empresa' => $id_empresa]);
        $qtd_maquinas = $stmt->fetchColumn();
        
        // Comparar quantidade de máquinas com o limite de acessos
        if ($qtd_maquinas >= $empresa['qtd_acessos']) {
            return false; // Limite de acessos atingido
        }
        
        return true; // Ainda há espaço para mais máquinas
    }
    
    return false; // Empresa não encontrada
}

// Verifica o método HTTP e chama a função apropriada
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['cod_maquina'])) {
            // Consultar máquina por ID
            $cod_maquina = $_GET['cod_maquina'];
            $sql = "SELECT * FROM maquinas WHERE cod_maquina = :cod_maquina";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['cod_maquina' => $cod_maquina]);
            $maquina = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($maquina) {
                response($maquina);
            } else {
                response(['message' => 'Máquina não encontrada'], 404);
            }
        } else {
            // Listar todas as máquinas
            $sql = "SELECT * FROM maquinas";
            $stmt = $pdo->query($sql);
            $maquinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            response($maquinas);
        }
        break;

    case 'POST':
        // Adicionar nova máquina
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->cod_maquina) && !empty($data->id_empresa)) {
            // Verificar se o limite de acessos foi atingido
            if (!verificaLimiteAcessos($data->id_empresa)) {
                response(['message' => 'Você atingiu o limite de acessos cadastrados'], 400);
                exit();
            }

            $sql = "INSERT INTO maquinas (cod_maquina, id_empresa) VALUES (:cod_maquina, :id_empresa)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'cod_maquina' => $data->cod_maquina,
                'id_empresa' => $data->id_empresa
            ]);
            response(['message' => 'Máquina cadastrada com sucesso'], 201);
        } else {
            response(['message' => 'Dados incompletos'], 400);
        }
        break;

    case 'PUT':
        // Atualizar máquina
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id) && !empty($data->cod_maquina) && !empty($data->id_empresa)) {
            // Verificar se o limite de acessos foi atingido
            if (!verificaLimiteAcessos($data->id_empresa)) {
                response(['message' => 'Você atingiu o limite de acessos cadastrados'], 400);
                exit();
            }

            $sql = "UPDATE maquinas SET cod_maquina = :cod_maquina, id_empresa = :id_empresa WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'cod_maquina' => $data->cod_maquina,
                'id_empresa' => $data->id_empresa,
                'id' => $data->id
            ]);
            response(['message' => 'Máquina atualizada com sucesso']);
        } else {
            response(['message' => 'Dados incompletos ou máquina não encontrada'], 400);
        }
        break;

    case 'DELETE':
        // Deletar máquina
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "DELETE FROM maquinas WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            response(['message' => 'Máquina deletada com sucesso']);
        } else {
            response(['message' => 'ID da máquina não fornecido'], 400);
        }
        break;

    default:
        response(['message' => 'Método HTTP não suportado'], 405);
        break;
}
?>
