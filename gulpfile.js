const {src, dest, watch, parallel} = require('gulp');
const concat = require('gulp-concat');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const cssmin = require('gulp-clean-css');


function css() {
  return src([
    'assets/src/css/prism.css',
    'assets/src/css/ish.css',
    'assets/src/css/index.css',
  ])
    .pipe(concat('index.css'))
    .pipe(dest('assets/dist'))
    .pipe(cssmin())
    .pipe(rename('index.min.css'))
    .pipe(dest('assets/dist'));
}


function js() {
  return src([
    'assets/src/js/jquery.min.js',
    'assets/src/js/prism.js',
    // 'assets/src/js/url-handler.js',
    'assets/src/js/ish.js',
    'assets/src/js/index.js',
  ])
    .pipe(concat('index.js'))
    .pipe(dest('assets/dist'))
    .pipe(uglify())
    .pipe(rename('index.min.js'))
    .pipe(dest('assets/dist'));
}


function watchScripts() {
  watch('assets/src/**/*.js', js);
}

function watchStyles() {
  watch('assets/src/**/*.css', css);
}


module.exports = {
  default: parallel(
    css,
    js
  ),
  watch: parallel(
    watchStyles,
    watchScripts
  ),
};
