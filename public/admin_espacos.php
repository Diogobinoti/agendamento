<?php
// public/admin_espacos.php
require '../includes/auth.php';
exigirAdmin();
require '../config/conexao.php';

$msg = '';
$modoEditar = null;

// --- Excluir espaço ---
if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $stmt = $conn->prepare('UPDATE espacos SET ativo = 0 WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $msg = ['tipo' => 'ok', 'texto' => 'Espaço removido com sucesso.'];
}

// --- Carregar dados para edição ---
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $stmtE = $conn->prepare('SELECT * FROM espacos WHERE id = ? AND ativo = 1');
    $stmtE->bind_param('i', $id);
    $stmtE->execute();
    $res = $stmtE->get_result();
    $modoEditar = $res->fetch_assoc();
    $stmtE->close();
}

// --- Salvar (criar ou editar) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome']      ?? '');
    $tipo       = trim($_POST['tipo']      ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $capacidade = $_POST['capacidade'] !== '' ? (int)$_POST['capacidade'] : null;
    $preco      = (float)($_POST['preco']  ?? 0);
    $editarId   = (int)($_POST['editar_id'] ?? 0);

    if ($nome === '' || $tipo === '' || $preco < 0) {
        $msg = ['tipo' => 'erro', 'texto' => 'Preencha nome, tipo e preço corretamente.'];
    } else {
        if ($editarId > 0) {
            // Atualizar
            $stmtU = $conn->prepare(
                'UPDATE espacos SET nome=?, tipo=?, descricao=?, capacidade=?, preco_hora=?
                 WHERE id = ?'
            );
            $stmtU->bind_param('sssdii', $nome, $tipo, $descricao, $preco, $capacidade, $editarId);
            // Capacidade pode ser null
            $stmtU = $conn->prepare(
                'UPDATE espacos SET nome=?, tipo=?, descricao=?, capacidade=?, preco_hora=?
                 WHERE id = ?'
            );
            $stmtU->bind_param('sssidi', $nome, $tipo, $descricao, $capacidade, $preco, $editarId);
            $stmtU->execute();
            $stmtU->close();
            $msg = ['tipo' => 'ok', 'texto' => 'Espaço atualizado com sucesso.'];
        } else {
            // Inserir
            $stmtI = $conn->prepare(
                'INSERT INTO espacos (nome, tipo, descricao, capacidade, preco_hora)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmtI->bind_param('sssid', $nome, $tipo, $descricao, $capacidade, $preco);
            $stmtI->execute();
            $stmtI->close();
            $msg = ['tipo' => 'ok', 'texto' => 'Espaço cadastrado com sucesso.'];
        }
        $modoEditar = null; // limpa o form após salvar
    }
}

// --- Listar espaços ativos ---
$espacos = $conn->query('SELECT * FROM espacos WHERE ativo = 1 ORDER BY nome');
?>
<?php include '../includes/header.php'; ?>

<main class="container">
    <div class="page-top">
        <h1>Gerenciar Espaços</h1>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg['tipo'] ?>"><?= htmlspecialchars($msg['texto']) ?></div>
    <?php endif; ?>

    <!-- Formulário cadastrar / editar -->
    <div class="card">
        <h2><?= $modoEditar ? 'Editar Espaço' : 'Novo Espaço' ?></h2>

        <form method="POST">
            <?php if ($modoEditar): ?>
                <input type="hidden" name="editar_id" value="<?= $modoEditar['id'] ?>">
            <?php endif; ?>

            <div class="form-grid-2">
                <div>
                    <label>Nome</label>
                    <input type="text" name="nome" placeholder="Ex: Quadra B"
                           value="<?= htmlspecialchars($modoEditar['nome'] ?? $_POST['nome'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Tipo</label>
                    <input type="text" name="tipo" placeholder="Ex: Quadra, Laboratório, Sala"
                           value="<?= htmlspecialchars($modoEditar['tipo'] ?? $_POST['tipo'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Capacidade (pessoas)</label>
                    <input type="number" name="capacidade" min="1"
                           placeholder="Opcional"
                           value="<?= htmlspecialchars($modoEditar['capacidade'] ?? $_POST['capacidade'] ?? '') ?>">
                </div>
                <div>
                    <label>Preço por hora (R$)</label>
                    <input type="number" name="preco" step="0.01" min="0" placeholder="0.00"
                           value="<?= htmlspecialchars($modoEditar['preco_hora'] ?? $_POST['preco'] ?? '') ?>" required>
                </div>
            </div>

            <label>Descrição</label>
            <textarea name="descricao" rows="3" placeholder="Descreva o espaço..."><?= htmlspecialchars($modoEditar['descricao'] ?? $_POST['descricao'] ?? '') ?></textarea>

            <div class="form-acoes">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEditar ? 'Salvar Alterações' : 'Cadastrar Espaço' ?>
                </button>
                <?php if ($modoEditar): ?>
                    <a href="admin_espacos.php" class="btn btn-outline">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabela de espaços -->
    <?php if ($espacos->num_rows === 0): ?>
        <div class="alert alert-info">Nenhum espaço cadastrado.</div>
    <?php else: ?>
        <div class="tabela-wrap mt-1">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Capacidade</th>
                        <th>Preço/h</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($e = $espacos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $e['id'] ?></td>
                        <td><?= htmlspecialchars($e['nome']) ?></td>
                        <td><?= htmlspecialchars($e['tipo']) ?></td>
                        <td><?= $e['capacidade'] ? $e['capacidade'] . ' pessoas' : '—' ?></td>
                        <td>R$ <?= number_format($e['preco_hora'], 2, ',', '.') ?></td>
                        <td class="acoes-tabela">
                            <a href="admin_espacos.php?editar=<?= $e['id'] ?>"
                               class="btn btn-outline btn-sm">✏️ Editar</a>
                            <a href="admin_espacos.php?excluir=<?= $e['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Remover este espaço?')">🗑️ Remover</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>