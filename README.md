<h1>phpsamba</h1>
<h2>Biblioteca php para manipulação de unidades de disco, diretórios, samba e usuários</h1>
<h3>Requisitos:</h3>
<ul>
  <li>Ubuntu server 20.04 lts ou posterior</li>
  <li>Pilha lampp junto com php7.5 e apache 2 ou posterior</li>
  <li>Permissão sudo para o usuario php</li>
  <li>Pacote samba instalado</li>
</ul>

<h3>Como implementar no projeto:</h3>
<ul>
  <li><h4>Incluir na pagina php:</h4>
    <ul>
      <li>include 'class/biblioteca.php';</li>
      <li>$u= new biblioteca(); </li>
      <li>$u=$u->blib;</li>
      <li>$u->local='/var/www/nome_pasta_projeto'; "diretorio da biblioteca"</li>
    </ul>
  </li>

</ul>

<h3>Métodos para unidade:</h3>
<ul>
  <li> $u->monta_unidade('nome_pasta_unidade','sda1'); </li>
  <li> $u->desmonta_unidade('nome_pasta_a_desmontar'); </li>
  <li> $u->edita_hd('nome_pasta_atual','sda1','novo_nome_pasta','sb5'); </li>
</ul>

<h3>Métodos para diretorio:</h3>
<ul>
  <li> $u->remove_pasta('pasta_a_remover'); </li>
  <li> print_r( $u->lista_pastas('/diretorio_a_listar') ); </li>
  <li> $u->novo_diretorio('/diretorio/nome_do_novo_diretorio','usuario_dono','grupo_dono'); </li>
  <li> $u->remove_diretorio('/diretorio_a_remover'); </li>
  <li> $u->edita_diretorio('/diretorio_atua','/novo_diretorio'); </li>
</ul>

<h3>Métodos para o samba:</h3>
<ul>
  <li> print_r( $u->procura_samba('/media/HD_01/php_server') ); </li>
  <li> public $novo_smb=[
    <ul>
      <li>['nome_smb','nome de compartilhamento'],</li>
      <li>['comment','comentario'],</li>
      <li>['path','diretorio_da_pasta'],</li>
      <li>['public','no'],</li>
      <li>['only guest','no'],</li>
      <li>['valid users','@grupo_ue_pode_acessar,usuario_que_pode_acessar'],</li>
      <li>['writable','yes'],</li>
      <li>['printable','no'],</li>
      <li>['SECURITY',false]</li>
    </ul>
  ];</li>
  <li> $u->adiciona_samba(); </li>

  <li> $u->remove_samba('/diretorio_a_remover_do_samba'); </li>
  <li> $u->edita_samba('/diretorio_a_editar_do_samba'); </li>

</ul>

<h3>Métodos para usuário:</h3>
<ul>
  <li> print_r($u->lista_usuario()); </li>
  <li> print_r($u->lista_grupo()); </li>

  <li> $u->novo_usuario=[
    <ul>
      <li>'nome'=>  'Nome usuario',</li>
      <li>'login'=> 'usuario',</li>
      <li>'senha'=> '1234'</li>
    </ul>
  ];
  </li>
  <li> $u->cadastra_usuario(); </li>

  <li> $u->remove_usuario('usuario_a_remover'); </li>
  <li> $u->edita_usuario('usuario_a_editar'); </li>
  <li> $u->define_senha('usuario_a_definir_senha','1234'); </li>
  <li> $u->login('usuario_a_autenticar','1234'); </li>


  <li> $u->cadastra_grupo('novo_grupo'); </li>
  <li> $u->edita_grupo('grupo_a_editar','novo_nome_do_grupo'); </li>
  <li> $u->remove_grupo('grupo_a_remover'); </li>
  <li> $u->adiciona_usuario_grupo('usuario_a_adicionar','grupo_a_ser_adicionado'); </li>
  <li> $u->remove_usuario_grupo('usuario_a_retirar_do_grupo','grupo_do_usuario'); </li>

</ul>
