<?php

use GreenCheap\Application as App;

App::config()->set('system/site', App::config('system/site')->merge([
    'frontpage' => 1, 'view' => ['logo' => 'storage/greencheap-logo.svg']
]));

App::db()->insert('@system_config', ['name' => 'theme-one', 'value' => '{"logo_contrast":"storage\\/greencheap-logo-contrast.svg","_menus":{"main":"main","offcanvas":"main"},"_positions":{"hero":[1],"footer":[2]},"_widgets":{"1":{"title_hide":true,"title_size":"uk-panel-title","alignment":true,"html_class":"","panel":""},"2":{"title_hide":true,"title_size":"uk-panel-title","alignment":"true","html_class":"","panel":""}},"_nodes":{"1":{"title_hide":true,"title_large":false,"alignment":true,"html_class":"","sidebar_first":false,"hero_image":"storage\\/home-hero.jpg","hero_viewport":true,"hero_contrast":true,"navbar_transparent":true}}}']);

App::db()->insert('@system_node', ['priority' => 1, 'status' => 1, 'title' => 'Home', 'slug' => 'home', 'path' => '/home', 'link' => '@page/1', 'type' => 'page', 'menu' => 'main', 'data' => "{\"defaults\":{\"id\":1}}"]);

App::db()->insert('@system_node', ['priority' => 2, 'status' => 1, 'title' => 'Blog', 'slug' => 'blog', 'path' => '/blog', 'link' => '@blog', 'type' => 'blog', 'menu' => 'main']);

App::db()->insert('@system_node', ['priority' => 1, 'status' => 1, 'title' => 'About Us', 'slug' => 'about-us', 'path' => '/about-us', 'link' => '@page/2', 'type' => 'page', 'menu' => 'main', 'data' => "{\"defaults\":{\"id\":1}}"]);

App::db()->insert('@system_widget', ['title' => 'Hello, I\'m GreenCheap', 'type' => 'system/text', 'status' => 1, 'nodes' => 1, 'data' => '{"content":"<h1 class=\"uk-heading-large uk-margin-large-bottom\">Hello, I\'m GreenCheap,<br class=\"uk-visible@s\"> your new favorite CMS.<\/h1>\n\n<a class=\"uk-button uk-button-default uk-button-large\" href=\"http:\/\/www.greencheap.net\">Get started<\/a>"}']);

App::db()->insert('@system_widget', ['title' => 'Powered by GreenCheap', 'type' => 'system/text', 'status' => 1, 'data' => '{"content": "<div class=\"uk-text-center\">\n    <ul class=\"uk-grid-large uk-flex uk-flex-center\" uk-grid>\n        <li>\n            <a href=\"https://twitter.com/greencheapnet\" uk-icon=\"icon:twitter;ratio:1.5\" title=\"Twitter GreenCheap Account\" target=\"_blank\"></a>\n        </li>\n        <li>\n            <a href=\"https://github.com/greencheap\" uk-icon=\"icon:github;ratio:1.5\" title=\"Github GreenCheap Page\" target=\"_blank\"></a>\n        </li>\n        <li>\n            <a href=\"https://greencheap.net\" uk-icon=\"icon:greencheap;ratio:1.5\" title=\"GreenCheap Official Website\" target=\"_blank\"></a>\n        </li>\n    </ul>\n    <p>\n        ♥ Made by **GreenCheap** with love and caffeine.\n    </p>\n</div>", "markdown": true}']);

App::db()->insert('@system_page', [
    'title' => 'Home',
    'content' => "<div class=\"uk-width-3-4@m uk-container\">\n    \n<h3 class=\"uk-h1 uk-margin-large-bottom\">Uniting fresh design and clean code<br class=\"uk-visible@s\"> to create beautiful websites.</h3>\n    \n<p class=\"uk-width-2-3@m uk-container\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\n    \n</div>",
    'data' => '{"title":true}'
]);

