layui.define(['jquery'], function (exports) {
    var  $ = layui.jquery;
    var  obj = {
        index:0,
       render:function(config){
           var el = config.el;
           obj.config = config;
           $(el).hide();
           var dl = $("<dl></dl>");
           obj.dl =dl;
           dl.addClass("fieldlist").attr('data-name',config.name).attr('data.listidx',obj.index);
           dl.append('<dd><ins>键名</ins><ins>键值</ins></dd>')
           
           $(dl).append('<button type="button" class="layui-btn layui-btn-sm field-add"><i class="layui-icon layui-icon-addition"></i>添加</button>');
           $(el).parent().append(dl)
           $(dl).on("click","button",function(){
            if($(this).hasClass("field-add")){
                obj.append();
            }else if($(this).hasClass("field-remove")){
                $(this).parents("dd").remove();
            }
        })
       },
       append:function(data){
        var dd = $("<dd></dd>");
        $(dd).append('<input type="text" name="'+obj.config.name+'['+obj.index+'][key]"   size="10" class="layui-input"/>')
        $(dd).append('<input type="text" name="'+obj.config.name+'['+obj.index+'][value]"   size="10" class="layui-input"/> ')
        $(dd).append('<button type="button" class="layui-btn layui-btn-sm field-remove"><i class="layui-icon layui-icon-close"></i></button>')
        $(dd).append('<button type="button" class="layui-btn layui-btn-sm"><i class="layui-icon layui-icon-slider"></i></button>')
        $(obj.dl).find(".field-add").before(dd);
        obj.index++;
        $(obj.dl).attr('data.listidx',obj.index);
       },
    };

    exports('fieldList' , obj);
});