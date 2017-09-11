## Image Processor 


```bash

    composer install 

```

install inotify-tools 

```bash

    apt install inotify-tools #debian or #ubuntu etc... 

```

define driver image process ```parameters.yml```: 
    
```yaml

        image_driver: imagick  #or gd
      
```

define S3 credentials: 

```yaml

        s3_key: mys3key 
        s3_secret: mys3 secret key
        region: region of bucket 
        version: #version or latest
        bucket_name: bucket name

```

define many profiles in ```config.yml``` example: 

```yaml


            property:
                - {width: 1600, height: null, quality: 100 }
                - {width: 1280, height: null, quality: 100 }
                - {width: 900, height: null, quality: 100 }
                - {width: 600, height: null, quality: 100 }
                - {width: 420, height: null, quality: 100 }
                - {width: 300, height: null, quality: 100 }


```

## Commands list:  

### process your image 

```bash

    bin/console image:process --path=/Images/myimage.png --profile=property --outputDirectory=/tmp/images -vvv

```

### listener directory for sync with s3 

```bash

    bin/console -vvv s3:transport -d /tmp/images

```

### push all images on s3

```bash

    bin/console  sync:directory -d /tmp/images

```
