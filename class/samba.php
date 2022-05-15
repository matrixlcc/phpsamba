<?php

class samba extends diretorio{

  public $local="/opt/lampp/htdocs/montahd";
  public $doc_samba=[];
  public $doc_inicio=[];

  public $novo_smb=[
    ['nome_smb',false],
    ['comment',false],
    ['path',false],
    ['public','no'],
    ['only guest','no'],
    ['valid users',false],
    ['writable','yes'],
    ['printable','no'],
    ['SECURITY',false]
  ];

  public function verificador($txt,$v=['[',']'] ){
    @$com=str_replace($v,['',''],$txt);
    if($com!=$txt){
      return true;
    }else{
      return false;
    }
  }

  public function trata_conteudo($v){
    $v=str_replace(' ','<>',$v);
    $v=explode('><',$v);
    $v=implode('',$v);

    if($v[0].$v[1]=='<>'){
      $v=substr($v, 2);
    }

    $tot=strlen($v);
    $nf0=$tot-1;
    $nf1=$tot-2;
    if($v[$nf1].$v[$nf0]=='<>'){
      $v=substr($v, 0,-2);
    }

    //echo "\n".$v;
    $v=preg_replace('/\s/','',$v);
    //$v=str_replace("^M",'',$v);
    $v=str_replace('<>',' ',$v);
    return $v;
  }//metodo

  public function abre_doc(){
    $this->doc_samba=[];
    $this->doc_inicio=[];
    $v= shell_exec("sudo cat /etc/samba/smb.conf");

    $v=explode("\n",$v);
    $tot=count($v);
    $cont_pasta=-1;
    for($x=0;$x<$tot;$x++){
      //echo "\nlinha:".$x;
      if( $this->verificador($v[$x],['[',']']) ){
        //echo 'pasta';
        $con=str_replace(['[',']'],['',''],$v[$x]);
        $cont_pasta++;
        $this->doc_samba[$cont_pasta]['nome_smb']=$this->trata_conteudo($con);
      }else if( $this->verificador($v[$x],['=']) && $cont_pasta>=0 ){
        //dados pasta
        //echo 'conteudo';
        $con=explode('=',$v[$x]);

        $v0=$this->trata_conteudo($con[0]);
        $v1=$this->trata_conteudo($con[1]);

        //echo $v0.'='.$v1;

        $this->doc_samba[$cont_pasta]['val'][]=[ $v0, $v1 ];
        $this->doc_samba[$cont_pasta][$v0]=$v1;

      }else if($v[$x]!=""){
        $v[$x]=str_replace(" ","<>",$v[$x]);
        $v[$x]=preg_replace('/\s/','',$v[$x]);//tira todo espaco
        $v[$x]=str_replace("<>"," ",$v[$x]);
        $this->doc_inicio[]=$v[$x];
      }

    }//for
    $this->doc_inicio=array_values(array_filter($this->doc_inicio));
    //$this->doc_inicio;
    //$this->doc_samba);
    //echo "\nfim metodo";
  }//metodo

  public function procura_samba($dir){
    //$this->abre_doc();
    $v=$this->doc_samba;
    //print_r($v);
    for($x=0;$x<count($v);$x++){
      //$c=$v[$x]['val'];
      //for($y=0;$y<count($c);$y++){
        //print_r($c[$y]);

      //}//for
      //echo "\n(".$dir."-".$v[$x]['path'].")";

      if(@$v[$x]['path']==$dir){
        //echo "\n(".$dir."-".$v[$x]['path'].")";
        return [$x,$v[$x]];
      }//if

    }//for

  }//metodo

  public function gera_arquivo(){
    $smb=$this->doc_samba;
    $ini=$this->doc_inicio;
    $inicio=implode("\n",$ini);

    $corpo='';
    $tot=count($smb);
    for($x=0;$x<$tot;$x++){
      $corpo=$corpo."\n[".$smb[$x]['nome_smb'].']';
      //$corpo=$corpo.implode("\n",$smb[$x]['val']);
      for($y=0;$y<@count($smb[$x]['val']);$y++){
        $corpo=$corpo."\n".implode(" = ",$smb[$x]['val'][$y]);
      }//for
      $corpo=$corpo."\n";
    }//for

    //echo $corpo;
    $fp = fopen("temp/temp_samba.txt","w");
    fwrite($fp,$corpo);
    fclose($fp);

    shell_exec('cd '.$this->local.'/script&&sudo bash ./script_samba.sh');
  }//metodo

  public function adiciona_samba(){
    $this->abre_doc();
    $v=$this->novo_smb;
    $tot=count($v);
    $tot_samba=count($this->doc_samba);
    for($x=0;$x<$tot;$x++){
      //contador
      $this->doc_samba[$tot_samba][ $v[$x][0] ]=$v[$x][1];
      //monta
      if($v[$x][0]!='nome_smb'){
        $this->doc_samba[$tot_samba]['val'][  ]=[ $v[$x][0],$v[$x][1] ];
      }

    }//for
    //print_r($this->doc_samba);
    $this->gera_arquivo();
  }//metodo

  public function remove_samba($dir){
    $this->abre_doc();
    //print_r($this->doc_samba);
    $dao=$this->procura_samba($dir);
    //print_r($dao);
    $this->doc_samba[$dao[0]]=false;
    $this->doc_samba=array_values(array_filter($this->doc_samba));
    //print_r($this->doc_samba);
    $this->gera_arquivo();
  }

  public function edita_samba($dir){
    $this->abre_doc();
    //print_r($this->doc_samba);
    $dao=$this->procura_samba($dir);

    $v=$this->novo_smb;
    $tot=count($v);
    //$tot_samba=count($this->doc_samba);
    $this->doc_samba[ $dao[0] ]=[];//limpa valor
    for($x=0;$x<$tot;$x++){
      //contador
      $this->doc_samba[ $dao[0] ][ $v[$x][0] ]=$v[$x][1];
      //monta
      if($v[$x][0]!='nome_smb'){
        $this->doc_samba[ $dao[0] ]['val'][  ]=[ $v[$x][0],$v[$x][1] ];
      }

    }//for

    $this->gera_arquivo();
  }





}//class



?>
