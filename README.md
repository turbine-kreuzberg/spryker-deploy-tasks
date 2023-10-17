# Spryker Deploy Tasks

This package provides a functionality for Spryker to execute one-time tasks after a deployment based on the environment and store.  

* [Installation](#installation)
* [Usage](#usage)
* [Configuration](#configuration)
* [Credits](#credits)
* [License](#license)

## Installation

```
composer require [--dev] turbine-kreuzberg/spryker-deploy-tasks
```

## Usage

### Create new deploy task file

```php
vendor/bin/console deploy:tasks:create
```

This will create a new YAML file with an auto-generated name containing the current timestamp (e.g. `tasks.1697540350.yml`) and this content:
    
```yaml
tasks:
# add new deploy tasks as an array to this file

# a task has to be an item with the mandatory keys 'command', 'execute_for_store' and 'execute_on'
# - the value for 'command' should be an executable shell command line (multiple commands and options possible)
# - the entries for 'execute_for_store' have to match the value of env var APPLICATION_STORE
# - the entries for 'execute_on' have to match the value of env var SHOP_ENV (dev,int,stage,prd)
- command: "some/command/to/execute --with-option --another-option-with-value foo && some/other/command"
  execute_for_store:
  - DE
  - EN
  execute_on:
  - dev
  - int
  - stage
  - prd
# to skip executing a task in a particular environment, it needs to be
# removed from the 'execute_on' list
- command: "some/other/command/to/execute/everywhere/but/production"
  execute_for_store:
  - DE
  - EN
  execute_on:
  - dev
  - int
  - stage
# to skip executing a task in a particular store, it needs to be
# removed from the 'execute_for_store' list
- command: "some/other/command/to/execute/everywhere/only/for/store-en"
  execute_for_store:
  - EN
  execute_on:
  - dev
  - int
  - stage
  - prd
# task can have additional optional keys, e.g. 'description'
- command: "yet/another/command/to/execute"
  description: "description for yet another command to execute"
  execute_for_store:
  - DE
  - EN
  execute_on:
  - dev
  - int
  - stage
  - prd
```

### Execute deploy tasks

```php
vendor/bin/console deploy:tasks:execute
```

This command will execute deploy tasks from all YAML files that have not been executed for the current environment and store yet.
Executed tasks will be logged in a new table `txb_deploy_tasks` in the database. The mechanism is very much the same as for Propel migrations.

## Credits

- [All Contributors](../../../-/graphs/main)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
