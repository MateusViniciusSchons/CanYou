
<!--FORMULARIO PARA LOGAR-->

<section class="escolha escolha-login fora">  
               
    <form method="post" class="login-form centro" >
        <h1>Login</h1> 

        <i class="fas fas2 fa-envelope"></i>
        <input 
            class='longo'
            type="email" 
            name="email" 
            id="email" 
            placeholder="Seu email"
            required='required'
        >

        <i class="fas fas2 fa-key"></i>
        <input 
            class='longo'
            type="password"
            name="senha"
            id="senha"
            placeholder="Sua senha"
            required='required'
        >
        
        <button type="submit" class='envia'>Logar</button>
    </form>

    <!--CADASTRAR PROFESSOR-->
    <a href="?pagina=cadastrar_professor" class="link-cadastrar">Ainda não possuo uma conta!</a>

</section>

<?php 
    // VALIDA LOGIN
    if (isset($_POST['email']) && isset($_POST['senha'])) {
        
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        require_once('bd/conexao.php');

        //VE SE EXISTE USUÁRIO COM AS INFORMAÇÕES DIGITADAS
        $sql = "SELECT ID_usuario, tipo FROM tbusuarios 
                    WHERE email = '$email' 
                    AND senha = md5('$senha') 
                    AND deletado = false";
        $consulta = mysqli_query($conexao, $sql);
        $existe_usuario = mysqli_num_rows($consulta);

        if ($existe_usuario == 1) {
            // PEGA O ID DO USUARIO
            while ($recebe_usuario = mysqli_fetch_array($consulta)) {
                
                $ID_usuario = $recebe_usuario[0];
                $tipo = $recebe_usuario[1];
                $loga = true; 
                // SE FOR UM PROFESSOR, VERIFICA SE O EMAIL FOI VERIFICADO
                if ($tipo == 'professor') {
                    $sql = "SELECT verificado, prof_confirmado FROM tbusuarios WHERE ID_usuario = $ID_usuario";
                    $pega_verificacao = mysqli_query($conexao, $sql);

                    if ($pega_verificacao) {
                        while ($recebe_verificacao = mysqli_fetch_array($pega_verificacao)) {

                            // VE SE O EMAIL FOI VERIFICADO E SE O PROFESSOR FOI CONFIRMADO
                            if ($recebe_verificacao[0] == 'sim' && $recebe_verificacao[1] == 'sim') {
                                $loga = true;
                            } else {
                                $loga = false; 
                            }
                        }
                    }
                }
                // SE É POSSIVEL LOGAR, ENTÃO O USUÁRIO É LOGADO
                if ($loga == true) {
                    
                    $_SESSION['ID_usuario'] = $ID_usuario;
                    $_SESSION['tipo'] = $tipo;
                    $_SESSION['logado'] = true;
                    $url = "index_logado.php";
                    echo "<script> window.location.href = 'index_logado.php'; </script>";
                } else {
                    echo "<script> window.alert('Não foi possivel logar, veja se você ja confirmou seu email, talvez o administrador ainda não tenha verificado seu cadastro') </script>";
                }
            }
        } else {
            echo "<script> window.alert('Não existe usuário com estas informações!') </script>";
            header('Refresh: 0');
        }
    }
?>