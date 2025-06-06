// salvar_tcc.php - CRIE ESTE ARQUIVO
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexaoBancoDados.php';
session_start();

// Verifica se o professor está logado antes de permitir o cadastro
if (!isset($_SESSION['cod_prof'])) {
    header("Location: entrada_professor.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta e sanitiza os dados do formulário
    $titulo = htmlspecialchars(trim($_POST['titulo']));
    $resumo = htmlspecialchars(trim($_POST['resumo']));
    $id_tipoTCC = intval($_POST['id_tipoTCC']); // Converte para inteiro
    $RA_aluno = htmlspecialchars(trim($_POST['RA_aluno']));
    $RA_aluno2 = isset($_POST['RA_aluno2']) && !empty($_POST['RA_aluno2']) ? htmlspecialchars(trim($_POST['RA_aluno2'])) : null;
    $RA_aluno3 = isset($_POST['RA_aluno3']) && !empty($_POST['RA_aluno3']) ? htmlspecialchars(trim($_POST['RA_aluno3'])) : null;
    $id_professor_orientador = intval($_POST['id_professor_orientador']);

    try {
        // Prepara a inserção na tabela TCC
        $stmt_tcc = $gestor->prepare("INSERT INTO TCC (titulo, resumo, id_tipoTCC, RA_aluno, RA_aluno2, RA_aluno3, id_professor_orientador) VALUES (:titulo, :resumo, :id_tipoTCC, :RA_aluno, :RA_aluno2, :RA_aluno3, :id_professor_orientador)");

        // Vincula os parâmetros
        $stmt_tcc->bindParam(':titulo', $titulo);
        $stmt_tcc->bindParam(':resumo', $resumo);
        $stmt_tcc->bindParam(':id_tipoTCC', $id_tipoTCC, PDO::PARAM_INT);
        $stmt_tcc->bindParam(':RA_aluno', $RA_aluno);
        $stmt_tcc->bindParam(':RA_aluno2', $RA_aluno2);
        $stmt_tcc->bindParam(':RA_aluno3', $RA_aluno3);
        $stmt_tcc->bindParam(':id_professor_orientador', $id_professor_orientador, PDO::PARAM_INT);

        $stmt_tcc->execute();

        // Redireciona para a agenda ou uma página de sucesso
        header("Location: agendaProfessor.php?status=tcc_cadastrado_sucesso");
        exit();

    } catch (PDOException $e) {
        error_log("Erro ao salvar TCC: " . $e->getMessage());
        header("Location: cadastrar_tcc.php?erro=salvar_tcc_falha");
        exit();
    }
} else {
    // Se não for um POST, redireciona de volta para o formulário
    header("Location: cadastrar_tcc.php");
    exit();
}
?>