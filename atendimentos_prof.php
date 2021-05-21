<?php ob_start(); ?>
<!-- MOSTRA ATENDIMENTOS AGUARDANDO CONFIRMACAO ------------------------------------------------------------------------------- -->
<section class="atendimentos fora">
    <section class="atend">
        <section class="no-aguardo">
            <h1>Atendimentos aguardando confirmação</h1>
            <?php
                include('bd/conexao.php');

                $sql = "SELECT ID_hor_livre, ID_aluno, data, assunto FROM tbalunoprofessor 
                            WHERE deletado = false 
                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                            AND confirmado = 'p_usu'";
                $pega_idhorLivre = mysqli_query($conexao, $sql);
                if ($pega_idhorLivre) {
                    if(mysqli_num_rows($pega_idhorLivre) <= 0) {
                        echo "<h6>Nenhum atendimento aguardando confirmação.</h6>";
                    } else {
                        while ($recebe_idhorLivre = mysqli_fetch_array($pega_idhorLivre)) {
                            $sql = "SELECT horario_ini FROM tbhor_livres 
                                        WHERE ID_hor_livre = ".$recebe_idhorLivre[0];
                            $pega_infos_horLivre = mysqli_query($conexao, $sql);
                            if ($pega_infos_horLivre) {
                                while ($recebe_infos_horLivre = mysqli_fetch_array($pega_infos_horLivre)) {
                                    $sql = "SELECT nome FROM tbusuarios 
                                                WHERE ID_usuario = ".$recebe_idhorLivre[1]."";
                                    $pega_nome_user = mysqli_query($conexao, $sql);
                                    if ($pega_nome_user) {
                                        while ($recebe_nome_user = mysqli_fetch_array($pega_nome_user)) {
                                            // formata a data no padrao dd/mm/AAAA
                                            include_once('./utils/helpers.php');
                                            $data = formatarData($recebe_idhorLivre[2]);
                                            echo "<section class='atends'>";
                                            echo "".$recebe_nome_user[0]." solicitou um atendimento para o dia ".$data." às ".$recebe_infos_horLivre[0];
                                            echo " Assunto: ".$recebe_idhorLivre[3];
                                            echo "<form method='post'>";
                                            echo "<input type='hidden' name='ID-aluno' value='".$recebe_idhorLivre[1]."'>";
                                            echo "<input type='hidden' name='ID-hor-livre' value='".$recebe_idhorLivre[0]."'>";
                                            echo "<input type='hidden' name='data' value='".$recebe_idhorLivre[2]."'>";
                                            echo "<button type='hidden' class='excluir duplo' name='Confirmar' value='não'>Cancelar</button>";
                                            echo "<button type='hidden' class='confirmar duplo' name='Confirmar' value='sim'>Confirmar</button>";
                                            echo "</form>";
                                            echo "</section>";
                                        }
                                    }
                                }
                            }
    
                        }

                    }
                }
            ?>
        </section>
<!--        MANDAR OBSERVAÇÃO PARA CONFIRMAR ATENDIMENTO OU EXCLUIR ATENDIMENTO (CANCELAR)-->
            <?php
                if (isset($_POST['Confirmar'])) {
                    if ($_POST['Confirmar'] == 'sim') {
                        echo "<section class='fundo-escuro'>";
                        echo "<form method='post' class='form-sel-aln'>";
                        echo "<textarea name='obs' class='obs1' placeholder='Devolva uma mensagem para o aluno, escreva o local, materiais que deve levar, etc.'></textarea>";
                        echo "<input type='hidden' name='ID-aluno' value='".$_POST['ID-aluno']."'>";
                        echo "<input type='hidden' name='ID-hor-livre' value='".$_POST['ID-hor-livre']."'>";
                        echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
                        echo "<button type='hidden' class='envia' name='Confirmar_' value='sim'>Confirmar</button>";
                        echo "</form>";
                        echo "</section>";
                    } else if ($_POST['Confirmar'] == 'não') {
                        // se for cancelado
                        
                        $sql = "UPDATE tbalunoprofessor 
                        SET deletado = true
                            WHERE ID_aluno = ".$_POST['ID-aluno']." 
                            AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                            AND data = '".$_POST['data']."'";
                        
                        //$sql = "DELETE FROM tbalunoprofessor WHERE ID_prof = ".$_SESSION['ID_usuario']." AND ID_aluno = ".$_POST['ID-aluno']." AND ID_hor_livre = ".$_POST['ID-hor-livre']." AND data = '".$_POST['data']."'";
                        $deleta = mysqli_query($conexao, $sql);
                        header('Refresh: 0');
                        // Mandar email para o usuario
                    }
                }
            ?>

