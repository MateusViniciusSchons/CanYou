<?php ob_start(); ?>
<section class='fora'>
    <section class="dentro">
<!-- MOSTRA O NOME DA TURMA -->
<h1 class='titulo_turma'>
    <?php 
        if(!isset($_SESSION['ano']) || !isset($_SESSION['eixo'])) {
            // Retornar para a página de turmas
        }

        echo $_SESSION['ano']."º ";
        
        switch ($_SESSION['eixo']) {
            case 'Adm':
                echo "Administração";
                break;
            case 'Agro':
                echo "Agropecuária";
                break;
            case 'Alm':
                echo "Alimentos";
                break;
            case 'Info':
                echo "Informática";
                break;
        } 
    ?>
</h1>
<!--============================================================     ALUNOS     ============================================================-->

<!--MOSTRA OS ALUNOS DA TURMA-->
<section class="alunos">
    <h2>Alunos</h2>
    <?php
        require('bd/conexao.php');
        $sql = "SELECT ID_usuario, nome, email FROM tbusuarios AS usu 
                    WHERE deletado = false 
                    AND usu.ID_usuario IN (SELECT ID_aluno FROM tbturma_aluno AS TA
                        WHERE deletado = false 
                        AND TA.ID_turma IN (SELECT ID_turma FROM tbturmas AS turm
                            WHERE deletado = false 
                            AND turm.eixo = '".$_SESSION['eixo']."' AND turm.ano = '".$_SESSION['ano']."'
                        )
                    ) AND tipo='aluno'";
        $aluno = mysqli_query($conexao, $sql);
        if ($aluno) {
            if (mysqli_num_rows($aluno) > 0) {
                while ($recebe_aluno = mysqli_fetch_array($aluno)) {
                    echo "<section class='aluno'>";
                    echo "<section class='aluno1'>";
                    echo "<div class='infos-aluno'>";
                    echo "<ul class='infos_aluno'>";
                    echo "<div class='square'><i class='fas fa-user'></i></div>";
    
                    echo "<ul class='opcoes_aluno'>";
    
                    //BOTÃO PARA ALTERAR INFORMACOES DE UM ALUNO
                    echo "<li><form class='infos-alterar-aluno' method='post'>";
                    echo "<input type='hidden' name='nome' value='".$recebe_aluno[1]."'>";
                    echo "<input type='hidden' name='email' value='".$recebe_aluno[2]."'>";
                    echo "<input type='hidden' name='ID_aluno' value='".$recebe_aluno[0]."'>";
                    echo "<button class='alterar-inf-usu' type='submit'><i class='fas fa-pencil-alt'></i></button>";
                    echo "</form></li>";
                    //BOTÃO PARA EXCLUIR UM ALUNO
                    echo "<li><form method='post' class='info-excluir-aluno'>";
                    echo "<input type='hidden' name='IDusuario_exc' value='".$recebe_aluno[0]."'>";
                    echo "<button class='excluir-usuario' type='submit'><i class='fas fa-trash'></i></button>";
                    echo "</form></li>";
                    echo"</ul>";
                    echo "<li>".$recebe_aluno[1]."</li>";
                    echo "<li><r class='email'>".$recebe_aluno[2]."</r></li>";
                    echo "</ul>";
                    echo "</div>";
    
                    echo "</section>";
                    echo "</section>";
                }
            } else {
                echo "<h3>Nenhum aluno criado nesta turma até o momento</h3>";
            }
        }
        //BOTAO PARA ABRIR FORM DE CADASTRAR ALUNOS
        echo "<button class='abrir-adc-alunos'><i class='fas fa-plus'></i></button>";

        
    ?>
