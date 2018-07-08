require('./check-versions')();

process.env.NODE_ENV = 'production';

let buildWebpackConfig = require('./webpack.prod.conf');
let chalk = require('chalk');
let config = require('../config');
let merge = require('webpack-merge');
let path = require('path');
let rm = require('rimraf');
let shell = require('shelljs');
let webpack = require('webpack');
let webpackConfig = merge(buildWebpackConfig, {
    plugins: [
        new webpack.ProgressPlugin()
    ]
});

rm(path.join(config.build.assetsRoot, config.build.assetsSubDirectory), err => {
    if (err) throw err;
    webpack(webpackConfig, function (err, stats) {
        if (err) throw err;
        process.stdout.write(stats.toString({
                colors: true,
                modules: false,
                children: false,
                chunks: false,
                chunkModules: false
            }) + '\n\n');
        let assetsPath = path.join(__dirname, '../../../../../../public/assets/install');

        console.log(chalk.cyan('  Moving files to path ' + assetsPath + '\n'));

        shell.rm('-rf', assetsPath);
        shell.mkdir('-p', assetsPath);
        shell.config.silent = true;
        shell.cp('-R', path.join(__dirname, '../dist/assets/install/css'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/install/js'), assetsPath);
        shell.config.silent = false;

        console.log(chalk.cyan('  Build complete.\n'));
    })
});