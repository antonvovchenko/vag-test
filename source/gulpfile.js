var gulp = require('gulp'),
//uglify = require('gulp-uglify'),
    concat = require('gulp-concat');


gulp.task('default', [
    'compileAppJs',
    'configCoreJs',
    'compileAppCss',
    'compilePrintCss',
    'compileReportCss'
]);


//watchers
gulp.watch('resources/assets/js/App.js', ['compileAppJs']);
gulp.watch('resources/assets/css/main.css', ['compileAppCss']);
gulp.watch('resources/assets/css/print.css', ['compilePrintCss']);
gulp.watch('resources/assets/css/report.css', ['compileReportCss']);

gulp.watch('resources/assets/js/config/*.js', ['configCoreJs']);


//tasks
gulp.task('compileAppJs', function () {
    gulp.src('resources/assets/js/App.js')
        .pipe(concat('app.js'))
        .pipe(gulp.dest('public/build/assets/js/'))
});

gulp.task('configCoreJs', function () {
    gulp.src('resources/assets/js/config/*.js')
        .pipe(concat('config.js'))
        .pipe(gulp.dest('public/build/assets/js/'))
});

gulp.task('compileAppCss', function () {
    gulp.src('resources/assets/css/main.css')
        .pipe(concat('app.css'))
        .pipe(gulp.dest('public/build/assets/css/'))
});

gulp.task('compilePrintCss', function () {
    gulp.src('resources/assets/css/print.css')
        .pipe(concat('print.css'))
        .pipe(gulp.dest('public/build/assets/css/'))
});

gulp.task('compileReportCss', function () {
    gulp.src('resources/assets/css/report.css')
        .pipe(concat('report.css'))
        .pipe(gulp.dest('public/build/assets/css/'))
});