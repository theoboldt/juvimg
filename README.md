# juvimg
Image resizing service for *juvem* for Google App Engine

![PHP Composer](https://github.com/theoboldt/juvimg/workflows/PHP%20Composer/badge.svg)
[![Build Status](https://travis-ci.org/theoboldt/juvimg.svg?branch=master)](https://travis-ci.org/github/theoboldt/juvimg)
[![Maintainability](https://api.codeclimate.com/v1/badges/00751c28db54cd4da37e/maintainability)](https://codeclimate.com/github/theoboldt/juvimg/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/00751c28db54cd4da37e/test_coverage)](https://codeclimate.com/github/theoboldt/juvimg/test_coverage)

## Configuration
In order to use *juvimg*, you need to configure *juvimg* and deploy at google cloud plattform and configure your *juvem* installation for usage of *juvimg*.

### juvimg
In order to use the package with Google cloud plattform, you need a setup using flexible environment for PHP. 
A template of the config file `app.yaml.dist` is available in source code. 

Besides all settings defined in template, you need to configure following `env_variables`:
* In `APP_PASSWORD` the password for HTTP Basic authentication is defined. This is used in combination with username `user` by your *juvem* installation in order to authorize at juvimg service.
* In `APP_SECRET` the symfony application secret is defined. You may want to check out [http://nux.net/secret] in order to configure this value

### juvem
In order to use an *juvimg* installation by *juvem* you must define the following parameters in your *juvem* installation:

* Define the base uri to your *juvimg* installation in `juvimg.url`
* The value you configured in `APP_PASSWORD` in your *juvimg* installation needs to be stored in *juvem* in parameter `juvimg.password`
