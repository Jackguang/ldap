<?php

namespace wzg\ldap\models;

use Yii;
use wzg\ldap\components\Ldap;
/**
 * BizRule
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class User extends \yii\db\ActiveRecord
{

    public $first_name;   //用户姓名
    public $second_name;   //用户姓名
    public $company;    //用户公司
    public $title;      //用户职位
    public $location;   //地址
    public $email;      //emial
    public $mobile;     //手机号
    public $password;   //密码
    public $depar;      //部门
    public $cn;         
    public function rules()
    {
        return [
            [['first_name','second_name','email','cn','password','company','depar','title','mobile'], 'required'],
            [['email'], "requiredEmail", 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'first_name' => '姓',
            'second_name' => '名',
            'company' => '公司',
            'title' => '职位',
            'location' => '地址',
            'email' => '邮箱',
            'mobile' => '手机号',
            'password' => '密码',
            'depar' => '部门',
            'cn' => 'cn',
        ];
    }
    public function requiredEmail($attribute, $params)
    {
        //是否已存在该用户
        $ldap = new Ldap();
        $user = $ldap->getUserInfo($this->email);
        if(!empty($user[0])){
         $this->addError($attribute, "用户已存在");
        }
    }
}
