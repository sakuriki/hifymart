## HifyMart

Website thương mại điện tử sử dụng Nuxtjs và Laravel API

## Demo

[https://vietmart.ga](https://vietmart.ga)

## Setup

```bash
$ git clone https://github.com/sakuriki/hifymart.git
$ cd hifymart
$ cp .env.example .env
$ composer install
$ php artisan key:generate
$ php artisan jwt:secret
$ php artisan vietnam-zone:import
$ php artisan migrate --seed
```

Find and uncomment this line `App\Providers\SettingsServiceProvider::class` in `config.php/app.php`

Make sure the settings (google map key, vnpay and fb page id) are set up correctly:

```
GOOGLE_MAPS_API_KEY=xxxxxxxxxxxxxxxxxxxxx

VNPAY_TMNCODE=xxxxxxxxxxxxxxxxxxxxx
VNP_HASHSECRET=xxxxxxxxxxxxxxxxxxxxx

FB_PAGE=xxxxxxxxxxxxxxxxxxxxx // fanpage ID
```

IMPORTANT: Make sure that your .env file is updated with the right settings for APP_URL (for your back-end APIs) and CLIENT_URL (for your front-end / Nuxt). These values need to match what you will set in the client-side setup section.

```
APP_URL=http://localhost
CLIENT_URL=http://localhost:3000
CLIENT_PORT=3000
CLIENT_HTTPS=false
```

Don't forget to setup your Redis server and match the config inside .env file

```
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Front-end setup (Nuxtjs):

```bash
$ yarn install
or
$ npm install
```

Update nuxt.config.js to match the server:port where your Laravel API server is running:

1. baseUrl and apiUrl in the env{} section (either make sure process.env is set, or change the default)

```
  env: {
    baseUrl: process.env.CLIENT_BASE_URL || "http://localhost:3000",
    apiUrl: process.env.APP_URL || "http://localhost:8000",
    GOOGLE_MAPS_API_KEY: process.env.GOOGLE_MAPS_API_KEY,
    appName: process.env.APP_NAME || "HifyMart",
    fbPage: process.env.FB_PAGE || 104938607602675
  },
```

Example: `baseUrl: process.env.BASE_URL || 'http://localhost:3000'`

2. baseURL and https in the axios: {} section

```
  /*
  ** Axios module configuration
  */
  axios: {
    // See https://github.com/nuxt-community/axios-module#options
    baseURL: (process.env.APP_URL || "http://localhost:8000") + "/api/",
    https: process.env.CLIENT_HTTPS || true
  },
```

Example: `baseURL: "http://localhost:8000/api"`

Finally, start the nuxt development server.

```
$ yarn dev
or
$ npm run dev
```
## Optional

Change your Vuetify config in `client/vuetify.option.js`

## Useful commands

Seeding the database:

```bash
$ php artisan db:seed
```

Generating fake data:

```bash
$ php artisan db:seed --class=DevDatabaseSeeder
```
