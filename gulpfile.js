var _ = require('lodash'),
    merge = require('merge-stream'),
    gulp = require('gulp');

/**
 * Copy required asset files to app/assets/** folder
 */
gulp.task('assets', function() {
    var modules_dirname = 'node_modules',
        config = {
            'app': { uikit: '*', vue: '*', flatpickr: '*', lodash: { files: 'lodash*.js', dest: '/dist' } },
            'app/system/modules/editor/app': { tinymce: '*', marked: '*', codemirror: '*' }
        };

    return merge.apply(null, _.map(config, (assets, module) => {
        var files, assets_dirname = module + '/assets';
        return _.map(assets, (cfg, asset) => {
            var source = modules_dirname + '/' + asset,
                dest = assets_dirname + '/' + asset;
            files = (typeof cfg === 'object' && cfg.files) ? source + '/' + cfg.files : source + '/' + '**';
            dest = (typeof cfg === 'object' && cfg.dest) ? dest + cfg.dest : dest;
            return gulp.src([files]).pipe(gulp.dest(dest));
        })
    }));
});

gulp.task('default', gulp.series('assets'));