<!--        CONFIRMA ATENDIMENTO -->
            <?php
                if (isset($_POST['Confirmar_']) && $_POST['Confirmar_'] == 'sim') {
                    $sql = "UPDATE tbalunoprofessor SET confirmado = 'sim' WHERE ID_prof = ".$_SESSION['ID_usuario']." AND ID_aluno = ".$_POST['ID-aluno']." AND ID_hor_livre = ".$_POST['ID-hor-livre']." AND data = '".$_POST['data']."'";
                    $atualiza = mysqli_query($conexao, $sql);
                    $sql = "UPDATE tbalunoprofessor SET obs = '".$_POST['obs']."' WHERE ID_prof = ".$_SESSION['ID_usuario']." AND ID_aluno = ".$_POST['ID-aluno']." AND ID_hor_livre = ".$_POST['ID-hor-livre']." AND data = '".$_POST['data']."'";
                    $atualiza = mysqli_query($conexao, $sql);
                    header('Refresh: 0');
                } 
            ?>

<!--        MOSTRA OS ATENDIMENTOS AGENDADOS --------------------------------------------------------------------------------------- -->
        <section class="agendados">
            <h1>Atendimentos Agendados</h1>
            <?php
                include('bd/conexao.php');

                $sql = "SELECT DISTINCT ID_hor_livre, data, assunto FROM tbalunoprofessor 
                            WHERE deletado = false 
                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                            AND confirmado = 'sim' 
                            AND ocorreu = 'aindaN'";
                $pega_idhorLivre = mysqli_query($conexao, $sql);
                if ($pega_idhorLivre) {
                    if(mysqli_num_rows($pega_idhorLivre) <= 0) {
                        echo "<h6>Nenhum atendimento agendado até o momento.</h6>";
                    } else {
                        while ($recebe_idhorLivre = mysqli_fetch_array($pega_idhorLivre)) {
                            echo "<section class='atends'>";
                            // pega o nome do professor
                            $sql = "SELECT nome FROM tbusuarios 
                                        WHERE ID_usuario = ".$_SESSION['ID_usuario'];
                            $pega_nome_prof = mysqli_query($conexao, $sql);
                            if ($pega_nome_prof ) {
                                while ($recebe_nome_prof = mysqli_fetch_array($pega_nome_prof )) {
                                    //formata a data
                                    include_once('./utils/helpers.php');
                                    $data = formatarData($recebe_idhorLivre[1]);
                                    echo "Você tem um atendimento marcado para ".$data." com ";
                                }
                            }
                            //pega o nome dos alunos
                            $sql = "SELECT nome FROM tbusuarios
                                        WHERE ID_usuario IN ( SELECT ID_aluno FROM tbalunoprofessor
                                            WHERE deletado = false  
                                            AND ID_hor_livre = ".$recebe_idhorLivre[0]." 
                                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                                            AND data = '".$recebe_idhorLivre['1']."' 
                                            AND confirmado = 'sim'
                            )";
                            $pega_aluno = mysqli_query($conexao, $sql);
                            if ($pega_aluno) {
                                $cont = 0;
                                $num_alunos = mysqli_num_rows($pega_aluno);
                                while ($recebe_aluno = mysqli_fetch_array($pega_aluno)) {
                                    // escreve os alunos com as separacoes corretas entre os nomes
                                    if ($cont == 0) {
                                        echo $recebe_aluno[0];
                                    } else if ($cont < ($num_alunos - 1)) {
                                        echo ", ".$recebe_aluno[0];
                                    } else if ($cont == ($num_alunos - 1)){
                                        echo " e ".$recebe_aluno[0];
                                    }
                                    $cont += 1;
                                }
                            }
                            
                            $sql = "SELECT horario_ini FROM tbhor_livres 
                                        WHERE deletado = false 
                                        AND ID_hor_livre = ".$recebe_idhorLivre[0]."";
                            $pega_horario = mysqli_query($conexao, $sql);
                            if ($pega_horario) {
                                while ($recebe_horario = mysqli_fetch_array($pega_horario)) {
                                    echo " às ".$recebe_horario[0]." <br>Assunto: ".$recebe_idhorLivre[2];
    
                                    //SE O HORARIO DO ATENDIMENTO JA TIVER PASSADO, O PROFESSOR PODE DIZER SE JA OCORREU OU SE NINGUEM VEIO OU ATÉ MESMO QUEM VEIO
                                    date_default_timezone_set('America/Sao_Paulo');
                                    // HORA SEM HORARIO DE VERAO
                                        $hora = date(' H:i', strtotime('-1 hour'));
                                        $date = date(' Y-m-d');
                                
                                    if (strtotime($recebe_idhorLivre[1]) < strtotime($date) || $recebe_idhorLivre[1] == $date && $recebe_infos_horLivre[1] < $hora) {
                                        // Se o horario ja passou, da para ver se todos foram ao atendimento
                                        // BOTOES PARA VALIDAR OU NAO UM ATENDIMENTO
                                        echo "<form method='post' class='aconteceu-ou-n'>";
                                        echo "<input type='hidden' name='ID-hor-livre' value='".$recebe_idhorLivre[0]."'>";
                                        echo "<input type='hidden' name='data' value='".$recebe_idhorLivre[1]."'>";
                                        echo "<input type='hidden' name='hora' value='".$recebe_horario[0]."'>";
                                        echo "<input type='hidden' name='ID-prof' value='".$_SESSION['ID_usuario']."'>";
                                        // BOTOES PARA VALIDAR ATENDIMENTO
                                        echo "<button type='submit' class='aconteceu envia' style='font-size: 1em' name='abrir-validar-atendimento' value='aconteceu'>O atendimento aconteceu</button>";
                                        // BOTAO PARA EXCLUIR ATENDIMENTO
                                        echo "<button type='submit' class='N-aconteceu envia' style='background-color: rgb(242, 112, 122); font-size: 1em' name='atendimento-n-ocorreu' value='nao-aconteceu'>O atendimento não aconteceu</button>";
                                        echo "</form>";
                                    }
                                }
                            }
                            echo "</section>";
                        }
                    }
                }
            ?>
        </section>
    </section>
