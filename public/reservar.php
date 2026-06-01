<?php
// public/reservar.php
require '../includes/auth.php';
exigirLogin();
require '../config/conexao.php';

$erro    = '';
$sucesso = '';

// Pré-seleciona o espaço se veio via GET (link do dashboard)
$espacoPreSelecionado = (int)($_GET['espaco'] ?? 0);

// Busca espaços ativos para o select
$espacos = $conn->query('SELECT id, nome, tipo FROM espacos WHERE ativo = 1 ORDER BY nome');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId  = (int)$_SESSION['id'];
    $espacoId   = (int)($_POST['espaco']  ?? 0);
    $data       = $_POST['data']   ?? '';
    $inicio     = $_POST['inicio'] ?? '';
    $fim        = $_POST['fim']    ?? '';

    // Validações básicas
    if (!$espacoId || !$data || !$inicio || !$fim) {
        $erro = 'Preencha todos os campos.';
    } elseif ($fim <= $inicio) {
        $erro = 'O horário de fim deve ser maior que o de início.';
    } elseif ($data < date('Y-m-d')) {
        $erro = 'Não é possível reservar para datas passadas.';
    } else {
        // Verifica conflito de horário (ignora reservas canceladas)
        $stmtConf = $conn->prepare(
            'SELECT id FROM reservas
             WHERE espaco_id   = ?
               AND data_reserva = ?
               AND status      = "ativa"
               AND hora_inicio  < ?
               AND hora_fim     > ?'
        );
        $stmtConf->bind_param('isss', $espacoId, $data, $fim, $inicio);
        $stmtConf->execute();
        $stmtConf->store_result();

        if ($stmtConf->num_rows > 0) {
            $erro = 'Este horário já está reservado. Escolha outro período.';
        } else {
            $stmtIns = $conn->prepare(
                'INSERT INTO reservas (usuario_id, espaco_id, data_reserva, hora_inicio, hora_fim)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmtIns->bind_param('iisss', $usuarioId, $espacoId, $data, $inicio, $fim);

            if ($stmtIns->execute()) {
                $sucesso = 'Reserva criada com sucesso! 🎉';
            } else {
                $erro = 'Erro ao salvar a reserva. Tente novamente.';
            }
            $stmtIns->close();
        }
        $stmtConf->close();
    }
}
?>
<?php include '../includes/header.php'; ?>

<main class="container form-center">
    <div class="card">
        <h2>Nova Reserva</h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-ok"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="POST" id="formReserva" novalidate>

            <label for="espaco">Espaço</label>
            <select id="espaco" name="espaco" required>
                <option value="">-- Selecione --</option>
                <?php
                // Reinicia o cursor se já veio um POST
                $espacos->data_seek(0);
                while ($e = $espacos->fetch_assoc()):
                    $sel = ((int)($e['id']) === $espacoPreSelecionado || (int)($e['id']) === (int)($_POST['espaco'] ?? 0))
                        ? 'selected' : '';
                ?>
                    <option value="<?= $e['id'] ?>" <?= $sel ?>>
                        [<?= htmlspecialchars($e['tipo']) ?>] <?= htmlspecialchars($e['nome']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="data">Data</label>
            <input type="date" id="data" name="data"
                   min="<?= date('Y-m-d') ?>"
                   value="<?= htmlspecialchars($_POST['data'] ?? '') ?>" required>

            <!-- Indicador de disponibilidade (atualizado via JS) -->
            <div id="disponibilidade" class="disponibilidade-box" style="display:none">
                <span id="disp-status"></span>
            </div>

            <div class="horario-grid">
                <div>
                    <label for="inicio">Início</label>
                    <input type="time" id="inicio" name="inicio"
                           value="<?= htmlspecialchars($_POST['inicio'] ?? '') ?>" required>
                </div>
                <div>
                    <label for="fim">Fim</label>
                    <input type="time" id="fim" name="fim"
                           value="<?= htmlspecialchars($_POST['fim'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Horários ocupados (renderizado via JS) -->
            <div id="ocupados-wrap" style="display:none">
                <p class="label-ocupados">⛔ Horários já reservados nesta data:</p>
                <div id="lista-ocupados" class="lista-ocupados"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-1">Confirmar Reserva</button>
        </form>

        <div class="text-center mt-1">
            <a href="minhas_reservas.php">← Ver minhas reservas</a>
        </div>
    </div>
</main>

<script>
// Disponibilidade em tempo real
(function () {
    const selectEspaco = document.getElementById('espaco');
    const inputData    = document.getElementById('data');
    const dispBox      = document.getElementById('disponibilidade');
    const dispStatus   = document.getElementById('disp-status');
    const ocupadosWrap = document.getElementById('ocupados-wrap');
    const listaOcupados = document.getElementById('lista-ocupados');

    function fmt(hora) {
        // Remove segundos se existirem: "08:00:00" -> "08:00"
        return hora.substring(0, 5);
    }

    async function verificar() {
        const espaco = selectEspaco.value;
        const data   = inputData.value;

        if (!espaco || !data) {
            dispBox.style.display = 'none';
            ocupadosWrap.style.display = 'none';
            return;
        }

        dispBox.style.display = 'block';
        dispStatus.textContent = '⏳ Verificando disponibilidade…';
        dispStatus.className = '';

        try {
            const res  = await fetch(`api/disponibilidade.php?espaco=${espaco}&data=${data}`);
            const json = await res.json();

            if (json.reservas && json.reservas.length > 0) {
                dispStatus.textContent = `⛔ ${json.reservas.length} horário(s) ocupado(s)`;
                dispStatus.className = 'disp-ocupado';

                listaOcupados.innerHTML = json.reservas
                    .map(r => `<span class="tag-ocupado">${fmt(r.hora_inicio)} – ${fmt(r.hora_fim)}</span>`)
                    .join('');
                ocupadosWrap.style.display = 'block';
            } else {
                dispStatus.textContent = '✅ Espaço disponível nesta data!';
                dispStatus.className = 'disp-livre';
                ocupadosWrap.style.display = 'none';
            }
        } catch (e) {
            dispStatus.textContent = 'Não foi possível verificar a disponibilidade.';
        }
    }

    selectEspaco.addEventListener('change', verificar);
    inputData.addEventListener('change', verificar);

    // Dispara ao carregar se já tiver valores (ex: retorno de POST)
    if (selectEspaco.value && inputData.value) verificar();
})();
</script>

<?php include '../includes/footer.php'; ?>