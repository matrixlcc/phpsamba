<?php
class diretorio extends unidade{
  public $array_dir=[
    'permicao',
    'num1',
    'usuario',
    'grupo',
    'num2',
    'mes',
    'dia',
    'ano'
  ];
  public $lista_dir=[[]];

  public function lista_pastas($dir='/'){
    $l=shell_exec('sudo ls -lt '.$dir);
    $l=explode("\n",$l);
    for($x=1;$x<count($l)-1;$x++){
      $v=$l[$x];

      $v=str_replace(' ','<>',$v);
      $v=explode('><',$v);
      $v=implode('',$v);

      //echo "--------------------------\n";
      $v=explode('<>',$v);
      for($y=0;$y<8;$y++){
        $this->lista_dir[$x-1][$this->array_dir[$y]]=$v[$y];
        //echo "\n".''.$v[$y];
        $v[$y]=false;
      }//for
      $v=@array_values(array_filter($v));
      //print_r($v);
      $v=implode(' ',$v);
      $this->lista_dir[$x-1]['nome']=$v;
    }//for
    return $this->lista_dir;
  }//metodo

  public function novo_diretorio($dir,$user,$grupo){
    shell_exec('sudo mkdir '.$dir);
    shell_exec('sudo chown '.$user.':'.$grupo.' '.$dir);
  }

  public function remove_diretorio($dir){
    shell_exec("sudo rm -r ".$dir);
  }

  public function edita_diretorio($dir_velho,$dir_novo){
    shell_exec("sudo mv ".$dir_velho." ".$dir_novo);
  }

  public function dono_diretorio($dir,$user,$grupo){
    shell_exec('sudo chown -c -R '.$user.':'.$grupo.' '.$dir);
  }



}//class


 ?>
