# MdWiki
simple wiki with markdown files as input

## Installation and update

### Requirements
Webserver with Apache 2.4 (or similar) and php 7.4

### Installation and upgrade
1. copy and past the code in a specific folder and configure the Webserver
2. create new `app/settings.ini` file starting from `app/settings-example.ini` and fill the fields
3. insert the `.md` files in `data/md-wiki-docs`
4. create the `index` in json format
```
{
 "filename.md" : "Name"
}
```
5.execute the enable/refresh search

### Images
the uploaded images can be placed into `public/img/upload` folder

### Enable/refresh search
once added the new `.md` files and updated the `index` file execute
```
node bin/index-search.js
```

## To do
- [ ] add a load more feature
- [ ] add version management

## Thanks to
- Fat-Free Framework 3.8 (https://fatfreeframework.com)
- Font Awesome Free 6.3.0 (https://fontawesome.com)
- Bootstrap 5.0.2 (https://getbootstrap.com/)
- jQuery 3.6.3 (https://jquery.com/)
- jQuery UI 1.13.2 (https://jqueryui.com/)
- moment.js 2.29.4 (http://momentjs.com)
- Fuse.js 6.6.2 (https://fusejs.io/)
- Parsedown 1.7.4 (https://parsedown.org/)
- Simple load more 1.5.3 (https://github.com/zeshanshani/simple-load-more)