</section>
<!--============================================================     OPCOES ALUNOS     ============================================================-->

    <!-- FORM PARA CADASTRAR ALUNOS -->
    <?php
        echo "<section class='form-adc-aln fundo-escuro'>";
            echo "<form class='section-atras' method='post'>";
                echo "<h2 class='fechar-adc-alunos'><i class='fas fa-window-close'></i></h2>";
                echo "<h1 class='titulo'>Adicionar aluno</h1>";
                echo "<i class='fas fas2 fa-user'></i><input class='longo' type='text' name='nome_aln' placeholder='Nome do aluno'>";
                echo "<i class='fas fas2 fa-envelope-open-text'></i><input class='longo' type='email' name='email-aln' placeholder='E-mail do aluno'>";
                echo "<i class='fas fas2 fa-key'></i><input class='longo' type='password' name='senha-aln' placeholder='Senha do aluno'>";
                // hidden para informações da turma
                echo "<input type='hidden' name='".$_SESSION['ano'].$_SESSION['eixo']."' value='".$_SESSION['ano'].$_SESSION['eixo']."'>";
                echo "<button type='submit' class='envia' name='LOL'>Enviar</button>";
            echo "</form>";
        echo "</section>";
    ?>
    
    <!-- RECEBE INFORMACOES PARA CADASTRAR ALUNOS NA TURMA -->
    <?php
        if (isset($_POST['nome_aln']) && isset($_POST['email-aln']) && isset($_POST['senha-aln'])) {
            // Recebe as informacoes
            $nome_aluno = $_POST['nome_aln'];
            $email_aluno = $_POST['email-aln'];
            $senha_aluno = $_POST['senha-aln'];

            $sql = "SELECT email FROM tbusuarios 
                        WHERE deletado = false 
                        AND email = '$email_aluno'";
            $pega_email = mysqli_query($conexao, $sql);
            $numero_de_usuarios = mysqli_num_rows($pega_email);
            // SE NÃO EXISTE USUARIO COM MESMO EMAIL
            if ($numero_de_usuarios == 0) {

                $sql = "SELECT ID_turma FROM tbturmas 
                            WHERE ano = ".$_SESSION['ano']." 
                            AND eixo = '".$_SESSION['eixo']."'";
                $pega_turma = mysqli_query($conexao, $sql);
                if ($pega_turma) {
                    while ($recebe_turma = mysqli_fetch_array($pega_turma)) {
                        // CADASTRA ALUNO
                        $sql = "INSERT INTO tbusuarios (nome, email, senha, tipo, deletado) values ('$nome_aluno', '$email_aluno', md5('$senha_aluno'), 'aluno', false)";
                        $cadastra_aluno = mysqli_query($conexao, $sql);

                        $sql = "SELECT ID_usuario FROM tbusuarios 
                                    WHERE deletado = false 
                                    AND email = '$email_aluno'";
                        $pega_ID_usuario = mysqli_query($conexao, $sql);
                        if ($pega_ID_usuario) {
                            while ($recebe_ID_usuario = mysqli_fetch_array($pega_ID_usuario) ) {
                                    
                                //ASSOCIA O ALUNO COM A TURMA
                                $sql = "INSERT INTO tbturma_aluno (ID_turma, ID_aluno, deletado) values (".$recebe_turma[0].", ".$recebe_ID_usuario[0].", false)";
                                $cadastra_aluno_turma = mysqli_query($conexao, $sql);
                                header('Refresh: 0');
                            }
                        }
                    }
                }

            } else {
                echo "<script> window.alert('Não foi possivel cadastrar. Já existe usuário com este email') </script>";
            }
        }
    ?>

    <!-- FORM PARA ALTERAR DADOS DE ALUNOS DA TURMA -->
    <?php
        if (isset($_POST['nome']) && isset($_POST['email'])) {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            echo "<section class='fundo-escuro'>";
            echo "<form method='post' class='alter-aluno '>";
            
            echo "<h1 class='title'>Alterar dados</h1>";
            echo "<i class='fas fas2 fa-user'></i><input class='longo' type='text' name='novo_nome' class='novo_nome' value='".$nome."'>";
            echo "<i class='fas fas2 fa-envelope'></i><input class='longo' type='email' name='novo_email' class='novo_email' value=".$email.">";
            echo "<label><input type='checkbox' class='def-senha' id='N-senha' name='definir-nova-senha' value='sim'> Definir nova senha</label>";
            echo "<i class='fas fas2 fa-key'></i><input class='longo' type='password' class='nova_senha' class='nova_senha' name='nova_senha' placeholder='nova senha do aluno'>";
            echo "<input type='hidden' name='antigo-email' value=".$_POST['email'].">";
            echo "<input type='hidden' name='ID' value=".$_POST['ID_aluno'].">";
            echo "<button class='fechar-alterar-alunos'><i class='fas fa-window-close'></i></button>";
            echo "<button type='submit' class='mandar envia' name='Enviar'><i class='fas fa-check'></i> Alterar</button>";

            echo "</form>";
            echo "</section>";
        }
    ?>

    <!-- RECEBE INFORMACOES PARA ALTERAR DADOS DE UM ALUNO -->
    <?php
        if (isset($_POST['novo_nome']) && isset($_POST['novo_email'])) {
            $novo_nome = $_POST['novo_nome'];
            // se os emails nove e antigo são iguais, quer dizer que o email ja esta validado e não há outro igual
            if ($_POST['novo_email'] == $_POST['antigo-email']) {
                if (isset($_POST['definir-nova-senha']) && strlen($_POST['nova_senha'])) {
                    // o email não foi modificado e o usuario definiu uma nova senha
                    $sql = "UPDATE tbusuarios SET nome = '$novo_nome', senha = md5('".$_POST['nova_senha']."') WHERE ID_usuario = ".$_POST['ID'];
                    $atualiza = mysqli_query($conexao, $sql);
                    header('Refresh: 0');
                } else if (!isset($_POST['definir-nova-senha'])) {
                    $sql = "UPDATE tbusuarios SET nome = '$novo_nome' WHERE ID_usuario = ".$_POST['ID'];
                    $atualiza = mysqli_query($conexao, $sql);
                    header('Refresh: 0');
                } else {
                    echo "<script> window.alert('Você selecionou Modificar senha mas não digitou a nova senha.'); </script>";
                }
                
            } else {
                // um email diferente foi digitado, necessita validação
                $sql = "SELECT email FROM tbusuarios 
                            WHERE deletado = false 
                            AND email = '".$_POST['novo_email']."'";
                $em = mysqli_query($conexao, $sql);
                if ($em) {
                    if (mysqli_num_rows($em) == 0) {
                        if (isset($_POST['definir-nova-senha']) && strlen($_POST['nova_senha']) > 0) {
                            $sql = "update tbusuarios SET nome = '$novo_nome', email = '".$_POST['novo_email']."', senha = md5('".$_POST['nova_senha']."') WHERE ID_usuario = ".$_POST['ID'];
                            $atualiza = mysqli_query($conexao, $sql);
                            header('Refresh: 1');
                        } else if (!isset($_POST['definir-nova-senha']) || !$_POST['definir-nova-senha']) {
                            $sql = "update tbusuarios SET nome = '$novo_nome', email = '".$_POST['novo_email']."' WHERE ID_usuario = ".$_POST['ID'];
                            $atualiza = mysqli_query($conexao, $sql);
                            header('Refresh: 0');
                        } else {
                            echo "<script> window.alert('Não foi possivel modificar. Você selecionou 'Modificar senha' mas não digitou a nova senha.'); </script>";
                        }
                    } else {
                        echo "<script> window.alert('Não foi possivel modificar.O email digitado já está sendo usado por outro usuario'); </script>";
                    }
                    
                } 
            }
        }
    ?>

    <!-- FORM DE SEGURANÇA PARA EXCLUIR ALUNO -->
    <?php
        if (isset($_POST['IDusuario_exc'])) {
            echo "<section class='excluir-usuar fundo-escuro'>";
            
            echo "<form method='post' class='form-excluir-usuario'>";
            echo "<h2>Quer mesmo excluir este aluno?</h2>";
            echo "<input type='hidden' name='IDuser' value='".$_POST['IDusuario_exc']."'>";
            echo "<button class='cancelar duplo'>Cancelar</button>";
            echo "<button type='submit' name='excluir-aln' class='excluir duplo' value='sim'>Excluir</button>";
            echo "</form>";
            
            echo "</section>";
        }
    ?>

    <!-- RECEBE INFORMACOES PARA EXCLUIR ALUNO -->
    <?php
        if (isset($_POST['excluir-aln'])) {
            $sql = "UPDATE tbusuarios 
                    SET deletado = true
                        WHERE ID_usuario = ".$_POST['IDuser'];
            //$sql = "DELETE FROM tbusuarios WHERE ID_usuario = ".$_POST['IDuser'];
            $deleta = mysqli_query($conexao, $sql);

            $sql = "UPDATE tbturma_aluno 
                    SET deletado = true
                        WHERE ID_aluno = ".$_POST['IDuser'];
            //$sql = "DELETE FROM tbturma_aluno WHERE ID_aluno = ".$_POST['IDuser'];
            $deleta = mysqli_query($conexao, $sql);
            
            $sql = "UPDATE tbalunoprofessor 
                    SET deletado = true
                        WHERE ID_aluno = ".$_POST['IDuser'];
            //$sql = "DELETE FROM tbalunoprofessor WHERE ID_aluno = ".$_POST['IDuser'];
            $deleta = mysqli_query($conexao, $sql);

            header('Refresh: 0');
        }
    ?>

