<?php
// public/login.php
session_start();

if (!empty($_SESSION['id'])) {
    header('Location: dashboard.php');
    exit;
}

require '../config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        // Prepared statement — sem risco de SQL injection
        $stmt = $conn->prepare('SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // password_verify compara com o hash bcrypt armazenado
            if (password_verify($senha, $usuario['senha'])) {
                session_regenerate_id(true); // previne session fixation
                $_SESSION['id']   = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['tipo'] = $usuario['tipo'];
                header('Location: dashboard.php');
                exit;
            }
        }
        $erro = 'E-mail ou senha inválidos.';
        $stmt->close();
    }
}
?>
<?php include '../includes/header.php'; ?>

<main class="container form-center">
    <div class="card card-sm">
        <h2>Entrar no sistema</h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email"
                   placeholder="seu@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha"
                   placeholder="Sua senha" required>

            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>

        <p class="text-center mt-1">
            Não tem conta? <a href="cadastro.php">Cadastre-se</a>
        </p>
    </div>
</main>

<?php include '../includes/footer.php'; ?>