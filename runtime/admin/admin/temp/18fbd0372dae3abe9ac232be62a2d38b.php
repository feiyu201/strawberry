<?php /*a:1:{s:56:"E:\phpstudy\WWW\okadmin\app\admin\view\plugin\index.html";i:1600845800;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>插件列表</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="/static/css/oksub.css">
	<script type="text/javascript" src="/static/lib/loading/okLoading.js"></script>
</head>
<body>
<div class="ok-body">
	<!--模糊搜索区域-->
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
			limit: 10000,
			page: true,
			toolbar: true,
			toolbar: "#toolbarTpl",
			height:'full',
			cols: [[
				{field: "name", title: "标识", width: 100, sort: true},
				{field: "title", title: "名称", width: 100},
				{field: "version", title: "版本", width: 100},
				{field: "description", title: "简述", width: 200},
				{field: "author", title: "作者", width: 100},
				
				{field: "status", title: "状态", width: 100, templet: "#statusTpl"},
				
				{field: "button", title: "操作按钮"},
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
				case "install":
					install(data.name);
					break;
				case "uninstall":
					uninstall(data.name);
					break;
				case "config":
					config(data.name);
					break;
			}
		});

	
		
		function install(id) {
			okLayer.confirm("确定要安装吗？", function () {
				okUtils.ajax("<?php echo url('install'); ?>", "get", {id: id}, true).done(function (response) {
					if(response.code==1){
						console.log(response);
						//userTable.reload();
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

		function config(id) {
			okLayer.open("配置", "config?id="+id, "90%", "90%", function (layero) {
				//let iframeWin = window[layero.find("iframe")[0]["name"]];
				//iframeWin.initForm(data);
			}, function () {
				userTable.reload();
			})
		}

		function del(id) {
			okLayer.confirm("确定要删除吗？", function () {
				okUtils.ajax("<?php echo url('delete'); ?>", "get", {idsStr: id}, true).done(function (response) {
					
					
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
		/* <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="batchEnabled">批量启用</button>
		<button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="batchDisabled">批量停用</button> */
	</div>
</script>
<!-- 行工具栏模板 -->


<script type="text/html" id="statusTpl">
	{{#  if(d.status == 'normal'){ }}
	<span class="layui-btn layui-btn-normal layui-btn-xs">启用</span>
	{{#  } else if(d.status != 'normal') { }}
	<span class="layui-btn layui-btn-warm layui-btn-xs">停用</span>
	{{#  } }}
</script>


</body>
</html>
