<?php
require 'Scheduler.php';

// Conexão
$pdo = new PDO('mysql:host=localhost;dbname=agendamento', 'root', '');
$scheduler = new Scheduler($pdo);

// 1. Lógica de POST: Se clicou num botão, atualiza o status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action']; // 'confirmar' ou 'cancelar'

    try {
        if ($action == 'confirmar') {
            $scheduler->updateStatus($id, 'confirmed');
            $msg = "Agendamento #$id confirmado!";
        } elseif ($action == 'cancelar') {
            $scheduler->updateStatus($id, 'canceled');
            $msg = "Agendamento #$id cancelado.";
        }
    } catch (Exception $e) {
        $error = "Erro: " . $e->getMessage();
    }
}

// 2. Busca apenas os PENDENTES para mostrar na lista
$stmt = $pdo->query("SELECT * FROM appointments WHERE status = 'pending' ORDER BY start_at ASC");
$pendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 5px 10px; cursor: pointer; color: white; border: none; }
        .btn-ok { background-color: green; }
        .btn-del { background-color: red; }
        .msg { background: #dff0d8; color: #3c763d; padding: 10px; }
    </style>
</head>
<body>

<h1>Painel Administrativo</h1>
<a href="index.php">Ir para o Calendário (Visão do Cliente)</a>

<?php if (isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

<?php if (count($pendentes) == 0): ?>
    <p>Nenhum agendamento pendente.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Data/Hora (UTC)</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pendentes as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['client_name']) ?></td>
                <td><?= $p['start_at'] ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" name="action" value="confirmar" class="btn btn-ok">Aprovar</button>
                        <button type="submit" name="action" value="cancelar" class="btn btn-del">Rejeitar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>