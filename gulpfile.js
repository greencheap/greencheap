const _ = require('lodash')
const merge = require('merge-stream')
const gulp = require('gulp')

/**
 * Copy required asset files to app/assets/** folder
 */
gulp.task('assets', () => {
    const modules_dirname = 'node_modules',
        config = {
            'app': {vue: '*', flatpickr: '*', lodash: {files: 'lodash*.js', dest: '/dist'}},
            'app/system/modules/editor/app': {tinymce: '*', marked: '*', codemirror: '*'}
        };

    return merge.apply(null, _.map(config, (assets, module) => {
        let files, assets_dirname = module + '/assets';
        return _.map(assets, (cfg, asset) => {
            let source = modules_dirname + '/' + asset,
                dest = assets_dirname + '/' + asset;
            files = (typeof cfg === 'object' && cfg.files) ? source + '/' + cfg.files : source + '/' + '**';
            dest = (typeof cfg === 'object' && cfg.dest) ? dest + cfg.dest : dest;
            return gulp.src([files]).pipe(gulp.dest(dest));
        })
    }));
});

gulp.task('uikit', () => {
    return gulp.src('./node_modules/uikit/dist/**/*')
        .pipe(gulp.dest('./app/assets/uikit/dist/'));
})

gulp.task('default', gulp.parallel('assets', 'uikit'));
