# random-picture-php-script
A quick & dirty PHP script to mimic very basic unsplash.it functionality with your own folder with pictures. It's based on [this](https://www.dyn-web.com/code/random-image-php/), but adapted to serve a native picture file + the ability to avoid throwing the same picture again for the next 30 pictures.

Based on requester IP, images can be selected from different folders.
This is useful in an internal network where the clients always have the same IP address (static or fixed DHCP).