<!--============================================================     DISCIPLINAS     ============================================================-->
<h2 class='discc'>Disciplinas</h2>
<section class="disciplinas">

    <?php
        require('bd/conexao.php');
//      SELECIONA AS DISCIPLINAS DA TURMA E O PROFESSOR LIGADO A CADA UMA
        $sql = "SELECT nome, ID_disc FROM tbdisciplinas AS disc 
                    WHERE deletado = false 
                    AND ID_disc IN ( SELECT ID_disc FROM tbturmadisciplinaprofessor AS TDP
                        WHERE deletado = false 
                        AND ID_turma IN ( SELECT ID_turma FROM tbturmas AS turm
                            WHERE ano = ".$_SESSION['ano']." 
                            AND eixo = '".$_SESSION['eixo']."'
                        )
                    )";
        $pega_discs = mysqli_query($conexao, $sql);
        if ($pega_discs) {
            if (mysqli_num_rows($pega_discs) > 0) {
            while ($recebe_discs = mysqli_fetch_array($pega_discs)) {
//              SELECIONA O PROFESSOR LIGADO A CADA DISCIPLINA
                $sql = "SELECT nome, email, ID_usuario FROM tbusuarios 
                            WHERE deletado = false 
                            AND ID_usuario IN ( SELECT ID_prof FROM tbturmadisciplinaprofessor AS TDP
                            WHERE deletado = false 
                            AND ID_disc = ".$recebe_discs[1]." 
                            AND TDP.ID_turma IN ( SELECT ID_turma FROM tbturmas AS turm
                                WHERE turm.eixo = '".$_SESSION['eixo']."' 
                                AND turm.ano = ".$_SESSION['ano'].")
                        );";
                $pega_prof = mysqli_query($conexao, $sql);
                if ($pega_prof) {
                    while ($recebe_prof = mysqli_fetch_array($pega_prof)) {
                        
                        echo "<section class='disciplina'>";
                        echo "<div class='esquerda'>";
                        echo "<i class='fas fas3 fa-book'></i><h3>".$recebe_discs[0]."</h3>";
                        echo "<i class='fas fas3 fa-chalkboard-teacher'></i><h4>".$recebe_prof[0]."</h4>";
                        echo "</div>";

                        echo "<ul class='opcoes_disc'>";
//                      BOTAO PARA ALTERAR INFORMAÇÕES DA DISCIPLINA
                        echo "<form class='infos-alterar-disc' method='post'>";
                        echo "<input type='hidden' name='ID_disc_alter' value='".$recebe_discs[1]."'>";
                        echo "<input type='hidden' name='ID_prof_alter' value='".$recebe_prof[2]."'>";
                        echo "<button type='submit' name='mudar-prof' value='mudar-prof' class='alterar-inf-disc'><i class='fas fa-wrench'></i></button>";
                        echo "</form>";

//                      BOTAO PARA EXCLUIR DISCIPLINA DA TURMA
                        echo "<form method='post' class='form-excluir-disc-turma'>";
                        echo "<input type='hidden' name='IDdisc_exc' value='".$recebe_discs[1]."'>";
                        echo "<input type='hidden' name='ID_prof_exc' value='".$recebe_prof[2]."'>";
                        echo "<button type='submit' name='excluir-disci' value='excluir-disc' class='excluir-disc'><i class='fas fa-trash'></i></button>";
                        echo "</form>";
                        echo "</ul>";
                        echo "</section>";
                    }
                }
            }
            } else {
                echo "<h2>Nenhuma disciplina adicionada à turma até o momento</h2>";
            }
        }
