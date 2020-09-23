<?php /*a:1:{s:55:"E:\phpstudy\WWW\okadmin\app\admin\view\admin\index.html";i:1600760613;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>用户列表</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="/static/css/oksub.css">
	<script type="text/javascript" src="/static/lib/loading/okLoading.js"></script>
</head>
<body>
<div class="ok-body">
	<!--模糊搜索区域-->
	<div class="layui-row">
		<!-- <form class="layui-form ok-search-form">
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">开始日期</label>
					<div class="layui-input-inline">
						<input type="text" class="layui-input" placeholder="开始日期" autocomplete="off" id="startTime" name="startTime">
					</div>
				</div>
				<div class="layui-inline">
					<label class="layui-form-label">截止日期</label>
					<div class="layui-input-inline">
						<input type="text" class="layui-input" placeholder="截止日期" autocomplete="off" id="endTime" name="endTime">
					</div>
				</div>
				<div class="layui-inline">
					<label class="layui-form-label">用户名</label>
					<div class="layui-input-inline">
						<input type="text" class="layui-input" placeholder="账号" autocomplete="off" name="username">
					</div>
				</div>
				
				<div class="layui-inline">
					<label class="layui-form-label">昵称</label>
					<div class="layui-input-inline">
						<input type="text" class="layui-input" placeholder="昵称" autocomplete="off" name="nickname">
					</div>
				</div>
				<div class="layui-inline">
					<label class="layui-form-label">邮箱</label>
					<div class="layui-input-inline">
						<input type="text" class="layui-input" placeholder="邮箱" autocomplete="off" name="email">
					</div>
				</div>
				
				<div class="layui-inline">
					<label class="layui-form-label">请选择状态</label>
					<div class="layui-input-inline">
						<select name="status" lay-verify="" lay-search>
							<option value="" selected>请选择状态</option>
							<option value="normal">启用</option>
							<option value="stop">停用</option>
						</select>
					</div>
				</div>
				<div class="layui-inline">
					<div class="layui-input-inline">
						<button class="layui-btn" lay-submit="" lay-filter="search">
							<i class="layui-icon">&#xe615;</i>
						</button>
					</div>
				</div>
			</div>
		</form> -->
	</div>
	<!--数据表格-->
	<table class="layui-hide" id="tableId" lay-filter="tableFilter"></table>
</div>
<!--js逻辑-->
<script src="/static/lib/layui/layui.js"></script>
<script>
	layui.use(["element", "jquery", "table", "form", "laydate", "okLayer", "okUtils", "okMock"], function () {
		let table = layui.table;
		let form = layui.form;
		let laydate = layui.laydate;
		let okLayer = layui.okLayer;
		let okUtils = layui.okUtils;
		let okMock = layui.okMock;
		let $ = layui.jquery;

		okLoading.close($);

		laydate.render({elem: "#startTime", type: "datetime"});
		laydate.render({elem: "#endTime", type: "datetime"});

		let userTable = table.render({
			elem: '#tableId',
			url: '<?php echo url("getList"); ?>',
			limit: 20,
			page: true,
			toolbar: true,
			toolbar: "#toolbarTpl",
			height:'full',
			cols: [[
				{type: "checkbox", fixed: "left"},
				{field: "id", title: "ID", width: 100, sort: true},
				{field: "username", title: "用户名", width: 100},
				{field: "nickname", title: "昵称", width: 100},
				{field: "avatar", title: "头像", width: 100, templet: "#logoTpl"},
				{field: "email", title: "邮箱", width: 200},
				{field: "status", title: "状态", width: 100, templet: "#statusTpl"},
				{field: "createtime_text", title: "创建时间", width: 150},
				{title: "操作",  align: "center", fixed: "right", templet: "#operationTpl"}
			]],
			done: function (res, curr, count) {
				console.info(res, curr, count);
			}
		});

		form.on("submit(search)", function (data) {
			userTable.reload({
				where: data.field,
				page: {curr: 1}
			});
			return false;
		});

		table.on("toolbar(tableFilter)", function (obj) {
			switch (obj.event) {
				case "batchEnabled":
					batchEnabled();
					break;
				case "batchDisabled":
					batchDisabled();
					break;
				case "batchDel":
					batchDel();
					break;
				case "add":
					add();
					break;
			}
		});

		table.on("tool(tableFilter)", function (obj) {
			let data = obj.data;
			switch (obj.event) {
				case "edit":
					edit(data);
					break;
				case "del":
					del(data.id);
					break;
			}
		});

		function batchEnabled() {
			okLayer.confirm("确定要批量启用吗？", function (index) {
				layer.close(index);
				let idsStr = okUtils.tableBatchCheck(table);
				if (idsStr) {
					okUtils.ajax("<?php echo url('setNormal'); ?>", "put", {idsStr: idsStr}, true).done(function (response) {
						if(response.code==1){
							console.log(response);
							okUtils.tableSuccessMsg(response.msg);
						}else{
							okLayer.greenTickMsg(response.msg, function () {
						        
						    })
						}
					}).fail(function (error) {
						console.log(error)
					});
				}
			});
		}

		function batchDisabled() {
			okLayer.confirm("确定要批量停用吗？", function (index) {
				layer.close(index);
				let idsStr = okUtils.tableBatchCheck(table);
				if (idsStr) {
					okUtils.ajax("<?php echo url('setStop'); ?>", "put", {idsStr: idsStr}, true).done(function (response) {
						
						if(response.code==1){
							console.log(response);
							okUtils.tableSuccessMsg(response.msg);
						}else{
							okLayer.greenTickMsg(response.msg, function () {
						        
						    })
						}
					}).fail(function (error) {
						console.log(error)
					});
				}
			});
		}

		function batchDel() {
			okLayer.confirm("确定要批量删除吗？", function (index) {
				layer.close(index);
				let idsStr = okUtils.tableBatchCheck(table);
				if (idsStr) {
					okUtils.ajax("<?php echo url('delete'); ?>", "delete", {idsStr: idsStr}, true).done(function (response) {
						//console.log(response);
						//okUtils.tableSuccessMsg(response.msg);
						if(response.code==1){
							console.log(response);
							okUtils.tableSuccessMsg(response.msg);
						}else{
							okLayer.greenTickMsg(response.msg, function () {
						        
						    })
						}
					}).fail(function (error) {
						console.log(error)
					});
				}
			});
		}

		function add() {
			okLayer.open("添加用户", "<?php echo url('add'); ?>", "90%", "90%", null, function () {
				userTable.reload();
			})
		}

		function edit(data) {
			okLayer.open("更新用户", "edit?id="+data.id, "90%", "90%", function (layero) {
				//let iframeWin = window[layero.find("iframe")[0]["name"]];
				//iframeWin.initForm(data);
			}, function () {
				userTable.reload();
			})
		}

		function del(id) {
			okLayer.confirm("确定要删除吗？", function () {
				okUtils.ajax("<?php echo url('delete'); ?>", "delete", {idsStr: id}, true).done(function (response) {
					
					
					if(response.code==1){
						console.log(response);
						okUtils.tableSuccessMsg(response.msg);
					}else{
						okLayer.greenTickMsg(response.msg, function () {
					        
					    })
					}
					
				}).fail(function (error) {
					console.log(error)
				});
			})
		}
	})
</script>
<!-- 头工具栏模板 -->
<script type="text/html" id="toolbarTpl">
	<div class="layui-btn-container">
		<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="batchEnabled">批量启用</button>
		<button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="batchDisabled">批量停用</button>
		<button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="batchDel">批量删除</button>
		<button class="layui-btn layui-btn-sm" lay-event="add">添加用户</button>
	</div>
</script>
<!-- 行工具栏模板 -->
<script type="text/html" id="operationTpl">
	<a href="javascript:" title="编辑" lay-event="edit"><i class="layui-icon">&#xe642;</i></a>
	<a href="javascript:" title="删除" lay-event="del"><i class="layui-icon">&#xe640;</i></a>
</script>

<script type="text/html" id="statusTpl">
	{{#  if(d.status == 'normal'){ }}
	<span class="layui-btn layui-btn-normal layui-btn-xs">启用</span>
	{{#  } else if(d.status != 'normal') { }}
	<span class="layui-btn layui-btn-warm layui-btn-xs">停用</span>
	{{#  } }}
</script>

<script type="text/html" id="roleTpl">
	{{#  if(d.role == 0){ }}
	<span class="layui-btn layui-btn-normal layui-btn-xs">超级会员</span>
	{{#  } else if(d.role == 1) { }}
	<span class="layui-btn layui-btn-warm layui-btn-xs">普通用户</span>
	{{#  } }}
</script>

<script type="text/html" id="logoTpl">
	<image src="{{d.avatar}}" style="width: auto;height: 100%;"/>
</script>

</body>
</html>
