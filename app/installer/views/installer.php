<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <link href="app/system/modules/theme/favicon.ico" rel="shortcut icon" type="image/x-icon">
        <link href="app/system/modules/theme/apple_touch_icon.png" rel="apple-touch-icon-precomposed">
        <?php $view->script('system_theme','app/system/modules/theme/app/bundle/theme.js' , ['installer']) ?>
        <?php $view->script('installer', 'app/installer/app/bundle/installer.js', ['vue', 'uikit']) ?>
        <?= $view->render('head') ?>
    </head>
    <body>
        <section class="uk-grid-collapse" uk-grid>
            <div class="uk-width-2-3@m uk-width-1-2@s uk-visible@s uk-background-primary">
                <div class="uk-position-relative uk-visible-toggle" tabindex="-1" uk-slideshow="ratio: false;autoplay:true">
                    <ul class="uk-slideshow-items" uk-height-viewport="">
                        <li>
                            <img data-src="<?= $image['image'] ?>" alt="<?= $image['information']['label'] ?>" uk-img uk-cover>
                            <div class="uk-position-bottom-left uk-position-small uk-text-small">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="uk-margin-small-right">
                                        <a href="<?= $image['author']['profileUrl'] ?>" target="_blank">
                                            <img data-src="<?= $image['author']['avatarUrl'] ?>" width="40" class="uk-border-circle" uk-img>
                                        </a>
                                    </div>
                                    <div class="uk-light">
                                        <a href="<?= $image['author']['profileUrl'] ?>" target="_blank">
                                            <h4 class="uk-h6 uk-margin-remove uk-text-bold"><?= $image['author']['fullName'] ?></h4>
                                        </a>
                                        <span class="uk-display-block"><i uk-icon="icon:flag-alt;ratio:0.8"></i> <?= $image['information']['locationName'] ?></span> 
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-light" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                    <a class="uk-position-center-right uk-position-small uk-hidden-hover uk-light" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
                    </div>
                </div>
            </div>
            <div class="uk-width-1-3@m uk-width-1-2@s uk-flex uk-flex-center uk-flex-middle" uk-height-viewport="expand:true">
                <div class="uk-width-expand uk-padding uk-text-center">
                    <div id="installer" v-cloak></div>
                </div>
            </div>
        </section>
    </body>
</html>
