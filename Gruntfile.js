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

		watch:  {
			styles: {
				files: [
					'assets/sass/**/*.scss'
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

		sass: {
			options: {
				outputStyle: 'expanded',
				lineNumbers: true,
				includePaths: [
					'bower_components/bourbon/app/assets/stylesheets',
					'bower_components/neat/app/assets/stylesheets'
				]
			},
			dist: {
				files: {
					'assets/css/wds-hero-widget.css': [
						'assets/sass/mixins/media-queries.scss',
						'assets/sass/public/wds-hero-widget.scss',
					]
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
		}

	} );

	// Default task.
	grunt.registerTask( 'styles', [ 'sass' ] );
	grunt.registerTask( 'php', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask( 'default', ['styles', 'php'] );

	grunt.util.linefeed = '\n';
};
