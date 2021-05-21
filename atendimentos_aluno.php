<?php ob_start(); ?>
<!-- LISTA OS CONVITES PARA ATENDIMENTO -->
    <section class="atendimentos fora">
        <section class='convites'>
            <h1>Convites para atendimento em grupo</h1>
            <?php
                include('bd/conexao.php');
                $sql = "SELECT ID_hor_livre, ID_prof, data FROM tbalunoprofessor 
                            WHERE deletado = false
                            AND ID_aluno = ".$_SESSION['ID_usuario']." 
                            AND confirmado = 'aindaN'";
                $pega_infos_atendimento = mysqli_query($conexao, $sql);
                if ($pega_infos_atendimento) {
                    if (mysqli_num_rows($pega_infos_atendimento) > 0) {
                        while ($recebe_infos_atendimento = mysqli_fetch_array($pega_infos_atendimento)) {
    
                            $sql = "SELECT horario_ini FROM tbhor_livres 
                                        WHERE deletado = false 
                                        AND ID_hor_livre = ".$recebe_infos_atendimento[0];
                            $pega_horario = mysqli_query($conexao, $sql);
                            if ($pega_horario) {
                                while ($recebe_horario = mysqli_fetch_array($pega_horario)) {
    
                                    $sql = "SELECT nome FROM tbusuarios 
                                                WHERE deletado = false 
                                                AND ID_usuario = ".$recebe_infos_atendimento[1];
                                    $pega_nome_prof = mysqli_query($conexao, $sql);
                                    if ($pega_nome_prof) {
                                        while ($recebe_nome_prof = mysqli_fetch_array($pega_nome_prof)) {
    
                                            include_once('./utils/helpers.php');
                                            $data = formatarData($recebe_infos_atendimento[2]);
                                            echo "<section class='convite'>";
                                            // mostra as chamadas de amigos para atendimentos
                                            $i = 0;
                                            echo "Você foi convidado para participar de um atendimento no dia ".$data." às ".$recebe_horario[0]." com Prof ".$recebe_nome_prof[0]." junto com ";
                                            
                                            // mostra os alunos que foram convidados junto com ele
                                            $sql = "SELECT ID_aluno FROM tbalunoprofessor 
                                                        WHERE deletado = false 
                                                        AND ID_hor_livre = ".$recebe_infos_atendimento[0]." 
                                                        AND data = '".$recebe_infos_atendimento[2]."' 
                                                        AND ID_aluno <> ".$_SESSION['ID_usuario']."";
                                            $pega_alunos = mysqli_query($conexao, $sql);
                                            if ($pega_alunos) {
                                                while ($recebe_alunos = mysqli_fetch_array($pega_alunos)) {
    
                                                    $sql = "SELECT nome FROM tbusuarios 
                                                    WHERE ID_usuario = ".$recebe_alunos[0];
                                                    $pega_nome_aluno = mysqli_query($conexao, $sql);
                                                    if ($pega_nome_aluno) {
                                                        while ($recebe_nome_aluno = mysqli_fetch_array($pega_nome_aluno)) {
                                                            
                                                            // VE QUAIS ALUNOS CONFIRMARAM, QUAIS CANCELARAM E QUAIS AINDA DEIXARAM PENDENTE
                                                            $sql = "SELECT confirmado FROM tbalunoprofessor 
                                                                        WHERE deletado = false
                                                                        AND ID_aluno = ".$recebe_alunos[0]." 
                                                                        AND ID_hor_livre = ".$recebe_infos_atendimento[0]." 
                                                                        AND data = '".$recebe_infos_atendimento[2]."'";
                                                            $pega_confirmacao_aluno = mysqli_query($conexao, $sql);
                                                            if ($pega_confirmacao_aluno) {
                                                                while ($recebe_confirmacao_aluno = mysqli_fetch_array($pega_confirmacao_aluno)) {
                                                                    if ($recebe_confirmacao_aluno[0] == 'p_usu') {
                                                                        $confirmacao = 'confirmado';
                                                                    } else if ($recebe_confirmacao_aluno[0] == 'aindaN') {
                                                                        $confirmacao = 'pendente';
                                                                    } else if ($recebe_confirmacao_aluno[0] == 'nao') {
                                                                        $confirmacao = 'cancelado';
                                                                    }
                                                                }
                                                            }
                                                            // ageita as virgulas nos nomes
                                                            if ($i == 0) {
                                                                echo "<r >".$recebe_nome_aluno[0]."</r>";
                                                            } else {
                                                                echo ", <r >".$recebe_nome_aluno[0]."</r>";
                                                            }
    
                                                        $i += 1;
    
                                                        }
                                                    }
    
                                                }
                                            }
    
                                            // FORM ESCONDIDO PARA CONFIRMAR OU CANCELAR ATENDIMENTO
                                            echo "<form method='post'>";
                                            echo "<input type='hidden' name='ID_prof' value='".$recebe_infos_atendimento[1]."'>";
                                            echo "<input type='hidden' name='ID_aluno' value='".$_SESSION['ID_usuario']."'>";
                                            echo "<input type='hidden' name='ID_hor' value='".$recebe_infos_atendimento[0]."'>";
                                            echo "<input type='hidden' name='data' value='".$recebe_infos_atendimento[2]."'>";
    
                                            // BOTAO PARA CANCELAR ATENDIMENTO
                                            echo "<button type='submit' class='excluir duplo' name='cancel' value='cancel'>Cancelar</button>";
    
                                            // BOTAO PARA CONFIRMAR ATENDIMENTO
                                            echo "<button type='submit' class='confirmar duplo' name='confirm' value='confirm'>Confirmar</button>";
    
                                            echo "</form>";
                                            echo "</section>";
                                        }
                                    }
    
                                }
                            }
                        }
                    } else {
                        echo "<h6>Nenhum convite para atendimento no momento.</h6>";
                    }
                }
            ?>
        </section>

