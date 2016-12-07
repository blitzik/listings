module.exports = function (grunt) {

    require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        concat: {
            base_js: {
                options: {
                    separator: ";"
                },

                src: [
                    "bower_components/jquery/dist/jquery.js",
                    //"bower_components/nette-forms/src/assets/netteForms.js",
                    "vendor/nette/forms/src/assets/netteForms.js",
                    "bower_components/nette.ajax.js/nette.ajax.js",
                    "www/assets/js/original/jquery-ui.min.js",
                    "www/assets/js/original/timeConverter.js",
                    "www/assets/js/original/main.js"
                ],
                dest: "www/assets/js/concatenated/js.js"
            },
            listing_item_js: {
                options: {
                    separator: ";"
                },
                src: [
                    "www/assets/js/original/listingItemSliders.js"
                ],
                dest: "www/assets/js/concatenated/listingItemSliders.js"
            }
        },

        uglify: {
            base_js: {
                files: {
                    "www/assets/js/js.min.js": ["www/assets/js/concatenated/js.js"]
                }
            },
            listing_item_js: {
                files: {
                    "www/assets/js/listingItemSliders.min.js": ["www/assets/js/concatenated/listingItemSliders.js"]
                }
            }
        },

        sass: {
            accounts: {
                options: {
                    style: "expanded"
                },
                files: {
                    "www/assets/css/original/accounts.css": ["www/assets/css/scss/accounts/accounts.scss"]
                }
            },

            listings: {
                options: {
                    style: "expanded"
                },
                files: {
                    "www/assets/css/original/listings.css": ["www/assets/css/scss/listings/listings.scss"]
                }
            }
        },

        cssmin: {
            accounts: {
                files: {
                    "www/assets/css/temp/accounts.css": [
                        "www/assets/css/original/accounts.css"
                    ]
                }
            },

            listings: {
                files: {
                    "www/assets/css/temp/listings.css": [
                        "www/assets/css/original/listings.css",
                        "www/assets/css/original/jquery-ui.css"
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
                        src: ["bower_components/font-awesome-sass/assets/fonts/font-awesome/*"],
                        dest: "www/assets/css/fonts/font-awesome/"
                    }
                ]
            }
        },

        watch: {
            sass: {
                files: [
                    //"www/assets/css/scss/accounts/*.{scss,sass}",
                    "www/assets/css/scss/listings/_styles.scss",
                    "www/assets/css/scss/common/*.{scss,sass}"

                ],
                tasks: ["sass:listings"]
            }
        }

    });

    grunt.registerTask("default", ["copy", "sass", "concat", "cssmin", "uglify"]);

    grunt.registerTask("css_accounts", ["sass:accounts", "cssmin:accounts"]);

    grunt.registerTask("css_listings", ["sass:listings", "cssmin:listings"]);

    grunt.registerTask("js", ["concat", "uglify"]);

    grunt.registerTask("watch_css", ["watch"]);

};
