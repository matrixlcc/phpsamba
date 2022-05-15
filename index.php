<?php
  //montando ou desmontando unidade de backup
  function monta_unidade(){
    echo shell_exec('cd /home/administrator/samba&&sudo bash ./monta_unidade.sh');
  }

  function desmonta_unidade(){
    echo shell_exec('cd /home/administrator/samba&&sudo bash ./desmonta_unidade.sh');
  }

  function verifica_unidade(){
    return exec('sudo cat /home/administrator/samba/estado.txt');
  }
  @$estado=$_GET['bnt'];
  if($estado=="ativa"){
    monta_unidade();
  }else if($estado=='inativa'){
    desmonta_unidade();
  }
  //unidades
  include 'class/unidade.php';
  include 'class/diretorio.php';
  include 'class/samba.php';
  $u= new samba();
  //print_r( $u->html_unidades() );

?>




<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
  <head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="x-ua-compatible" content="IE=Edge;chrome=1" />
   <title>Unidade de backup</title>
    <style type="text/css">
      *{
        font-style: Arial;
      }
      .desmontado{
        color: #c00;
      }
      .montado{
        color: #151cda;
      }
      .bnt_ativa{
        font-size: 16px;
        padding: 10px;
        border:solid 1px rgb(34, 36, 34);
        color: rgb(22, 22, 18);
        text-decoration: none;
      }

      .corpo{
        width: 450px;
        margin: 0 auto;
        text-align: center;
      }

      .img_unidade{
        width: 50px;
      }

      .unidades{
        text-align: left;
      }
      .rodape{
        clear: both;
      }

      /*dentro unidades*/

      .unidade{
        background-color: #EEE;
        padding: 10px 0;
        margin: 10px 0;
        border-radius: 3px;
      }
      .particao{
        float: left;
      }
      .nome_unidade{
        font-size: 14px;
        color: #333;
        padding: 5px;
      }
      .nome_particao{
        margin:0 5px;
        background-color: rgb(163, 222, 230);

        padding: 10px 0;
        color: rgb(10, 104, 117);
        border-radius: 3px;
      }
      .barra_uso{
        background-color: rgb(25, 140, 135);
        height: 3px;
        border-radius:10px;
      }
      .barra_inteira{
        background-color: #EEE;
        padding: 2px;
        margin: 3px 0;
      }
      .alinhamento{
        padding: 0 5px;
      }

    </style>
  </head>
  <body>
    <div class="corpo">
    <?php
      $estado=verifica_unidade();
      if($estado=="desmontado"){
        echo '<img class="img_unidade" src="unidade.png"/>
        <h1 class="desmontado">HD de Backup Desmontado</h1>
        <a class="bnt_ativa" href="?bnt=ativa">Montar unidade</a>';
      }else{
        echo '<img class="img_unidade" src="unidade.png"/>
        <h1 class="montado">HD de Backup Montado</h1>
        <a class="bnt_ativa" href="?bnt=inativa">Desmontar unidade</a>';
      }
      //montagem de hd
    ?>
      <div class="unidades" style="">
        <?php echo $u->html_unidades();
        //echo $u->monta_unidade('pasta_teste','sda1');
        //$u->desmonta_unidade('pasta_teste');
        //echo $u->edita_hd('pasta_teste','sda1','nova_unidade','sb5');
        //$u->remove_pasta('nova_pasta');
        //print_r( $u->lista_pastas('/media/HD_01/backup/documentos/') );
        //$u->novo_diretorio('/media/hd_montado','administrator','administrator');
        //$u->remove_diretorio('/media/pasta_mkdir');
        //$u->edita_diretorio('/media/pasta_teste_new','/media/pasta_teste');
        //$u->abre_doc();
        //print_r( $u->procura_samba('/media/HD_01/php_server') );
        //$u->adiciona_samba();
        //$u->gera_arquivo();
        //$u->remove_samba('/media/HD_01/php_server');
        //$u->edita_samba('/media/HD_01/php_server');
        //print_r( $u->doc_samba );
        //$u->monta_script();

        ?>
      </div><!--unidades-->
    </div>

  </body>
</html>