<!-- RECEBE INFORMACOES PARA CONFIRMAR ATENDIMENTO -->
    <?php
        if (isset($_POST['confirm'])) {
        
            $sql = "UPDATE tbalunoprofessor SET confirmado = 'p_usu' WHERE ID_aluno = ".$_POST['ID_aluno']." AND ID_hor_livre = ".$_POST['ID_hor']." AND ID_prof = ".$_POST['ID_prof']." AND data = '".$_POST['data']."'";
            $atualiza = mysqli_query($conexao, $sql);
            header('Refresh: 0');
        
        }
    ?>

<!-- RECEBE INFORMACOES PARA CANCELAR ATENDIMENTO -->
    <?php
        if (isset($_POST['cancel'])) {
            $sql = "UPDATE tbalunoprofessor 
                        SET deletado = true
                            WHERE ID_aluno = ".$_POST['ID_aluno']." 
                            AND ID_hor_livre = ".$_POST['ID_hor']." 
                            AND ID_prof = ".$_POST['ID_prof']." 
                            AND data = '".$_POST['data']."'";

            //$sql = "DELETE FROM tbalunoprofessor WHERE ID_aluno = ".$_POST['ID_aluno']." and ID_hor_livre = ".$_POST['ID_hor']." and ID_prof = ".$_POST['ID_prof']." and data = '".$_POST['data']."'";
            $apaga = mysqli_query($conexao, $sql);
            header("Refresh: 0");
        }
    ?>
