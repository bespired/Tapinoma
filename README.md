# Tapinoma 

Nodeless Sass and Js for Laravel

## Getting started

Add Tapinoma to your composer.json file:

```
"require": {
    "bespired/tapinoma": dev-master"
}
```

Use composer to install this package.

```
$ composer update
```

### Get it working 

Copy the public assets:
```
$ cp -r vendor/bespired/tapinoma/public/ public/.
```

Preping the layout:

```
<html>
    <head>
        <title>App Name - @yield('title')</title>
        <link href="https://fonts.googleapis.com/css?family=Oswald:100,300,700|Playfair+Display" rel="stylesheet">
        <link rel="stylesheet" href="/assets/styles.php/style.scss">

    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>
        <script src="/assets/jsrc.php/script.jsrc"></script>
    </body>
</html>
```

Your resources\assets\style.css should look something like:
```
@import 'sass/settings';
@import 'sass/vendor';
@import 'sass/base';
@import 'sass/app';
```
and you should have some sass in resources\assets\sass\

Your resources\assets\script.jsrc should look something like:
```
@import jsrc/jquery;
@import jsrc/wheel;
@import jsrc/title;
@import jsrc/teletype;
```
And you should have some js in resources\assets\jsrc\  
but nothing that needs transpiling.


### That's it
No node, no ruby, no compass, no nothing.

