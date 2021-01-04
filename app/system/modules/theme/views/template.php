<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', $intl->getLocaleTag()) ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?= $view->render('head') ?>
    <?php $view->script('system_theme','system/theme:app/bundle/theme.js' , ['uikit' , 'vue']) ?>
    <?php $view->script('system_theme_header','system/theme:app/bundle/header.js' , 'system_theme') ?>
</head>
<body>
    <header id="header">
        <nav class="uk-navbar-container">
            <div class="uk-container uk-visible@s" uk-navbar>
                <div class="uk-navbar-left">
                    <div class="uk-navbar-item uk-inline">
                        <a class="uk-navbar-toggle"><i uk-icon="icon:align-text-left;ratio:2" class="uk-margin-medium-right"></i><span class="uk-text-lead uk-text-uppercase">{{ title | trans }}</span></a>
                        <div class="tm-drop" uk-drop="offset:-50;mode:click">
                            <div class="uk-card uk-card-body uk-card-default">
                                <Navbar :navs="navs" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-navbar-right">
                    <ul class="uk-navbar-item uk-grid">
                        <li><a @click="onDarkMode" :uk-icon="`icon:${darkMode ? 'sun':'moon'};ratio:1.3`" :uk-tooltip="`${darkMode ? 'Light Mode':'Dark Mode'}` | trans"></a></li>
                        <li><a href="https://greencheap.net/docs" uk-icon="icon:robot;ratio:1.3" target="_blank" :title="'Help'|trans" uk-tooltip></a></li>
                        <li><a :href="$url('/')" uk-icon="icon:laptop;ratio:1.3" target="_blank" :title="'Visit Site'|trans" uk-tooltip></a></li>
                        <li><a :href="$url('user/logout')" uk-icon="icon:logout;ratio:1.3" :title="'Logout'|trans" uk-tooltip></a></li>
                        <li><a :href="$url('admin/user/edit' , {id:user.id})"><img :src="$url(user.avatar)" class="uk-border-circle" width="30px" height="30px"></a></li>
                    </ul>
                </div>
            </div>

            <div class="uk-container uk-hidden@s" uk-navbar>
                <div class="uk-navbar-left">
                    <a class="uk-navbar-toggle" href="#modal-mobile-menu" uk-toggle uk-icon="icon:align-text-left;ratio:2"></a>
                </div>
                <div class="uk-navbar-center">
                    <div class="uk-navbar-item">
                        <span class="uk-text-bold uk-text-uppercase">{{ title | trans }}</span>
                    </div>
                </div>
                <div class="uk-navbar-right">
                    <a :href="$url('admin/user/edit' , {id:user.id})" class="uk-navbar-item"><img :src="$url(user.avatar)" class="uk-border-circle" width="30px" height="30px"></a>
                </div>
            </div>
        </nav>
        <aside v-if="subnav.length > 0" class="uk-section uk-section-primary tm-section-2xsmall uk-visible@s">
            <div class="uk-container">
                <ul class="uk-tab">
                    <li v-for="(sub , id) in subnav" :key="id" :class="{'uk-active':sub.active}">
                        <a :href="$url(sub.url)">{{sub.label}}</a>
                    </li>
                </ul>
            </div>
        </aside>

        <div id="modal-mobile-menu" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog uk-height-viewport uk-padding-small">
                <ul class="tm-mobile-items">
                    <li><a @click="onDarkMode" :uk-icon="`icon:${darkMode ? 'sun':'moon'};ratio:1.6`" :uk-tooltip="`${darkMode ? 'Light Mode':'Dark Mode'}` | trans"></a></li>
                    <li><a href="https://greencheap.net" uk-icon="icon:robot;ratio:1.6" target="_blank" :title="'Help'|trans" uk-tooltip></a></li>
                    <li><a :href="$url('/')" uk-icon="icon:laptop;ratio:1.6" target="_blank" :title="'Visit Site'|trans" uk-tooltip></a></li>
                    <li><a :href="$url('user/logout')" uk-icon="icon:logout;ratio:1.6" :title="'Logout'|trans" uk-tooltip></a></li>
                    <li><a class="uk-modal-close-default" uk-icon="icon:close;ratio:1.6" :title="'Close'|trans" uk-tooltip></a></li>
                </ul>
                <div v-if="subnav.length" class="uk-margin">
                    <h5 class="uk-text-bold uk-h6 uk-text-uppercase" style="letter-spacing: 2px;">{{'Sub Menus' | trans}}</h5>
                    <ul class="uk-margin-top uk-nav-default" uk-nav>
                        <li v-for="(sub , id) in subnav" :key="id" :class="{'uk-active':sub.active}">
                            <a :href="$url.route(sub.url)">{{sub.label}}</a>
                        </li>
                    </ul>
                </div>
                <div class="uk-margin-large-top">
                    <h5 class="uk-text-bold uk-h6 uk-text-uppercase" style="letter-spacing: 2px;">{{'Applications' | trans}}</h5>
                    <div class="uk-margin-top">
                        <Navbar :navs="navs" />
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="uk-section uk-section-small">
        <div class="uk-container">
            <?= $view->render('content') ?>
        </div>
    </section>
</body>
</html>
