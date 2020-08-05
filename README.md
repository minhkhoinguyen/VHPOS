# NexoPOS 4

## Installation
Before showing the step to install the application there is prior consideration to have in mind. The current root folder of the application, having the folders "app", "bootstrap", "config"... shouldn't be at the root of your server. If you're using Linux, you should configure apache to use the folder "public" (where the index.php is located) as the RootDocument of the installation. For Windows users, with [laragon](https://laragon.org/), you can also point what is the root directory. This technique prevents a lot of exploits. 

The following installation steps require additionnal skills on using CLI (Command Line Interface), but when we'll release NexoPOS builds, that will be a full installation with all the dependencies. We might also create an installer with a very simplified user interface.

- Make sure to have PHP 7.4 & Apache Configured with required extensions : php-xml, php-mbstring, php-msqli... These are often already provided by virtual server like Laragon, XAMP, WAMP, MAMP.
- [Installing Composer](https://getcomposer.org/download/).
- Install Git (that will be helfpul if you want to contribue or just to download).
- Run the following CLI command on the directory where NexoPOS should be installed : `sudo git clone https://github.com/blair2004/NexoPOS-v4.git`
- Run on the CLI `cd NexoPOS-v4`, if that's the directory name created by the previous step.
- Run on the CLI `composer install`, to install Laravel and all dependencies.
- (Optional) if the project comes without a .env file, you need to create one. You can use the .env.example that should be available at the root. Then run `php artisan key:generate`
- (Optional) Run on the CLI `npm i` to install JavaScript dependencies if you plan to contribute.
- (Optional) Run `php artisan serve` if you don't have your virtual server pointing to your installation. This will run a php server for development purpose only.

As NexoPOS doesn't have a frontend already, you'll end on the default Laravel page. Access `/do-setup/` to launch the installer.

## Contribution Guidelines
Do you plan to contribute ? That's awesome. We're open to any type of contributions. If you're a developper, you'll start by forking the project and deploying that locally for further tests. If youjust have some ideas, consider posting that as an issue. We'll review the ideas and decide to implement it.

## Documentation

- [Menu API](/readme/menu.md)
- [Javascript/Classes/FormValidation](https://github.com/Blair2004/NexoPOS-4x/blob/master/readme/javascript/classes/form-validation.md)


