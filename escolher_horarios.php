<?php ob_start(); ?>
<section class="fora">
<!-- MOSTRA HORARIOS CADASTRADOS -->

    <?php
        include('bd/conexao.php');
//      MOSTRA O DIA
        echo "<section class='horarios_dias'>";
        $sql = "SELECT DISTINCT dia FROM tbhor_livres 
                    WHERE deletado = false 
                    AND ID_prof = ".$_SESSION['ID_usuario']." ORDER BY dia";
        $pega_dia = mysqli_query($conexao, $sql);
        if ($pega_dia) {
            while ($recebe_dia = mysqli_fetch_array($pega_dia)) {
                echo "<section class='horarios_dia'>";
                echo "<h4 class='mostra-dia'>".$recebe_dia[0]."</h4>";
// MOSTRA OS HORARIOS DE CADA DIA
                $sql = "SELECT horario_ini, horario_fim, ID_hor_livre FROM tbhor_livres 
                            WHERE deletado = false 
                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                            AND dia = '".$recebe_dia[0]."'";
                $pega_hor = mysqli_query($conexao, $sql);
                if ($pega_hor) {
                    while ($recebe_hor = mysqli_fetch_array($pega_hor)) {
                        echo "<h5 class='mostra-hora'>".$recebe_hor[0]." - ".$recebe_hor[1]."</h5>";
//                      BOTAO PARA EXCLUIR HORARIO LIVRE
                        echo "<form method='post' class='ho'>";
                        echo "<input type='hidden' name='ID_horario_liv' value='".$recebe_hor[2]."'>";
                        echo "<button type='submit' class='excluir-horario-livre' name='exclui' value='exclui'><i class='fas fa-trash-alt'></i></button>";
                        echo "</form>";
                    }
                }
                echo "</section>";
            }
        }
        //BOTAO PARA ADICIONAR HORARIOS LIVRES
        echo "<button class='cad-hor-livres envia'><i class='fas fa-plus'></i></button>";
                
        echo "</section>";
    ?>

<!-- FORM PARA CADASTRAR HORARIOS LIVRES -->
<section class='form-cad-hor-liv fundo-escuro'>
<form method="post" class="form_cad_hor centro">
        <button class="fechar-cad-hor-liv">X</button>

    <label><input type="checkbox" name="dia[]" value="seg" >seg</label>
    <label><input type="checkbox" name="dia[]" value="ter" >ter</label>
    <label><input type="checkbox" name="dia[]" value="qua" >qua</label>
    <label><input type="checkbox" name="dia[]" value="qui" >qui</label>
    <label><input type="checkbox" name="dia[]" value="sex" >sex</label>

    <select name='opcao-horario' class='horarios'>
        <option value='07:50-08:20'>07:50 - 08:20</option>
        <option value='08:20-08:50'>08:20 - 08:50</option>
        <option value='08:50-09:20'>08:50 - 09:20</option>
        <option value='09:20-09:50'>09:20 - 09:50</option>
        <option value='09:50-10:20'>09:50 - 10:20</option>
        <option value='10:20-10:50'>10:20 - 10:50</option>
        <option value='10:50-11:20'>10:50 - 11:20</option>
        <option value='11:30-12:00'>11:30 - 12:00</option>
        <option value='12:00-12:30'>12:00 - 12:30</option>
        <option value='12:30-13:00'>12:30 - 13:00</option>
        <option value='13:00-13:30'>13:00 - 13:30</option>
        <option value='13:30-14:00'>13:30 - 14:00</option>
        <option value='14:00-14:30'>14:00 - 14:30</option>
        <option value='14:30-15:00'>14:30 - 15:00</option>
        <option value='15:00-15:30'>15:00 - 15:30</option>
        <option value='15:30-16:00'>15:30 - 16:00</option>
        <option value='16:00-16:30'>16:00 - 16:30</option>
        <option value='16:30-17:00'>16:30 - 17:00</option>
    </select>
    <button type="submit" class='envia'>Enviar</button>
</form>
    </section>

