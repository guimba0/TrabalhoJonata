
<!DOCTYPE html>
<?php
require_once 'conexaoBancoDados.php';
session_start();

// 1. Verifica se o professor já está logado
if (isset($_SESSION['cod_prof'])) { // Usa 'cod_prof' da sessão
    header("Location: agendaProfessor.php");
    exit();
}

// 2. Processa o formulário de login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CORREÇÃO: O HTML usa 'id_professor', então $_POST['id_professor']
    if (isset($_POST['id_professor']) && !empty($_POST['id_professor'])) {
        $codigoProfessorDigitado = htmlspecialchars(trim($_POST['id_professor']));

        try {
            // Consulta usa :codigoProfessor
            $stmt = $gestor->prepare("SELECT id_professor, Nome FROM Professor WHERE id_professor = :codigoProfessor");
            // CORREÇÃO: Vincula o valor ao placeholder correto
            $stmt->bindParam(':codigoProfessor', $codigoProfessorDigitado, PDO::PARAM_INT);
            $stmt->execute();
            $professorAutenticado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($professorAutenticado) {
                // CORREÇÃO: Armazena o ID do banco de dados na sessão como 'cod_prof'
                $_SESSION['cod_prof'] = $professorAutenticado['id_professor'];
                $_SESSION['nome_professor'] = $professorAutenticado['Nome'];

                header("Location: agendaProfessor.php");
                exit();
            } else {
                header("Location: entrada_professor.php?erro=invalido");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Erro ao validar professor: " . $e->getMessage());
            header("Location: entrada_professor.php?erro=db_error");
            exit();
        }
    } else {
        header("Location: entrada_professor.php?erro=vazio");
        exit();
    }
}
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Professor - Sistema de TCC</title>
    <link rel="stylesheet" href="estilo/estilo.css">
    
</head>
<body>
    <header>
        <h1>Login do Professor</h1>
    </header>

    <main>
        <div class="form-container">
            <form action="entrada_professor.php" method="POST">
                <label for="id_professor">Código do Professor (ID):</label>
                <input type="text" id="id_professor" name="id_professor" required>
                <?php
                if (isset($_GET['erro'])) {
                    switch ($_GET['erro']) {
                        case 'invalido':
                            echo '<p class="error-message">Código inválido. Tente novamente.</p>';
                            break;
                        case 'vazio':
                            echo '<p class="error-message">Por favor, insira o código do professor.</p>';
                            break;
                        case 'db_error':
                            echo '<p class="error-message">Erro no banco de dados. Tente novamente mais tarde.</p>';
                            break;
                        default:
                            echo '<p class="error-message">Erro desconhecido.</p>';
                            break;
                    }
                }
                ?>
                <button type="submit">Entrar</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Sistema de TCC. Todos os direitos reservados.</p>
    </footer>
</body>
</html>