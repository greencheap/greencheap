<?php $view->script("system_comment_settings", "system/comment:app/bundle/settings.js", "vue"); ?>

<form id="app" class="uk-form-horizontal" @submit.prevent="save" v-cloak>
    <div class="uk-clearfix">
        <div class="uk-margin">
            <button type="submit" class="uk-align-right uk-button uk-button-primary">{{ 'Save' | trans }}</button>
            <h1 class="tm-module-title">{{ 'Settings' | trans }}</h1>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label">{{ 'Threshold of comment' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <select class="uk-select uk-form-width-small" v-model="config.threshold_comment">
                <option value="">{{ 'Unlimited' | trans }}</option>
                <option value="-1 min">{{ '%val% Minute' | trans({val:1}) }}</option>
                <option value="-5 min">{{ '%val% Minute' | trans({val:5}) }}</option>
                <option value="-10 min">{{ '%val% Minute' | trans({val:10}) }}</option>
                <option value="-20 min">{{ '%val% Minute' | trans({val:20}) }}</option>
            </select>
            <p class="uk-text-small uk-text-muted uk-margin-remove">
                The user will be notified when there is a reply.
            </p>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label">{{ 'Comments per page' | trans }}</label>
        <div class="uk-form-controls">
            <input type="number" class="uk-input uk-form-width-small" min="1" disabled v-model.number="config.comments_per_page">
        </div>
    </div>
    
    <div class="uk-margin">
        <label class="uk-form-label">{{ 'Others' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <p><label><input type="checkbox" class="uk-checkbox uk-margin-small-right" v-model="config.notify_reply">{{ 'Notification of response' | trans }}</label></p>
            <p><label><input type="checkbox" class="uk-checkbox uk-margin-small-right" v-model="config.approved_admin">{{ 'Admin approved comment' | trans }}</label></p>
            <p><label><input type="checkbox" class="uk-checkbox uk-margin-small-right" v-model="config.markdown_enabled">{{ 'Markdown can be used' | trans }}</label></p>
            <p><label><input type="checkbox" class="uk-checkbox uk-margin-small-right" v-model="config.attribute_people">{{ 'Attribute people' | trans }}</label></p>
            <p><label><input type="checkbox" class="uk-checkbox uk-margin-small-right" v-model="config.to_quote">{{ 'To qoute' | trans }}</label></p>
        </div>
    </div>
</form>