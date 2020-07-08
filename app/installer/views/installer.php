<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <link href="app/system/modules/theme/favicon.ico" rel="shortcut icon" type="image/x-icon">
        <link href="app/system/modules/theme/apple_touch_icon.png" rel="apple-touch-icon-precomposed">
        <?php $view->style('installer-css', 'app/installer/assets/css/installer.css') ?>
        <?php $view->script('vue-dist1', 'app/assets/vue/dist/vue.js') ?>
        <?php $view->script('vue1', 'app/system/app/bundle/vue.js', ['uikit', 'uikit-icons', 'vue-dist1', 'lodash', 'locale']) ?>
        <?php $view->script('installer', 'app/installer/app/bundle/installer.js', ['vue1', 'uikit']) ?>
        <?= $view->render('head') ?>
    </head>
    <body>
        <div id="installer" v-cloak></div>
    </body>
</html>
