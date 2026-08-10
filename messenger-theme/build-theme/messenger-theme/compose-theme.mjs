import fs from 'fs';
import {zip} from 'zip-a-folder';

console.log('removing previous build');
if (fs.existsSync('./messenger-theme.zip')) {
  fs.rmSync('./messenger-theme.zip');
}
if (fs.existsSync('./build-theme')) {
    fs.rmSync('./build-theme', {recursive: true, force: true});
}
console.log('removed previous build.');

console.log('Copying theme files');
if (!fs.existsSync('../build-theme')) {
  fs.mkdirSync('../build-theme');
  fs.mkdirSync('../build-theme/messenger-theme');
}
// copy build folder to output folder
fs.cpSync('./', '../build-theme/messenger-theme', {recursive: true});

console.log('Copied theme files.');
console.log('removing node_modules and git history');

fs.rmSync('../build-theme/messenger-theme/node_modules', {recursive: true, force: true});
fs.rmSync('../build-theme/messenger-theme/.git', {recursive: true, force: true});

console.log('removed node_modules and git history');

fs.cpSync('../build-theme', './build-theme', {recursive: true});

fs.rmSync('../build-theme', {recursive: true, force: true});

// create zip??
zip('./build-theme', './messenger-theme.zip', {destPath: './'});

console.log('Created ZIP');
