<?php ob_start(); ?>
<!-- FORM PARA CADASTRAR DISCIPLINAS -->
<section class="cadastrar-disciplinas">
    
    <section class="profes-cadast fora">
        <form method="post" class='cad-disc centro'>
        <h1 class='cadas-disc'>Cadastrar disciplinas</h1>
            <label><i class="fas fas2 fa-book"></i>
            <input 
                type="text" 
                name="nome_disc" 
                class='nome-disc longo' 
                placeholder="Nome da disciplina"
                required
            ></label>
            <h4><r class='email a2'>Professores que podem atuar nesta disciplina: </r></h4>
            <!-- LISTA OS PROFESSORES QUE EXISTEM E FORAM CONFIRMADOS -->
            <?php
                include('bd/conexao.php');
                $sql = "SELECT nome, email, ID_usuario FROM tbusuarios 
                            WHERE deletado = false 
                            AND prof_confirmado = 'sim' 
                            AND verificado = 'sim'";
                $pega_prof = mysqli_query($conexao, $sql);
                if ($pega_prof) {
                    if(mysqli_num_rows($pega_prof) < 1) {
                        echo "<script> alert('Não há professores cadastrados até o momento. Impossível cadastrar.')</script>";
                    } else {
                        while ($recebe_prof = mysqli_fetch_array($pega_prof)) {
                            
                            echo "<label class='label-profes'><input type='checkbox' class='profes' name='ID-prof[]' value='".$recebe_prof[2]."'> ".$recebe_prof[0]."</label>";

                        }
                    }
                    
                }
            ?>
            <button type="submit" class='envia'>Cadastrar</button>
        </form>
    </section>
</section>

<!-- RECEBE INFORMACOES PARA CADASTRAR DISCIPLINA -->
    <?php
        if (isset($_POST['nome_disc'])) {
            if(!isset($_POST['ID-prof'])) {
                echo "<script> alert('Não foi possível cadastrar a disciplina. Professor não selecionado');</script>";
            } else {
                // ve se não há outra disciplina com o mesmo nome
                $sql = "SELECT nome FROM tbdisciplinas 
                            WHERE deletado = false 
                            AND nome = '".$_POST['nome_disc']."'";
                $existe_disc = mysqli_query($conexao, $sql);
                if ($existe_disc) {
                    if (mysqli_num_rows($existe_disc) == 0) {
                        // Cadastra a disciplina
                        $sql = "INSERT INTO tbdisciplinas (nome, deletado) VALUES ('".$_POST['nome_disc']."', false)";
                        $insere = mysqli_query($conexao, $sql);

                        // Pega informacoes e cadastra professor na turma
                        foreach ($_POST['ID-prof'] as $key => $value) {
                            // Pega o ID da disciplina
                            $sql = "SELECT ID_disc FROM tbdisciplinas 
                                        WHERE deletado = false 
                                        AND nome = '".$_POST['nome_disc']."'";
                            $ID_discs = mysqli_query($conexao, $sql);
                            if ($ID_discs) {
                                while ($recebeID_discs = mysqli_fetch_array($ID_discs)) {
                                    // Cria uma relacao entre professor e disciplina
                                    //$sql = "INSERT INTO tbturmadisciplinaprofessor (ID_prof, ID_disc, deletado) VALUES ($value, ".$recebeID_discs[0].", false)";
                                    $sql = "INSERT INTO tbdisciplina_professor (ID_professor, ID_disciplina, deletado) VALUES ($value, ".$recebeID_discs[0].", false)";
                                    mysqli_query($conexao, $sql);
                                    echo "<script> window.alert('Disciplina cadastrada com sucesso!') </script>";
                                    header('Refresh: 0');
                                }
                            }
                        }
                    }else {
                        echo "<script> window.alert('Já existe disciplina cadastrada com este nome.') </script>";
                    }
                }
            }
        }
    ?>