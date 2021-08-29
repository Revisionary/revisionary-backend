// https://gist.github.com/leymannx/f7867942184d01aa2311

var gulp = require("gulp"),
	sass = require("gulp-sass"),
	prefix = require("gulp-autoprefixer"),
	sassLint = require("gulp-sass-lint"),
	sourcemaps = require("gulp-sourcemaps");

// SETTINGS
// ---------------

var sassOptions = {
	outputStyle: "expanded",
};

// BUILD SUBTASKS
// ---------------

gulp.task("styles", function () {
	return gulp
		.src("./assets/styles/*.scss")
		.pipe(sourcemaps.init())
		.pipe(sass(sassOptions))
		.pipe(prefix())
		.pipe(sourcemaps.write("./"))
		.pipe(gulp.dest("./assets/styles/"));
});

gulp.task("sass-lint", function () {
	gulp.src("./assets/styles/*.scss")
		.pipe(sassLint())
		.pipe(sassLint.format())
		.pipe(sassLint.failOnError());
});

gulp.task("watch", function () {
	gulp.watch("./assets/styles/style.scss", gulp.series("styles"));
	gulp.watch("./assets/styles/revise.scss", gulp.series("styles"));
	//gulp.watch('scss/**/*.scss', gulp.series('styles'));
});

// BUILD TASKS
// ------------

gulp.task("default", gulp.series("styles", "watch"));
gulp.task("build", gulp.series("styles"));
