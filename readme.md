base.proj
=======

Create a new project from one or more project templates using provided PHP or BASH script.  More advanced functionality is provided by the PHP scripts, but the BASH script has few dependencies.

Early stages, usage and files subject to change.

PHP
----

Use `bin/console create` to create a new project at a given directory with 0 or more project types, where choosing a project type copies the files from that project type in the `templates` directory into the new project directory.  The files in `templates/base` will always be added to the project.  Usage looks like:

``` sh
bin/console create git php dest
```

to create a new project in a folder `dest` with the `base`, `git`, and `php` project type folder contents.

There is also some project management.  Create a project in a defined parent folder by prefixing a path with ":".  Use the `open` subcommand to open a given project with a defined command.  More commands will likely be added to help with projects.

With the default command script, a `config.local.yml` or `config.yml.php` can be added to the `config` directory using Symfony style configuration to change settings, with the `baseProjOpts` specifying configuration options, eg:

``` php
parameters:
  baseProjOpts:
    openCommand: 'gvim'
    projPath: '/home/me/projects'
```

The script makes use of some Symfony console commands, which make use of a PHP class, `TJM\BaseProj\BaseProj(null, array)`.  This can be used for programatic project management.

BASH
----

Use `bin/create` to create a new project much like the PHP version, except without advanced merging and other features.  Usage looks like:

``` sh
bin/create git php dest
```

Requires `bash` executable to exist locally as well as `rsync` and `mkdir` commands (common on Mac OS X, Linux, BSD).

License
------

<footer>
<p>SPDX-License-Identifier: 0BSD OR Unlicense OR CC0-1.0</p>
</footer>