App::db()->insert('@system_page', [
    'title' => 'About Us',
    'content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In enim ante, dignissim id consequat id, pulvinar at enim. Pellentesque orci tellus, eleifend in urna id, egestas faucibus justo. Nunc fringilla dui ut magna tempus venenatis. Donec nisl sapien, faucibus ac faucibus sed, tincidunt rhoncus lacus. Curabitur gravida feugiat est, sit amet convallis lorem facilisis a. Pellentesque et lacinia libero. Morbi est justo, aliquet eget nibh eget, sollicitudin egestas odio. Curabitur feugiat sit amet tellus ultricies malesuada. Maecenas vitae ornare metus. Morbi mattis metus enim, vitae rutrum lectus porta eget. Sed elementum feugiat feugiat. Nullam porttitor placerat ligula in aliquet. In eleifend varius sagittis.

    Aliquam pellentesque ligula vel tortor cursus, eget feugiat lacus lacinia. Duis facilisis dolor vel eros blandit sagittis. Curabitur sit amet fringilla felis. Pellentesque vel lectus diam. Pellentesque ipsum enim, blandit ut nunc at, semper finibus erat. Etiam sit amet tincidunt lectus. Vestibulum non fermentum felis, quis vehicula nisi. Duis hendrerit venenatis egestas. Maecenas eu egestas neque, sit amet malesuada nunc. Donec erat justo, commodo sed mattis ac, porta vitae metus. Quisque bibendum porta quam, non egestas urna tincidunt sed. Vivamus sagittis consequat turpis auctor tempor. Fusce quis elit ullamcorper, laoreet magna ut, laoreet purus.
    
    Nam scelerisque lacinia mollis. Etiam vehicula, ipsum nec malesuada faucibus, quam magna lobortis elit, pellentesque sagittis enim erat at justo. Vivamus diam turpis, hendrerit non tortor id, fringilla ullamcorper augue. Curabitur id erat fringilla, mattis ipsum vel, tristique libero. Duis sit amet nisi ac nibh placerat viverra. Donec et mattis ex. Curabitur ut suscipit velit. Etiam vestibulum blandit turpis, nec tristique erat. Quisque posuere, lorem ultricies auctor vehicula, libero turpis malesuada ex, at ornare dui elit at metus. Quisque dapibus vulputate diam sed sodales. Nam a metus lorem. Aenean eget ipsum commodo, mattis neque ac, aliquet mi. Nunc in tortor a tellus ullamcorper interdum. Vestibulum venenatis odio arcu, ac sollicitudin tellus laoreet a. Curabitur in lacus et ex eleifend posuere sed in tortor. Proin ex enim, imperdiet in convallis eu, auctor ut enim.
    
    Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Etiam at nisi elementum, aliquet libero non, porta nulla. Quisque ac efficitur lectus. Nulla pellentesque quam elit, vel condimentum purus commodo quis. Nunc tempor nisl vel velit cursus sagittis. Nam interdum faucibus libero, sed tincidunt purus dignissim eu. Curabitur risus justo, auctor vitae euismod et, vestibulum vel enim. Nulla facilisi. Aenean erat mi, gravida eget nulla eu, porttitor bibendum nunc. Aliquam a risus urna.
    
    Suspendisse tristique urna in consequat tempor. Quisque dictum orci id urna tristique cursus. Fusce accumsan, eros ac pretium dictum, velit nulla pharetra est, id sagittis eros massa vel nibh. Donec placerat justo quis dolor feugiat tristique. Duis pretium auctor diam, ac suscipit orci euismod at. Cras volutpat orci in lectus elementum tincidunt. Nulla est arcu, elementum id facilisis vitae, egestas eu lorem. Nulla volutpat, nisi at bibendum porttitor, nulla quam vestibulum risus, ut hendrerit nunc mauris non orci. Cras odio justo, dignissim nec malesuada id, laoreet eget metus. Nam et dolor id arcu tristique dignissim a vel sem. Phasellus velit eros, ullamcorper eu rhoncus vel, efficitur tempus nulla. Integer porta pellentesque vestibulum. ",
    'data' => '{"title": true, "markdown": true}'
]);

if (App::db()->getUtility()->tableExists('@blog_post')) {
    App::db()->insert('@blog_post', [
        'user_id' => 1,
        'slug' => 'hello-greencheap',
        'title' => 'Hello GreenCheap',
        'status' => 2,
        'date' => date('Y-m-d H:i:s'),
        'modified' => date('Y-m-d H:i:s'),
        'content' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
        'excerpt' => '',
        'comment_status' => 1,
        'data' => '{"title":null,"markdown":true}'
    ]);
}
