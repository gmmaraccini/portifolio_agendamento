<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Agendamento</title>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        #calendar { max-width: 900px; margin: 0 auto; }
    </style>
</head>
<body>

<h2 style="text-align:center">Agendamento Barbearia</h2>
<div id='calendar'></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek', // Visualização semanal com horas
            locale: 'pt-br',             // Tradução
            slotMinTime: '08:00:00',     // O calendário começa as 8h
            slotMaxTime: '19:00:00',     // O calendário termina as 19h
            allDaySlot: false,           // Remove a linha de "dia todo"

            // 1. Onde buscar os eventos? Na nossa API
            events: 'api.php',

            // 2. O que acontece quando clica num horário vazio?
            dateClick: function(info) {
                // Pega a data e hora clicada (ex: 2023-10-20T14:30:00)
                var dateStr = info.dateStr;

                // Pergunta simples para testar (depois trocamos por Modal Bonito)
                var nome = prompt('Digite seu nome para agendar em: ' + info.dateStr);

                if (nome) {
                    // Envia para o PHP salvar
                    fetch('api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            name: nome,
                            date: dateStr
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Agendado com sucesso!');
                                calendar.refetchEvents(); // Atualiza a tela
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        });
                }
            }
        });

        calendar.render();
    });
</script>
</body>
</html>