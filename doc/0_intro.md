# Intro

## What is this library ?

The "sitphp/benchmarks" library can help you to measure the time and memory that a PHP script uses.
It can start taking note of a given moment that a script is running and record the start time and memory the script is using.
The package can also keep track of the time that elapsed and the memory that was used until one or more points of the same script.
It can as well measure the time and memory used when calling a given piece of code passed to the package as callable function.

## Requirements

This library requires at least PHP 7.2 It should be installed from composer which will make sure your configuration matches requirements.
 > {.note .info} Note : You can get composer here : [https://getcomposer.org](https://getcomposer.org).

        
## Install

Once you have composer installed, add the `"sitphp/benchmarks": "2.4.*"` line in the `"require"` section of your composer.json file :
    
```json
{
    "require": {
        "sitphp/benchmarks": "2.4.*"
    }
}
```

Then just run the following composer command to install the library :

```bash
composer update
```
