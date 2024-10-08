<?php
class unidade{
  public $nome_coluna=[
    'nome',
    'data',
    'RM',
    'tamanho',
    'RO',
    'tipo',
    'dir'
  ];

  public $arquivo;

  public $local="/opt/lampp/htdocs/montahd";

  public function monta_script(){
    $script_samba=
    "#!/bin/bash".
    "\nsudo cat ".$this->local."/temp/temp_samba.txt > /teste.txt;".
    "\n#sudo cat ".$this->local."/temp/temp_samba.txt > /etc/samba/smb.conf;".
    "\n#sudo systemctl restart smbd;";

    $fp = fopen("script/script_samba.sh","w");
    fwrite($fp,$script_samba);
    fclose($fp);

    $script_unidade=
    "#!/bin/bash".
    "\nsudo cat ".$this->local."/temp/temp_unidade.txt > /teste.txt;".
    "\n#sudo cat ".$this->local."/temp/temp_unidade.txt > /etc/fstab;".
    "\n#sudo mount -a;";

    $fp = fopen("script/script_unidade.sh","w");
    fwrite($fp,$script_unidade);
    fclose($fp);
  }

  public function reagrupa($uni,$nome=false){
    //echo "linha";
    $r=[];

    $uni=str_replace(' ','<>',$uni);
    $uni=explode('><',$uni);
    $uni=implode('',$uni);

    $uni=explode('<>',$uni);
    for($i=0;$i<count($uni);$i++){
        //if(@$uni[$i]==true){
          $r[ $this->nome_coluna[$i] ]=$uni[$i];
        //}
    }//for
    $r[ 'part' ]=[];

    return $r;
  }

  public function lista_bash($txt=false){
    $re=[];
    if($txt==false){
      $uni= shell_exec('lsblk');
    }else{
      $uni=$txt;
    }

    $uni=explode("\n",$uni);

    for($i=1;$i<count($uni)-1;$i++){
      //teste
      $r[]= $this->reagrupa($uni[$i]);

    }
    return $r;

  }//metodo

  public function lista_unidades(){
    $d=$this->lista_bash();
    $r=[];
    $num_pai=0;
    for($i=0;$i<count($d);$i++){
      $d=$this->lista_bash();
    //print_r($d);
    $r=[];
    $num_pai=0;
    for($i=0;$i<count($d);$i++){
      $sub=$d[$i]['nome'];

      $car_remo=[
        '|-',
        '`-',
        '└─',
        '├─'
      ];
      $sub=str_replace($car_remo,'-',$sub);


      if($sub[0]=='-'){
        //arruma nome

        //remove caracter -
        $d[$i]['nome']=str_replace('-','',$sub);

        //armazena sub
        $d[$num_pai]['part'][]=$d[$i];
        $d[$i]=false;

      }else{
        //armazena pai
        $num_pai=$i;
      }


    }//for

    return  array_values(array_filter($d));//remove null e reorganiza indice
  }//metodo

  public function trata_nome($n){
    //return $nome;
    @$n=explode('/',$n);
    @$n=array_reverse($n);
    return @$n[0];
  }


  public function uso_disco($n){
    $n='df -a /dev/'.$n;
    $n=shell_exec($n);
    $n=explode(' ',$n);
    $n=array_reverse($n);
    return $n[1];
  }

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
    $com="sudo mkdir /media/".$nome."&&sudo chmod -R 775 /media/".$nome;
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
    $fp = fopen("temp/temp_unidade.txt","w");
    fwrite($fp,$arquivo);
    fclose($fp);

    shell_exec('cd '.$this->local.'/script&&sudo bash ./script_unidade.sh');
    //shell_exec('sudo cat /opt/lampp/htdocs/montahd/temp.txt > /teste.txt');

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


}
?>
