define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'applets/index',
        add_url: 'applets/add',
        edit_url: 'applets/edit',
        delete_url: 'applets/delete',
        export_url: 'applets/export',
        modify_url: 'applets/setNormal',
    };

    var Controller = {

        index: function () {
            ea.table.render({
                init: init,
                limit: 20,
                page: true,
                cols: [[
                    { type: "checkbox", fixed: "left" },
                    { field: "id", title: "ID", sort: true },
                    { field: "name", title: "名称" },
                    { field: "appid", title: "appid" },
                    { field: "secret", title: "secret" },
                    { field: 'status', title: '状态', width: 85, selectList: {'hidden': '禁用', 'normal': '启用'}, templet: ea.table.switch},
                    { width: 250, title: '操作', templet: ea.table.tool}
                ]],
             });

            ea.listen();
        },
        add: function () {
            ea.listen();
        },
        edit: function () {
            ea.listen();
        },
    };
    return Controller;
});