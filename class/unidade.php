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


  public function reagrupa($uni,$nome=false){
    $r=[];

    $uni=str_replace(' ','<>',$uni);
    $uni=explode('><',$uni);
    $uni=implode('',$uni);

    $uni=explode('<>',$uni);
    for($i=0;$i<count($uni);$i++){

          $r[ $this->nome_coluna[$i] ]=$uni[$i];
    }//for
    $r[ 'part' ]=[];
    return $r;
  }

  public function lista_bash($txt=false)
  {
    $re=[];
    if($txt==false){
      $uni= shell_exec('lsblk');
    }else{
      $uni=$txt;
    }

    $uni=explode("\n",$uni);

    for($i=1;$i<count($uni)-1;$i++){
      $r[]= $this->reagrupa($uni[$i]);

    }
    return $r;

  }//metodo
  
  public function lista_unidades(){
    $d=$this->lista_bash();
    $r=[];
    $num_pai=0;
    for($i=0;$i<count($d);$i++){
      $sub=str_replace('└─','-',$d[$i]['nome']);
      $sub=str_replace('├─','-',$sub);


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

    return  array_values(array_filter($d));
  }//metodo

  public function trata_nome($n){
    //return $nome;
    @$n=explode('/',$n);
    @$n=array_reverse($n);
    return @$n[0];
  }

  public function html_unidades(){
    $d=$this->lista_unidades();

    $html='';

    for($x=0;$x<count($d);$x++){
      if(
        $d[$x]['nome'][0]=='s' &&
        $d[$x]['nome'][1]=='d'
      ){
        $html=$html.
        '<div class="unidade">'.
        ' <div class="nome_unidade">'.
        $d[$x]['nome'].' ( '.
        $d[$x]['tamanho'].
        ' )</div>';


        $p=$d[$x]['part'];
        $quan=count($p);
        @$porcento=100/ $quan;

        for($y=0;$y<$quan;$y++){

            $html=$html.
            '<div class="particao" style="width: '.$porcento.'%;">'.
            '<div class="nome_particao"><div class="alinhamento">'.
            $p[$y]['nome'].' ( '.
            $this->trata_nome($p[$y]['dir']).' )<br/>'.
            '<div class="barra_inteira"><div class="barra_uso" style="width: '.$this->uso_disco($p[$y]['nome']).';"></div></div>'.
            $this->uso_disco($p[$y]['nome']).' de '.$p[$y]['tamanho'].'<br/>'.
            '</div>'.
            '</div>'.
            '</div>';
        }//for
        $html=$html.
        '<div class="rodape"></div>'.
        '</div>';

      }//if
    }//for

    return $html;

  }

  public function uso_disco($n){
    $n='df -a /dev/'.$n;
    $n=shell_exec($n);
    $n=explode(' ',$n);
    $n=array_reverse($n);
    return $n[1];
  }


}

  ?>
