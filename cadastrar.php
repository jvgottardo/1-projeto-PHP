<?php
// Incluir arquivo de configuração
require_once "conn.php";
 
// Defina variáveis e inicialize com valores vazios
$email = $email_err = "";
$username = $password =  "";
$username_err = $password_err = "";
 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validar nome de usuário
    if(empty(trim($_POST["nome"]))){
        $username_err = "Por favor coloque um nome de usuário.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["nome"]))){
        $username_err = "O nome de usuário pode conter apenas letras, números e sublinhados.";
    }else{
        // Prepare uma declaração selecionada
        $sql = "SELECT id FROM usuarios WHERE nome = :nome";
        
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_username, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_username = trim($_POST["nome"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Este nome de usuário já está em uso.";
                } else{
                    $username = trim($_POST["nome"]);
                }
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    
    // Validar email
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor insira um email.";     
    } 
    else{
        $email = trim($_POST["email"]);
    }

    // Validar senha
    if(empty(trim($_POST["senha"]))){
        $password_err = "Por favor insira uma senha.";     
    } elseif(strlen(trim($_POST["senha"])) < 6){
        $password_err = "A senha deve ter pelo menos 6 caracteres.";
    } else{
        $password = trim($_POST["senha"]);
    }

    
    
   
    
    // Verifique os erros de entrada antes de inserir no banco de dados
    if(empty($username_err) && empty($password_err) && empty($email_err)){
        
        // Prepare uma declaração de inserção
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
         
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":senha", $param_password, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Redirecionar para a página de login
                header("location: login.php");
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente.";
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
    <title>Cadastro</title>
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
                    <h1>Cadastro</h1>
                    <p class="text-muted"> Por favor, preencha este formulário para criar uma conta.</p> 
                    
                    <label class="text-muted">Nome do usuário</label>
                    <input type="text" name="nome" placeholder="Nome" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?> value=<?php echo $username; ?>> 
                    <span class="text-muted"><?php echo $username_err; ?></span>

                    <label class="text-muted">Email do usuário</label>
                    <input  type="email" name="email" placeholder="Email" <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?> value=<?php echo $email; ?>> 
                    <span class="text-muted"><?php echo $email_err; ?></span>

                    <label class="text-muted">Senha do usuário</label>
                    <input id ="senha" type="password" name="senha" placeholder="Senha" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>value=<?php echo $password; ?>> 
                    <p class="text-muted"><input  type="checkbox" onclick="myFunction()" > Mostrar Senha </p>
                    <span class="text-muted" ><?php echo $password_err; ?></span>

                <input type="submit" value="Criar Conta"> 
                <p class="text-muted">Já tem uma conta? <a href="login.php">Entre aqui</a>.</p>
                
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
