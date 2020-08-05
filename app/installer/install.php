<?php

use GreenCheap\Application as App;

App::config()->set('system/site', App::config('system/site')->merge([
    'frontpage' => 1, 'view' => ['logo' => 'storage/greencheap-logo.svg']
]));

App::db()->insert('@system_config', ['name' => 'theme-one', 'value' => '{"logo_contrast":"storage\\/greencheap-logo-contrast.svg","_menus":{"main":"main","offcanvas":"main"},"_positions":{"hero":[1],"footer":[2]},"_widgets":{"1":{"title_hide":true,"title_size":"uk-panel-title","alignment":true,"html_class":"","panel":""},"2":{"title_hide":true,"title_size":"uk-panel-title","alignment":"true","html_class":"","panel":""}},"_nodes":{"1":{"title_hide":true,"title_large":false,"alignment":true,"html_class":"","sidebar_first":false,"hero_image":"storage\\/home-hero.jpg","hero_viewport":true,"hero_contrast":true,"navbar_transparent":true}}}']);

App::db()->insert('@system_node', ['priority' => 1, 'status' => 1, 'title' => 'Home', 'slug' => 'home', 'path' => '/home', 'link' => '@page/1', 'type' => 'page', 'menu' => 'main', 'data' => "{\"defaults\":{\"id\":1}}"]);

App::db()->insert('@system_node', ['priority' => 3, 'status' => 1, 'title' => 'Blog', 'slug' => 'blog', 'path' => '/blog', 'link' => '@blog', 'type' => 'blog', 'menu' => 'main']);

App::db()->insert('@system_widget', ['title' => 'Hello, I\'m GreenCheap', 'type' => 'system/text', 'status' => 1, 'nodes' => 1, 'data' => '{"content":"<h1 class=\"uk-heading-large uk-margin-large-bottom\">Hello, I\'m GreenCheap,<br class=\"uk-visible@s\"> your new favorite CMS.<\/h1>\n\n<a class=\"uk-button uk-button-default uk-button-large\" href=\"http:\/\/www.greencheap.net\">Get started<\/a>"}']);

App::db()->insert('@system_widget', ['title' => 'Powered by GreenCheap', 'type' => 'system/text', 'status' => 1, 'data' => '{"content": "<div class=\"uk-text-center\">\n    <ul class=\"uk-grid-large uk-flex uk-flex-center\" uk-grid>\n        <li>\n            <a href=\"https://twitter.com/greencheapnet\" uk-icon=\"icon:twitter;ratio:1.5\" title=\"Twitter GreenCheap Account\" target=\"_blank\"></a>\n        </li>\n        <li>\n            <a href=\"https://github.com/greencheap\" uk-icon=\"icon:github;ratio:1.5\" title=\"Github GreenCheap Page\" target=\"_blank\"></a>\n        </li>\n        <li>\n            <a href=\"https://greencheap.net\" uk-icon=\"icon:greencheap;ratio:1.5\" title=\"GreenCheap Official Website\" target=\"_blank\"></a>\n        </li>\n    </ul>\n    <p>\n        ♥ Made by **GreenCheap** with love and caffeine.\n    </p>\n</div>", "markdown": true}']);

App::db()->insert('@system_page', [
    'title' => 'Home',
    'content' => "<div class=\"uk-width-3-4@m uk-container\">\n    \n<h3 class=\"uk-h1 uk-margin-large-bottom\">Uniting fresh design and clean code<br class=\"uk-visible@s\"> to create beautiful websites.</h3>\n    \n<p class=\"uk-width-2-3@m uk-container\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\n    \n</div>",
    'data' => '{"title":true}'
]);

if (App::db()->getUtility()->tableExists('@blog_post')) {
    App::db()->insert('@blog_post', [
        'user_id' => 1,
        'slug' => 'hello-greencheap',
        'title' => 'Hello GreenCheap',
        'status' => 3,
        'date' => date('Y-m-d H:i:s'),
        'modified' => date('Y-m-d H:i:s'),
        'content' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
        'excerpt' => '',
        'data' => '{"title":null,"markdown":true}'
    ]);
}
