<?php
// cadastrar_tcc.php - SALVE ESTE CÓDIGO NESTE ARQUIVO.
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexaoBancoDados.php';
session_start();

// Verifica se o professor está logado (opcional, mas boa prática para páginas internas)
if (!isset($_SESSION['cod_prof'])) {
    header("Location: entrada_professor.php");
    exit();
}

$idProfessorLogado = $_SESSION['cod_prof'];
$nomeProfessorLogado = $_SESSION['nome_professor'] ?? 'Professor(a)';

// --- FUNÇÃO PARA BUSCAR DADOS DO BANCO DE DADOS ---
/**
 * @param PDO $pdo - Objeto PDO de conexão com o banco de dados.
 * @param string $querySql - A query SQL a ser executada.
 * @param string $errorMessage - Mensagem de erro a ser exibida e logada em caso de falha.
 * @return array - Array associativo com os resultados da query ou um array vazio em caso de erro.
 */
function fetchDataFromDatabase(PDO $pdo, string $querySql, string $errorMessage): array {
    try {
        $stmt = $pdo->query($querySql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $errorMessage . ": " . $e->getMessage();
        error_log($errorMessage . ": " . $e->getMessage());
        return []; // Retorna um array vazio em caso de erro
    }
}

// --- BUSCA LISTA DE PROFESSORES usando a nova função ---
$professores = fetchDataFromDatabase(
    $gestor,
    "SELECT id_professor, Nome FROM Professor ORDER BY Nome ASC",
    "Erro ao buscar professores"
);

// --- BUSCA LISTA DE ALUNOS usando a nova função ---
$alunos = fetchDataFromDatabase(
    $gestor,
    "SELECT RA, Nome, email FROM Aluno ORDER BY Nome ASC",
    "Erro ao buscar alunos"
);

// --- BUSCA LISTA DE TIPOS DE TCC usando a nova função ---
$tipos = fetchDataFromDatabase(
    $gestor,
    "SELECT id_tipoTCC, nome_Tipo FROM tipotcc ORDER BY nome_Tipo ASC",
    "Erro ao buscar tipos de TCC"
);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo TCC</title>
    <link rel="stylesheet" href="style/estilo.css"> <style>
        /* Seus estilos básicos para o formulário (se não estiverem no seu estilo.css) */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        header { background-color: #333; color: white; padding: 1em 0; width: 100%; text-align: center; margin-bottom: 20px; }
        main { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); width: 90%; max-width: 600px; }
        form { display: flex; flex-direction: column; gap: 15px; }
        label { font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="date"], textarea, select {
            width: calc(100% - 20px); padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        textarea { resize: vertical; min-height: 80px; }
        button[type="submit"] { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: background-color 0.3s ease; }
        button[type="submit"]:hover { background-color: #0056b3; }
        select option[disabled] { color: #999; }
        nav { margin-top: 10px; }
        nav a { color: white; text-decoration: none; margin: 0 10px; }
        nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>Cadastrar Novo TCC</h1>
        <p>Olá, Professor(a) <?php echo htmlspecialchars($nomeProfessorLogado); ?>!</p>
        <nav>
            <a href="agendaProfessor.php">Minha Agenda</a> |
            <a href="logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <form action="salvar_tcc.php" method="POST">
            <label for="titulo">Título do TCC:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="resumo">Resumo do TCC:</label>
            <textarea id="resumo" name="resumo" rows="5" required placeholder="Digite um breve resumo do TCC..."></textarea>

            <label for="id_tipoTCC">Tipo de TCC:</label>
            <select id="id_tipoTCC" name="id_tipoTCC" required>
                <?php if (empty($tipos)): ?>
                    <option value="" disabled selected>Nenhum tipo de TCC encontrado. Verifique o banco de dados.</option>
                <?php else: ?>
                    <option value="" disabled selected>Selecione o tipo de TCC</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo['id_tipoTCC']); ?>">
                            <?php echo htmlspecialchars($tipo['nome_Tipo']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="RA_aluno">Aluno 1 (Principal):</label>
            <select id="RA_aluno" name="RA_aluno" required>
                <?php if (empty($alunos)): ?>
                    <option value="" disabled selected>Nenhum aluno encontrado.</option>
                <?php else: ?>
                    <option value="" disabled selected>Selecione o aluno principal</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?php echo htmlspecialchars($aluno['RA']); ?>">
                            <?php echo htmlspecialchars($aluno['Nome']) . " (" . htmlspecialchars($aluno['email']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="RA_aluno2">Aluno 2 (Opcional):</label>
            <select id="RA_aluno2" name="RA_aluno2">
                <option value="" selected>Não selecionar</option>
                <?php if (!empty($alunos)): ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?php echo htmlspecialchars($aluno['RA']); ?>">
                            <?php echo htmlspecialchars($aluno['Nome']) . " (" . htmlspecialchars($aluno['email']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="RA_aluno3">Aluno 3 (Opcional):</label>
            <select id="RA_aluno3" name="RA_aluno3">
                <option value="" selected>Não selecionar</option>
                <?php if (!empty($alunos)): ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?php echo htmlspecialchars($aluno['RA']); ?>">
                            <?php echo htmlspecialchars($aluno['Nome']) . " (" . htmlspecialchars($aluno['email']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="id_professor_orientador">Professor Orientador:</label>
            <select id="id_professor_orientador" name="id_professor_orientador" required>
                <?php if (empty($professores)): ?>
                    <option value="" disabled selected>Nenhum professor encontrado.</option>
                <?php else: ?>
                    <option value="" disabled selected>Selecione o professor orientador</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?php echo htmlspecialchars($professor['id_professor']); ?>"> <?php echo htmlspecialchars($professor['Nome']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <button type="submit">Salvar TCC</button>
        </form>
    </main>
</body>
</html>