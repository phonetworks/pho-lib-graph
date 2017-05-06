Thanks for your interest in contributing.

More information about the libgraph internals can be found at the docs/ folder of this package.

If the folder is not generated yet, you can form it by typing:

```sh
composer install
vendor/bin/phpdoc run -t docs/ -d src/ --template=clean --ignore="*tests?*,*Tests?*"
```