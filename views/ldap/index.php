<?php

use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Html;
?>

<table class="table table-striped table-bordered" style="margin-top: 20px" id="table1">
    <thead>
        <tr>
            <th style="text-align:center;">用户</th>
            <th style="text-align:center;">邮箱</th>
            <th style="text-align:center;">职位</th>
            <th style="text-align:center;">公司</th>
            <th style="text-align:center;"><a href="javascript:void(0)">同步所有用户</a></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($user as $key=>$val){?>
            <tr>
                <td align="center"><?= $val['username']?></td>
                <td align="center"><?= $val['email']?></td>
                <td align="center"><?= $val['title']?></td>
                <td align="center"><?= $val['company']?></td>
                <td align="center"><a href="javascript:void(0)" class="synchro_user" user-email="<?= $val['email']?>">同步用户</a></td>
            </tr>
        <?php }?>
    </tbody>
</table>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    $('.synchro_user').click(function(){
        var email = $(this).attr('user-email');
        var $csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({  
            url: "/ldap/ldap/add-user",  
            type: 'post',  
            data: {  
                'email': email,  
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
