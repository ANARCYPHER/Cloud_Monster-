<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error Occurred</title>
    <style>
        *{
            margin:0;
            padding: 0;
            box-sizing: border-box;
        }
        body{
            background: #000000;
            font-family: sans-serif;
        }
        .page{
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            padding: 15px;
            width: 100%;
        }
        .error-card-title {
            background: #cd201f;
            padding: 8px 15px;
            color: #fff;
            border-radius: 3px 3px 0 0;
        }
        .error-card-body {
            padding: 15px;
            background: #fff;
            font-weight: bold;
        }
        pre{
            white-space: pre-wrap;
            white-space: -moz-pre-wrap;
            white-space: -pre-wrap;
            white-space: -o-pre-wrap;
            word-wrap: break-word;
        }
        .error-card-footer {
            padding: 8px 15px;
            background: #3b4651;
            color: #eeeff1;
            border-radius: 0 0  3px 3px;

        }
        .app-logo{
            text-align: right;
        }
        .error, .fatal{
            background: #cd201f;
        }
        .warning{
            background: #fab005;
        }
        .info{
            background: #45aaf2;
        }
        .debug{
            background: #17a2b8;
        }


    </style>
</head>
<body>

<div class="page">

    <div class="error-card">
        <div class="app-logo">
            <a href="#">
                <img src="<?php echo htmlspecialchars($logo); ?>" height="30" alt="">
            </a>
        </div>

        <div class="error-card-title <?=$level?>">
            <h3><?php echo htmlspecialchars(ucwords($title)); ?></h3>
        </div>
        <div class="error-card-body">


                                        <pre>
<?php echo htmlspecialchars($msg); ?>
                        </pre>


        </div>
        <div class="error-card-footer">
            <p>
                <b><?php echo htmlspecialchars($file); ?></b> on line <b><?php echo htmlspecialchars($line); ?></b>
            </p>



        </div>


    </div>

</div>

</body>
</html>