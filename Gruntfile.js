module.exports = function (grunt) {

    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            base_js: {
                options: {
                    separator: ';'
                },

                src: [
                    'bower_components/jquery/dist/jquery.js',
                    'bower_components/nette-forms/src/assets/netteForms.js',
                    'bower_components/nette.ajax.js/nette.ajax.js',
                    'www/assets/js/original/main.js'
                ],
                dest: 'www/assets/js/concatenated/js.js'
            }
        },

        uglify: {
            base_js: {
                files: {
                    'www/assets/js/js.min.js': ['www/assets/js/concatenated/js.js']
                }
            }
        },

        sass: {
            front: {
                options: {
                    style: 'expanded'
                },
                files: {
                    'www/assets/css/original/front.css': ['www/assets/css/scss/front/front.scss']
                }
            }
        },

        cssmin: {
            front: {
                files: {
                    'www/assets/css/front.min.css': [
                        'www/assets/css/original/front.css'
                    ]
                }
            }
        },

        copy: {
            font_awesome: {
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['bower_components/font-awesome-sass/assets/fonts/font-awesome/*'],
                        dest: 'www/assets/fonts/font-awesome/'
                    }
                ]
            }
        },

        watch: {
            sass: {
                files: 'www/assets/css/scss/front/*.{scss,sass}',
                tasks: ['sass:front']
            }
        }

    });

    grunt.registerTask('default', ['copy', 'sass', 'concat', 'cssmin', 'uglify']);

    grunt.registerTask('css', ['sass:front', 'cssmin:front']);

    grunt.registerTask('js', ['concat:base_js', 'uglify:base_js']);

    grunt.registerTask('watch_front_css', ['watch']);

};
