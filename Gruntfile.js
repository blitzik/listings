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
                    'bower_components/bootstrap/js/dist/alert.js',
                    'bower_components/bootstrap/js/dist/modal.js',
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
            accounts: {
                options: {
                    style: 'expanded'
                },
                files: {
                    'www/assets/css/original/accounts.css': ['www/assets/css/scss/accounts/accounts.scss']
                }
            }
        },

        cssmin: {
            accounts: {
                files: {
                    'www/assets/css/accounts.min.css': [
                        'www/assets/css/original/accounts.css'
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
                        dest: 'www/assets/css/fonts/font-awesome/'
                    }
                ]
            }
        },

        watch: {
            sass: {
                files: [
                    'www/assets/css/scss/accounts/*.{scss,sass}',
                    'www/assets/css/scss/common/*.{scss,sass}'

                ],
                tasks: ['sass:accounts']
            }
        }

    });

    grunt.registerTask('default', ['copy', 'sass', 'concat', 'cssmin', 'uglify']);

    grunt.registerTask('css_accounts', ['sass:accounts', 'cssmin:accounts']);

    grunt.registerTask('js', ['concat:base_js', 'uglify:base_js']);

    grunt.registerTask('watch_accounts_css', ['watch']);

};
