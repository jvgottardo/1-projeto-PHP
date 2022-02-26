<?php
// Inicialize a sessão
session_start();
 
// Verifique se o usuário já está logado, em caso afirmativo, redirecione-o para a página de boas-vindas
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: bemvindo.php");
    exit;
}
 
// Incluir arquivo de configuração
require_once "conn.php";
 
// Defina variáveis e inicialize com valores vazios
$email = $password = $nome = "";
$email_err = $password_err = $login_err = $nome_err = "";
 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Verifique se o nome de usuário está vazio
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor, insira o email da conta.";
    } else{
        $email = trim($_POST["email"]);
    }

    if(empty(trim($_POST["nome"]))){
        $nome_err = "Por favor, insira o nome da conta.";
    } else{
        $nome = trim($_POST["nome"]);
    }
    
    // Verifique se a senha está vazia
    if(empty(trim($_POST["senha"]))){
        $password_err = "Por favor, insira sua senha.";
    } else{
        $password = trim($_POST["senha"]);
    }
    
    // Validar credenciais
    if(empty($email_err) && empty($password_err) && empty($nome_err)){
        // Prepare uma declaração selecionada
        $sql = "SELECT id, email, nome, senha FROM usuarios WHERE email = :email AND nome = :nome";
        
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_email = trim($_POST["email"]);
            $param_nome = trim($_POST["nome"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Verifique se o nome de usuário existe, se sim, verifique a senha
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $email = $row["email"];
                        $nome = $row["nome"];
                        $hashed_password = $row["senha"];
                        if(password_verify($password, $hashed_password)){
                            // A senha está correta, então inicie uma nova sessão
                            session_start();
                            
                            // Armazene dados em variáveis de sessão
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            $_SESSION["nome"] = $nome;                            
                            
                            // Redirecionar o usuário para a página de boas-vindas
                            header("location: bemvindo.php");
                        } else{
                            // A senha não é válida, exibe uma mensagem de erro genérica
                            $login_err = "Email da conta ou senha inválidos.";
                        }
                    }
                } else{
                    // O nome de usuário não existe, exibe uma mensagem de erro genérica
                    $login_err = "Email da conta ou senha inválidos.";
                }
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    
    // Fechar conexão
    unset($pdo);
}
?>
 
 <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
   <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"  class="box">
                    <h1>Login</h1>
                    <p class="text-muted"> Por favor, preencha os campos para fazer o login.</p> 
                    
                    <?php 
                    if(!empty($login_err)){
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                        }        
                    ?>

                <label class="text-muted">Nome do usuário</label>
                <input type="text" name="nome" placeholder="Nome" <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>value=<?php echo $nome; ?>>
                <span class="text-muted"><?php echo $nome_err; ?></span>

                <label class="text-muted" >Email do usuário</label>
                <input type="email" name="email" placeholder="Email" <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?> value=<?php echo $email; ?>>
                <span class="text-muted"><?php echo $email_err; ?></span>

                <label class="text-muted" >Senha do usuário</label>
                <input type="password" id="senha" name="senha" placeholder="Senha" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>>
                    <p class="text-muted"><input  type="checkbox" onclick="myFunction()" > Mostrar Senha </p>
                    <span class="text-muted" ><?php echo $password_err; ?></span>

                    <input type="submit" value="Entrar">
                    <p class="text-muted">Não tem uma conta? <a href="cadastrar.php">Inscreva-se agora</a>.</p>
                
                    <div class="col-md-12">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<script>

function myFunction() {
    var x = document.getElementById("senha");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
    }
</script>