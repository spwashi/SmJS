"use strict";

let gulp = require('gulp');

require("babel-polyfill");
const babel      = require("babel-core/register");
const path       = require('path');
const browserify = require('browserify');
const watchify   = require('watchify');
const babelify   = require('babelify');
const source     = require('vinyl-source-stream');
const buffer     = require('vinyl-buffer');
const merge      = require('utils-merge');
const rename     = require('gulp-rename');
const uglify     = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const mocha      = require('gulp-mocha');
/* nicer browserify errors */
const gutil = require('gulp-util');
const chalk = require('chalk');

function map_error(err) {
    if (err.fileName) {
        // regular error
        gutil.log(chalk.red(err.name) + ': '
                  + chalk.yellow(err.fileName.replace(__dirname + '/src/js/', '')) + ': ' + 'Line ' + chalk.magenta(err.lineNumber)
                  + ' & ' + 'Column ' + chalk.magenta(err.columnNumber || err.column)
                  + ': '
                  + chalk.blue(err.description))
    } else {
        // browserify error..
        gutil.log(chalk.red(err.name)
                  + ': '
                  + chalk.yellow(err.message))
    }
    
    this.emit('end');
}
const _deploy_dir   = path.resolve(__dirname, '_deploy');
const file_name     = `${_deploy_dir}/_entities/index.js`;
const dist_dir_name = `${_deploy_dir}/dist`;

const _test_dir_name = path.resolve(__dirname, 'tests', '_deploy');

gulp.task('mocha', function () {
    return gulp.src([_test_dir_name + '/index.js'])
               .pipe(mocha({
                               compilers: [
                                   'js:babel-core/register',
                               ]
                           }));
});

/**
 * Put everything together
 * @param bundler
 * @return {*}
 */
function bundle_js_dev(bundler) {
    return bundler.bundle()
                  .on('error', map_error)
                  .pipe(source('app.js'))
                  .pipe(buffer())
                  .pipe(gulp.dest(dist_dir_name))
                  .pipe(rename('app.min.js'))
                  .pipe(sourcemaps.init({loadMaps: true}))
                  // // capture sourcemaps from transforms
                  .pipe(uglify())
                  .pipe(sourcemaps.write('.'))
                  .pipe(gulp.dest(dist_dir_name))
}

// Update the rendered files whenever one of the files under the index is updated
gulp.task('watchify', function () {
    let args    = merge(watchify.args, {debug: true, verbose: true});
    let bundler =
            watchify(browserify(file_name, args))
                .transform(babelify, {/* opts */});
    bundle_js_dev(bundler);
    bundler.on('update', _updated_filename => {
        console.log([new Date, _updated_filename.length < 2 ? _updated_filename || null : _updated_filename]);
        bundle_js_dev(bundler)
    })
});

// Without watchify
gulp.task('browserify', function () {
    let bundler = browserify(file_name, {debug: true}).transform(babelify, {/* options */});
    return bundle_js_dev(bundler)
});

// Without sourcemaps
gulp.task('browserify-production', function () {
    let bundler = browserify(file_name).transform(babelify, {/* options */});
    return bundler.bundle()
                  .on('error', map_error)
                  .pipe(source('app.js'))
                  .pipe(buffer())
                  .pipe(rename('app.min.js'))
                  .pipe(uglify())
                  .pipe(gulp.dest(`${_deploy_dir}`))
});