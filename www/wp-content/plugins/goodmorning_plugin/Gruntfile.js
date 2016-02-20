module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({

	// LESS / CSS

	// Compile Less
	// Compile the less files
    less: {
      development: {
        options: {
          compress: true,
          yuicompress: true,
          optimization: 2
        },
        files: {
          "admin.css": "less/admin.less", // destination file and source file
        }
      }
    },

    autoprefixer: {
		options: {
			// Task-specific options go here.
		},
		style: {
            src: 'admin.css',
			dest: 'admin.css'
   		}
	},

    // JAVASCRIPT

    // JS HINT
    // How's our code quality
    jshint: {
	    options: {
			reporter: require('jshint-stylish'),
			force: true,
	    },
    	all: ['js/**/*.js', '!js/**/*.min.js', '!js/bootstrap/**/*.js', '!js/vendor/**/*.js']
  	},

    // Concat
    // Join together the needed files.
	concat_in_order: {
		main: {
			files: {
		        'js/admin.min.js': ['js/admin.js']
			},
			options: {
			    extractRequired: function(filepath, filecontent) {
				    var path = require('path');

			        var workingdir = path.normalize(filepath).split(path.sep);
			        workingdir.pop();

			        var deps = this.getMatches(/@depend\s"(.*\.js)"/g, filecontent);
			        deps.forEach(function(dep, i) {
			            var dependency = workingdir.concat([dep]);
			            deps[i] = path.join.apply(null, dependency);
			        });
			        return deps;
			    },
			    extractDeclared: function(filepath) {
			        return [filepath];
			    },
			    onlyConcatRequiredFiles: true
			}
		}
	},

	// Uglify
	// We minify the files, we just concatenated
	uglify: {
	    mstartup: {
	      options: {
	      },
	      files: {
	        'js/admin.min.js': ['js/admin.min.js']
	      }
	    }
	},

	// Copy
	// Copy files from the vendor folder to need places elsewhere
	copy: {
		main: {
			files: [
				{expand: true, flatten: true, src: ['bower_components/**/*.eot', 'bower_components/**/*.ttf', 'bower_components/**/*.woff', 'bower_components/**/*.woff2'], dest: 'fonts/', filter: 'isFile'},
				// Copy all found font files from the vendor folder to the fonts folder
			]
		}
	},

	// WATCHER / SERVER

    // Watch
    watch: {
	    js: {
		    files: ['js/**/*.js', 'admin/**/*.js'],
		    tasks: ['handle_js'],
			options: {
			},
	    },
		less: {
			files: ['less/**/*.less'], // which files to watch
			tasks: ['less', 'autoprefixer'],
			options: {
				// livereload: true
			},
		},
		css: {
			files: ['**/*.css', '*.css'],
			tasks: [],
			options: {
				livereload: true
			}
		},
		vendor: {
			files: ['bower_components/**/*'],
			task: ['copy']
		},
		livereload: {
			files: ['js/*.min.js', '**/*.php', '**/*.html'], // Watch all files
			options: {
				livereload: true
			}
		},
    }
  });

  grunt.registerTask( 'handle_js', ['concat_in_order', 'uglify', 'jshint'] );

};

