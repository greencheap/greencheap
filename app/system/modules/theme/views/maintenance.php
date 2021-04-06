<html lang="<?= str_replace("_", "-", $app["translator"]->getLocale()) ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= $view->render("head") ?>
        <?php $view->script("system_theme", "system/theme:app/bundle/theme.js", ["uikit", "vue"]); ?>
    </head>
    <body>
        <section class="uk-section uk-section-default tm-greencheap-concept uk-background-image uk-background-cover" data-src="<?= $view->url()->getStatic("app/system/modules/theme/assets/images/default-bg.svg") ?>" uk-img>
            <div class="tm-greencheap-content-wrapper">
                <div class="uk-text-center">
                    <img width="200" src="<?= $view->url()->getStatic($logo) ?>" alt="GreenCheap">
                    <div class="uk-panel uk-width-xlarge uk-margin">
                        <h1 class="uk-h3 uk-margin-small uk-text-bold uk-text-uppercase"><?= __("We are currently under construction") ?></h1>
                        <p class="uk-margin uk-text-italic"><?= $message ?></p>
                    </div>
                </div>
            </div>
        </section>
        <?= $view->render("footer") ?>
    </body>
</html>