<!--  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- LISTA OS ATENDIMENTOS AGUARDANDO CONFIRMAÇÃO DO PROFESSOR -->
    <section class='aguardando-confirm-prof convites'>
        <h1>Atendimentos aguardando confirmação do professor</h1>
        <?php
            include('bd/conexao.php');
            $sql = "SELECT ID_hor_livre, ID_prof, data FROM tbalunoprofessor 
                        WHERE deletado = false
                        AND ID_aluno = ".$_SESSION['ID_usuario']." 
                        AND confirmado = 'p_usu'"; ///
            $pega_infos_atend = mysqli_query($conexao, $sql);
            if ($pega_infos_atend) {
                if (mysqli_num_rows($pega_infos_atend) > 0) {
                    while ($recebe_infos_atend = mysqli_fetch_array($pega_infos_atend)) {
    
                        $sql = "SELECT horario_ini FROM tbhor_livres 
                                    WHERE deletado = false 
                                    AND ID_hor_livre = ".$recebe_infos_atend[0];
                        $pega_hor = mysqli_query($conexao, $sql);
                        if ($pega_hor) {
                            while ($recebe_hor = mysqli_fetch_array($pega_hor)) {
    
                                $sql = "SELECT nome FROM tbusuarios 
                                            WHERE deletado = false 
                                            AND ID_usuario = ".$recebe_infos_atend[1];
                                $pega_nome_prof = mysqli_query($conexao, $sql);
                                if ($pega_nome_prof) {
                                    while ($recebe_nome_prof = mysqli_fetch_array($pega_nome_prof)) {
    
                                        // formata a data para dd/mm/AAAA
                                        include_once('./utils/helpers.php');
                                        $data = formatarData($recebe_infos_atend[2]);
                                        //$explode = explode('-', $recebe_infos_atend[2]);
                                        //$data = date('d/m/Y', mktime(00, 00, 00, $explode[1], $explode[2], $explode[0]));
                                        echo "<section class='aguardando-confirm-prof-text convite'>";
    
                                        echo "Você tem um atendimento pendente para o dia ".$data." às ".$recebe_hor[0]." com Prof ".$recebe_nome_prof[0];
                                        
                                        echo "</section>";
                                    }
                                }
    
                            }
                        }
    
                    }
                } else {
                        echo "<br><h6>Nenhum atendimento aguardando confirmação no momento.</h6>";
                    }
            }
        ?>
    </section>

<!-- LISTA ATENDIMENTOS CONFIRMADOS PELO PROFESSOR -->
    <section class='aguardando-confirm-prof convites'>
        <h1>Atendimentos Agendados</h1>
        <?php
            include('bd/conexao.php');
            $sql = "SELECT ID_hor_livre, ID_prof, obs, data FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_aluno = ".$_SESSION['ID_usuario']." 
                        AND confirmado = 'sim' 
                        AND ocorreu <> 'sim'";
            $pega_infosAtend = mysqli_query($conexao, $sql);
            if ($pega_infosAtend) {
                if (mysqli_num_rows($pega_infosAtend) > 0) {
                    while ($recebe_infosAtend = mysqli_fetch_array($pega_infosAtend)) {
                        
                        $sql = "SELECT horario_ini FROM tbhor_livres 
                                    WHERE ID_hor_livre = ".$recebe_infosAtend[0];
                        $pega_Horario = mysqli_query($conexao, $sql);
                        if ($pega_Horario) {
                            while ($recebe_Horario = mysqli_fetch_array($pega_Horario)) {
                                
                                $sql = "SELECT nome FROM tbusuarios 
                                            WHERE ID_usuario = ".$recebe_infosAtend[1];
                                $pega_nome_user = mysqli_query($conexao, $sql);
                                if ($pega_nome_user) {
                                    while ($recebe_nome_user = mysqli_fetch_array($pega_nome_user)) {
                                        // formata data em dd/mm/AAAA
                                        include_once('./utils/helpers.php');
                                        $date = formatarData($recebe_infosAtend[3]);
                                        //$date = explode('-', $recebe_infosAtend[3]);
                                        //$date = date('d/m/Y', mktime(00,00,00,$date[1], $date[2], $date[0]));
                                        echo "<section class='aguardando-confirm-prof-text convite'>";
                                        echo "Você tem um atendimento marcado para ".$date." com Prof ".$recebe_nome_user[0]." às ".$recebe_Horario[0];
                                        echo "<br> Considerações do professor : ".$recebe_infosAtend[2];
                                        echo "</section>";
                                    }
                                }
    
                            }
                        }
                        
                    }
                } else {
                        echo "<h6>Nenhum atendimento agendado no momento.</h6>";
                    }
            }
        ?>
    </section>
