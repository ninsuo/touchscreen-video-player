Touchscreen Video Player
========================

## What is it?

I'm in a sport club providing LifeFitness equipment. Each machine incorporate a tablet, offering TV, a few apps,
some sports-related things and... an Internet browser. For me it meant, "an online video player" that will play
my favorite movies.

![Login][doc/00-login.png)

![Video index][doc/01-index.png)

![Play][doc/02-play.png)

Once you logged-in through large numeric buttons, you can access videos stored in your server's directory. It
supports mp4, but prefer a big compression to avoid latency (internet connection isn't always good), such as:

```sh
ffmpeg -i originalMovie.avi -c:v libx264 -crf 24 -preset slow -c:a aac -strict experimental -b:a 192k -ac 2 lifefitnessMovie.mp4
```

This is quite minimal but does the job for me.

## Supported languages

- English
- Français

## Installation

First, install the application:

```sh
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar update
php app/console assets:install web --symlink
php app/console doctrine:schema:create
php app/console server:start
run http://127.0.0.1:8000
```

Now, move your videos to the `web/videos` directory. If you wish not to move them, create a symbolic link (don't forget
to disable directory indexing in your server configuration, or at least put an index.html inside the exposed video
directory).

Then, set-up at least one Resource Owner:

- GitHub Login: https://github.com/settings/developers
- StackExchange Login: https://stackapps.com/apps/oauth/
- Google Login: https://console.developers.google.com/project
- Twitter Login: https://apps.twitter.com/
- Facebook Login: https://developers.facebook.com/apps/

Once you created your first user on the UX, set it as admin by requesting:

```sql
UPDATE users SET is_admin = 1 WHERE id = 1;
```

Now, create the VIDEO group, and add your user in it. Sign-out and sign-in: you can now access your videos!

*Note*

If you want movie thumbnails, you need to install ffmpeg on your server, and allow php to exec(). That's
not mandatory though, you'll just have a fallback image instead.

## Usage

As you'll notice, all users can subscribe, but only the ones in group `VIDEO` can access the video player.
All administrators can manage users and groups through the "Administration" menu at the top.

All users can create/update their digilogin in the menu. They'll need to set a 10-digit login (something they know by heart,
like a phone number or whatever) and a 4-digit pin code. If someone try to bruteforce an account, its pin code will
be dropped after 10 tries (configurable on a per-user basis).

## Bonus

Encode your whole movies directory with the following script:

```php
<?php

foreach (glob("*.avi") as $file) {
	$dst = trim(str_replace('.avi', '.mp4', $file));
	$file = escapeshellarg(trim($file));
    if (!is_file($dst)) {
        $dst = escapeshellarg($dst);
        exec("ffmpeg -i {$file} -c:v libx264 -crf 24 -preset slow -c:a aac -strict experimental -b:a 192k -ac 2 {$dst}");
    }
}
```

## License

- This project is released under the MIT license

- Fuz logo is © 2013-2017 Alain Tiemblo

