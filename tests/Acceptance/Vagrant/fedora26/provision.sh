#!/bin/bash
set -e

# Install OS Requirements
sudo dnf install -y nss-tools jq xsel

# Install Nginx & PHP
sudo dnf install -y nginx curl zip unzip git \
              php-fpm php-cli php-mcrypt php-mbstring php-xml php-curl php-posix

# Install Composer
php -r "readfile('http://getcomposer.org/installer');" | sudo php -- --install-dir=/usr/bin/ --filename=composer

# Remove .composer directory created during installation
sudo rm -rf ~/.composer

# Configure Composer
mkdir -p ~/.config/composer
if [ "$VALET_ENVIRONMENT" == "testing" ]
then
  # If we are testing, we mirror the repository
  # so the shared folder stays untouched
  echo '{
    "minimum-stability": "dev",
    "repositories": [
      {
        "type": "path",
        "url": "/home/vagrant/valet-linux-plus",
        "options": {
          "symlink": false
        }
      }
    ]
  }' >> ~/.config/composer/composer.json
else
  # If we are developing, we sync the repository with the shared folder
  echo '{
    "minimum-stability": "dev",
    "repositories": [
      {
        "type": "path",
        "url": "/home/vagrant/valet-linux-plus"
      }
    ]
  }' >> ~/.config/composer/composer.json
fi

# Require Valet
composer global require "bishwajitcadhikary/valet-linux-plus @dev" --no-interaction --no-ansi

# Add Composer bin to PATH
echo "PATH=\"\$HOME/.config/composer/vendor/bin:\$PATH\"" >> ~/.profile
source ~/.profile
