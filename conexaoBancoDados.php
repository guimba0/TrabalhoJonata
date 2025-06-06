<?php
// conexaoBancoDados.php

// Este arquivo tem como ÚNICO objetivo estabelecer e retornar a conexão PDO com o banco de dados.
// Ele NÃO deve conter consultas a tabelas específicas (como 'agenda'), pois isso é responsabilidade
// dos scripts que de fato precisam desses dados (ex: Cadastrar_tcc.php, salvar_tcc.php).

// --- CONFIGURAÇÕES DO BANCO DE DADOS ---
// ATENÇÃO: Substitua os valores abaixo pelos SEUS DADOS REAIS de conexão.
// Se você está usando XAMPP no seu computador, as configurações abaixo são as mais comuns.
$host = 'localhost'; // Geralmente 'localhost'
$db = 'consultaagenda'; // <--- MUDE PARA O NOME DO SEU BANCO DE DADOS (onde estão as tabelas de TCC, Aluno, Professor, etc.)
$user = 'root';        // <--- MUDE PARA O SEU NOME DE USUÁRIO DO BANCO DE DADOS (geralmente 'root' para XAMPP)
$pass = '';            // <--- MUDE PARA SUA SENHA DO BANCO DE DADOS (geralmente vazia '' para XAMPP)
$charset = 'utf8mb4';  // Conjunto de caracteres recomendado para evitar problemas com acentos

// Data Source Name (DSN) - String de conexão para o PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opções adicionais para a conexão PDO
$options = [
    // Define o modo de erro: PDO lançará exceções em caso de erros no SQL.
    // Isso é útil para depuração e tratamento de erros de forma estruturada.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    // Define o modo de busca padrão: PDO retornará linhas como arrays associativos.
    // Isso significa que você acessa colunas por nome (ex: $aluno['Nome']) em vez de por índice.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // Desabilita a emulação de prepared statements (para maior segurança e desempenho).
    // O driver do MySQL lida com prepared statements de forma nativa.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Tenta criar uma nova instância da conexão PDO
    // A conexão bem-sucedida é armazenada na variável $gestor
    $gestor = new PDO($dsn, $user, $pass, $options);
    // Se a conexão for bem-sucedida, você pode (opcionalmente, para teste) descomentar a linha abaixo:
    // echo "Conexão com o banco de dados realizada com sucesso!";

} catch (\PDOException $e) {
    // Se ocorrer um erro durante a conexão, o bloco catch é executado.
    // A função 'die()' encerra a execução do script e exibe uma mensagem de erro.
    // Em um ambiente de produção real, você registraria este erro em um arquivo de log
    // e mostraria uma mensagem mais amigável ao usuário (evitando detalhes técnicos).
    die("Erro fatal na conexão com o banco de dados: " . $e->getMessage() . "<br>");
}

// IMPORTANTE:
// Este arquivo NÃO DEVE CONTER NENHUMA OUTRA CONSULTA SQL (como SELECT * FROM agenda).
// Sua única função é fornecer a variável $gestor (a conexão PDO) para outros scripts.
// As consultas a tabelas específicas (Aluno, Professor, tipotcc, tcc) são feitas
// nos arquivos Cadastrar_tcc.php e salvar_tcc.php, onde elas são realmente necessárias.

?>