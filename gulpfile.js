var gulp = require('gulp');
var jade = require('gulp-jade');

/* Jade to HTML  */
gulp.task('jade', function(){
	gulp.src('layouts/views/**/*.jade')
	.pipe(jade({
		pretty: true
	}))
	.pipe(gulp.dest('./dist'))
})

gulp.task('watch', function(){
	gulp.watch('layouts/views/**/*.jade', ['jade'])
});

gulp.task('default', ['watch']);