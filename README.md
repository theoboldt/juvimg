# juvimg
Image resizing service for juvem for Google App Engine

## Configuration
In order to use *juvimg*, you need to configure *juvimg* and deploy at google cloud plattform and configure your *juvem* installation for usage of *juvimg*.

### juvimg
In order to use the package with Google cloud plattform, you need a setup using flexible environment for PHP. 
A template of the config file `app.yaml.dist` is available in source code. 

Besides all settings defined in template, you need to configure following `env_variables`:
* In `APP_PASSWORD` the password for HTTP Basic authentication is defined. This is used in combination with username `user` by your *juvem* installation in order to authorize at juvimg service.
* In `APP_SECRET` the symfony application secret is defined. You may want to check out http://nux.net/secret in order to configure this value