//      BOTAO PARA ADICIONAR DISCIPLINAS
        echo "<button class='abrir-adc-disciplina'><i class='fas fa-plus'></i></button>";
    ?>
<!--============================================================     OPCOES DISCIPLINAS     ============================================================-->

    <!-- FORM PARA ADICIONAR DISCIPLINA À TURMA -->
    <?php
    echo "<section class='form-adc-disc-turma fundo-escuro'>";
         echo "<form method='post' class='adc-dis'>";
         echo "<button class='fechar-adc-disciplina'>X</button>";
         echo "<h1 class='discs-turma'>Disciplina</h1>";
         
         //SELECIONA AS DISCIPLINAS QUE AINDA NÃO ESTÃO NA TURMA
        $sql = "SELECT nome, ID_disc FROM tbdisciplinas AS disc
                    WHERE deletado = false 
                    AND disc.ID_disc NOT IN ( SELECT ID_disc FROM tbturmadisciplinaprofessor AS TDP
                        WHERE deletado = false 
                        AND TDP.ID_turma IN ( SELECT ID_turma FROM tbturmas AS turm
                            WHERE turm.ano = ".$_SESSION['ano']." 
                            AND turm.eixo = '".$_SESSION['eixo']."'
                        )
                    )";
        $pega_disc_adicionar = mysqli_query($conexao, $sql);
        if ($pega_disc_adicionar) {
            if (mysqli_num_rows($pega_disc_adicionar) > 0) {
                echo "<select class='select-disc' required='' name='ID-disc'>";
                while ($recebe_disc_adicionar = mysqli_fetch_array($pega_disc_adicionar)) {
                    echo "<option class='disciplin' value='".$recebe_disc_adicionar[1]."'>".$recebe_disc_adicionar[0]."</option>";
                }
                echo "</select>";
                echo "<button type='submit' class='disc-enviar envia' name='adicionar-disci'>Continuar</button>";
            } else {
                echo "Não há disciplinas disponíveis para adicionar à turma.";
                //header('Refresh: 0');
            }
        } else {
            echo "Não há disciplinas disponíveis para adicionar à turma.";
        }
        // HIDDEN PARA INFORMAÇÕES DA TURMA
        echo "<button type='submit' class='disc-enviar envia' name='cancelar'>Cancelar</button>";
        echo "</form>";
        echo "</section>";
    ?>

    <!-- FORM PARA ESCOLHER O PROFESSOR PARA DAR ESTA DISCIPLINA NA TURMA -->
    <?php
        if (isset($_POST['adicionar-disci'])) {
            echo "<section class='adc-disc-prof fundo-escuro'>";
            echo "<form method='post' class='alter-d'>"; 
            echo "<button class='fechar-adc-prof-disciplina fechar-adc-disciplina'>X</button>";
            echo "<h1 class='discs-turma'>Professor</h1>";
         
            echo"<p>Professor(a): </p>";
            $sql = "SELECT ID_usuario, nome FROM tbusuarios 
                        WHERE deletado = false 
                        AND tipo = 'professor' 
                        AND ID_usuario IN ( SELECT ID_professor FROM tbdisciplina_professor AS TDP 
                            WHERE deletado = false 
                            AND ID_disciplina = ".$_POST['ID-disc']."
            )";
            //substituí: AND ID_usuario IN ( SELECT ID_prof FROM tbturmadisciplinaprofessor AS TDP 
            $pega_prof = mysqli_query($conexao, $sql);
            if ($pega_prof) {
                if(mysqli_num_rows($pega_prof) < 1) {
                    echo "<script> window.alert('Impossível incluir esta disciplina na turma, nenhum professor pode ministrá-la'); </script>";
                } else {
                    while ($recebe_prof = mysqli_fetch_array($pega_prof)) {
                        echo "<section class='disc-prof'>";
                        echo "<input type='hidden' name='id_disc' value='".$_POST['ID-disc']."'>";
                        echo "<label><input required='' type='radio' name='id_prof' value='".$recebe_prof[0]."'>";
                        echo $recebe_prof[1]."</label>";
                        echo "</section>";
                    }
                    echo "<button type='submit' class='envia' name='cadastrar-prof-disc' value='sim'>Adicionar</button>";
                }
            } else {
                echo "<script> window.alert('Impossível incluir esta disciplina na turma, nenhum professor pode ministrá-la'); </script>";
            }
            echo "<button type='submit' class='envia' name='cancelar' value='sim'>Cancelar</button>";
            
            echo "</form>";
            echo "</section>";
        }
    ?>

    <!-- RECEBE INFORMACOES PARA ADICIONAR DISCIPLINA E PROFESSOR À TURMA -->
    <?php
        if (isset($_POST['cadastrar-prof-disc'])) {
            $sql = "SELECT ID_turma FROM tbturmas 
                        WHERE ano = ".$_SESSION['ano']." 
                        AND eixo = '".$_SESSION['eixo']."'";
            $pegaTurma = mysqli_query($conexao, $sql);
            if ($pegaTurma) {
                while ($recebeTurma = mysqli_fetch_array($pegaTurma)) {
                    
                    //COMO SÓ FORAM MOSTRADAS AS DISCIPLINAS QUE AINDA NÃO ESTÃO NA TURMA, NÃO PRECISA VALIDAR
                    //$sql = "UPDATE tbturmadisciplinaprofessor SET ID_turma = '".$recebeTurma[0]."' WHERE ID_disc = ".$_POST['id_disc']." AND ID_prof = ".$_POST['id_prof']."";
                    $sql = "INSERT INTO tbturmadisciplinaprofessor (ID_turma, ID_disc, ID_prof, deletado) values (".$recebeTurma[0].", ".$_POST['id_disc'].", ".$_POST['id_prof'].", false)";
                    $insere = mysqli_query($conexao, $sql); 
                    header('Refresh: 0');
                }
            }
        }
    ?>

    <!-- FORM PARA ALTERAR PROFESSOR DA DISCIPLINA -->
    <?php
        if (isset($_POST['mudar-prof'])) {
            echo "<section class='alter-prof-disc fundo-escuro'>";
            echo "<form method='post' class='alter-prof'>";
            echo "<button class='fechar-alterar-prof-disc fechar-adc-disciplina'>X</button>";
            echo "<h1 class='titulo-alter-prof'>Selecione o novo professor</h1>";
            $sql = "SELECT ID_usuario, nome, email FROM tbusuarios 
                        WHERE deletado = false 
                        AND tipo = 'professor' 
                        AND ID_usuario IN ( SELECT ID_professor FROM tbdisciplina_professor
                            WHERE deletado = false 
                            AND ID_disciplina = ".$_POST['ID_disc_alter']."
            )";
            // Linha alterada: AND ID_usuario IN ( SELECT ID_prof FROM tbturmadisciplinaprofessor AS TDP
            $pega_professor = mysqli_query($conexao, $sql);
            if ($pega_professor) {
                while ($recebe_professor = mysqli_fetch_array($pega_professor)) {
                    $checked_or_no = $_POST['ID_prof_alter'] == $recebe_professor[0] ? "checked=''":"";
                    echo "<label><input type='radio' name='IDprof' value='".$recebe_professor[0]."' $checked_or_no >";
                    echo $recebe_professor[1]."</label>";
                }
            }
            echo "<input type='hidden' name='prof-anterior' value='".$_POST['ID_prof_alter']."'>";
            echo "<input type='hidden' name='IDdisc' value='".$_POST['ID_disc_alter']."'>";
            echo "<button type='submit' class='envia env-troca-prof' name='modifica-prof-turma' value='sim'>Enviar</button>";
            echo "</form>";
            echo "<section>";
        }
    ?>

    <!-- RECEBE INFORMACOES PARA ALTERAR O PROFESSOR DA DISCIPLINA -->
    <?php
        if (isset($_POST['modifica-prof-turma'])) {
            $sql = "SELECT ID_turma FROM tbturmas WHERE ano = ".$_SESSION['ano']." and eixo = '".$_SESSION['eixo']."'";
            $pegaID_turma = mysqli_query($conexao, $sql);
            if ($pegaID_turma) {
                while ($recebeID_turma = mysqli_fetch_array($pegaID_turma)) {
                    //$sql = "UPDATE tbturmadisciplinaprofessor SET ID_turma = NULL
                        //WHERE ID_turma = ".$recebeID_turma[0]." AND ID_disc = ".$_POST['IDdisc']." AND ID_prof = ".$_POST['prof-anterior']."";
                    //$modifica_prof_disc_turma = mysqli_query($conexao, $sql);

                    $sql = "UPDATE tbturmadisciplinaprofessor 
                                SET ID_prof = ".$_POST['IDprof']."
                                    WHERE ID_disc = ".$_POST['IDdisc']." 
                                    AND ID_turma = ".$recebeID_turma[0]."";

                        /* 
                        Query anterior: 
                         $sql = "UPDATE tbturmadisciplinaprofessor 
                                SET ID_turma = ".$recebeID_turma[0]."
                        WHERE ID_disc = ".$_POST['IDdisc']." AND ID_prof = ".$_POST['IDprof']."";
                        */
                    $modifica_prof_disc_turma = mysqli_query($conexao, $sql);
                    header("Refresh: 0");
                }
            }
        }
    ?>

    <!-- FORM DE SEGURANÇA PARA EXCLUIR DISCIPLINA DA TURMA -->
    <?php
        if (isset($_POST['excluir-disci'])) {
            echo "<section class='excl fundo-escuro'>";
            echo "<form method='post' class='form-excluir-disciplina'>";
            echo "<h2>Quer mesmo excluir esta disciplina da turma?</h2>";
            echo "<input type='hidden' name='ID_disciplina' value='".$_POST['IDdisc_exc']."'>";
            echo "<input type='hidden' name='ID_professor' value='".$_POST['ID_prof_exc']."'>";
            
            echo "<button class='cancelar duplo'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo' name='excluir-discii' value='sim'>Excluir</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>

    <!-- RECEBE INFORMAÇÕES PARA EXCLUIR DISCIPLINA DA TURMA -->
    <?php
        if (isset($_POST['excluir-discii'])) {
             $sql = "SELECT ID_turma FROM tbturmas WHERE ano = ".$_SESSION['ano']." AND eixo = '".$_SESSION['eixo']."'";
            $pegaTurma = mysqli_query($conexao, $sql);
            if ($pegaTurma) {
                while($recebeTurma = mysqli_fetch_array($pegaTurma)) {
                    $sql = "UPDATE tbturmadisciplinaprofessor 
                            SET deletado = true
                                WHERE ID_disc = ".$_POST['ID_disciplina']." 
                                AND ID_prof = ".$_POST['ID_professor']." 
                                AND ID_turma = ".$recebeTurma[0]."";
                    //$sql = "DELETE FROM tbturmadisciplinaprofessor WHERE ID_disc = ".$_POST['ID_disciplina']." and ID_prof = ".$_POST['ID_professor']." and ID_turma = ".$recebeTurma[0]."";
                    $exclui_t_disc_prof = mysqli_query($conexao, $sql);
                    header("Refresh: 0");
                }
            }
            
        }
    ?>
</section>
</section>
</section>