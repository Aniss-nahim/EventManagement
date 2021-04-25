# Event Management app

Event management app built with Symfony, Vuejs and Bootsrap.

## Environment setup
Before cloning the project make shure make sure you have everything you need in order to run the project. Here is the list of requirements :

* PHP version >= 7.1.3
* composer
* Local server (Xampp, Wamp, Laragon ...)
* Nodejs [link here](https://nodejs.org/en/)

## Intalling dependencies

After cloning the project into your local machine you will need to install all dependencies attacheched to the project. On you terminal run the two commands :
> Composer dependencies

```bash
composer install
```
> Nodejs dependencies

```bash
npm install
```

## Environment variables & building assets

inside the project you have to create a new file named `.env.local` and then copy the content of your `.env` file into it. Setup your database credentials inside the `.env.local` file.

Now all you have to do is to compile the assets, run:

```bash
npm run watch
```

## Run the app

You can run Symfony applications with any web server (Apache, nginx, the internal PHP web server, etc.). However, Symfony provides its own web server to make you more productive while developing your applications.


```bash
php bin/console server:run
```

Well done :clap: :clap:, Now your are good to Go !!!

--------
:sparkles: Happy coding :sparkles: