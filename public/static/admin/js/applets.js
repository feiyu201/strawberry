layui.use(['table', 'form', 'jquery', 'drawer', 'dropdown'], function() {
    let table = layui.table;
    let form = layui.form;
    let laydate = layui.laydate;
    let $ = layui.jquery;

    let userTable = table.render({
        elem: '#tableId',
        url: window.CONFIG.DATAURL,
        limit: 20,
        page: true,
        toolbar: true,
        toolbar: "#toolbarTpl",
        defaultToolbar: [{
            layEvent: 'refresh',
            icon: 'layui-icon-refresh',
        }, 'filter', 'print', 'exports'],
        height: 'full',
        cellMinWidth: 120,
        skin: 'line',
        cols: [[
            { type: "checkbox", fixed: "left" },
            { field: "id", title: "ID", sort: true },
            { field: "name", title: "名称" },

            { field: "appid", title: "appid" },


            { field: "secret", title: "secret" },

            { field: "status", title: "状态", width: 100, templet: "#statusTpl" },
            { title: "操作", align: "center", fixed: "right", templet: "#operationTpl" }
        ]],
        done: function (res, curr, count) {
            console.info(res, curr, count);
        }
    });

    form.on("submit(search)", function (data) {
        userTable.reload({
            where: data.field,
            page: { curr: 1 }
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
            case "refresh":
                window.refresh();
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
                del(obj);
                break;
            case "welcome":
                welcome(data);
                break;
        }
    });

    window.add = function() {
        layer.open({
            type: 2,
            maxmin: true,
            title: '新增',
            shade: 0.1,
            area: ['80%', '80%'],
            content: 'add'
        });
    }

    window.edit = function(data) {
        layer.open({
            type: 2,
            maxmin: true,
            title: '编辑',
            shade: 0.1,
            area: ['80%', '80%'],
            content: 'edit?id='+ data.id
        });
    }


    window.del = function(obj) {
        layer.confirm('确定要删除吗？', {
            icon: 3,
            title: '提示'
        }, function(index) {
            layer.close(index);
            let loading = layer.load();
            $.ajax({
                url:'delete',
                data:{idsStr:obj.data['id']},
                dataType: 'json',
                type: 'POST',
                success: function(res) {
                    layer.close(loading);
                    //判断有没有权限
                    if(res && res.code==999){
                        layer.msg(res.msg, {
                            icon: 5,
                            time: 2000,
                        })
                        return false;
                    }else if (res.code==1) {
                        layer.msg(res.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            obj.del();
                        });
                    } else {
                        layer.msg(res.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                }
            })
        });
    }

    window.refresh = function(param) {
        table.reload('tableId');
    }

    function welcome(data) {
        console.log(data);
        window.open(window.CONFIG.WELCOME + "?id=" + data.id);
    }
})