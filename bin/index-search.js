const fuse = require('../public/js/fuse/fuse.js');
const fs = require('fs');
const ini = require('./ini.js');
const path = require('path');

var search = [];

fs.promises.readdir('../data/md-wiki-docs/')
   .then(files => {
   		const config = ini.parse(fs.readFileSync('../app/settings.ini', 'utf-8'));
   		const index = JSON.parse(fs.readFileSync('../data/md-wiki-docs/'+config.index, 'utf8'));
       for (let file of files) {
       	if (typeof index[file] !== 'undefined' && file !== config.error){
	        const data = fs.readFileSync('../data/md-wiki-docs/'+file, 'utf8');
	        const filename = path.basename('../data/md-wiki-docs/'+file);
	        const name = index[filename];
			var entry = {
			  name: name,
			  filename: filename,
			  text: data
			}
			search.push(entry);
       	}
    }
	const myIndex = fuse.createIndex(['filename', 'name', 'text'], search);
	fs.writeFile('../public/data/fuse-index.json', JSON.stringify(myIndex.toJSON()), (err) => {
	  if (err)
	    console.log(err);
	});
	fs.writeFile('../public/data/fuse-search.json', JSON.stringify(search), (err) => {
	  if (err)
	    console.log(err);
	});
})
.catch(err => {
	console.log(err)
})