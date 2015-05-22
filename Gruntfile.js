module.exports = function( grunt ) {

	require('load-grunt-tasks')(grunt);

	var pkg = grunt.file.readJSON( 'package.json' );

	var bannerTemplate = '/**\n' +
		' * <%= pkg.name %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
		' * <%= pkg.homepage %>\n' +
		' *\n' +
		' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
		' * Licensed GPLv2+\n' +
		' */\n';

	var compactBannerTemplate = '/**\n' +
		' * <%= pkg.name %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> | <%= pkg.homepage %> | Copyright (c) <%= grunt.template.today("yyyy") %>; | Licensed GPLv2+\n' +
		' */\n';

	// Project configuration
	grunt.initConfig( {

		pkg: pkg,

		autoprefixer: {
			options: {
				browsers: ['last 2 versions', 'ie 9']
			},
			dist: {
				src: ['assets/css/wds-hero-widget.css', '!*.min.css', '!bower_components', '!node_modules']
			}
		},

		cmq: {
			options: {
				log: false
			},
			dist: {
				files: {
					'assets/css/wds-hero-widget.css': 'assets/css/wds-hero-widget.css'
				}
			}
		},

		csscomb: {
			dist: {
				files: [{
					expand: true,
					cwd: '',
					src: ['assets/css/wds-hero-widget.css', '!*.min.css', '!bower_components', '!node_modules'],
					dest: '',
				}]
			}
		},

		cssmin: {
			minify: {
				expand: true,
				cwd: '',
				src: ['assets/css/wds-hero-widget.css', '!*.min.css', '!bower_components', '!node_modules'],
				dest: '',
				ext: '.min.css'
			}
		},

		sass: {
			options: {
				sourceMap: true,
				outputStyle: 'expanded',
				lineNumbers: true,
				includePaths: [
					'assets/bower/bourbon/app/assets/stylesheets',
					'assets/bower/neat/app/assets/stylesheets'
				]
			},
			dist: {
				files: {
					'assets/css/wds-hero-widget.css': 'assets/sass/wds-hero-widget.scss'
				}
			}
		},

		makepot: {
			dist: {
				options: {
					domainPath: '/languages/',
					potFilename: pkg.name + '.pot',
					type: 'wp-plugin'
				}
			}
		},

		addtextdomain: {
			dist: {
				options: {
					textdomain: pkg.name
				},
				target: {
					files: {
						src: ['**/*.php']
					}
				}
			}
		},

		watch:  {
			styles: {
				files: [
					'assets/**/*.scss'
				],
				tasks: ['sass'],
				options: {
					spawn: false,
					livereload: true,
					debounceDelay: 500
				}
			},
			php: {
				files: ['**/*.php', '!vendor/**.*.php'],
				tasks: ['php'],
				options: {
					spawn: false,
					debounceDelay: 500
				}
			}
		},

	} );

	// Default task.
	grunt.registerTask( 'styles', [ 'sass', 'autoprefixer', 'cmq', 'csscomb', 'cssmin' ] );
	grunt.registerTask( 'php', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask( 'default', ['styles', 'php'] );

	grunt.util.linefeed = '\n';
};
