<?php
class usuario extends samba{
  public $local="/opt/lampp/htdocs/montahd";
  public $array_usr=[
    'usuario',
    'senha',
    'id_usuario',
    'id_grupo',
    'dados',
    'dir_usr',
    'dir_login'
  ];

  public $array_grup=[
    'grupo',
    'senha',
    'id_grupo',
    'usuarios'
  ];

  public $novo_usuario=[
    'login'=>false,
    'senha'=>false,
    'nome'=>false
  ];
  public $lista_usr=[];
  public $lista_grup=[];

  public function adiciona_log($login,$senha,$cript=true){
    if($this->busca_log($login)==false){
      if($cript==true){
        $senha=md5($senha);
      }
      shell_exec('echo "'.$login.':'.$senha.'" >> '.$this->local.'/dados/log');
    }//if
  }//metodo

  public function busca_log($login){
    $v=shell_exec('sudo grep "'.$login.':" '.$this->local.'/dados/log');
    if($v!=''){
      return $v;
    }else{
      return false;
    }//else
  }//metodo

  public function remove_log($login){
    //echo shell_exec('cat '.$this->local.'/dados/log');//abre arquivo
    $exe=
    'grep -Riv "'.$login.':" '.$this->local.'/dados/log > '.$this->local.'/temp/temp_log.txt;'.
    'cat '.$this->local.'/temp/temp_log.txt > '.$this->local.'/dados/log;';
    shell_exec($exe);
  }//metodo

  public function edita_log($login=false,$senha=false,$login_novo=false){
    $log=$this->busca_log($login);
    if($log!=false){
      $log=explode(':',$log);
    }
    $this->remove_log($login);

    if($login!=false && $senha!=false){
      $cript=true;
    }else{
      $senha= $log[1];
      $cript= false;
    }//else
    if($login_novo!=false){
      $login=$login_novo;
    }//if


    $this->adiciona_log($login,$senha,$cript);


  }//metodo

  public function lista_usuario($login=false){
    if($login!=false){
      $v=shell_exec('sudo grep "'.$login.':" /etc/passwd');
    }else{
      $v= shell_exec("sudo less /etc/passwd");
    }

    $v=explode("\n",$v);

    $tot=count($v)-1;
    $lista_usr=[];

    for($x=0;$x<$tot;$x++){
      $li=explode(':',$v[$x]);
      $tot2=count($li);
      for($y=0;$y<$tot2;$y++){
        if($this->array_usr[$y]=='dados'){
          $li[$y]=explode(',',$li[$y]);
          $lista_usr[$x][$this->array_usr[$y]]=$li[$y];
        }else {
          $lista_usr[$x][ $this->array_usr[$y] ]=$li[$y];
          if($y==$tot2-1){
            $lista_usr[$x]['grupos'] = $this->busca_grupos( $lista_usr[$x]['usuario'] );
          }//if

        }//else

      }//for
    }//for
    //print_r($this->lista_usr);
    if($login==false){
      $this->lista_usr=$lista_usr;
    }else{
      $lista_usr=$lista_usr[0];
    }
    return $lista_usr;
  }//metodo

  public function lista_grupo($grupo=false){
    if($grupo!=false){
      $v= shell_exec('sudo grep "'.$grupo.':" /etc/group');
    }else{
      $v= shell_exec("sudo cat /etc/group");
    }//else
    $v=explode("\n",$v);

    $tot=count($v)-1;

    for($x=0;$x<$tot;$x++){
      $li=explode(':',$v[$x]);
      $tot2=count($li);
      for($y=0;$y<$tot2;$y++){
        if($this->array_grup[$y]=='usuarios'){
          $li[$y]=explode(',',$li[$y]);
          $lista_grup[$x][$this->array_grup[$y]]=$li[$y];
        }else {
          $lista_grup[$x][$this->array_grup[$y]]=$li[$y];
        }

      }//for
    }//for
    //print_r($this->lista_grup);
    if($grupo==false){
      $this->lista_grup=$lista_grup;
    }else{
      $lista_grup=$lista_grup[0];
    }
    return $lista_grup;
  }//metodo

