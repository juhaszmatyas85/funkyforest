const gulp = require('gulp');
const concat = require('gulp-concat');
const minify = require("gulp-minify-css");
const sass = require("gulp-sass")(require('sass'));
const strip = require("gulp-strip-css-comments");
const rename = require("gulp-rename");
const copy = require("gulp-copy");
const watch = require("gulp-watch");
const newer = require('gulp-newer');
const postcss = require('gulp-postcss');

const styleSrc = 'scss';
const styleDest = './';

gulp.task('scss', function () {
    const task = gulp
        .src(styleSrc + '/Main.scss')
        .pipe(sass());
    // .pipe(sourcemaps.init())
    // .pipe( postcss([ require('precss'), require('autoprefixer') ]) )
    // .pipe( sourcemaps.write('.') )
    // .pipe(strip());

    return task
        .pipe(rename('bundle.css'))
        .pipe(gulp.dest(styleDest));
});

gulp.task('watch', function () {
    gulp.watch([styleSrc + '/**/*.scss', '!' + styleSrc + '/Vendor.scss'], gulp.parallel('scss'));
});

gulp.task('build', gulp.parallel('scss'));

gulp.task('default', gulp.parallel('build'));
