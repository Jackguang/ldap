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
        //return null;
    }

    public function close()
    {
        ldap_close($this->m_lc);
        $this->m_lc = null;
    }

    protected function clean_user($user)
    {
        $user_array = [];
        if ($user['count'] == 0)
        {
            return $user_array;
        }
        unset($user['count']);
        foreach ($user as $key => $val) {
            if(isset($val['mail'][0]) && isset($val['displayname'][0])){
                $user_array[$key]['username'] = $val['displayname'][0]; 
                $user_array[$key]['email'] = $val['mail'][0];
                $user_array[$key]['title'] = isset($val['title'][0])  ? $val['title'][0] : '';
                $user_array[$key]['mobile'] = isset($val['telephonenumber'][0])  ? $val['telephonenumber'][0] : '';
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
    public function getList()
    {
       $result = false;
       if($this->connect($this->m_strManager,$this->m_strPassword) != null)
       {
           $justthese = array('mail','displayname','title','telephonenumber');//选择要获取的用户属性
           $r = @ldap_search($this->m_lc, $this->m_strBaseDn, 'uid=*',$justthese);
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