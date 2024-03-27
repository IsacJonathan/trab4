<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora</title>
</head>
<body>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="num1">Número 1:</label>
    <input type="number" name="num1" id="num1" required>
    <br><br>
    <label for="op">Operação:</label>
    <select name="op" id="op" required>
        <option value="+" selected>+</option>
        <option value="-">-</option>
        <option value="*">*</option>
        <option value="/">/</option>
    </select>
    <br><br>
    <label for="num2">Número 2:</label>
    <input type="number" name="num2" id="num2" required>
    <br><br>
    <button type="submit">Calcular</button>
</form>

<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "seu_usuario";
$password = "sua_senha";
$dbname = "trabalho";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos foram recebidos
    if (isset($_POST['num1'], $_POST['num2'], $_POST['op'])) {
        // Limpa e valida os dados recebidos
        $num1 = filter_input(INPUT_POST, 'num1', FILTER_VALIDATE_FLOAT);
        $num2 = filter_input(INPUT_POST, 'num2', FILTER_VALIDATE_FLOAT);
        $op = $_POST['op'];

        // Verifica se os números são válidos
        if ($num1 === false || $num2 === false) {
            echo "Números inválidos.";
            exit;
        }

        // Realiza a operação solicitada
        switch ($op) {
            case '+':
                $result = $num1 + $num2;
                break;
            case '-':
                $result = $num1 - $num2;
                break;
            case '*':
                $result = $num1 * $num2;
                break;
            case '/':
                if ($num2 != 0) {
                    $result = $num1 / $num2;
                } else {
                    echo "Não é possível dividir por zero.";
                    exit;
                }
                break;
            default:
                echo "Operação inválida.";
                exit;
        }

        // Insere os dados no banco de dados
        $sql = "INSERT INTO historico_calculadora (num1, num2, operacao, resultado) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ddsd", $num1, $num2, $op, $result);

        if ($stmt->execute()) {
            echo "Resultado: " . $result;
        } else {
            echo "Erro ao inserir no banco de dados: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        echo "Por favor, preencha todos os campos.";
    }
} else {
    echo "Método de requisição inválido.";
}

$conn->close();
?>

</body>
</html>
