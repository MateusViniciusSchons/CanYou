<?php ob_start(); ?>
<!-- MOSTRA O RELATORIO DO ALUNO -->
<section class="relatorios fora">
    <section class="dentro-rel">
        <?php
            include('bd/conexao.php');

            // Pega o nome do aluno para mostrar
            $sql = "SELECT nome FROM tbusuarios 
                        WHERE deletado = false 
                        AND ID_usuario = ".$_SESSION['aluno-relatorio'];
            $pega_nome_aluno = mysqli_query($conexao, $sql);
            if ($pega_nome_aluno) {
                while ($recebe_nome_aluno = mysqli_fetch_array($pega_nome_aluno)) {

                    $nome_aluno = $recebe_nome_aluno[0];

                }       
            }

            echo "<h1 class='titulo-rela'>Atendimentos que $nome_aluno participou:</h1>";
            // Busca a lista de atendimentos que participou 
            $sql = "SELECT data, ID_prof, confirmado, assunto, observacao_professor, ID_hor_livre FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_aluno = ".$_SESSION['aluno-relatorio']." 
                        AND ocorreu = 'sim'";
            $pega_infos_atend = mysqli_query($conexao, $sql);
            if ($pega_infos_atend) {
                echo "Numero de atendimentos de que participou: ".mysqli_num_rows($pega_infos_atend)."<br>";
                while ($recebe_infos_atend = mysqli_fetch_array($pega_infos_atend)) {
                    // Pega o nome do professor referido a tal atendimento
                    $sql = "SELECT nome FROM tbusuarios 
                                WHERE deletado = false 
                                AND ID_usuario = ".$recebe_infos_atend[1];
                    $pega_nome_prof = mysqli_query($conexao, $sql);
                    if ($pega_nome_prof) {
                        while ($recebe_nome_prof = mysqli_fetch_array($pega_nome_prof)) {
                            $data = explode ('-', $recebe_infos_atend[0]);
                            $data = date('d/m/Y', mktime(0, 0, 0, $data[1], $data[2], $data[0]));
                            
                            echo "<section class='aten'>";
                            echo "No dia ".$data.", esteve em atendimento com Prof ".$recebe_nome_prof[0];
                            echo "</section>";
                        }
                    }
                }
               
            }

            //Atendimentos que não participou

            echo "<h1 style='margin-top: 10px' class='titulo-rela'>Atendimentos marcados com $nome_aluno que não ocorreram:</h1>";
            // Busca a lista de atendimentos
            $sql = "SELECT data, ID_prof, confirmado, assunto, observacao_professor, ID_hor_livre, observacao_aluno FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_aluno = ".$_SESSION['aluno-relatorio']." 
                        AND ocorreu = 'nao'";
            $pega_infos_atend = mysqli_query($conexao, $sql);
            if ($pega_infos_atend) {
                //echo "Numero de atendimentos: ".mysqli_num_rows($pega_infos_atend)."<br>";
                while ($recebe_infos_atend = mysqli_fetch_array($pega_infos_atend)) {
                    // Pega o nome do professor referido a tal atendimento
                    $sql = "SELECT nome FROM tbusuarios 
                                WHERE deletado = false 
                                AND ID_usuario = ".$recebe_infos_atend[1];
                    $pega_nome_prof = mysqli_query($conexao, $sql);
                    if ($pega_nome_prof) {
                        while ($recebe_nome_prof = mysqli_fetch_array($pega_nome_prof)) {
                            $data = explode ('-', $recebe_infos_atend[0]);
                            $data = date('d/m/Y', mktime(0, 0, 0, $data[1], $data[2], $data[0]));
                            
                            echo "<section class='aten''>";
                            echo "No dia ".$data.", foi agendado um atendimento com Prof ".$recebe_nome_prof[0] ." mas não ocorreu ";
                            echo "<br>Observação do professor: " . $recebe_infos_atend[4];
                            echo "<br>Observação do aluno: " . $recebe_infos_atend[6];
                            echo "</section>";
                        }
                    }
                }
               
            }

            //Atendimentos que ainda não ocorreram

            echo "<h1 style='margin-top: 10px' class='titulo-rela'>Atendimentos de $nome_aluno que ainda não ocorreram:</h1>";
            // Busca a lista de atendimentos 
            $sql = "SELECT data, ID_prof, confirmado, assunto, observacao_professor, ID_hor_livre, observacao_aluno FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_aluno = ".$_SESSION['aluno-relatorio']." 
                        AND ocorreu = 'aindaN'";
            $pega_infos_atend = mysqli_query($conexao, $sql);
            if ($pega_infos_atend) {
                //echo "Numero de atendimentos: ".mysqli_num_rows($pega_infos_atend)."<br>";
                while ($recebe_infos_atend = mysqli_fetch_array($pega_infos_atend)) {
                    // Pega o nome do professor referido a tal atendimento
                    $sql = "SELECT nome FROM tbusuarios 
                                WHERE deletado = false 
                                AND ID_usuario = ".$recebe_infos_atend[1];
                    $pega_nome_prof = mysqli_query($conexao, $sql);
                    if ($pega_nome_prof) {
                        while ($recebe_nome_prof = mysqli_fetch_array($pega_nome_prof)) {
                            $data = explode ('-', $recebe_infos_atend[0]);
                            $data = date('d/m/Y', mktime(0, 0, 0, $data[1], $data[2], $data[0]));
                            
                            echo "<section class='aten''>";
                            echo "No dia ".$data.", foi agendado um atendimento com Prof ". $recebe_nome_prof[0] ." mas ainda não ocorreu ";
                            echo "</section>";
                        }
                    }
                }
            }

            //Atendimentos excluidos

            echo "<h1 style='margin-top: 10px' class='titulo-rela'>Atendimentos de $nome_aluno que foram excluidos:</h1>";
            // Busca a lista de atendimentos 
            $sql = "SELECT data, ID_prof, confirmado, assunto, observacao_professor, ID_hor_livre, observacao_aluno FROM tbalunoprofessor 
                        WHERE deletado = false 
                        AND ID_aluno = ".$_SESSION['aluno-relatorio']." 
                        AND ocorreu = 'aindaN'";
            $pega_infos_atend = mysqli_query($conexao, $sql);
            if ($pega_infos_atend) {
                //echo "Numero de atendimentos: ".mysqli_num_rows($pega_infos_atend)."<br>";
                while ($recebe_infos_atend = mysqli_fetch_array($pega_infos_atend)) {
                    // Pega o nome do professor referido a tal atendimento
                    $sql = "SELECT nome FROM tbusuarios 
                                WHERE deletado = false 
                                AND ID_usuario = ".$recebe_infos_atend[1];
                    $pega_nome_prof = mysqli_query($conexao, $sql);
                    if ($pega_nome_prof) {
                        while ($recebe_nome_prof = mysqli_fetch_array($pega_nome_prof)) {
                            $data = explode ('-', $recebe_infos_atend[0]);
                            $data = date('d/m/Y', mktime(0, 0, 0, $data[1], $data[2], $data[0]));
                            
                            echo "<section class='aten''>";
                            echo "No dia ".$data.", foi agendado um atendimento com Prof ". $recebe_nome_prof[0] ." mas ainda não ocorreu ";
                            echo "</section>";
                        }
                    }
                }
            }
        ?>
    </section>
</section> 

<!--Listar por: Ocorreu: (aindaN, nao, sim)-->