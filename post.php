<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


    <?php

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    $blog = htmlspecialchars($_POST['blog'], ENT_QUOTES);

    echo "お名前は".$name."で、ブログ名は".$blog."です。";

    ?>
</body>
</html>