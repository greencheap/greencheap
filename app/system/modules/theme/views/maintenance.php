<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $view->render('head') ?>
    <?php $view->style('theme', 'system/theme:css/theme.css') ?>
</head>

<body>
    <div class="uk-section-primary">
        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center tm-background uk-height-viewport">
            <div class="uk-text-center uk-width-xlarge" uk-height-viewport="expand:true">
                <img width="800px" src="<?= $view->url()->getStatic($logo) ?>" alt="GreenCheap">
                <div class="uk-panel">
                    <h1 class="uk-h3 uk-margin-small uk-text-bold"><?= __('We are currently under construction') ?></h1>
                    <p class="uk-margin"><?= $message ?></p>
                </div>
            </div>

            <div class="uk-position-absolute uk-position-bottom-center uk-position-medium">
                <p class="developmentSignature">&hearts; Made by <a target="_blank" href="https://greencheap.net">GreenCheap</a> with love and caffeine.</p>
            </div>
        </div>
    </div>
</body>

</html>