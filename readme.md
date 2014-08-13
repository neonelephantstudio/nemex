## nemex

This is the official repository for nemex – a tiny app that helps you to track and curate ideas and projects. It’s self-hosted and based on markdown.

More info: [nemex.io](http://nemex.io)


### About the development

This repository marks the beginning of a complete rewrite. Currently, all the PHP sources have been re-written from scratch and the JavaScript sources have been cleaned up a bit. Further clean up of the JavaScript sources, image assets and HTML markup will follow.


### Usage

Change the username and password in the `config.php` and upload all files to your server. 

nemex expects to have a `projects/` directory to upload and save all files into. This directory needs to be writeable by PHP. On many shared hosters, this means you'll have to set the chmod to 0777. Also see the `FILE_CREATION_MODE` setting in the config.php.

