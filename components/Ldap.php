<?php

namespace wzg\ldap\components;
use yii;
/**
 * @Author   wangzhiguang
 * @DateTime 2018-04-10
 * ldap 认证、添加、编辑、删除
 */
class Ldap
{
    protected $m_lc = null;
    protected $m_strServerIp = '';
    protected $m_iServerPort = 0;
    protected $m_bIsAnonymous = false;
    protected $m_strBaseDn = '';
    protected $m_strManager = 'admin';
    protected $m_strPassword = '';

    protected function getConfig()
    {
        $ldap_config = \Yii::$app->params['ldap'];
        $this->m_strServerIp = $ldap_config['host'];
        $this->m_userDN = $ldap_config['userDN'];
        $this->m_iServerPort = 389;
        $this->m_bIsAnonymous = false;
        $this->m_strBaseDn = $ldap_config['baseDN'];
        $this->m_strPassword = $ldap_config['password'];
    }
    function __construct() { //构造方法
        $this->getConfig();
    }

    public function connect($username,$password)
    {
        if(null != $this->m_lc)
        {
            return $this->m_lc;
        }

        $this->m_lc = ldap_connect($this->m_strServerIp, $this->m_iServerPort);

        if(null != $this->m_lc)
        {
            if(!$this->m_bIsAnonymous)
            {
                ldap_set_option($this->m_lc, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($this->m_lc, LDAP_OPT_REFERRALS,0); 
                $username = $this->generateUserDn($username);
                $result = @ldap_bind($this->m_lc, $username, $password);

                if(!$result)
                {
                    return null;
                }
            }

        }
        return $this->m_lc;
    }

    public function close()
    {
        ldap_close($this->m_lc);
        $this->m_lc = null;
    }
    /**
     * login
     *
     * @param string $username
     * @return string
     */
    public function ldapLogin($username,$pass){
        $result = false;
        $username = strstr($username, '@', TRUE);
        if($this->connect($this->m_strManager,$this->m_strPassword) != null)
        {
            $r = @ldap_search($this->m_lc, $this->m_strBaseDn, 'uid=' . $username);
            if ($r) 
            {
                $res = @ldap_get_entries($this->m_lc, $r);
                if (!empty($res)) 
                {
                    ldap_set_option($this->m_lc, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($this->m_lc, LDAP_OPT_REFERRALS,0); 
                    if (@ldap_bind($this->m_lc, $res[0]['dn'], $pass) ) 
                    {
                        $result = true;
                    }
                }
            } 
        }
        $this->close();
        return $result;
    }
    protected function clean_user($user)
    {
        $user_array = [];
        if ($user['count'] == 0)
        {
            return $user_array;
        }
        unset($user['count']);
        $company = Yii::$app->params['ldap_company'];
        foreach ($user as $key => $val) {
            if(isset($val['mail'][0]) && isset($val['displayname'][0])){
                $str=str_replace('ou=','',$val['dn']); 
                $info_array = explode(',', $str);
                $user_array[$key]['company'] = isset($company[$info_array[2]]) ? $company[$info_array[2]] : '';
                $user_array[$key]['username'] = $val['displayname'][0]; 
                $user_array[$key]['email'] = $val['mail'][0];
                $user_array[$key]['title'] = isset($val['title'][0])  ? $val['title'][0] : '';
                $user_array[$key]['mobile'] = isset($val['telephonenumber'][0])  ? $val['telephonenumber'][0] : '';
                $user_array[$key]['dn'] = $val['dn'];
            }
        }
        return $user_array;
    }
    /**
     * 获取所有用户
     * @Param    参数描述
     * @DateTime 2019-05-07
     * @return   [type]       [description]
     */
    public function getList($username)
    {
       $username = $username ? '*'.$username.'*' : '*';
       $result = false;
       if($this->connect($this->m_strManager,$this->m_strPassword) != null)
       {
           $justthese = array('mail','displayname','title','telephonenumber','dn','uid');//选择要获取的用户属性
           $r = @ldap_search($this->m_lc, $this->m_strBaseDn, "(&(uid=*)(displayname=$username))",$justthese);
           if ($r) 
           {
               $result = @ldap_get_entries($this->m_lc, $r);
           }
       }
       $this->close();
       $result = $this->clean_user($result);
       return $result;
    }
    /**
     * 添加用户
     * @Param    参数描述
     * @DateTime 2019-05-17
     * @return   [type]       [description]
     */
    public function addUser($data)
    {
       if($this->connect($this->m_strManager,$this->m_strPassword) != null)
       {

           $company = $data['User']['company'];
           $depar = $data['User']['depar'];
           //检查节点
           $this->checkDn($company,$depar);
           $info["objectClass"][]="posixAccount";
           $info["objectClass"][]="top";
           $info["objectClass"][]="inetOrgPerson";
           $info["givenName"]= $data['User']['first_name'];
           $info["sn"]=$data['User']['second_name'];
           $info["displayName"]=$data['User']['first_name'].$data['User']['second_name'];
           $info["homeDirectory"]=$data['User']['company'];
           $info["mail"]=$data['User']['email'];
           $info["cn"]=$data['User']['email'];
           $info["userPassword"]=$data['User']['password'];
           $info["uidNumber"]="0";
           $info["gidNumber"]="0";
           $info["telephoneNumber"]=$data['User']['mobile'];
           $info["title"]=$data['User']['title'];
           $uid = substr($info["mail"],0,strrpos($info["mail"],"@"));

           $r = @ldap_add($this->m_lc,"uid=".$uid.",ou=".$depar.",ou=".$company.",dc=ret,dc=cn", $info);
       }
       $this->close();
       return true;
    }
    /**
     * 检查是否存在公司，部门节点
     * @DateTime 2019-05-20
     * @param    [type]       $company [公司]
     * @param    [type]       $depar      [部门]
     */
    public function checkDn($company,$depar){
      $res = false;
      $r = @ldap_list($this->m_lc, "ou=$depar,ou=$company,dc=ret,dc=cn", "objectClass=*", array(""));
      if(!$r){
          $info["objectClass"][]="top";
          $info["objectClass"][]="organizationalUnit";
          $info["ou"]= $depar;
          $res = ldap_add($this->m_lc,"ou=".$depar.",ou=".$company.",dc=ret,dc=cn", $info);
      }else{
        $res = true;
      }
      return $res;
    }
    /**
     * 获取用户信息
     * @Param    参数描述
     * @DateTime 2019-05-08
     */
    public function getUserInfo($email){
        if($this->connect($this->m_strManager,$this->m_strPassword) != null)
        {
            $justthese = array('mail','displayname','title','telephonenumber','dn','uid');//选择要获取的用户属性
            $r = @ldap_search($this->m_lc, $this->m_strBaseDn, 'mail='.$email,$justthese);
            if ($r) 
            {
                $res = @ldap_get_entries($this->m_lc, $r);
            }
        }
        $res = $this->clean_user($res);
        $this->close();
        return $res;
    }
    /**
     * 删除LDAP用户
     * @DateTime 2019-05-20
     * @param    [type]       $email [description]
     */
    public function delUser($email){
      $result = false;
      $user_info = $this->getUserInfo($email);
      if(empty($user_info[0])){
        return false;
      }
      if($this->connect($this->m_strManager,$this->m_strPassword) != null)
      {
        $result = ldap_delete($this->m_lc, $user_info[0]['dn']);
      }
      $this->close();
      return $result;
    }
    /**
     * Replace macros {$username} with given username value
     *
     * @param string $username
     * @return string
     */
    private function generateUserDn($username)
    {
        $this->getConfig();
        return str_replace('{$username}', $username, $this->m_userDN);
    }
}