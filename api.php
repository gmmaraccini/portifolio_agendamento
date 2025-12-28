<?php
// api.php
header('Content-Type: application/json');
require 'Scheduler.php';

// Conexão (Ajuste a senha se precisar)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=agendamento', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $scheduler = new Scheduler($pdo);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro de conexão: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// 1. GET: Retorna os agendamentos para o calendário
if ($method === 'GET') {
    // Busca agendamentos (Em um sistema real, filtraria por mês para não pesar)
    $stmt = $pdo->query("SELECT id, client_name as title, start_at as start, end_at as end FROM appointments WHERE status = 'confirmed'");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($events);
    exit;
}

// 2. POST: Cria um novo agendamento
if ($method === 'POST') {
    // Lê o JSON enviado pelo Javascript
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }

    try {
        // Tenta agendar (Serviço ID 1 fixo por enquanto para teste)
        $scheduler->bookAppointment(
            1,
            $input['name'],
            'email@teste.com', // Email fixo para teste
            $input['date'],    // Data enviada pelo JS
            'America/Sao_Paulo'
        );
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}