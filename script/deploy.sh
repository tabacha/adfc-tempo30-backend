#!/bin/bash
rsync -r --delete-after --quiet $TRAVIS_BUILD_DIR/ travis@tools.adfc-hamburg.de:/var/www/html/tempo30b/$TRAVIS_BRANCH