<?php

namespace wzg\ldap\controllers;

use yii;
use yii\web\Controller;
use backend\models\AdminUser;
use wzg\ldap\components\Ldap;
/**
 * Default controller for the `wzg` module
 */
class LdapController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    	$ldap = new Ldap();
    	$user = $ldap->getList();
        return $this->render('index',[
        'user'=>$user
      ]);
    }
    /**
     * 添加用户至系统
     * @Param    参数描述
     * @DateTime 2019-05-08
     * @return   [type]       [description]
     */
    public function actionAddUser(){
        $email = Yii::$app->request->post('email');
        //系统是否已经存在用户
        $find_user = AdminUser::find()->where(['email'=>$email])->one();
        $company = yii::$app->params['ret'];
        if(empty($find_user)){
            $ldap = new Ldap();
            $user_info = $ldap->getUserInfo($email);
            if(count($user_info) == 1){
                $model = new AdminUser();
                $model->username = $user_info[0]['username'];
                $model->email = $user_info[0]['email'];
                $model->company = array_search($user_info[0]['company'], $company);;
                $model->save(false);
                echo '同步成功';die;
            }
        }else{
            echo '系统已存在该用户';die;
        }
    }
}
