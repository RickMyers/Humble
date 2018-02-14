var fs           = require('fs');
var childProcess = require('child_process');
var exec         = require('child_process').exec,child;
module.exports = function (grunt)   {
    // Project configuration.
    grunt.initConfig({
        jshint: {
            // http://jshint.com/docs/options/
            options: {
                curly: true,
                globals: {
                    jQuery: true
                },
            },
            devfiles: ['web/app.js', 'web/main.js', 'web/router.js', 'web/clients/**/*.js'],
        },
        watch: {
            options: {
                port: 80,
                livereload: true,
            },
            css: {
                files: ['css/*.css'],
            },
            js: {
                files: ['web/clients/**/*.js','web/app.js','web/main.js','web/router.js','Code/**/*.js'],
                tasks: ['jshint'],
            },
            hbs: {
                files: ['web/clients/**/*.hbs']
            },
            xml: {
                files: ['Code/**/config.xml','Code/**/Controllers/*.xml']
            },
            php: {
                files: ['Code/**/*.php']
            }
        }
    });

    /**
     * DEFAULT
     * 1) jshint
     * livereload tests + app + jshint when saving a file
     */
    grunt.registerTask('default', [
        // 'build',
        // 'styles:development',
        // 'thorax:inspector',
        // 'connect:development',
        'jshint',
        'watch'
    ]);

    grunt.event.on('watch', function(action, filepath, target) {
        console.log(action+","+filepath+","+target);
        if (filepath.indexOf('.php') !== -1) {
            if (filepath.indexOf('tpl.php') !== -1) {
                console.log('Smarty view file... skipping ['+filepath+']');
            } else {
                exec('php Module.php --W '+filepath, function (error,stdout,stderr) {
                    console.log(stdout);
                });
            }
        } else if (filepath.indexOf('.xml') !== -1) {
            if (filepath.indexOf('Controllers') !== -1) {
                exec('php Module.php --Y file='+filepath, function (error,stdout,stderr) {
                    console.log(stdout);
                });
            } else if (filepath.indexOf('etc') !== -1) {
                exec('php Module.php --U '+filepath, function (error,stdout,stderr) {
                    console.log(stdout);
                });
            }
            grunt.log.writeln(target + ': ' + filepath + ' has ' + action);
        } else {
            console.log('I got '+filepath);
        }
    });

    grunt.loadNpmTasks('grunt-simple-watch');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-handlebars-compiler');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-php');
    grunt.registerTask('default', ['watch']);
    grunt.loadNpmTasks('grunt-contrib-handlebars');
};