</section>

<!-- FORM PARA VALIDAR ATENDIMENTO -->
    <?php
        if (isset($_POST['abrir-validar-atendimento'])) {
                echo "<section class='fundo-escuro'>";
                
                echo "<form method='post' class='valid'>";
                echo "<h1>Selecione os alunos que foram</h1>";
            // lista os alunos que tinham agendado atendimento
            $sql = "SELECT nome, ID_usuario FROM tbusuarios 
                        WHERE deletado = false 
                        AND ID_usuario IN ( SELECT ID_aluno FROM tbalunoprofessor
                            WHERE deletado = false 
                            AND ID_prof = ".$_POST['ID-prof']." 
                            AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                            AND data = '".$_POST['data']."'
                        )";
            $pega_nome_aluno = mysqli_query($conexao, $sql);
            if ($pega_nome_aluno) {
                while ($recebe_nome_aluno = mysqli_fetch_array($pega_nome_aluno)) {
                    echo "<label><input type='checkbox' name='alunos[]' value='".$recebe_nome_aluno[1]."'> ".$recebe_nome_aluno[0]."</label>";
                }
            }

            echo "<input type='hidden' name='ID-hor-livre' value='".$_POST['ID-hor-livre']."'>";
            echo "<input type='hidden' name='ID-prof' value='".$_POST['ID-prof']."'>";
            echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
            echo "<input type='hidden' name='hora' value='".$_POST['hora']."'>";
            echo "<button type='submit' name='foi' value='foi'>Pronto</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>

    <!-- FORM PARA ADICIONAR NOTA (RECLAMAÇÃO) DO PROFESSOR -->

    <?php
        if (isset($_POST['foi']) && isset($_POST['alunos'])) {
            echo "<section class='alunos-selecionar'>";
            echo "<form method='post' class='seg-exc-atend'>";
            echo "<h2>Observações</h2>";
            echo "Para quem foi: <textarea name='observacao'></textarea>";
            echo "Para quem não foi: <textarea name='observacao_n_foi'></textarea>";
            foreach ($_POST['alunos'] as $key => $value) {
                echo "<input type='hidden' name='alunos[]' value='".$value."'>";
            }
            echo "<input type='hidden' name='ID-hor-livre' value='".$_POST['ID-hor-livre']."'>";
            echo "<input type='hidden' name='ID-prof' value='".$_POST['ID-prof']."'>";
            echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
            echo "<input type='hidden' name='hora' value='".$_POST['hora']."'>";
            echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo' name='atendimento_ocorreu_mesmo' value='sim'>Pronto!</button>";
            echo "</form>";
        }
    ?>

<!-- RECEBE INFORMACOES PARA DIZER QUE O ATENDIMENTO OCORREU -->
    <?php
        if (isset($_POST['atendimento_ocorreu_mesmo']) && isset($_POST['alunos'])) {

            foreach ($_POST['alunos'] as $key => $value) {
                // altera a confirmacao para ocorreu
                $sql = "UPDATE tbalunoprofessor 
                            SET ocorreu = 'sim', 
                            observacao_professor = '".$_POST['observacao']."' 
                            WHERE ID_prof = ".$_SESSION['ID_usuario']." 
                            AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                            AND ID_aluno = ".$value." 
                            AND data = '".$_POST['data']."'";
                $altera = mysqli_query($conexao, $sql);
            }
            // pega os usuarios que marcaram este atendimento
            $sql = "SELECT ID_aluno FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario']." 
                        AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                        AND data = '".$_POST['data']."'";
            $pega_aluno = mysqli_query($conexao, $sql);
            if ($pega_aluno) {
                while ($recebe_aluno = mysqli_fetch_array($pega_aluno)) {
                    // se eles não foram marcados como presentes, deleta o atendimento deles do sistema
                    $i = 1;
                    
                    foreach ($_POST['alunos'] as $key => $value) {
                        if ($value != $recebe_aluno[0]) {
                            $i *= 1;
                        } else {
                            $i = 0;
                        }
                    }
                    if ($i == 1) {
                        // Quem nao foi marcado como presente, tem seu atendimento marcado como nao ocorreu

                        $sql = "UPDATE tbalunoprofessor 
                                    SET ocorreu = 'nao',
                                    observacao_professor = '".$_POST['observacao_n_foi']."' 
                                        WHERE ID_aluno = ".$recebe_aluno[0]." 
                                        AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                                        AND ID_prof = ".$_SESSION['ID_usuario']." 
                                        AND data = '".$_POST['data']."'";
                        //$sql = "DELETE FROM tbalunoprofessor  WHERE ID_prof = ".$_POST['ID-prof']." AND ID_hor_livre = ".$_POST['ID-hor-livre']." AND data = '".$_POST['data']."' AND ID_aluno = ".$recebe_aluno[0];
                        $apaga = mysqli_query($conexao, $sql);
                        header('Refresh: 0');
                    }
                }
            }
        }
    ?>

<!-- FORM DE SEGURANÇA PARA EXCLUIR ATENDIMENTO -->
    <?php
        if (isset($_POST['excluir-atendimento'])) {
            echo "<section class='alunos-selecionar'>";
            echo "<form method='post' class='seg-exc-atend'>";
            echo "<h2>Deseja mesmo excluir este atendimento?</h2>";
            echo "<input type='hidden' name='ID-hor-livre' value='".$_POST['ID-hor-livre']."'>";
            echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
            echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo' name='excluir-atendimento_' value='sim'>Excluir</button>";
            echo "</form>";
        }
    ?>

<!-- RECEBE INFORMACOES PARA EXCLUIR ATENDIMENTO -->
    <?php
         if (isset($_POST['excluir-atendimento_'])) {
            $sql = "SELECT ID_aluno FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario']." 
                        AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                        AND data = '".$_POST['data']."'";
            $pega_aluno_apagar = mysqli_query($conexao, $sql);
            if ($pega_aluno_apagar) {
                while ($recebe_aluno_apagar = mysqli_fetch_array($pega_aluno_apagar)) {
                    $sql = "UPDATE tbalunoprofessor 
                        SET deletado = true
                            WHERE ID_aluno = ".$_POST['ID_aluno']." 
                            AND ID_hor_livre = ".$_POST['ID_hor']." 
                            AND ID_prof = ".$_POST['ID_prof']." 
                            AND data = '".$_POST['data']."'";
                    //$sql = "DELETE FROM tbalunoprofessor  WHERE ID_prof = ".$_SESSION['ID_usuario']." AND ID_hor_livre = ".$_POST['ID-hor-livre']." AND data = '".$_POST['data']."' AND ID_aluno = ".$recebe_aluno_apagar[0];
                    $apaga = mysqli_query($conexao, $sql);
                }
            }
         }
    ?>

<!-- FORM DE SEGURANÇA PARA DIZER Q O ATENDIMENTO NAO OCORREU -->
    <?php
        if (isset($_POST['atendimento-n-ocorreu'])) {
            echo "<section class='alunos-selecionar'>";
            echo "<form method='post' class='seg-exc-atend'>";
            echo "<h2>Deseja mesmo dizer que este atendimento não ocorreu?</h2>";
            echo "<input type='hidden' name='ID-hor-livre' value='".$_POST['ID-hor-livre']."'>";
            echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
            echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo' name='atendimento_n_ocorreu' value='sim'>Quero!</button>";
            echo "</form>";
        }
    ?>
<!-- FORM PARA ADICIONAR NOTA (RECLAMAÇÃO) DO PROFESSOR -->

    <?php
        if (isset($_POST['atendimento_n_ocorreu'])) {
            echo "<section class='alunos-selecionar'>";
            echo "<form method='post' class='seg-exc-atend'>";
            echo "<h2>Observações</h2>";
            echo "<textarea name='observacao'></textarea>";
            echo "<input type='hidden' name='ID-hor-livre' value='".$_POST['ID-hor-livre']."'>";
            echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
            echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo' name='atendimento_n_ocorreu_mesmo' value='sim'>Pronto!</button>";
            echo "</form>";
        }
    ?>

<!-- RECEBE INFORMACOES PARA DIZER QUE O ATENDIMENTO NAO OCORREU -->
    <?php
         if (isset($_POST['atendimento_n_ocorreu_mesmo'])) {
            $sql = "SELECT ID_aluno FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario']." 
                        AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                        AND data = '".$_POST['data']."'";
            $pega_aluno_apagar = mysqli_query($conexao, $sql);
            if ($pega_aluno_apagar) {
                while ($recebe_aluno_apagar = mysqli_fetch_array($pega_aluno_apagar)) {
                    $sql = "UPDATE tbalunoprofessor 
                        SET ocorreu = 'nao',
                        observacao_professor = '".$_POST['observacao']."' 
                            WHERE ID_aluno = ".$recebe_aluno_apagar[0]." 
                            AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                            AND data = '".$_POST['data']."'";
                    //$sql = "DELETE FROM tbalunoprofessor  WHERE ID_prof = ".$_SESSION['ID_usuario']." AND ID_hor_livre = ".$_POST['ID-hor-livre']." AND data = '".$_POST['data']."' AND ID_aluno = ".$recebe_aluno_apagar[0];
                    $apaga = mysqli_query($conexao, $sql);
                }
            }
         }
    ?>