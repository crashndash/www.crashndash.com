'use strict';
const gulp = require('gulp');
const sass = require('gulp-sass');

gulp.task('scss', _ => {
  return gulp.src('assets/scss/app.scss')
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: [
        'node_modules/foundation-sites/scss'
      ],
      errLogToConsole: true,
      error: function(err) {
        console.log(err);
      }
    }))
    .pipe(gulp.dest('build/'));
});
