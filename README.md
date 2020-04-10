Acquia BLT SimpleSAMLPhp
====

This is an [Acquia BLT](https://github.com/acquia/blt) plugin providing integration with [SimpleSAMLPhp](https://www.drupal.org/project/simplesamlphp_auth).

This plugin provides a set of commands in the `simplesamlphp` namespace that will initialize SimpleSAMLPhp integration using a set of template configuration files.

This plugin is **community-supported**. Acquia does not provide any direct support for this software or provide any warranty as to its stability.

## Installation and usage

To use this plugin, you must already have a Drupal project using BLT 12 or higher.

In your project, require the plugin with Composer:

`composer require acquia/blt-simplesamlphp`

This plugin provides commands for automating the setup process for SimpleSAMLphp
and assists in deploying configuration files to Acquia Cloud. You must
already be familiar with the process of configuring SimpleSAMLphp as described
in the [instructions for using SimpleSAMLphp on Acquia Cloud](https://docs.acquia.com/resource/simplesaml/).

Before proceeding, prepare your SimpleSAMLphp configuration by completing the
following tasks:

1. Run the following command to perform initial installation tasks: `blt recipes:simplesamlphp:init`

    Tasks completed by the initialization command include the following:

    -  Adds the [simpleSAMLphp
       Authentication](https://www.drupal.org/project/simplesamlphp_auth)
       module as a project dependency in your ``composer.json`` file.
    -  Copies configuration files to
       ``${project.root}/simplesamlphp/config``.
    -  Adds a ``simplesamlphp`` property to the ``blt/blt.yml`` file, which
       instructs Acquia BLT to include your SimpleSAMLphp configuration during
       deployments to Acquia Cloud.
    -  Creates a symbolic link in the docroot to the web-accessible
       directory of the ``simplesamlphp`` library.

1. Follow the [instructions for using SimpleSAMLphp on Acquia Cloud](https://docs.acquia.com/resource/simplesaml/) to update the configuration files located in the
    ``${project.root}/simplesamlphp/config`` directory.

1. Run the following command to copy the configuration files to the
   local SimpleSAML library: `blt source:build:simplesamlphp-config`

   Note:
       The ``source:build:simplesamlphp-config`` command is strictly for local
       use, and because the command overwrites vendor files, running the
       command will make not make any changes that are visible to Git.

SimpleSAMLphp should now be ready for testing in your local environment. When
you are ready to test in an Acquia Cloud environment, commit your configuration
files and deploy a build artifact as usual using ``blt artifact:deploy`` or one
of Acquia BLT's supported continuous integration services. Acquia BLT will add
and commit your configuration files when building a deploy artifact.

# License

Copyright (C) 2020 Acquia, Inc.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
