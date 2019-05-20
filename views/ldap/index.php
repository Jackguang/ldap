<?php

use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div class="article-search">

    <form action="/ldap/ldap/index" method="get">
    <div class="col-md-2">
        <label class="control-label">用户姓名</label>
        <input type="text"  class="form-control" name="username" value="<?= $username?>">
    </div>
<!--     <div class="col-md-2">
        <label class="control-label">邮箱</label>
        <input type="text"  class="form-control" name="email" value="<?= $email?>">
    </div> -->
    <div class="form-group">
        <button type="submit" class="btn btn-primary" style="margin-top: 24px;">搜索</button>  
        <?= Html::a('添加LDAP用户', '#', [
                            'class' => 'btn btn-primary data-update',
                            'style' =>'margin-top: 24px;',
                            'title' => '维护',
                            'data-pjax' => '0',
                            'data-toggle' => 'modal',
                            'data-target' => '#update-modal',
                        ]);?>
    </div>
    </form>
</div>
<table class="table table-striped table-bordered" style="margin-top: 20px" id="table1">
    <thead>
        <tr>
            <th style="text-align:center;">用户姓名</th>
            <th style="text-align:center;">邮箱</th>
            <th style="text-align:center;">职位</th>
            <th style="text-align:center;">公司</th>
            <th style="text-align:center;">系统角色</th>
            <th style="text-align:center;"><!-- <a href="javascript:void(0)">同步所有用户</a> --></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($user as $key=>$val){?>
            <tr>
                <td align="center"><?= $val['username']?></td>
                <td align="center"><?= $val['email']?></td>
                <td align="center"><?= $val['title']?></td>
                <td align="center"><?= $val['company']?></td>
                <td align="center">
                    <select>
                        <option value="">请选择</option>
                        <?php foreach ($roleArray['allModels'] as $key => $value): ?>
                            <option value="<?= $key?>"><?= $key?></option>
                        <?php endforeach ?>>
                    </select>
                </td>
                <td align="center">
                    <a href="javascript:void(0)" class="glyphicon glyphicon-refresh synchro_user" title="同步至系统" user-email="<?= $val['email']?>"></a>
                    <a href="/ldap/ldap/delete?email=<?= $val['email']?>" title="删除" aria-label="删除" data-pjax="0" data-confirm="您确定要删除【<?= $val['username']?>】吗？" data-method="post"><span class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr>
        <?php }?>
    </tbody>
</table>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    $('.synchro_user').click(function(){
        var email = $(this).attr('user-email');
        var role = $(this).parent().prev().find('select').val();
        if(!role){
            alert('请选择角色');
            return false;
        }
        var $csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({  
            url: "/ldap/ldap/add-user",  
            type: 'post',  
            data: {  
                'email': email,  
                'role': role,  
                '_csrf-frontend': $csrfToken,
            },  
            success: function(data) {  
                alert(data);  
            },  
            error: function(XMLHttpRequest, textStatus, errorThrown) {  
                alert("发生错误");  
            }  
        }); 
    })
</script>
<?php 
use yii\bootstrap\Modal;
// 更新操作
Modal::begin([
    'id' => 'update-modal',
    'header' => '<h4 class="modal-title" id="modal-title"></h4>',
    'footer' => '',
]); 
$updateJs = <<<JS
    $('.data-update').on('click', function () {
        var j_url = '/ldap/ldap/add';
        $.get(j_url, {  },
            function (data) {
                $('#modal-title').html('添加LDAP用户')
                $('.modal-body').html(data);
            }  
        );
    });
   
JS;
$this->registerJs($updateJs);
Modal::end();
?>
