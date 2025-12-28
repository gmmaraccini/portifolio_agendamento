<?php

class Scheduler {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Tenta criar um agendamento.
     * Retorna true se sucesso, ou lança exceção se falhar.
     */
    public function bookAppointment($serviceId, $clientName, $clientEmail, $userDateString, $userTimezone = 'America/Sao_Paulo') {

        // 1. Configurar o fuso horário do usuário
        $timezone = new DateTimeZone($userTimezone);

        // 2. Criar objeto DateTime com a data que o usuário enviou
        $startDateTime = new DateTime($userDateString, $timezone);

        // 3. Pegar a duração do serviço para calcular o fim
        $service = $this->getService($serviceId);
        if (!$service) throw new Exception("Serviço não encontrado.");

        // Clonamos para não alterar o startDateTime original
        $endDateTime = clone $startDateTime;
        $endDateTime->modify("+{$service['duration_minutes']} minutes");

        // 4. Converter TUDO para UTC antes de salvar/verificar
        $startDateTime->setTimezone(new DateTimeZone('UTC'));
        $endDateTime->setTimezone(new DateTimeZone('UTC'));

        // Formatar para MySQL
        $utcStart = $startDateTime->format('Y-m-d H:i:s');
        $utcEnd = $endDateTime->format('Y-m-d H:i:s');

        // 5. Verificar disponibilidade (Regra de Ouro)
        if (!$this->isSlotAvailable($utcStart, $utcEnd)) {
            throw new Exception("Horário indisponível! Já existe um agendamento.");
        }

        // 6. Salvar no banco
        $sql = "INSERT INTO appointments (service_id, client_name, client_email, start_at, end_at) 
                VALUES (:sid, :name, :email, :start, :end)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'sid' => $serviceId,
            'name' => $clientName,
            'email' => $clientEmail,
            'start' => $utcStart,
            'end' => $utcEnd
        ]);

        return true;
    }

    private function isSlotAvailable($utcStart, $utcEnd) {
        // Lógica de intersecção de datas
        // "Se o novo começo for antes do fim existente E o novo fim for depois do começo existente"
        $sql = "SELECT COUNT(*) FROM appointments 
                WHERE status != 'canceled' 
                AND (start_at < :end_at AND end_at > :start_at)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['start_at' => $utcStart, 'end_at' => $utcEnd]);

        return $stmt->fetchColumn() == 0;
    }

    private function getService($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza o status de um agendamento (confirmar ou cancelar)
     */
    public function updateStatus($id, $status) {
        $allowed = ['confirmed', 'canceled'];
        if (!in_array($status, $allowed)) {
            throw new Exception("Status inválido.");
        }

        $sql = "UPDATE appointments SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }
}
?>