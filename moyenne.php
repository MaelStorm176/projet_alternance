<?php
include_once ("class/_db.class.php");
if (isset($_POST["id_etudiant"], $_POST["select_matiere"], $_POST["note_input"], $_POST["note_input_max"])){
    $oDB = new Mysql();
    $sql = "INSERT INTO note (valeur, id_matiere, valeur_max, id_etudiant) VALUES (".$_POST["note_input"].",".$_POST["select_matiere"].", ".$_POST["note_input_max"].", ".$_POST["id_etudiant"].") ON DUPLICATE KEY UPDATE (valeur=".$_POST["note_input"].", valeur_max = ".$_POST["note_input_max"].")";
    $oDB->ExecuteSQL($sql);
    $sql = "";
    $oDB->Close();
}

?>