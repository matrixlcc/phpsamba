<?php
class biblioteca{
  public $blib;
  public function __construct(){
    include 'unidade.php';
    include 'diretorio.php';
    include 'samba.php';
    include 'usuario.php';
    $this->blib= new usuario();
  }

}
?>
