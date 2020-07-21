<?php $view->script('categories' , 'system/categories:app/bundle/categories.js' , 'vue') ?>

<section id="app" v-cloak>
    {{'Hello World'}}
    <a class="uk-button uk-button-primary" :href="$url.route('admin/categories/edit')">Ekle</a>
</section>