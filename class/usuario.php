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
    'login'=>'nome_login',
    'senha'=>'',
    'nome'=>'Nome Usuario'
  ];
  public $lista_usr=[];
  public $lista_grup=[];

  public function lista_usuario(){
    $v= shell_exec("sudo less /etc/passwd");
    $v=explode("\n",$v);

    $tot=count($v)-1;

    for($x=0;$x<$tot;$x++){
      $li=explode(':',$v[$x]);
      $tot2=count($li);
      for($y=0;$y<$tot2;$y++){
        if($this->array_usr[$y]=='dados'){
          $li[$y]=explode(',',$li[$y]);
          $this->lista_usr[$x][$this->array_usr[$y]]=$li[$y];
        }else {
          $this->lista_usr[$x][ $this->array_usr[$y] ]=$li[$y];
          if($y==$tot2-1){
            $this->lista_usr[$x]['grupos'] = $this->busca_grupos( $this->lista_usr[$x]['usuario'] );
          }//if

        }//else

      }
    }//for

    //print_r($this->lista_usr);
  }//metodo

  public function lista_grupo(){
    //echo shell_exec('sudo cat /etc/group');
    $v= shell_exec("sudo cat /etc/group");
    $v=explode("\n",$v);

    $tot=count($v)-1;

    for($x=0;$x<$tot;$x++){
      $li=explode(':',$v[$x]);
      $tot2=count($li);
      for($y=0;$y<$tot2;$y++){
        if($this->array_grup[$y]=='usr'){
          $li[$y]=explode(',',$li[$y]);
          $this->lista_grup[$x][$this->array_grup[$y]]=$li[$y];
        }else {
          $this->lista_grup[$x][$this->array_grup[$y]]=$li[$y];
        }

      }//for
    }//for
    //print_r($this->lista_grup);
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
  }//metodo

  public function remove_usuario($login){
    $exe=
    'sudo userdel -r '.$login.';';
    shell_exec($exe);
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
  }//metodo

  public function define_senha($login,$senha){
    $exe=
    'sudo -H -u root bash -c "sudo echo '.$login.':'.$senha.' | chpasswd";'.
    '(echo '.$senha.'; echo '.$senha.') | sudo smbpasswd -s -a '.$login.';';
    shell_exec($exe);
  }//metodo

  public function login($login,$senha){
    $exe=
    '(echo 1234) | ssh '.$login.'@127.0.0.1';
    echo shell_exec($exe);
  }//metodo





}



?>
