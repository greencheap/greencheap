<?php $view->script('system_comment_edit' , 'system/comment:app/bundle/edit.js' , 'vue') ?>

<section id="app" v-cloak>
    <div class="uk-clearfix">
        <div class="uk-align-right">
            <a :href="$url.route('admin/comment/page')" class="uk-button uk-button-default">{{ 'Cancel' | trans }}</a>
            <a :href="comment.url" class="uk-button uk-button-secondary" target="_blank">{{ 'Open New Window' | trans }}</a>
            <button @click.prevent="save" class="uk-button uk-button-primary">{{ 'Save' | trans }}</button>
        </div>
        <h3 class="uk-margin-remove">{{ 'Edit Comment' | trans }}</h3>
    </div>
    <div uk-grid>
        <div class="uk-width-expand@m">
            <article class="uk-comment uk-margin">
                <header class="uk-comment-header">
                    <div class="uk-grid-medium uk-flex-middle" uk-grid>
                        <div class="uk-width-auto">
                            <img class="uk-comment-avatar" width="50" v-gravatar="comment.author.email">
                        </div>
                        <div class="uk-width-expand">
                            <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" :href="$url.route('admin/user/edit', { id: comment.user_id })">{{ comment.author.name }}</a></h4>
                            <ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
                                <li><a >{{ comment.created | date }}</a></li>
                                <li><a @click.prevent="changeEdit">{{ 'Edit' | trans }}</a></li>
                            </ul>
                        </div>
                    </div>
                </header>
                <div class="uk-comment-body">
                    <textarea class="uk-width-expand uk-height-small uk-textarea" v-model="comment.content" :disabled="!isEdit"></textarea>
                </div>
            </article>
        </div>
        <div class="uk-width-medium@m">
            <div class="uk-margin">
                <label>{{ 'Status' | trans }}</label>
                <div class="uk-form-controls">
                    <select class="uk-select" v-model="comment.status">
                        <option v-for="(status , id) in statuses" :value="id">{{ status }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</section>