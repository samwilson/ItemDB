The Ormic module system
=======================

* Each module is completely contained within a directory in this `modules`
  directory.
* The module is identified by its name, in `alllowercase` and `AllCamelCaps`
  forms.
* The structure of each module directory mirrors that of the top-level
  application, with `modules/modname/Providers/ModNameServiceProvider.php` being
  the only file that is *required*.
* Module migration names must not conflict with those in core or each other.
  Prefix them with the module's name.
