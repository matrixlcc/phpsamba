<?php

class unidade_opr extends unidade{
  public $arquivo;
  public function abre_arquivo(){
    $arq= shell_exec('cat /etc/fstab');
    $arq=explode("\n",$arq);
    $bloco=[];
    $linhas=[];
    echo "\n";
    for($i=0;$i<count($arq);$i++){
      if($arq[$i]!=''){
        //armazena atual
        $linhas[]=$arq[$i];
        //echo "\n".$arq[$i];
      }else{
        //inicia novo arm
        //echo "\n novo bloco";
        $blocos[]=$linhas;
        $linhas=[];
      }
    }//for
    $re=array_values(array_filter($blocos));
    return $this->arquivo=$re;
  }//metodo

  public function remove_pasta($nome){
    $com="sudo rm -r /media/".$nome;
    shell_exec($com);
  }//metodo

  public function pasta_unidade($nome){
    $com="sudo mkdir /media/".$nome."&&sudo chmod -R 777 /media/".$nome;
    shell_exec($com);
  }//metodo

  public function aplica_bash(){
    @$txt=$this->arquivo;
    $arquivo='';
    for($x=0;$x<count($txt);$x++){
      $lin=$txt[$x];

      for($y=0;$y<count($lin);$y++){
        $arquivo=$arquivo.$lin[$y]."\n";
      }
      $arquivo=$arquivo."\n";
    }
    //echo $com="sudo echo 'teste_funi' > /opt/teste.txt";
    //echo shell_exec("sudo echo 'teste do shell' > /teste.txt");
    //shell_exec('sudo echo  "'.$arquivo.'" > /etc/fstab&&sudo mount -a');
    $fp = fopen("temp.txt","w");
    fwrite($fp,$arquivo);
    fclose($fp);

    shell_exec('cd /opt/lampp/htdocs/montahd/class&&sudo bash ./script.sh');

  }//metodo

  public function monta_unidade($nome,$hd,$edi=false){
    if($edi==false){
      $this->abre_arquivo();
    }//if
    $this->pasta_unidade($nome);
    $this->arquivo[]=[
      "/dev/".$hd." /media/".$nome." ntfs",
      "auto,rw,exec,users,dmask=000,fmask=111,nls=utf8 0 0"
    ];
    if($edi==false){
      $this->aplica_bash();
    }//if

    //print_r($this->arquivo);
  }//metodo

  public function desmonta_unidade($nome,$edi=false){
    //passando
    $txt=$this->arquivo;
    $tot=$txt;
    for($x=0;$x<$tot;$x++){
      //quebra
      $v=$txt[$x][0];
      $v=explode(' ',$v);
      $v=$v[1];
      $v=explode('/',$v);
      $v=array_reverse($v);
      $v=$v[0];
      if($v==$nome){
        //remove do texto
        $txt[$x]=false;
        //$x=$tot;
        //break;
      }//if
    }//for

    //$this->arquivo=false;
    $this->arquivo= @array_values(array_filter($txt));
    //print_r( $this->arquivo );
    if($edi==false){
      $this->aplica_bash();
      $this->remove_pasta($nome);
    }//if

  }//metodo

  public function edita_hd($nome,$hd,$nome_n,$hd_n){
    $this->desmonta_unidade($nome);
    $this->monta_unidade($nome_n,$hd_n);
  }//metodo

}//class


?>
