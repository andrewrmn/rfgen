const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
// const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );

// Set different CSS extraction for editor only and common block styles
const blockCSSPlugin = new ExtractTextPlugin({
  filename: './dist/ow-gutenberg.css',
});

// Configuration for the ExtractTextPlugin.
const extractConfig = {
  use: [
    { loader: 'raw-loader' },
    {
      loader: 'postcss-loader',
      options: {
        plugins: [require('autoprefixer')],
      },
    },
    {
      loader: 'sass-loader',
      query: {
        outputStyle:
          'production' === process.env.NODE_ENV ? 'compressed' : 'nested',
      },
    },
  ],
};

module.exports = {
  entry: {
    './dist/ow-gutenberg': './src/index.js',
  },
  output: {
    path: path.resolve(__dirname),
    filename: '[name].js',
  },
  watch: true,
  devtool: 'cheap-eval-source-map',
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['es2015', 'react']
          }
        },
      },
      {
        test: /style\.s?css$/,
        use: blockCSSPlugin.extract(extractConfig),
      },
      {
        test: /\.(jpg|png)$/,
        use: {
          loader: 'url-loader',
        }
      }
    ],
  },
  plugins: [
    blockCSSPlugin,
    // new BrowserSyncPlugin({
    //   // Load localhost:3333 to view proxied site
    //   host: 'localhost',
    //   port: '3333',
    //   // Change proxy to your local WordPress URL
    //   proxy: 'https://gutenberg.local'
    // })
  ],
};
