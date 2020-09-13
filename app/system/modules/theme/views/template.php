<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', $intl->getLocaleTag()) ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?php $view->style('theme', 'system/theme:css/theme.css') ?>
    <?php $view->script('theme', 'system/theme:js/theme.js', ['vue', 'marked']) ?>
    <?= $view->render('head') ?>
</head>

<body>
    <div id="sidebar" class="gc-sidebar uk-section uk-padding-remove uk-section-muted">
        <div class="gc-sidebar-height-xsmall uk-flex uk-flex-middle uk-flex-center">
            <a :href="$url('admin/user/edit' , {id:user.id})" class="uk-flex">
                <img :src="$url(user.avatar)" class="uk-border-circle" width="50px" height="50px">
                <div class="uk-margin-small-left">
                    <h4 class="uk-margin-remove uk-h6 uk-text-bold">{{user.name}}</h4>
                    <span class="uk-display-block gc-font-small uk-text-muted">{{user.email}}</span>
                </div>
            </a>
        </div>
        <div class="gc-sidebar-height-auto uk-flex uk-flex-middle" v-cloak>
            <ul class="uk-nav uk-nav-default gc-nav">
                <li v-for="(nav , id) in navs">
                    <a :href="nav.url" :class="{'gc-nav-active': nav.active }">
                        <i><img :src="nav.icon" width="20px" height="20px" uk-svg></i>
                        <span>{{nav.label | trans}}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="gc-sidebar-height-small uk-flex uk-flex-middle uk-flex-center" v-cloak>
            <ul class="uk-nav uk-nav-default gc-nav">
                <li>
                    <a :href="$url('/')" target="_blank">
                        <i><img :src="$url('app/system/modules/theme/images/icons/visit.svg')" width="20px" height="20px" uk-svg></i>
                        <span>{{ 'Visit Site' | trans }}</span>
                    </a>
                </li>
                <li>
                    <a href="https://greencheap.net" target="_blank">
                        <i><img :src="$url('app/system/modules/theme/images/icons/help.svg')" width="20px" height="20px" uk-svg></i>
                        <span>{{ 'Help' | trans }}</span>
                    </a>
                </li>
                <li>
                    <a :href="$url('user/logout')">
                        <i><img :src="$url('app/system/modules/theme/images/icons/lock.svg')" width="20px" height="20px" uk-svg></i>
                        <span>{{ 'Logout' | trans }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="gc-wrapper">
        <header id="navbar" class="uk-navbar-container" v-cloak>
            <nav style="padding:0px 10px" uk-navbar>
                <div class="uk-navbar-left">
                    <div class="uk-navbar-item uk-inline">
                        <button class="uk-button uk-button-default uk-button-large gc-button-icon">
                            <i class="uk-icon uk-icon-image" :style="'background-image:url('+$url('/app/system/modules/theme/images/icons/menu.svg')+');'"></i>
                        </button>
                        <div uk-drop="mode: click;animation: uk-animation-slide-top-small; duration: 300">
                            <div v-if="!navs" class="gc-border-radius uk-text-center uk-width-expand uk-padding uk-box-shadow-medium uk-background uk-background-default">
                                <h3 class="uk-h5 uk-text-center">{{ 'There are no installed applications. You can install the application you want from the market.' | trans }}</h3>
                                <a :href="$url.route('admin/system/marketplace')" class="uk-button uk-button-primary uk-button-small">{{ 'Go To Marketplace' | trans }}</a>
                            </div>
                            <div v-else class="gc-border-radius uk-width-expand uk-padding-small uk-box-shadow-medium uk-background uk-background-default">
                                <div class="uk-grid-collapse uk-child-width-1-3" uk-grid>
                                    <div v-for="(nav , id) in navs" :key="id">
                                        <a :href="$url(nav.url)" :class="{'gc-navbar-menu-item-active':nav.active}" class="gc-navbar-menu-item uk-flex uk-flex-center">
                                            <div>
                                                <img :src="nav.icon">
                                                <span class="uk-text-center uk-display-block uk-margin-small-top">{{nav.label | trans}}</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="uk-navbar-right">
                    <div class="uk-navbar-item uk-inline">
                        <update />
                    </div>

                    <!--<div class="uk-navbar-item uk-inline">
                        <notifications />
                    </div>-->

                    <div class="uk-navbar-item uk-hidden@m">
                        <a href="#mobile" uk-toggle class="uk-button uk-button-default uk-button-large gc-button-icon">
                            <i class="uk-icon uk-icon-image" style="background-image:url(/app/system/modules/theme/images/icons/align.svg);"></i>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="uk-section uk-section-primary uk-margin uk-light" style="padding:20px 20px" v-if="subnav.length">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-medium@s uk-flex uk-flex-left@s uk-flex-center">
                        <h1 class="uk-h3">{{title | trans}}</h1>
                    </div>
                    <div class="uk-width-expand@s uk-flex uk-flex-right@s uk-flex-center uk-flex-middle">
                        <ul class="uk-subnav">
                            <li v-for="(sub , id) in subnav" :class="{'uk-active':sub.active}"><a class="uk-text-capitalize" :href="$url(sub.url)">{{sub.label | trans}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>
        <div class="uk-section uk-padding-remove" style="margin-top:10px">
            <div style="padding:0px 20px">
                <?= $view->render('content') ?>
            </div>
        </div>
    </div>
    
    <div id="mobile" uk-offcanvas>
        <div class="uk-offcanvas-bar">
            <button class="uk-offcanvas-close" type="button" uk-close></button>
            <a :href="$url('admin/user/edit' , {id:user.id})" class="uk-flex">
                <img :src="$url(user.avatar)" class="uk-border-circle" width="50px" height="50px">
                <div class="uk-margin-small-left">
                    <h4 class="uk-margin-remove uk-h6 uk-text-bold">{{user.name}}</h4>
                    <span class="uk-display-block gc-font-small uk-text-muted">{{user.email}}</span>
                </div>
            </a>
            <div class="uk-position-center-left uk-position-center-right">
                <ul class="uk-nav uk-nav-default gc-offcanvas">
                    <li v-for="(nav , id) in navs">
                        <a :href="nav.url" :class="{'gc-nav-active': nav.active }">
                            <i><img :src="nav.icon" width="20px" height="20px" uk-svg></i>
                            <span>{{nav.label | trans}}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="uk-position-bottom uk-flex uk-flex-middle uk-flex-center" v-cloak>
                <ul class="uk-nav uk-nav-default gc-nav">
                    <li>
                        <a :href="$url('/')" target="_blank">
                            <i><img :src="$url('app/system/modules/theme/images/icons/visit.svg')" width="20px" height="20px" uk-svg></i>
                            <span>{{ 'Visit Site' | trans }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://greencheap.net" target="_blank">
                            <i><img :src="$url('app/system/modules/theme/images/icons/help.svg')" width="20px" height="20px" uk-svg></i>
                            <span>{{ 'Help' | trans }}</span>
                        </a>
                    </li>
                    <li>
                        <a :href="$url('user/logout')">
                            <i><img :src="$url('app/system/modules/theme/images/icons/lock.svg')" width="20px" height="20px" uk-svg></i>
                            <span>{{ 'Logout' | trans }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>