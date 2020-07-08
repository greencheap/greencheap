<?php $view->script('panel-finder') ?>

<div id="storage">
    <panel-finder root="<?= htmlentities($root) ?>" mode="<?= $mode ?>"></panel-finder>
</div>

<script>
    new Vue({name: 'storage', el: '#storage'})
</script>
