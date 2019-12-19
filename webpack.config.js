const VueLoader = require('vue-loader')
const path = require('path')

module.exports = {
  devtool: 'cheap-module-source-map',
  entry: {
    main: ['@babel/polyfill', './src/main.scss', './src/main.js'],
    admin: ['./src/admin/admin.js']
  },
  output: {
    path: path.join(__dirname, 'assets/'),
    filename: '[name].min.js'
  },
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.esm.js',
      'modules-root': path.resolve(__dirname, 'modules')
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  module: {
    rules: [
      {
        enforce: 'pre',
        test: /\.js$/,
        exclude: ['/node_modules/', '/vendor/'],
        loader: 'eslint-loader'
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        query: {
        }
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      {
        test: /\.s?css$/,
        extract: true,
        use: [
          'style-loader',
          'css-loader?importLoaders=1&minimize=1',
          'postcss-loader'
        ]
      },
      {
        test: /\.(woff(2)?|ttf|eot)(\?v=\d+\.\d+\.\d+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'fonts/'
          }
        }]
      },
      {
        test: /\.(gif|png|jpe?g)$/i,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'img/'
          }
        }]
      },
      {
        test: /\.(svg)$/i,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'svg/'
          }
        }]
      }
      // {
      //   test: /\.(eot|svg|ttf|woff|woff2)$/,
      //   loader: 'file-loader?name=/fonts/[name].[ext]'
      // }
    ]
  },
  plugins: [
    new VueLoader.VueLoaderPlugin()
  ]
}
