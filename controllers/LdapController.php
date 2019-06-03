<?php

namespace wzg\ldap\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use wzg\ldap\components\Ldap;
use mdm\admin\models\Assignment;
use wzg\ldap\models\User;
use mdm\admin\models\searchs\AuthItem as AuthItemSearch;
/**
 * Default controller for the `wzg` module
 */
class LdapController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $username = Yii::$app->request->get('username');
        $email = Yii::$app->request->get('email');
    	$ldap = new Ldap();
    	$user = $ldap->getList($username);
        //获取系统角色
        $searchRole = new AuthItemSearch(['type' => 1]);
        $dataProvider = $searchRole->search([]);
        $roleArray = ArrayHelper::toArray($dataProvider);
        // echo '<pre>';
        // print_r(ArrayHelper::toArray($dataProvider));die;
        return $this->render('index',[
        'user'=>$user,
        'email'=>$email,
        'username'=>$username,
        'roleArray'=>$roleArray
      ]);
    }
    /**
     * 添加LDAP用户
     * @Param    参数描述
     * @DateTime 2019-05-17
     * @return   [type]       [description]
     */
    public function actionAdd(){
       $this->layout = false;
       $cache = Yii::$app->cache;
       $ldap = new Ldap();
       $ldap_company = $cache->getOrSet('ldap_company', function ()use ($ldap) {
           return $ldap->getCompanyDepart('company');
       },600);
       $ldap_depart = $cache->getOrSet('ldap_depart', function ()use ($ldap) {
           return $ldap->getCompanyDepart('depart');
       },600);
       $model = new User();
       if ($data = Yii::$app->request->post()) {
           $ldap->addUser($data);
           return $this->redirect(Yii::$app->request->referrer);
       }

       return $this->render('add', [
           'model' => $model,
           'ldap_company' => $ldap_company,
           'ldap_depart' => $ldap_depart,
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
        $item = Yii::$app->request->post('role');
        $tablename = yii::$app->params['ldaptablename'];
        $company_field = yii::$app->params['company_field'];
        //系统是否已经存在用户
        $find_user = $tablename::find()->where(['email'=>$email])->one();
        $company = yii::$app->params['ret'];
        if(empty($find_user)){
            $ldap = new Ldap();
            $user_info = $ldap->getUserInfo($email);
            if(count($user_info) == 1){
                $model = new $tablename;
                $model->username = $user_info[0]['username'];
                $model->email = $user_info[0]['email'];
                $model->created_at = time();
                $model->updated_at = time();
                $model->$company_field = array_search($user_info[0]['company'], $company);
                $model->save(false);
                //添加角色
                $items[] = $item;
                $ass_model = new Assignment($model->id);
                $success = $ass_model->assign($items);
                echo '同步成功';die;
            }
        }else{
            echo '系统已存在该用户';die;
        }
    }
    /**
     * 删除LDAP用户
     * @DateTime 2019-05-20
     * @param    [type]       $email [邮箱]
     */
    public function actionDelete($email)
    {
        $ldap = new Ldap();
        $ldap->delUser($email);
        return $this->redirect(['index']);
    }
    public function actionValidateForm () {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new User();   //这里要替换成自己的模型类
        $model->load(Yii::$app->request->post());  
        return \yii\widgets\ActiveForm::validate($model);  
    }
}
