<?php
    //FUNÇÃO QUE VÊ SE O EMAIL DO USUARIO JA EXISTE NO BANCO
    function verificaEmail ($email) {
        include("bd/conexao.php");
        $sql = "SELECT * FROM tbusuarios WHERE deletado = false AND email LIKE '%$email%'";
        $consulta = mysqli_query($conexao, $sql);
        $num_registros = mysqli_num_rows($consulta);
        // Se não tem usuário cadastrado com este email
        if ($num_registros == 0) {
            // PODE CADASTRAR USUARIO
            return true; //true pode cadastrar
        } else {
            // mensagem de email ja existente no sistema
            return false; // false nao pode
        }
    }

    // FORMATA DATA EM dd/mm/aaaa
    function formatarData($data) {
        
        //$novaData = explode('-', $data);
        //$novaData = date('d/m/Y', mktime(0,0,0,$data[2],$data[1],$data[0]));
        $novaData = date("d/m/Y", strtotime("$data"));

        return $novaData;
    }
?>