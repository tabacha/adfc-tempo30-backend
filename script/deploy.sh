#!/bin/bash
rsync -r --delete-after --exclude=travis-ssh-key   --quiet $TRAVIS_BUILD_DIR/ travis@tools.adfc-hamburg.de:/var/www/html/tempo30-backend/$TRAVIS_BRANCH