<?php /*a:7:{s:63:"E:\phpstudy\WWW\okadmin\app\admin\view\form_builder\layout.html";i:1600853100;s:69:"E:\phpstudy\WWW\okadmin\app\admin\view\form_builder\items\hidden.html";i:1600853059;s:67:"E:\phpstudy\WWW\okadmin\app\admin\view\form_builder\items\text.html";i:1600851418;s:71:"E:\phpstudy\WWW\okadmin\app\admin\view\form_builder\items\textarea.html";i:1600851593;s:68:"E:\phpstudy\WWW\okadmin\app\admin\view\form_builder\items\radio.html";i:1600851586;s:71:"E:\phpstudy\WWW\okadmin\app\admin\view\form_builder\items\checkbox.html";i:1600851012;s:69:"E:\phpstudy\WWW\okadmin\app\admin\view\form_builder\items\select.html";i:1600851469;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>添加用户</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="/static/css/oksub.css">
	<script type="text/javascript" src="/static/lib/loading/okLoading.js"></script>
	<style>
		.layui-upload-list img{
			height:100px;
			width:100px;
		}
	</style>
</head>
<body>
<!--内容开始-->
<section class="content">
   
    <!--数据表开始-->
    <form class="layui-form layui-form-pane ok-form" name="form-builder" method="<?php echo htmlentities($form_method); ?>" action="<?php echo htmlentities($form_url); ?>" <?php if(!empty($submit_confirm)) echo 'submit_confirm'; ?>>
        <!---->
        <?php if($form_items): ?>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="box box-body">
                    <!---->
                    <?php if(is_array($form_items) || $form_items instanceof \think\Collection || $form_items instanceof \think\Paginator): $i = 0; $__LIST__ = $form_items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$form): $mod = ($i % 2 );++$i;switch($form['type']): case "hidden": ?>
                            
                            
        <input type="hidden" name="<?php echo htmlentities($form['name']); ?>" value="<?php echo htmlentities((isset($form['value']) && ($form['value'] !== '')?$form['value']:'')); ?>" id="<?php echo htmlentities($form['name']); ?>" <?php echo (isset($form['extra_attr']) && ($form['extra_attr'] !== '')?$form['extra_attr']:''); ?>>



                        <?php break; case "text": ?>
                        	
                        	<div class="row layui-form-item  <?php echo htmlentities((isset($form['extra_class']) && ($form['extra_class'] !== '')?$form['extra_class']:'')); ?>" id="form_group_<?php echo htmlentities($form['name']); ?>">
        <label class="layui-form-label" for="<?php echo htmlentities($form['name']); ?>"><?php echo htmlentities(htmlspecialchars($form['title'])); ?></label>
        <div class="layui-input-inline">
            <?php if(!(empty($form['group']) || (($form['group'] instanceof \think\Collection || $form['group'] instanceof \think\Paginator ) && $form['group']->isEmpty()))): ?>
            <div class="input-group">
            <?php endif; if(!(empty($form['group']['0']) || (($form['group']['0'] instanceof \think\Collection || $form['group']['0'] instanceof \think\Paginator ) && $form['group']['0']->isEmpty()))): ?>
                <span class="input-group-addon"><?php echo $form['group']['0']; ?></span>
                <?php endif; ?>
                <input class="layui-input" type="text" id="<?php echo htmlentities($form['name']); ?>" name="<?php echo htmlentities($form['name']); ?>" value="<?php echo htmlentities($form['value']); ?>" placeholder="<?php echo htmlentities($form['placeholder']); ?>" <?php echo $form['extra_attr']; ?>>
                <?php if(!(empty($form['group']['1']) || (($form['group']['1'] instanceof \think\Collection || $form['group']['1'] instanceof \think\Paginator ) && $form['group']['1']->isEmpty()))): ?>
                <span class="input-group-addon"><?php echo $form['group']['1']; ?></span>
                <?php endif; if(!(empty($form['group']) || (($form['group'] instanceof \think\Collection || $form['group'] instanceof \think\Paginator ) && $form['group']->isEmpty()))): ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="layui-form-mid layui-word-aux">
            <?php if(!(empty($form['required']) || (($form['required'] instanceof \think\Collection || $form['required'] instanceof \think\Paginator ) && $form['required']->isEmpty()))): ?> *<?php endif; if(!(empty($form['tips']) || (($form['tips'] instanceof \think\Collection || $form['tips'] instanceof \think\Paginator ) && $form['tips']->isEmpty()))): ?> <?php echo $form['tips']; ?><?php endif; ?>
        </div>
</div>


                        <?php break; case "textarea": ?>
                            
                            <div class="row layui-form-item <?php echo htmlentities((isset($form['extra_class']) && ($form['extra_class'] !== '')?$form['extra_class']:'')); ?>" id="form_group_<?php echo htmlentities($form['name']); ?>">
     <label class="layui-form-label" for="<?php echo htmlentities($form['name']); ?>"><?php echo htmlentities(htmlspecialchars($form['title'])); ?></label>
        <div class="layui-input-inline">
            <textarea class="layui-textarea" id="<?php echo htmlentities($form['name']); ?>" name="<?php echo htmlentities($form['name']); ?>" rows="3" placeholder="<?php echo htmlentities($form['placeholder']); ?>" <?php echo $form['extra_attr']; ?>><?php echo htmlentities($form['value']); ?></textarea>
        </div>
        <div class="layui-form-mid layui-word-aux">
            <?php if(!(empty($form['required']) || (($form['required'] instanceof \think\Collection || $form['required'] instanceof \think\Paginator ) && $form['required']->isEmpty()))): ?> *<?php endif; if(!(empty($form['tips']) || (($form['tips'] instanceof \think\Collection || $form['tips'] instanceof \think\Paginator ) && $form['tips']->isEmpty()))): ?> <?php echo $form['tips']; ?><?php endif; ?>
        </div>
</div>
                        <?php break; case "radio": ?>
                            
                            <div class="row layui-form-item <?php echo htmlentities((isset($form['extra_class']) && ($form['extra_class'] !== '')?$form['extra_class']:'')); ?>" id="form_group_<?php echo htmlentities($form['name']); ?>">
        <label class="layui-form-label" for="<?php echo htmlentities($form['name']); ?>"><?php echo htmlentities(htmlspecialchars($form['title'])); ?></label>
        <div class="layui-input-inline">
                <?php if(is_array($form['options']) || $form['options'] instanceof \think\Collection || $form['options'] instanceof \think\Paginator): $i = 0; $__LIST__ = $form['options'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i;?>
                    <input type="radio" name="<?php echo htmlentities($form['name']); ?>" class="dd_radio" id="<?php echo htmlentities($form['name']); ?><?php echo htmlentities($i); ?>" value="<?php echo htmlentities($key); ?>" <?php if($key == (isset($form['value']) && ($form['value'] !== '')?$form['value']:'')): ?>checked<?php endif; ?> <?php echo (isset($form['extra_attr']) && ($form['extra_attr'] !== '')?$form['extra_attr']:''); ?> title="<?php echo htmlspecialchars($option); ?>">
                   
                <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form-mid layui-word-aux">
            <?php if(!(empty($form['required']) || (($form['required'] instanceof \think\Collection || $form['required'] instanceof \think\Paginator ) && $form['required']->isEmpty()))): ?> *<?php endif; if(!(empty($form['tips']) || (($form['tips'] instanceof \think\Collection || $form['tips'] instanceof \think\Paginator ) && $form['tips']->isEmpty()))): ?> <?php echo $form['tips']; ?><?php endif; ?>
        </div>
</div>

                        <?php break; case "checkbox": ?>
                            
                            <div class="row layui-form-item <?php echo htmlentities((isset($form['extra_class']) && ($form['extra_class'] !== '')?$form['extra_class']:'')); ?>" id="form_group_<?php echo htmlentities($form['name']); ?>">
        <label class="layui-form-label" for="<?php echo htmlentities($form['name']); ?>"><?php echo htmlentities(htmlspecialchars($form['title'])); ?></label>
        <div class="layui-input-inline">
                <?php if(is_array($form['options']) || $form['options'] instanceof \think\Collection || $form['options'] instanceof \think\Paginator): $i = 0; $__LIST__ = $form['options'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i;?>
                    <input type="checkbox" name="<?php echo htmlentities($form['name']); ?>[]" class="dd_radio" id="<?php echo htmlentities($form['name']); ?><?php echo htmlentities($i); ?>" value="<?php echo htmlentities($key); ?>" <?php if(in_array(($key), is_array((isset($form['value']) && ($form['value'] !== '')?$form['value']:''))?(isset($form['value']) && ($form['value'] !== '')?$form['value']:''):explode(',',(isset($form['value']) && ($form['value'] !== '')?$form['value']:'')))): ?>checked<?php endif; ?> <?php echo (isset($form['extra_attr']) && ($form['extra_attr'] !== '')?$form['extra_attr']:''); ?> title="<?php echo htmlspecialchars($option); ?>">
                <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form-mid layui-word-aux">
            <?php if(!(empty($form['required']) || (($form['required'] instanceof \think\Collection || $form['required'] instanceof \think\Paginator ) && $form['required']->isEmpty()))): ?> *<?php endif; if(!(empty($form['tips']) || (($form['tips'] instanceof \think\Collection || $form['tips'] instanceof \think\Paginator ) && $form['tips']->isEmpty()))): ?> <?php echo $form['tips']; ?><?php endif; ?>
        </div>
</div>
                        <?php break; case "select": ?>
                            
                            <div class="row layui-form-item <?php echo htmlentities((isset($form['extra_class']) && ($form['extra_class'] !== '')?$form['extra_class']:'')); ?>" id="form_group_<?php echo htmlentities($form['name']); ?>">
        <label class="layui-form-label" for="<?php echo htmlentities($form['name']); ?>"><?php echo htmlentities(htmlspecialchars($form['title'])); ?></label>
        <div class="layui-input-inline">
            <select class="form-control" id="<?php echo htmlentities($form['name']); ?>" name="<?php echo htmlentities($form['name']); ?>" <?php echo htmlentities((isset($form['extra_attr']) && ($form['extra_attr'] !== '')?$form['extra_attr']:'')); ?>>
                <option value=""><?php echo htmlentities($form['placeholder']); ?></option>
                <?php if(is_array($form['options']) || $form['options'] instanceof \think\Collection || $form['options'] instanceof \think\Paginator): $i = 0; $__LIST__ = $form['options'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo htmlentities($key); ?>" <?php if(((string)$form['value'] == (string)$key)): ?>selected<?php endif; ?>><?php echo htmlentities($option); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
        <div class="layui-form-mid layui-word-aux">
            <?php if(!(empty($form['required']) || (($form['required'] instanceof \think\Collection || $form['required'] instanceof \think\Paginator ) && $form['required']->isEmpty()))): ?> *<?php endif; if(!(empty($form['tips']) || (($form['tips'] instanceof \think\Collection || $form['tips'] instanceof \think\Paginator ) && $form['tips']->isEmpty()))): ?> <?php echo $form['tips']; ?><?php endif; ?>
        </div>
</div>


                        <?php break; default: ?>

                    <?php endswitch; ?>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    <!---->
                    <div class="layui-form-item">
                            <div class="layui-input-block">
                            	<?php if(isset($btn_hide) && !in_array('submit', $btn_hide)): ?>
                                <button class="layui-btn " lay-submit lay-filter="add"><?php echo htmlentities((isset($btn_title['submit']) && ($btn_title['submit'] !== '')?$btn_title['submit']:'提 交')); ?></button>
                                <?php endif; if(isset($btn_hide) && !in_array('back', $btn_hide)): ?>
                               
                                <?php endif; foreach($btn_extra as $key=>$vo): ?>
                                <?php echo (isset($vo) && ($vo !== '')?$vo:''); ?>
                                <?php endforeach; ?>
                            </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="box box-body">
                    <?php echo $empty_tips; ?>
                </div>
                <!-- /.box -->
            </div>
        </div>
        <?php endif; ?>
    </form>
    
</section>

<!--内容结束-->
<!--js逻辑-->
<script src="/static/lib/layui/layui.js"></script>
<script>
	
	
	
	layui.use(["element", "form", "laydate", "okLayer", "okUtils"], function () {
		let form = layui.form;
		let laydate = layui.laydate;
		let okLayer = layui.okLayer;
		let okUtils = layui.okUtils;

		okLoading.close();

		//laydate.render({elem: "#birthday", type: "datetime"});

		form.verify({
			
		});

		form.on("submit(add)", function (data) {
			okUtils.ajax("<?php echo htmlentities($form_url); ?>", "post", data.field, true).done(function (response) {
				//console.log(response);
				if(response.code==1){
					okLayer.greenTickMsg("编辑成功", function () {
						parent.layer.close(parent.layer.getFrameIndex(window.name));
					});
				}else{
					okLayer.greenTickMsg(response.msg, function () {
				        
				    })
				}
				
			}).fail(function (error) {
				console.log(error)
			});
			return false;
		});
	});
</script>



</body>
</html>
