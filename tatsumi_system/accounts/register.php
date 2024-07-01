<!DOCTYPE html>
<html lang='ja'>
    <head>
        <meta charset="UFT-8">
        <title></title>
    </head>
    <body>
        <?php
        if($_POST) {
          echo "htmlからPOST送信を受け取りました";
        } else {
          echo "htmlからPOST送信受信に失敗しました";
        }
        ?>
    </body>

</html>