<!DOCTYPE html>
<html>

<head>
    <title>GreenCheap Installer Errors</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <link href="app/system/modules/theme/css/theme.css" rel="stylesheet">
    <script src="app/system/modules/theme/js/theme.js"></script>
</head>

<body>
    <div class="uk-background-primary">
        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center tm-background uk-height-viewport">
            <div class="tm-container">
                <img class="uk-margin-bottom" src="app/system/assets/images/logo/logo-white.svg" alt="GreenCheap">
                <div class="uk-card uk-card-default uk-card-body">
                    <h1>System Requirements</h1>
                    <p>Please fix the following issues to proceed.</p>
                    <?php foreach ($failed as $req) : ?>
                        <p>
                            <span class="uk-label uk-label-danger">Error</span> <?php echo $req->getTestMessage() ?>
                        </p>
                        <p>
                            <span class="uk-label">Fix</span> <?php echo $req->getHelpHtml() ?>
                        </p>
                    <?php endforeach ?>
                </div>

            </div>
        </div>
    </div>
</body>

</html>