// Generated using webpack-cli https://github.com/webpack/webpack-cli

const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const WorkboxWebpackPlugin = require('workbox-webpack-plugin');
var LiveReloadPlugin = require('webpack-livereload-plugin');

const isProduction = process.env.NODE_ENV == 'production';


const stylesHandler = isProduction ? MiniCssExtractPlugin.loader : 'style-loader';



const config = {
    // entry: './src/index.ts',
    entry: {
        admin: './extensions/admin/resources/src/admin.ts'
    },
    output: {
        path: path.resolve(__dirname),
        filename: 'extensions/[name]/assets/js/[name].js'
    },
    devServer: {
        open: true,
        host: 'localhost',
        compress: true,
        port: 9090,
        hot: true
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: 'index.html',
            filename: path.resolve(__dirname, 'public/index.html')
        }),

        new LiveReloadPlugin({
        })
    ],
    module: {
        rules: [
            {
                test: /\.(js|jsx|ts|tsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader"
                }
            },
            {
                test: /\.css$/i,
                use: [stylesHandler,'css-loader'],
            },
            {
                test: /\.s[ac]ss$/i,
                use: [stylesHandler, 'css-loader', 'sass-loader'],
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2|png|jpg|gif)$/i,
                type: 'asset',
            },

            // Add your rules for custom modules here
            // Learn more about loaders from https://webpack.js.org/loaders/
        ],
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.jsx', '.js', '...'],
    },
    externals: {
        'preact': 'preact'
    }
};

module.exports = () => {
    if (isProduction) {
        config.mode = 'production';

        config.plugins.push(new MiniCssExtractPlugin({
            filename: 'extensions/[name]/assets/css/[name].css',
        }));


        config.plugins.push(new WorkboxWebpackPlugin.GenerateSW({
            swDest: path.resolve(__dirname, 'public/service-worker.js')
        }));

    } else {
        config.mode = 'development';
    }
    return config;
};
