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
            simple_lunch_listing_item_js: {
                options: {
                    separator: ";"
                },
                src: [
                    "www/assets/js/original/listingItemType1.js"
                ],
                dest: "www/assets/js/concatenated/listingItemType1.js"
            },
            range_lunch_listing_item_js: {
                options: {
                    separator: ";"
                },
                src: [
                    "www/assets/js/original/listingItemType2.js"
                ],
                dest: "www/assets/js/concatenated/listingItemType2.js"
            }
        },

        uglify: {
            base_js: {
                files: {
                    "www/assets/js/js.min.js": ["www/assets/js/concatenated/js.js"]
                }
            },
            simple_lunch_listing_item_js: {
                files: {
                    "www/assets/js/listingItemType1.min.js": ["www/assets/js/concatenated/listingItemType1.js"]
                }
            },
            range_lunch_listing_item_js: {
                files: {
                    "www/assets/js/listingItemType2.min.js": ["www/assets/js/concatenated/listingItemType2.js"]
                }
            }
        },

        sass: {
            public: {
                options: {
                    style: "expanded"
                },
                files: {
                    "www/assets/css/public.css": ["www/assets/css/scss/public/public.scss"]
                }
            },

            listings: {
                options: {
                    style: "expanded"
                },
                files: {
                    "www/assets/css/listings-bootstrap.css": ["www/assets/css/scss/listings/listings-bootstrap.scss"],
                    "www/assets/css/listings.css": ["www/assets/css/scss/listings/listings.scss"]
                }
            }
        },

        cssmin: {
            public: {
                files: {
                    "www/assets/css/public.min.css": [
                        "www/assets/css/public-bootstrap.css",
                        "www/assets/css/public.css"
                    ]
                }
            },

            listings: {
                files: {
                    "www/assets/css/listings.min.css": [
                        "www/assets/css/listings-bootstrap.css",
                        "www/assets/css/listings.css"
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
                        dest: "www/assets/fonts/font-awesome/"
                    }
                ]
            }
        }

    });

    grunt.registerTask("default", ["copy", "sass", "concat", "cssmin", "uglify"]);

    grunt.registerTask("css_public", ["sass:public", "cssmin:public"]);

    grunt.registerTask("css_min_public", ["cssmin:public"]);

    grunt.registerTask("css_listings", ["sass:listings", "cssmin:listings"]);

    grunt.registerTask("css_min_listings", ["cssmin:listings"]);

    grunt.registerTask("js_range_lunch", ["concat:range_lunch_listing_item_js", "uglify:range_lunch_listing_item_js"]);

    grunt.registerTask("js", ["concat", "uglify"]);

};
