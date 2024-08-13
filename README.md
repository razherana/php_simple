### Overview
First repo in github!
This php framework should work on it's own.
It has a built in ORM, Router, Database...

## Requirements
- PHP 8 (trying to make it work for PHP 7, that's why I don't add parameter types)
- Mysql

## How does it work ?
- This framework has a front-controller in `public/index.php` and loads `env.php`, `autoloader.php` and `function_autoloader.php`
- Then creates a new Application
- Loads the needed components
- Initialize and Execute all of the components on by one

## Disclaimer
This is an **amateur's work** who is still trying to learn PHP.