  public function busca_grupos($nome){
    $v= shell_exec("sudo id -nG ".$nome);
    @$v=explode(' ',$v);
    //print_r($r);
    return $v;
  }//metodo

  public function cadastra_usuario(){
    $n=$this->novo_usuario;
    $exe=
    'sudo useradd -g users -d /home/'.$n['login'].' -m -s /bin/bash '.$n['login'].';'.
    'sudo -H -u root bash -c "sudo echo '.$n['login'].':'.$n['senha'].' | chpasswd";'.
    'sudo usermod -c "'.$n['nome'].'" '.$n['login'].";".
    '(echo '.$n['senha'].'; echo '.$n['senha'].') | sudo smbpasswd -s -a '.$n['login'].';';
    shell_exec($exe);

    $this->adiciona_log($n['login'],$n['senha']);

    //$this->adiciona_log($n['login'],$n['senha']);
  }//metodo

  public function remove_usuario($login){
    $exe=
    'sudo userdel -r '.$login.';';
    shell_exec($exe);
    $this->remove_log($login);
  }//metodo

  public function edita_usuario($usr_an){
    $n=$this->novo_usuario;
    $exe=
    'sudo usermod -c "'.$n['nome'].'" '.$usr_an.';'.
    'sudo usermod -l '.$n['login'].' '.$usr_an.';'.
    'sudo mv /home/'.$usr_an.' /home/'.$n['login'].';'.
    'sudo usermod -d /home/'.$n['login'].' '.$n['login'].';'.
    'sudo userdel -r '.$usr_an.';';
    shell_exec($exe);
    $this->edita_log($usr_an,false,$n['login']);
  }//metodo

  public function define_senha($login,$senha){
    $exe=
    'sudo -H -u root bash -c "sudo echo '.$login.':'.$senha.' | chpasswd";'.
    '(echo '.$senha.'; echo '.$senha.') | sudo smbpasswd -s -a '.$login.';';
    shell_exec($exe);
    $this->edita_log($login,$senha);
  }//metodo

  public function login($login,$senha){
    //$exe=
    //'(echo 1234) | ssh '.$login.'@127.0.0.1';
    //' echo "%s\n" "$1234" | sudo -s ssh teste@127.0.0.1';
    //'echo -e "1234\n" | sudo -S login teste 1>&2';
    //'sudo -H -u root bash -c "sudo echo '.$login.':'.$senha.' | chpasswd";'.
    //'(echo '.$senha.') | sudo -s ssh teste@127.0.0.1;';

    //local da senha: /etc/shadow
    //'echo "'.$senha.'" | htpasswd -c -i '.$this->$local.'/temp/password '.$login.';';
    //shell_exec($exe);//armazena

    //echo $senha_sis=shell_exec('sudo grep "'.$login.'" /etc/shadow');
    //
    $v=shell_exec('sudo grep "'.$login.':'.md5($senha).'" '.$this->local.'/dados/log');
    if($v!=''){
      //$this->
      return $this->lista_usuario();
    }else{
      return false;
    }//else
  }//metodo

  public function cadastra_grupo($grupo){
    shell_exec('sudo addgroup '.$grupo);
  }//metodo

  public function remove_grupo($grupo){
    shell_exec('sudo groupdel '.$grupo);
  }//metodo

  public function edita_grupo($grupo_an,$grupo_no){
    shell_exec('sudo groupmod -n '.$grupo_no.' '.$grupo_an);
  }//metodo

  public function adicionando_usuario_grupo($usuario,$grupo){
    shell_exec('sudo usermod -a -G '.$grupo.' '.$usuario);
  }//metodo

  public function remove_usuario_grupo($usuario,$grupo){
    shell_exec('sudo gpasswd -d '.$usuario.' '.$grupo);
  }
}



?>
