<html lang="<?= str_replace('_', '-', $app['translator']->getLocale()) ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= $view->render('head') ?>
        <?php $view->script('system_theme','system/theme:app/bundle/theme.js' , ['uikit' , 'vue']) ?>
    </head>
    <body>
       

       <?= $view->render('footer') ?>
    </body>
</html>
