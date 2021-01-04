<?php if($app['config']->get('system/user')->get('registration') !== 'admin'): ?>
    <?php 
    $view->data('$comment_service', array_merge([
        'config' => $config ?? $app['comment.config'],
        'draft' => $app['comment.draft'],
        'user_id' => $app['user']->id,
        'user_config' => $app['config']->get('system/user'),
        'getCommentStatus' => $app['config']->get('system/comment')->get('approved_admin') && !$app['user']->isAdministrator() ? 0:1,
        'isAuthCanComment' => ($app['user']->hasPermission('comment: write comment') && $app['user']->isAuthenticated()) || $app['user']->isAdministrator()
    ] , compact('service')));

    $view->script('comment_service' , 'system/comment:app/bundle/service.js' , ['uikit' , 'vue']);
    $view->style('comment_service' , 'system/comment:css/comment.css');
    ?>
    <div id="comment_service" class="uk-margin-large-top" v-cloak>
        <div v-if="!isAuthCanComment">
            <p>{{ 'You must be logged in to post a comment.' | trans }} <a :href="$url.route('user/login' , {redirect: comment.data.type_url})">{{ 'Sign In' | trans }}</a></p>
        </div>
        <form v-if="isAuthCanComment" @submit.prevent="sendComment">
            <h3 v-if="!comment.parent_id">{{ 'Write Comment' | trans}}</h3>
            <h3 v-if="comment.parent_id">{{ 'Reply to Comment' | trans}}</h3>
            <div v-if="msg" class="uk-alert-success" uk-alert>{{msg}}</div>
            <div class="uk-margin">
                <Mentionable
                    v-if="config.attribute_people"
                    :keys="['@']"
                    :items="users"
                    offset="3"
                >
                    <textarea class="uk-textarea uk-width-expand uk-height-small" :placeholder=" 'Your Message' | trans " style="resize:none" v-on:keyup.alt.enter="sendComment" v-model="comment.content"></textarea>
                
                    <template #no-result>
                        <div class="uk-card uk-card-default uk-card-body uk-card-small">
                            {{ 'User not found' | trans }}
                        </div>
                    </template>

                    <template #item-@="{ key, item }">
                        <div class="tm-mention uk-flex uk-flex-middle">
                            <div class="uk-margin-small-right">
                                <img width="30" class="uk-border-circle" v-gravatar="item.email">
                            </div>
                            <div>
                            {{ item.value }}
                            </div>
                        </div>
                    </template>
                </Mentionable>
                <textarea v-else class="uk-textarea uk-width-expand uk-height-small" v-on:keyup.alt.enter="sendComment" :placeholder=" 'Your Message' | trans " style="resize:none" v-model="comment.content"></textarea>
                <p id="commentTextAreaAlert" style="display:none" class="uk-text-danger uk-margin-small uk-text-small">{{ 'You have to write a message to comment' | trans }}.</p>
            </div>
            <div class="uk-margin uk-text-right">
                <button v-if="comment.parent_id" type="button" @click.prevent="cancelReply" class="uk-button uk-button-default">{{ 'Cancel Reply' | trans }}</button>
                <button type="submit" class="uk-button uk-button-primary">{{ 'Send' | trans }}</button>
                <div class="uk-align-left uk-visible@m">
                    <span class="uk-text-meta">{{ 'Alt + Enter key saves your comment' | trans}}</span>
                </div>
            </div>
        </form>

        <div v-if="count" class="uk-margin-large-top">
            <div>
                <h3>{{ '{1} %count% Comment|]1,Inf[ %count% Comments' | transChoice(count, {count:count}) }}</h3>
            </div>
            <ul class="uk-comment-list uk-margin-large-top">
                <li v-for="com in comments">
                    <article class="uk-comment">
                        <header class="uk-comment-header">
                            <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                <div class="uk-width-auto">
                                    <img class="uk-comment-avatar uk-border-circle" width="50" height="50" :alt="com.author.name" v-gravatar="com.author.email">
                                </div>
                                <div class="uk-width-expand">
                                    <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#">{{com.author.name}}</a></h4>
                                    <ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
                                        <li><a href="#">{{ com.created | relativeDate }}</a></li>
                                        <li v-if="isAuthCanComment"><a href="#comment_service" uk-scroll="offset:200" @click="setReply(com.id)">{{ 'Reply' | trans }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </header>
                        <div class="uk-comment-body" v-html="com.content"></div>
                    </article>
                    <ul v-if="com.hasChildComment">
                        <li v-for="(child , idx) in com.hasChildComment" :key="idx">
                            <article class="uk-comment">
                                <header class="uk-comment-header">
                                    <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                        <div class="uk-width-auto">
                                            <img class="uk-comment-avatar uk-border-circle" width="50" height="50" :alt="child.author.name" v-gravatar="child.author.email">
                                        </div>
                                        <div class="uk-width-expand">
                                            <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#">{{child.author.name}}</a></h4>
                                            <ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
                                                <li><a href="#">{{ child.created | relativeDate }}</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </header>
                                <div class="uk-comment-body" v-html="child.content"></div>
                            </article>
                        </li>
                    </ul>
                </li> 
            </ul>
        </div>
    </div>
<?php endif ?>