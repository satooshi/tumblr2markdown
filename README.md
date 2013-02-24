tumblr2markdown
===============

This CLI tool can convert your tumblr blog to Octopress markdown. tumblr2markdown calls [tumblr API v1](http://www.tumblr.com/docs/en/api/v1) and use [pandoc](http://johnmacfarlane.net/pandoc/) internally. Please install pandoc before you use tumblr2markdown.

# Installation

To install tumblr2markdown with Composer just add the following to your composer.json file:

```js
// composer.json
{
    require: {
        "satooshi/tumblr2markdown": "dev-master"
    }
}
```

Then, you can install the new dependencies by running Composer’s update command from the directory where your composer.json file is located:

```sh
# install
$ php composer.phar install
# update
$ php composer.phar update satooshi/tumblr2markdown

# or you can simply execute composer command if you set composer command to
# your PATH environment variable
$ composer install
$ composer update satooshi/tumblr2markdown
```

Packagist page for this component is [https://packagist.org/packages/satooshi/tumblr2markdown](https://packagist.org/packages/satooshi/tumblr2markdown)

Or you can use git clone command:

```sh
# HTTP
$ git clone https://github.com/satooshi/tumblr2markdown.git
# SSH
$ git clone git@github.com:satooshi/tumblr2markdown.git
```

# Usage

```sh
# show help
$ php app/console.php tumblr:markdown -h

# run
$ php app/console .php tumblr:markdown -b satooshi-jp -t text --tagged blog
```
