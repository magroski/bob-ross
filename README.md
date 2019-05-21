# Bob Ross

[![Latest Stable Version](https://img.shields.io/packagist/v/magroski/bob-ross.svg?style=flat)](https://packagist.org/packages/magroski/bob-ross)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg?style=flat)](https://php.net/)
[![CircleCI](https://circleci.com/gh/magroski/bob-ross.svg?style=shield)](https://circleci.com/gh/magroski/bob-ross)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](https://github.com/magroski/bob-ross/blob/master/LICENSE)

This library provides an easy way to upload, manipulate and persist images.

> “We don't make mistakes, just happy little accidents.” - Bob Ross

## Usage examples

### Upload from multiple sources
```php
$persistenceHandler = new LocalFileSystem();

$painter = new Painter($persistenceHandler);

# From upload form ($_FILE)
$painter->loadFromFileGlobal('profile_pic');

# From file system
$painter->loadFromFileSystem('/home/bob/images/happy_trees.png');

# From Uri
$painter->loadFromUri('https://joyOfPainting.com/bob.png');
```

### Persist to different environments
```php
# Amazon S3
$s3Handler = new S3(new Config('credentials','key','region));
$painter = new Painter($s3Handler);
$painter->save('myFolder');

# Local
$localHandler = new LocalFileSystem();
$painter = new Painter($localHandler);
$painter->save('myFolder');
```

### Do size manipulations
```php
$localHandler = new LocalFileSystem();
$painter = new Painter($localHandler);
$painter->loadFromFileSystem('/home/img/tree.png');

$painter->saveFixedWidth(1200, '/home/img');
$painter->saveFixedHeight(800, '/home/img');
$painter->saveMaxWidhtHeight(1920, 1080, 'home/img');
$painter->saveThumb(200, 200, '/home/img');
```

### Convert between formats
```php
$localHandler = new LocalFileSystem();
$painter = new Painter($localHandler);
$painter->loadFromFileSystem('/home/img/tree.png');
$painter->setImageCovert('bmp');
$painter->save('/home/img');
```

### Change Jpeg quality
```php
$localHandler = new LocalFileSystem();
$painter = new Painter($localHandler);
$painter->loadFromFileSystem('/home/img/tree.png');
$painter->setJpegQuality(50);
$painter->save('/home/img');
```
