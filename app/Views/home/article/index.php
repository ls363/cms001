<table>
    <?php foreach($articleList as $v){?>
    <tr>
        <td><?php echo $v['id']; ?></td>
        <td><?php echo $v['title']; ?></td>
        <td><?php echo $v['created_at']; ?></td>
    </tr>
    <?php } ?>
</table>
<?php
