<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', $intl->getLocaleTag()) ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?= $view->render('head') ?>
    <?php $view->script('system_theme','system/theme:app/bundle/theme.js') ?>
    <?php $view->script('system_theme_header','system/theme:app/bundle/header.js' , 'vue') ?>
</head>
<body>
    <header id="header">
        <nav class="uk-navbar-container">
            <div class="uk-container uk-visible@s" uk-navbar>
                <div class="uk-navbar-left">
                    <a class="uk-navbar-toggle" uk-icon="icon:align-text-left;ratio:2"></a>
                    <div class="uk-navbar-item">
                        <span class="uk-text-lead">DASHBOARD</span>
                    </div>
                </div>
                <div class="uk-navbar-right">
                    <ul class="uk-navbar-item uk-grid">
                        <li><a @click="onDarkMode" :uk-icon="`icon:${darkMode ? 'sun':'moon'};ratio:1.3`" :uk-tooltip="`${darkMode ? 'Light Mode':'Dark Mode'}` | trans"></a></li>
                        <li><a :href="$url('/')" uk-icon="icon:robot;ratio:1.3" :title="'Help'|trans" uk-tooltip></a></li>
                        <li><a :href="$url('/')" uk-icon="icon:laptop;ratio:1.3" :title="'Visit Site'|trans" uk-tooltip></a></li>
                        <li><a :href="$url('user/logout')" uk-icon="icon:logout;ratio:1.3" :title="'Logout'|trans" uk-tooltip></a></li>
                        <li><a :href="$url('admin/user/edit' , {id:user.id})"><img :src="$url(user.avatar)" class="uk-border-circle" width="30px" height="30px"></a></li>
                    </ul>
                </div>
            </div>

            <div class="uk-container uk-hidden@s" uk-navbar>
                <div class="uk-navbar-left">
                    <a class="uk-navbar-toggle" uk-icon="icon:align-text-left;ratio:2"></a>
                </div>
                <div class="uk-navbar-center">
                    <div class="uk-navbar-item">
                        <span class="uk-text-bold">DASHBOARD</span>
                    </div>
                </div>
                <div class="uk-navbar-right">
                    <a :href="$url('admin/user/edit' , {id:user.id})" class="uk-navbar-item"><img :src="$url(user.avatar)" class="uk-border-circle" width="30px" height="30px"></a>
                </div>
            </div>
        </nav>
    </header>

    <section class="uk-section">
        <div class="uk-container">
            <button class="uk-button uk-button-primary">Merhaba Dünya</button>
        </div>
    </section>
</body>
</html>
