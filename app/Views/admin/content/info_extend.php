
    <?php if(!empty($extendFieldList)){?>
    <div class="layui-tab-item">
        <?php foreach($extendFieldList as $v){ ?>

        <div class="layui-form-item">
            <label class="layui-form-label"><?php echo $v['field_name']; ?></label>
            <div class="layui-input-block">
                <?php if($v['field_type'] == 'radio'){ ?>
                <?php foreach($v['field_option'] as $vf){?>
                <input type="radio" {if isset($info['<?php echo $v['field_input']; ?>']) && $info['<?php echo $v['field_input']; ?>'] == '<?php echo $vf; ?>'}checked{/if} name="<?php echo $v['field_input']; ?>" value="<?php echo $vf; ?>" title="<?php echo $vf; ?>" >
                <?php } ?>
                <?php } ?>
                <?php if($v['field_type'] == 'checkbox'){ ?>
                <?php foreach($v['field_option'] as $vf){?>
                    <input type="checkbox" {if isset($info['<?php echo $v['field_input']; ?>']) && $info['<?php echo $v['field_input']; ?>'] == '<?php echo $vf; ?>'}checked{/if} name="<?php echo $v['field_input']; ?>[]" value="<?php echo $vf; ?>" title="<?php echo $vf; ?>" >
                <?php } ?>
                <?php } ?>
                <?php if($v['field_type'] == 'select'){ ?>
                <select id="<?php echo $v['field_input']; ?>" name="<?php echo $v['field_input']; ?>">
                    <option value="0">请选择</option>
                    <?php foreach($v['field_option'] as $vf){?>
                        <option {if isset($info['<?php echo $v['field_input']; ?>']) && $info['<?php echo $v['field_input']; ?>'] == '<?php echo $vf; ?>'}selected{/if} value="<?php echo $vf; ?>"><?php echo $vf; ?></option>
                    <?php } ?>
                </select>
                <?php } ?>

                <?php if($v['field_type'] == 'text'){ ?>
                <input type="text" value="{$info['<?php echo $v['field_input']; ?>'] ?? ''}" name="<?php echo $v['field_input']; ?>"
                       placeholder="请输入<?php echo $v['field_name']; ?>" autocomplete="off" class="layui-input">
                <?php } ?>

                <?php if($v['field_type'] == 'textarea'){ ?>
                <textarea name="<?php echo $v['field_input']; ?>" placeholder="请输入<?php echo $v['field_name']; ?>" class="layui-textarea">{$info['<?php echo $v['field_input']; ?>'] ?? ''}</textarea>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>