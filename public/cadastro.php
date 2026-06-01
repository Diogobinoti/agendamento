<?php
// public/cadastro.php
session_start();

if (!empty($_SESSION['id'])) {
    header('Location: dashboard.php');
    exit;
}

require '../config/conexao.php';

$erro  = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } else {
        // Verifica se e-mail já existe
        $stmtChk = $conn->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmtChk->bind_param('s', $email);
        $stmtChk->execute();
        $stmtChk->store_result();

        if ($stmtChk->num_rows > 0) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_BCRYPT);

            $stmtIns = $conn->prepare(
                'INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)'
            );
            $stmtIns->bind_param('sss', $nome, $email, $hash);

            if ($stmtIns->execute()) {
                $sucesso = 'Cadastro realizado com sucesso! Faça login.';
            } else {
                $erro = 'Erro ao cadastrar. Tente novamente.';
            }
            $stmtIns->close();
        }
        $stmtChk->close();
    }
}
?>
<?php include '../includes/header.php'; ?>

<main class="container form-center">
    <div class="card card-sm">
        <h2>Criar conta</h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-ok"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome"
                   placeholder="João Silva"
                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email"
                   placeholder="seu@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label for="senha">Senha <small>(mín. 6 caracteres)</small></label>
            <input type="password" id="senha" name="senha"
                   placeholder="Crie uma senha" required>

            <label for="confirma">Confirmar senha</label>
            <input type="password" id="confirma" name="confirma"
                   placeholder="Repita a senha" required>

            <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
        </form>

        <p class="text-center mt-1">
            Já tem conta? <a href="login.php">Entrar</a>
        </p>
    </div>
</main>

<?php include '../includes/footer.php'; ?>