[MongoDB](http://www.mongodb.org/) Session driver for [Laravel 4](http://laravel.com/).

Installation
============

Add `navruzm/laravel-mongo-session` as a requirement to composer.json:

```json
{
    "require": {
        "navruzm/laravel-mongo-session": "*"
    }
}
```
And then run `composer update`

Once Composer has updated your packages open up `app/config/app.php` and change `Illuminate\Session\SessionServiceProvider` to `MongoSession\SessionServiceProvider`

Then open `app/config/session.php` and find the `driver` key and change to `mongo`.