#!/bin/bash
echo 'PSR-2 PHP CHECK'
echo '-----------------'
for f in $(find src/ -name '*.php'); do php phpcs.phar --standard=PSR2 --warning-severity=0 $f; done
grep -r --include=*.php -E '\$([a-zA-Z0-9]+)=(.*?)' ./src
echo 'PSR-2 JS CHECK'
echo '-----------------'
for f in $(find src/ -name '*.js' | grep -v '.min.'); do php phpcs.phar --standard=PSR2 --warning-severity=0 $f; done
echo 'PSR-2 CSS CHECK'
echo '-----------------'
for f in $(find src/ -name '*.css'); do php phpcs.phar --standard=PSR2 --warning-severity=0 $f; done
echo 'FILES WITH TRAILING SPACES'
echo '-----------------'
find ./src -type f -exec egrep -l " +$" {} \; | grep '.php\|.twig\|.js\|.yml'
echo 'FILES WITH LINES WITH MORE THAN 120 CHARS'
echo '-----------------'
grep -r --include=*.php '.\{120\}' ./src | grep -v "Route" | grep -v "OneToMany" | grep -v "ManyToOne" | grep -v "ORM"
echo 'FILES WITH 2 CONSECUTIVE BLANK LINES'
echo '-----------------'
find src | xargs pcregrep --buffer-size 1000000 -l -M $'\n\n\n' | grep '.php'
find src | xargs pcregrep --buffer-size 1000000 -l -M $'\n\n\n' | grep '.twig'
find src | xargs pcregrep --buffer-size 1000000 -l -M $'\n\n\n' | grep '.js'
echo 'SNAKE CASE VARIABLES'
echo '-----------------'
grep -r --include=*.php -E '\$([a-zA-Z]+)_([a-zA-Z]+)' ./src
./phpmd.phar ./src text controversial
echo 'TABS DETECTED'
echo '-----------------'
grep --include=*.twig --include=*.php --include=*.css -r $'\t' ./src
echo 'CAPITALS ON PHP VARIABLES'
echo '-----------------'
grep -r --include=*.php -E '\$([A-Z]+)' ./src
echo 'NON SPACES IN CONCATENATION'
echo '-----------------'
grep -r --include=*.php -E '\.\$' ./src | grep -v "trans" | grep -v "{0}" | grep -v "Inf\["
echo 'NON SPACES IN EQUALS'
echo '-----------------'
grep -r --include=*.php -E '\=\$' ./src
echo 'UNUSED CODE'
echo '-----------------'
./phpmd.phar ./src text unusedcode