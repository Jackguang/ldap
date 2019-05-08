<?php

use yii\helpers\Html;
?>

<table class="table table-striped table-bordered" style="margin-top: 20px" id="table1">
    <thead>
        <tr>
            <th style="text-align:center;">用户</th>
            <th style="text-align:center;">邮箱</th>
            <th style="text-align:center;">职位</th>
            <th style="text-align:center;"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($user as $key=>$val){?>
            <tr>
                <td align="center"><?= $val['username']?></td>
                <td align="center"><?= $val['email']?></td>
                <td align="center"><?= $val['title']?></td>
                <td align="center"><a href="">同步用户</a></td>
            </tr>
        <?php }?>
    </tbody>
</table>
