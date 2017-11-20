<?php
//****SQL Injection****
foreach ($_POST as &$inp){
        $inp=str_replace("'","''",$inp);
        $inp=str_replace("<","&lt;",$inp);
}
foreach ($_GET as &$inp){
        $inp=str_replace("<","&lt;",$inp);
}
foreach ($_REQUEST as &$inp){
        $inp=str_replace("<","&lt;",$inp);
}
?>