<!--  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- LISTA OS ATENDIMENTOS OCORRIDOS (PASSADOS) PARA ADICIONAR SUAS PERCEPÇÕES-->
    <section class='aguardando-confirm-prof convites'>
        <h1>Atendimentos Passados que você não foi</h1>
        <?php
            include('bd/conexao.php');
            $sql = "SELECT ID_hor_livre, ID_prof, obs, data, observacao_professor, observacao_aluno FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_aluno = ".$_SESSION['ID_usuario']." 
                        AND confirmado = 'sim' 
                        AND ocorreu = 'nao'";
            $pega_infosAtend = mysqli_query($conexao, $sql);
            if ($pega_infosAtend) {
                if (mysqli_num_rows($pega_infosAtend) > 0) {
                    while ($recebe_infosAtend = mysqli_fetch_array($pega_infosAtend)) {
                        if(empty($recebe_infosAtend[5])) {
                            $sql = "SELECT horario_ini FROM tbhor_livres 
                                        WHERE ID_hor_livre = ".$recebe_infosAtend[0];
                            $pega_Horario = mysqli_query($conexao, $sql);
                            if ($pega_Horario) {
                                while ($recebe_Horario = mysqli_fetch_array($pega_Horario)) {
                                    
                                    $sql = "SELECT nome FROM tbusuarios 
                                    WHERE ID_usuario = ".$recebe_infosAtend[1];
                                    $pega_nome_user = mysqli_query($conexao, $sql);
                                    if ($pega_nome_user) {
                                        while ($recebe_nome_user = mysqli_fetch_array($pega_nome_user)) {
                                            // formata data em dd/mm/AAAA
                                            include_once('./utils/helpers.php');
                                            $date = formatarData($recebe_infosAtend[3]);
                                            //$date = explode('-', $recebe_infosAtend[3]);
                                            //$date = date('d/m/Y', mktime(00,00,00,$date[1], $date[2], $date[0]));
                                            echo "<section class='aguardando-confirm-prof-text convite'>";
                                            echo "Você tem um atendimento de ".$date." com Prof ".$recebe_nome_user[0]." às ".$recebe_Horario[0]." aguardando resposta";
                                            echo "<br> Observação do professor : ".$recebe_infosAtend[4];
                                            echo "<form method='post'>";
                                            echo "<input type='hidden' name='ID-hor-livre' value='".$recebe_infosAtend[0]."'>";
                                            echo "<input type='hidden' name='ID-prof' value='".$recebe_infosAtend[1]."'>";
                                            echo "<input type='hidden' name='data' value='".$recebe_infosAtend[3]."'>";
                                            echo "<button type='submit' name='responder' value='sim'>Responder</button>";
                                            echo "</section>";
                                        }
                                    }

                                }
                            }
                        }
                        
                        
                    }
                } else {
                        echo "<h6>Nenhum atendimento aguardando sua resposta no momento.</h6>";
                    }
            }
        ?>
    </section>

<!-- FORM PARA RESPONDER -->
    <?php
        if (isset($_POST['responder'])) {
            echo "<section class='alunos-selecionar'>";
            echo "<form method='post' class='seg-exc-atend'>";
            echo "<h2>Observações</h2>";
            echo "<textarea name='observacao'></textarea>";
            echo "<input type='hidden' name='ID-hor-livre' value='".$_POST['ID-hor-livre']."'>";
            echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
            echo "<input type='hidden' name='ID-prof' value='".$_POST['ID-prof']."'>";
            echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo' name='responder_observacao' value='sim'>Responder</button>";
            echo "</form>";
        }
    ?>

<!-- RESPONDER -->
    <?php
        if (isset($_POST['responder_observacao'])) {
            $sql = "UPDATE tbalunoprofessor 
                SET observacao_aluno = '".$_POST['observacao']."'
                    WHERE ID_aluno = ".$_SESSION['ID_usuario']." 
                    AND ID_hor_livre = ".$_POST['ID-hor-livre']." 
                    AND ID_prof = ".$_POST['ID-prof']." 
                    AND data = '".$_POST['data']."'";
            //$sql = "DELETE FROM tbalunoprofessor  WHERE ID_prof = ".$_SESSION['ID_usuario']." AND ID_hor_livre = ".$_POST['ID-hor-livre']." AND data = '".$_POST['data']."' AND ID_aluno = ".$recebe_aluno_apagar[0];
            $responde = mysqli_query($conexao, $sql);
            header('Refresh: 0');
        }
    ?>