<?php
require_once 'db.php';

// --- Funções de Carregamento ---
session_start();
if (!isset($_SESSION['professor'])) {
    header('location: index.php');

    exit;
}

function carregarExercicios($pdo)
{
    $stmt = $pdo->query("SELECT e.idexercicio, e.nome_exercicio, g.nome_classe FROM exercicio e
inner join grupo g
on g.idgrupo = e.grupo_idgrupo");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function carregarAlunos($pdo)
{
    $idProfessor = $_SESSION['professor'];
    $stmt = $pdo->query("SELECT idaluno, nome_aluno FROM aluno WHERE id_professor = $idProfessor ORDER BY nome_aluno");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- Processamento de Salvamento da Ficha (AJAX POST) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aluno_id']) && isset($_POST['treino_json'])) {

    $alunoId = (int)$_POST['aluno_id'];
    $treinoJson = $_POST['treino_json'];

    header('Content-Type: application/json');

    try {
        // Verifica se o aluno existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM aluno WHERE idaluno = ?");
        $stmt->execute([$alunoId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Aluno não encontrado.']);
            exit;
        }

        // Salva a nova ficha no banco de dados
        // Para simplificar, vamos substituir a última ficha para o aluno
        // Em um sistema real, você manteria um histórico.

        // 1. Apaga a ficha existente (opcional, para manter o histórico limpo)
        $stmt = $pdo->prepare("DELETE FROM treino WHERE aluno_id = ?");
        $stmt->execute([$alunoId]);

        // 2. Insere a nova ficha
        $stmt = $pdo->prepare("INSERT INTO treino (aluno_id, treino_json) VALUES (?, ?)");
        $stmt->execute([$alunoId, $treinoJson]);

        echo json_encode(['success' => true, 'message' => 'Ficha salva com sucesso!']);
    } catch (PDOException $e) {
        error_log("Erro ao salvar ficha: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro interno ao salvar: ' . $e->getMessage()]);
    }
    exit;
}


// --- Dados para o HTML ---
$exercicios = carregarExercicios($pdo);
$alunos = carregarAlunos($pdo);

// Agrupamento dos exercícios por grupo muscular
$exercicios_agrupados = [];
foreach ($exercicios as $ex) {
    $exercicios_agrupados[$ex['nome_classe']][] = $ex;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montagem de Ficha de Treino</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <section>
        <menu>
            <a href="cadastro_aluno.php">Cadastro Aluno</a>
            <a href="meusalunos.php?idProfessor=<?php echo $_SESSION['professor']; ?>">Meus Alunos</a>
            <a href="sair.php">Sair</a>
        </menu>
    </section>
    <div class="container">
        <h1>Montar Ficha de Treino</h1>

        <div class="select-aluno">
            <label for="aluno-select">Selecionar Aluno:</label>
            <select id="aluno-select">
                <option value="">-- Selecione um Aluno --</option>
                <?php foreach ($alunos as $aluno): ?>
                    <option value="<?php echo $aluno['idaluno']; ?>"><?php echo htmlspecialchars($aluno['nome_aluno']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ficha-area">

            <div id="exercicio-banco" class="banco-exercicios">
                <h2>Banco de Exercícios</h2>
                <?php foreach ($exercicios_agrupados as $grupo => $lista): ?>
                    <div class="grupo-muscular">
                        <h3><?php echo htmlspecialchars($grupo); ?></h3>
                        <?php foreach ($lista as $ex): ?>
                            <div class="exercicio-item"
                                draggable="true"
                                data-id="<?php echo $ex['idexercicio']; ?>"
                                data-nome="<?php echo htmlspecialchars($ex['nome_exercicio']); ?>">
                                <?php echo htmlspecialchars($ex['nome_exercicio']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="ficha-drop-zone" class="ficha-treino">
                <h2>Ficha de Treino <span id="aluno-nome-display"></span></h2>
                <p class="placeholder-text">Arraste e solte os exercícios aqui para montar a ficha.</p>
            </div>

        </div>

        <button id="salvar-ficha" class="btn-salvar" disabled>Salvar Ficha</button>

    </div>

    <script src="script.js"></script>
</body>

</html>