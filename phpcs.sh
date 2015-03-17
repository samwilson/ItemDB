#!/bin/bash
echo "Checking PSR2 compliance..."
./vendor/bin/phpcs --report=full --extensions=php --ignore="vendor,*.blade.php" --standard=PSR2 .
