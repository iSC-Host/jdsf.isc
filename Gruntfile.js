module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    watch: {
      files: ['**/*.js'],
      tasks: ['jshint']
      options: {
        spawn: false,
      },
    },

    jshint: {
      files: ['Gruntfile.js', 'assets/**/*.js'],
      options: {
        globals: {
          jQuery: true
        }
      }
    },

    htmlmin: {                                     // Task 
      dist: {                                      // Target 
        options: {                                 // Target options 
          removeComments: true,
          collapseWhitespace: true
        },
        files: {                                   // Dictionary of files 
          'characters-min.html': 'contact.html',
          'comics-min.html': 'comics.html',
          'videos-min.html': 'videos.html',
          'about-min.html': 'about.html',
          'contact-min.html': 'contact.html'
        }
      },
      dev: {                                       // Another target 
        files: {
          'index-min.html': 'index.html'
        }
      }
    },

    uglify: {
      my_target: {
        files: {
          'assets/**/main.min.js': 'assets/**/main.js',
          'assets/**/plugins.min.js': 'assets/**/plugins.js',
          'assets/**/redirect.min.js': 'assets/**/redirect.js'
        }
      }
    },

    cssmin: {
      options: {
        shorthandCompacting: false,
        roundingPrecision: -1
      },
      target: {
        files: {
          'assets/**/base.min.css': 'assets/**/base.css'
          'assets/**/components.min.css': 'assets/**/components.css'
          'assets/**/main.min.css': 'assets/**/main.css'
          'assets/**/mq.min.css': 'assets/**/mq.css'
          'assets/**/normalize.min.css': 'assets/**/normalize.css'
          'assets/**/print.min.css': 'assets/**/print.css'
          'assets/**/redirect.min.css': 'assets/**/redirect.css'
          'assets/**/site.min.css': 'assets/**/site.css'
          'assets/**/utils.min.css': 'assets/**/utils.css'
        }
      }
    }

  });

  // Enabled plugins.
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-htmlmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  // A very basic default task.
  grunt.registerTask('default', 'Running for Espeñiol!', ['watch', 'jshint', 'htmlmin', 'uglify', 'cssmin'], function() {
    grunt.log.write('Now Grunt (JavaScript Task Runner) is running with <3 for Espeñiol...').ok();
  });

};