<!-- VALIDA E CADASTRA HORARIOS -->
    <?php
        if (isset($_POST['opcao-horario']) && isset($_POST['dia'])) {
            // separa o horario de inicio do horario de fim
            $Horario = explode('-', $_POST['opcao-horario']);

            $HorIni = $Horario[0];
            $HorFim = $Horario[1];

            //VALIDA OS HORARIOS ENTRADOS
            if (($HorIni >= "07:50" && $HorFim <= "17:10") && ($HorFim > $HorIni)) {
                //VE SE EXISTE HORARIO LIVRE QUE BATE COM ESTE
                foreach ($_POST['dia'] as $key => $value) {
                    $sql = "SELECT horario_ini, horario_fim FROM tbhor_livres 
                                WHERE deletado = false 
                                AND ID_prof = ".$_SESSION['ID_usuario']." 
                                AND dia = '".$value."'";
                    $pega_horarios = mysqli_query($conexao, $sql);
                    if ($pega_horarios) {
                        $i = 0;
                        while ($recebe_horarios = mysqli_fetch_array($pega_horarios)) {

                            $hor_ini_bd = $recebe_horarios[0];
                            $hor_fim_bd = $recebe_horarios[1];
                            
                            // como o horario fim é maior que o horario inicio, se nosso horario de inicio for maior que op horario de fim do banco, esta validado. O mesmo ocorre se o nosso horario de fim for menor que o horario inicio do banco
                            if ($HorFim <= $hor_ini_bd || $HorIni >= $hor_fim_bd) {
                                $i *= 1;
                            } else {
                                $i += 1;
                            }
                        }
                        if ($i == 0) {
                            // CADASTRA HORARIO LIVRE
                            $sql = "INSERT INTO tbhor_livres (ID_prof, horario_ini, horario_fim, dia, deletado) VALUES (".$_SESSION['ID_usuario'].", '$HorIni', '$HorFim', '$value', false)";
                            $cadastra_dia_livre = mysqli_query($conexao, $sql);
                            header('Refresh: 1');
                        } else {
                            echo "<script> alert('Horários inválidos! Estes horários já estão cadastrados') </script>";
                        }
                    }
                }
            }
        }
    ?>

<!-- FORM DE SEGURANÇA PARA EXCLUIR HORARIO LIVRE -->
        <?php
            if (isset($_POST['exclui'])) {
                echo "<section class='alunos-selecionar fundo-escuro'>";
                echo "<form method='post' class='excluir-horario'>";
                echo "<h2>Deseja mesmo excluir este horario livre?</h2>";
                echo "<section style='max-height: 200px'>";
                $sql = "SELECT data, ID_aluno FROM tbalunoprofessor 
                            WHERE ocorreu = 'aindaN'
                            AND ID_prof = ". $_SESSION['ID_usuario'] ." 
                            AND ID_hor_livre = ". $_POST['ID_horario_liv'] ." 
                            AND deletado = false";
                $pega_infos = mysqli_query($conexao, $sql);
                if($pega_infos) {
                    if(mysqli_num_rows($pega_infos) > 0) {
                        echo "<h3>Estes atendimentos serão cancelados:</h3>";
                        while($recebe_infos = mysqli_fetch_array($pega_infos)) {
                            $sql = "SELECT nome FROM tbusuarios 
                                WHERE ID_usuario = ".$recebe_infos[1]." ";
                            $pega_user = mysqli_query($conexao, $sql);
                            if($pega_user) {
                                while($recebe_user = mysqli_fetch_array($pega_user)) {
                                    
                                    echo "<small>Em " . $recebe_infos[0] . " Com " . $recebe_user[0] . "</small>||";

                                }
                            }
                        }
                    } else {
                        echo "<h4>Você não tem atendimentos marcados para este horário.</h4>";
                    }
                }
                echo "</section>";
                echo "<input type='hidden' name='ID_horario_livre' value='".$_POST['ID_horario_liv']."'>";
                echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
                echo "<button type='submit' class='excluir duplo' name='excluir-hor' value='sim'>Excluir</button>";
                echo "</form>";
                echo "</section>";
            }
        ?>

<!-- EXCLUI HORARIO LIVRE -->
    <?php
        if (isset($_POST['excluir-hor'])) {
            $sql = "SELECT ID_hor_livre FROM tbhor_livres 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario'];
            $pegaHor = mysqli_query($conexao, $sql);
            $linhas = mysqli_num_rows($pegaHor);
            // se tiver mais de um horario livre cadastrado, pode excluir, se não mostra a mensagem que não pode ter 0 horarios livres
            if ($linhas > 1) {
                // exclui horario livre
                $sql = "UPDATE tbhor_livres
                        SET deletado = true
                            WHERE ID_hor_livre = ".$_POST['ID_horario_livre'];
                //$sql = "DELETE FROM tbhor_livres WHERE ID_hor_livre = ".$_POST['ID_horario_livre'];
                $exclui_hor = mysqli_query($conexao, $sql);

                // exclui atendimentos que ainda não ocorreram
                // regra de negócio =-=-=-=-=-=-=-=-=--=
                $sql = "UPDATE tbalunoprofessor 
                        SET deletedo = true 
                            WHERE ID_hor_livre = ".$_POST['ID_horario_livre']." 
                            AND ocorreu = 'aindaN'";
                $deleta = mysqli_query($conexao, $sql);
                header('Refresh: 1');
            } else {     
                echo "<script> window.alert('Não pode ter 0 horarios livres'); </script>";
            }
        }
    ?>
</section>