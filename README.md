# thumbnailGenerator
Console Application for generating thumbnails of images and saving them in the storage [local, s3bucket] 

##Prerequisites:
- PHP 7.4 with php_imagic library
- Composer
- PHPUnit for tests

## Installation
- Download sources from git reposytory https://github.com/skowroms/thumbnailGenerator
- Install Composer
- Install dependencies : 'composer install'

## Usage:
 php bin/console app:generateThumbnails <localPath> <storageType> <outputPath>
                                                               
Arguments:                                                     
  localPath             The Local directory path with the image files you want to resize
  storageType           What type of storage do you want to use ['local', 's3bucket']
  outputPath            The output path to save images
  
For s3bucket you need to set up an .env file with login credential

S3_REGION=bucketRegion
S3_VERSION=bucketVersion
S3_BUCKET=bucketName
S3_KEY=bucketyKey
S3_SECRET=bucketSecret
