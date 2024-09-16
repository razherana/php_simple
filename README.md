## Overview

- First repo in github!
  This php framework should work on it's own.
  It has a built in ORM, Router, Database, Session, Console

## Requirements

- PHP 8 but trying to make it work for PHP 7
- Mysql

## How does it work ?

- This framework has a front-controller in `public/index.php` and loads `env.php`, `autoloader.php` and `function_autoloader.php`
- Then creates a new Application
- Loads the needed components
- Initialize and Execute all of the components one by one

## Start your project

- Start by doing `php console make -env ` to initialize the environment variables
- Then config/app, set the sub-folder application to false or true
- Use the php console help (command) to check the utilization of a command
- Read more at the documentation (doesn't exist yet)

## Disclaimer

This is an **amateur's work** who is still trying to learn PHP.
