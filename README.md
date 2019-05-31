### This repo is archived. It's successor is [rasmus0201/flexyfitness](https://github.com/rasmus0201/flexyfitness)

## Prerequisites
To run this project you must have

* [node installed](https://nodejs.org/en/). If not download and install latest version.
* PHP (7.0)
* Composer
* Angular CLI
* (OpenSSL - secret key)

## Project:
Next download project from GitHub. Either by `git clone https://github.com/rasmus0201/obbc.git` or by pressing the "Clone or Download" button and then Download ZIP

## Configuration:
It is important to note that the environment variables should be changed according to usages. Remember to rename .env.example to .env in the `server` folder. Be sure you have generated the secret app key (/server). It should be AES-256-CBC private key. You can generate it using this command: `openssl enc -aes-256-cbc -k secret -P -md sha1`. The "key" should now be pasted into the .env file setting the APP_KEY variable. If you don't have openssl installed i believe you could find some online generator, or install openssl.

## Client server (/client):
First navigate to the project folder and then the `client` folder.

* To install dependencies using `npm install`.
* To run angular type in `ng serve` for a dev server. It should be located at `http://localhost:4200/` by default.

##### Production:
It is also possible to build pure .js, .css and .html files for distribution.


## API server - (/server):
First navigate to the project folder and then the `server` folder.

* To install dependencies type the following command: `composer install`.
* To run the server type: `sudo php -S localhost:4300 -t public index.php` for a dev server. Navigate to `http://localhost:4300/` for the API interface.


## Customization:

It is possible to change the ports, just edit the client/src/environments/environment.ts file to the preferred port to the API server. Now when you run ng serve with port option: `ng serve --port 5000 --open` (in this example port 5000). Change PHP dev server command to the new port you set in the environment.ts file.
