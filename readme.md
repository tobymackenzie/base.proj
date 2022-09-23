base.proj
=======

Use `bin/create` script to create a new project at a given directory with 0 or more project types, where choosing a project type copies the files from that project type in the `src` directory into the new project directory.  The files in `src/base` will always be added to the project.  Usage looks like:

``` sh
bin/create git php dest
```

to create a new project in a folder `dest` with the `base`, `git`, and `php` project type folder contents.

Requires `bash` executable to exist locally as well as `rsync` and `mkdir` commands (common on Mac OS X, Linux, BSD).

Early stages, usage and files subject to change.

<footer>
<p>SPDX-License-Identifier: 0BSD OR Unlicense OR CC0-1.0</p>
</footer>
