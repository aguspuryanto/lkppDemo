var gulp = require('gulp');
var gulpMinifyCss = require('gulp-minify-css');

gulp.task('minify-css', function() {
  gulp.src('./templates/css/*.css')
    .pipe(gulpMinifyCss({
      compatibility: 'ie8'
    }))
    .pipe(gulp.dest('./templates/css/'));
});