<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:wght@200;300;400;500&display=swap" rel="stylesheet">
    <title>Logger Console</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing: border-box;
        }
        #app{
            width: 100%;
            height: 100vh;
            background: #2f3542;
            font-family: 'Source Code Pro', monospace;
            overflow: hidden;
        }
        .top {
            background: #b7b7b7;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color:#3b4651;
        }
        #console{
            color:#ababab;
            padding:8px;
            height: 100%;
            overflow-y: scroll;
            padding-bottom: 50px;
        }
        .console-command{
            font-size: 14px;
            font-weight: 300;
        }
        .line{
            color:#009d00;
        }
        .info .level{
            color:#1299DA;
        }
        .error .level{
            color:#b93232;
        }
        .debug .level{
            color:#27ae60;
        }
        .warn .level{
            color:#FF9900;
        }
        #app .console-command:hover{
            background: #fff;
            color:#000 ;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="top">
        <h3 class="title">Logger Console</h3>
        <div class="logo">
            <a href="#"><img src="<?php buildResourceURI('assets/cpanel/img/logo.png'); ?>" height="25" alt=""></a>
        </div>
    </div>
    <p class="console-command info " id="cm-dumy" style="display: none">
        <span class="datetime">[22:50:35 28-Nov-2019]</span>
        <span class="level ">[INFO]</span>
        <span class="file">[C:\xampp\htdocs\cloudmonster\app\handlers\CloudUpload.class.php]</span>[<span class="line">[1]</span>]  <span class="seprt">>>></span>
        <span class="msg "> Fatal error: class not found.  {{ ' . App\Helper . ' }}</span>
    </p>
    <div id="console">
    </div>
</div>
<script src="<?php buildResourceURI('assets/cpanel/libs/jquery/dist/jquery.min.js'); ?>"></script>
<script>
    const ROOT = '<?php _e(siteurl()) ?>';

    $(document).ready(function(){

        updateConsole();

    });





    function updateConsole(offset = 0){
        $.ajax({

            url: ROOT + '/cpanel/logger/get-log',
            data: {
                offset: offset
            },
            type: 'get',
            success: function(response) {

                if(response.success){
                    if('data' in  response){
                        let data = response.data;

                        if(data.length !== undefined && data.length > 0){

                            offset += data.length;
                            appendToConsole(data);

                        }
                    }

                    setTimeout(function(){ updateConsole( offset ) }, 5000);
                }

            },
            complete: function() {

                setTimeout(function(){ gotoBottom('console'); }, 1000);
            }
        });
    }




    function appendToConsole(data){

        data.forEach(function (item, index) {

            let commandNode = $("#cm-dumy").clone();

            commandNode.find('.datetime').text('['+item.timestamp+']');
            commandNode.find('.file').text('['+item.path+']');
            commandNode.find('.msg').text(''+item.message+'');
            commandNode.find('.level').text('['+item.level+']');
            commandNode.find('.line').text(''+item.line+'');

            commandNode.addClass(item.level);

            commandNode.show();
            commandNode.removeAttr('id');

            $("#console").append(commandNode.prop('outerHTML'));

        });


    }


    function gotoBottom(id){
        var element = document.getElementById(id);
        element.scrollTop = element.scrollHeight - element.clientHeight;
    }

</script>
</body>
</html>