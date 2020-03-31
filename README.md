## This is a Magento codebase for VBAM
As we don't have Magento cloud account yet, we use Magento Community Edition codebase for local developmenet

## Setup for local environment
Run normal Magento setup
- composer install
- php bin/magento setup:install --admin-firstname=Appscore --admin-lastname=Appscore --admin-email=bao.luong@appscore.com.au --admin-user=admin --admin-password='' --base-url='' --db-name='' --db-user='' --db-password='' --backend-frontname=admin --use-rewrites=1 --timezone=Australia/Melbourne
- php bin/magento setup:static-content:deploy -f
- php bin/magento cache:flush

## Setup grunt for LESS precompile files
- npm install -g grunt-cli
- npm install
- grunt exec
- grunt less
- grunt watch