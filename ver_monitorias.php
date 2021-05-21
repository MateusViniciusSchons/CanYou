<?php ob_start(); ?>
<!-- LISTA MONITORIAS COMPARTILHADAS COM A TURA DO ALUNO -->
<section class="monitorias-aluno fora">
    <section class="monits">
        <?php
            include('bd/conexao.php');
            $sql = "SELECT descricao, nome_aluno, local, disciplina, horario, data FROM tbmonitorias 
                        WHERE deletado = false 
                        AND ID_monitoria IN ( SELECT ID_monitoria FROM tbmonitoriaturma 
                            WHERE deletado = false 
                            AND ID_turma IN ( SELECT ID_turma FROM tbturma_aluno 
                                WHERE deletado = false 
                                AND ID_aluno = ".$_SESSION['ID_usuario']."
                            )
                        )";
            $pega_infos_monitorias = mysqli_query($conexao, $sql);
            if ($pega_infos_monitorias) {
                if (mysqli_num_rows($pega_infos_monitorias) > 0) {
                    while ($recebe_infos_monitorias = mysqli_fetch_array($pega_infos_monitorias)) {
                        echo "<section class='monitoria'>";
                        echo "<h1>Monitoria de ".$recebe_infos_monitorias[3]."</h1>";
                        echo "<h4>Dia: ".$recebe_infos_monitorias[5]." às ".$recebe_infos_monitorias[4]."</h4>";
                        echo "<h4>Local: ".$recebe_infos_monitorias[2]."</h4>";
                        echo "<h4>Monitor: ".$recebe_infos_monitorias[1]."</h4>";
                        echo "<h4>Descrição: ".$recebe_infos_monitorias[0]."</h4>";
                        echo "</section>";
                    }
                } else {
                    echo "<h5>Nenhuma monitoria cadastrada até o momento.</h5>";
                }
            }
        ?>
    </section>
</section>