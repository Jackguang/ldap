<?php

namespace wzg\ldap\controllers;

use yii;
use yii\web\Controller;
use wzg\ldap\components\Ldap;
/**
 * Default controller for the `wzg` module
 */
class DefaultController extends Controller
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
}
