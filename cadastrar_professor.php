
<!--CADASTRAR PROFESSOR-->
<section class='fora'>
    <form method="post" class="centro cadastrar-prof-form">
            <h1>Cadastro Professor</h1>

            <i class="fas fas2 fa-user"></i>
            <input 
                class='longo'
                type="text" 
                name="nome" 
                id="nome"
                placeholder="Seu nome"
                required="required"
            >
            
            <i class="fas fas2 fa-envelope"></i>
            <input 
                class='longo'
                type="email" 
                name="email" 
                id="email"
                placeholder="Seu email"
                required="required"
            >

            <i class="fas fas2 fa-key"></i>
            <input 
                class='longo'
                type="password" 
                name="senha" 
                id="senha"
                placeholder="Sua senha"
                required="required"
            >

            <i class="fas fas2 fa-key"></i>
            <input 
                class='longo'
                type="password" 
                name="senha2" 
                id="senha2"
                placeholder="Repita sua senha"
                required="required"
            >

            <button type="submit" class='envia'>Continuar</button>
    </form>

<?php
        //ABRE O FORM COM HORARIOS PARA CADASTRAR
        if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['senha2'])) {

            if ($_POST['senha'] == $_POST['senha2']) {

                $nome = $_POST['nome'];
                $senha = $_POST['senha'];
                $email = $_POST['email'];
                include_once('./utils/helpers.php');
                // VALIDA EMAIL DO USUARIO (VÊ SE JÁ EXISTE USUARIO COM ESTE EMAIL)
                $podeCadastrar = verificaEmail($email);

                if ($podeCadastrar == true) {
                        echo "<form method='post' class='form-cadastrar-horarios'>";
                        echo "<section class='envolve'>";
                        echo "<h1>Horario Livre</h1>";
                        
                        //inputs para reenviar as informacoes anteriores
                        echo "<input type='hidden'name='nome' value='".$_POST['nome']."'>";
                        echo "<input type='hidden'name='email' value='".$_POST['email']."'>";
                        echo "<input type='hidden'name='senha' value='".$_POST['senha']."'>";

                        echo "<label><i class='fas fas2 fa-calendar-alt'></i> <i class='fas fas2 fa-clock'></i><br>";
                        echo "<select name='dia' class='dias'>";
                        echo "<option value='seg'>Segunda</option>";
                        echo "<option value='ter'>Terça</option>";
                        echo "<option value='qua'>Quarta</option>";
                        echo "<option value='qui'>Quinta</option>";
                        echo "<option value='sex'>Sexta</option>";
                        echo "</select></label>";

                        echo "<label><select name='opcao-horario' class='horarios'>";
                        echo "<option value='07:50-08:20'>07:50 - 08:20</option>";
                        echo "<option value='08:20-08:50'>08:20 - 08:50</option>";
                        echo "<option value='08:50-09:20'>08:50 - 09:20</option>";
                        echo "<option value='09:20-09:50'>09:20 - 09:50</option>";
                        echo "<option value='09:50-10:20'>09:50 - 10:20</option>";
                        echo "<option value='10:20-10:50'>10:20 - 10:50</option>";
                        echo "<option value='10:50-11:20'>10:50 - 11:20</option>";
                        echo "<option value='11:30-12:00'>11:30 - 12:00</option>";
                        echo "<option value='12:00-12:30'>12:00 - 12:30</option>";
                        echo "<option value='12:30-13:00'>12:30 - 13:00</option>";
                        echo "<option value='13:00-13:30'>13:00 - 13:30</option>";
                        echo "<option value='13:30-14:00'>13:30 - 14:00</option>";
                        echo "<option value='14:00-14:30'>14:00 - 14:30</option>";
                        echo "<option value='14:30-15:00'>14:30 - 15:00</option>";
                        echo "<option value='15:00-15:30'>15:00 - 15:30</option>";
                        echo "<option value='15:30-16:00'>15:30 - 16:00</option>";
                        echo "<option value='16:00-16:30'>16:00 - 16:30</option>";
                        echo "<option value='16:30-17:00'>16:30 - 17:00</option>";
                        echo "</select><label>";

                        echo "<h3>*Poderá adicionar mais horários depois</h3>";
                        echo "<button type='submit' class='envia'>Cadastrar</button>";
                        echo "</section>";
                        echo "</form>";
                } else {
                    echo "<script> window.alert('Já existe um usuário com este email') </script>";
                }

            } else {
                echo "<script> window.alert('Senhas Diferentes') </script>";
            }

        }
