module.exports = function(config) {
  config.set({
    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',

    // frameworks to use
    frameworks: ['mocha'],

    // list of files / patterns to load in the browser
    files: [
      '../AtencionCiudadanaTesis/public/js/jquery.min.js',
      '../AtencionCiudadanaTesis/view/script/solicitud.js',
      '../AtencionCiudadanaTesis/view/script/solicitud.spec.js'
    ],

    // preprocess matching files before serving them to the browser
    preprocessors: {
      // Usamos webpack para procesar los archivos de prueba
      '../AtencionCiudadanaTesis/view/script/solicitud.spec.js': ['webpack']
    },

    // Configuración de webpack
    webpack: {
      mode: 'development',
      resolve: {
        extensions: ['.js', '.json']
      }
    },

    // test results reporter to use
    reporters: ['progress'],

    // web server port
    port: 9876,

    // enable / disable colors in the output (reporters and logs)
    colors: true,

    // level of logging
    logLevel: config.LOG_INFO,

    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,

    // start these browsers
    browsers: ['Chrome'],

    // Continuous Integration mode
    singleRun: false,

    // Concurrency level
    concurrency: Infinity
  })
}
