<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>跳转提示</title>
    <link href="/static/component/pear/css/pear.css" rel="stylesheet" />
    <link href="/static/admin/css/other/error.css" rel="stylesheet" />
    <style type="text/css">

        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333; }

    </style>
</head>
<body>
<div class="content">
    <img src="/static/admin/images/403.svg" alt="">
    <div class="content-r">
        <h1>403</h1>
        <p>抱歉，你无权访问该页面</p >
       <a id="href" href="<?php echo($url);?>"> <button class="pear-btn pear-btn-primary"><?php echo(strip_tags($msg));?></button></a>
        <br><br>
        <p class="jump">
            页面自动 跳转 <a id="href" href="<?php echo($url);?>"></a> 等待时间： <b id="wait"><?php echo($wait);?></b>
        </p>
    </div>

</div>
<script src="/static/component/layui/layui.js"></script>
<script src="/static/component/pear/pear.js"></script>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>