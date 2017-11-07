module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            jsLibraries: {
                src: [
                    'bower_components/jquery/dist/jquery.js',
                    'bower_components/jquery-ui/jquery-ui.js',
                    'bower_components/bootstrap/dist/js/bootstrap.min.js',
                    'bower_components/select2/dist/js/select2.js',
                    'bower_components/slimscroll/jquery.slimscroll.js',
                    'bower_components/datatables/media/js/jquery.dataTables.js',
                    'bower_components/datatables/media/js/dataTables.bootstrap.js',
                    'bower_components/datatables-helper/js/datatables-helper.js',
                    'bower_components/magnific-popup/dist/jquery.magnific-popup.js',
                    'bower_components/slimscroll/jquery.slimscroll.min.js'
                ],
                dest: 'Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Libraries.js',
                nonull: true
            },
            jsApplication: {
                src: [
                    'Packages/Application/Langeland.JiraDex/Resources/Private/JavaScript/**/*.js'
                ],
                dest: 'Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Application.js',
                nonull: true
            },
            cssLibraries: {
                src: [
                    'bower_components/fontawesome/css/font-awesome.css',
                    'bower_components/bootstrap/dist/css/bootstrap.min.css',
                    'bower_components/select2/dist/css/select2.css',
                    'bower_components/select2-bootstrap-theme/dist/select2-bootstrap.min.css',
                    'bower_components/datatables/media/css/dataTables.bootstrap.css',
                    'bower_components/magnific-popup/dist/magnific-popup.css',
                ],
                dest: 'Packages/Application/Langeland.JiraDex/Resources/Public/StyleSheets/Libraries.css'
            }
        },

        copy: {
            fonts: {
                files: [
                    {
                        expand: true,
                        src: [
                            'bower_components/fontawesome/fonts/*',
                            'bower_components/bootstrap/fonts/*'
                        ],
                        dest: 'Packages/Application/Langeland.JiraDex/Resources/Public/Fonts/',
                        filter: 'isFile',
                        flatten: true
                    }
                ]
            }
        },

        replace: {
            dist: {
                options: {
                    usePrefix: false,
                    patterns: [
                        {
                            match: '/fonts/',
                            replacement: '/Fonts/'
                        }
                    ]
                },
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['Packages/Application/Langeland.JiraDex/Resources/Public/StyleSheets/Libraries.css'],
                        dest: 'Packages/Application/Langeland.JiraDex/Resources/Public/StyleSheets/'
                    }
                ]
            }
        },

        uglify: {
            options: {
                sourceMap: true
            },
            jsApplication: {
                src: 'Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Application.js',
                dest: 'Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Application.min.js',
                nonull: true
            },
            jsLibraries: {
                src: 'Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Libraries.js',
                dest: 'Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Libraries.min.js',
                nonull: true
            }
        },

        compass: {
            app: {                            // Target
                options: {                       // Target options
                    outputStyle: 'compressed',
                    sassDir: 'Packages/Application/Langeland.JiraDex/Resources/Private/Compass',
                    cssDir: 'Packages/Application/Langeland.JiraDex/Resources/Public/StyleSheets/',
                    fontsDir: 'Packages/Application/Langeland.JiraDex/Resources/Public/Fonts/'
                }
            }
        },

        clean: {
            javaScript: ['Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Application.js', 'Packages/Application/Langeland.JiraDex/Resources/Public/JavaScript/Libraries.js'],
        },

        watch: {
            scripts: {
                files: ['Packages/Application/Langeland.JiraDex/Resources/Private/JavaScript/**/*.js'],
                tasks: ['concat:app', 'uglify:app'],
                options: {
                    spawn: false
                }
            },
            css: {
                files: ['Packages/Application/Langeland.JiraDex/Resources/Private/Compass/*.scss'],
                tasks: ['compass:app'],
                options: {
                    spawn: false
                }
            }
        }
    });

    // Load the plugin that provides the "concat" task.
    grunt.loadNpmTasks('grunt-contrib-concat');

    grunt.loadNpmTasks('grunt-contrib-compass');

    // Load the plugin that provides the "trim trailing spaces" task.
    grunt.loadNpmTasks('grunt-trimtrailingspaces');

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Load the plugin that provides the "watch" task.
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');

    grunt.loadNpmTasks('grunt-replace');

    // Default task(s).
    grunt.registerTask('default', ['concat', 'replace', 'uglify', 'compass', 'copy']);

}
