var gulp = require('gulp'),
    browserSync = require('browser-sync').create(),
    sass = require('gulp-sass'),
    rename = require('gulp-rename');

gulp.task('serve',function(){
    browserSync.init({
        server: "./main/view"
    });
    gulp.watch("main/sass/**/*.scss", ['sass']);
    gulp.watch(['./main/view/*.html','./main/Public/js/**/*.js']).on('change',browserSync.reload);
});

gulp.task('sass',function(){
    return gulp.src('./main/Public/sass/main.scss')
        .pipe(sass({
            outputStyle: 'expanded'
        }))
        .pipe(gulp.dest('./main/Public/css'))
        .pipe(browserSync.stream());
});

gulp.task('default',['serve']);

gulp.task('livesass',['sass'],function(){
    gulp.watch("main/Public/sass/**/*.scss",['sass']);
});
