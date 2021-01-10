<!DOCTYPE html>
<html>

<head>
    <title>GreenCheap Installer Errors</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <script src="/app/assets/uikit/dist/js/uikit.min.js"></script>
    <script src="/app/assets/uikit/dist/js/uikit-icons.min.js"></script>
    <script src="/app/system/modules/theme/app/bundle/theme.js"></script>
</head>

<body>
    <section class="uk-section uk-section-default tm-greencheap-concept uk-background-image uk-background-cover" data-src="app/system/modules/theme/assets/images/default-bg.svg" uk-img>
        <div class="tm-greencheap-content-wrapper">
            <div class="uk-text-center">
                <img width="200" src="app/system/modules/theme/assets/images/greencheap-logo.svg" alt="GreenCheap">
                <div class="uk-panel uk-width-xlarge uk-margin">
                    <h1 class="uk-h3 uk-margin-small uk-text-bold uk-text-uppercase">System Requirements</h1>
                    <p class="uk-margin uk-text-italic">Please fix the following issues to proceed.</p>
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
    </section>
</body>

</html>