?>

<?php
    // CADASTRA USUARIO COMO PROFESSOR   
    if (isset($_POST['dia']) && isset($_POST['opcao-horario'])) {

        include('bd/conexao.php');

        // VALIDA E CADASTRA HORARIO
        $horarios_splitados = explode('-', $_POST['opcao-horario']);
            
        $horarioInicio = $horarios_splitados[0];
        $horarioFim = $horarios_splitados[1];

        $explodeHorIni = explode(':', $horarioInicio);
        $explodeHorFim = explode(':', $horarioFim);

        //VALIDAÇÃO DE NUMEROS ENTRADOS
        if ($explodeHorIni[0] >= 07 && $explodeHorIni[0] <= 17 && $explodeHorFim[0] >= 07 && $explodeHorFim[0] <= 17) {

            if ($explodeHorIni[1] >= 0 && $explodeHorIni[1] < 60 && $explodeHorIni[1] >= 0 && $explodeHorIni[1] < 60) {
                if ($explodeHorIni[0] < $explodeHorFim[0] || ($explodeHorIni[0] == $explodeHorFim[0] && $explodeHorIni[1] < $explodeHorFim[1])) {
                    // VALIDA EMAIL
                    include_once('./utils/helpers.php');
                    $podeCadastrar = verificaEmail($_POST['email']);

                    if ($podeCadastrar == true) {
                        // Cadastra Usuário e manda um email de confirmação
                        $sql = "INSERT INTO tbusuarios (nome, email, senha, tipo, prof_confirmado, verificado, deletado) values('".$_POST['nome']."', '".$_POST['email']."', md5('".$_POST['senha']."'), 'professor', 'aindaN', 'sim', false)";
                        $insere = mysqli_query($conexao, $sql);

                        // CADASTRA HORÁRIOS LIVRES
                        $sql = "SELECT ID_usuario FROM tbusuarios 
                                    WHERE deletado = false 
                                    AND email = '".$_POST['email']."'";
                        $pega_usuario = mysqli_query($conexao, $sql);

                        if ($pega_usuario) {
                            
                            while ($recebe_usuario = mysqli_fetch_array($pega_usuario)) {
                                $sql = "INSERT INTO tbhor_livres (ID_prof, horario_ini, horario_fim, dia, deletado) values (".$recebe_usuario[0].", '$horarioInicio', '$horarioFim', '".$_POST['dia']."', false)";
                                $insere = mysqli_query($conexao, $sql);
                                header('Location: index.php?pagina=login');
                            }
                        }
                        //Manda as informações de cabecalho no link abaixo
                        
                        //$url = base_convert($_POST['nome'].$_POST['email'], 32, 16);
                        
                        //Manda um email para o usuario pedindo a validação do email
                        /*$to = $_POST['email'];
                        $subject = "Confirmação de email Can You";
                        $message = "<h3><strong>".$_POST['nome'].", nós da equipe Can You estamos Felizes que você tenha se cadastrado em nosso sistema</strong></h3><br><br> mas para que você possa ter acesso a todas as facilidades que o Can You oferece, ainda temos 2 passos de validação. O primeiro será a partir do momento em que você clicar no botão 'cadastrar' abaixo, a segunda se dará com a confirmação feita pela nossa equipe. <br><br><br><a href='127.0.0.1/PPI-SITE_NOVO/?pagina=confirma_prof.php&url=$url>Sou eu e quero confirmar este email<a>";
                        $header = "MIME-Version: 1.0\n";
                        $header .= "Content-type: text/html; charset=iso-8859-1\n";
                        $header .= "From: equipecanyou@gmail.com\n";
                        mail($to, $subject, $message, $header);*/
                    } else {
                        echo "<script> window.alert('Já existe um usuário cadastrado com este email!') </script>";
                    }
                }
            }
        }

    }
?>
</section>