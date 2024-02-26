# WooCommerce Payment Links

**Description:** Payment links for WooCommerce

## Anchors
- [Install dependencies](#install)
- [Build dependencies](#build)
- [Ignored folders and files](#ignore)
- [REST API](#api)
- [File Tree](#tree)



<h2 id="install">Installing the dependencies</h1>

**Install the plugin autoload and dependencies with the composer**
``` 
composer install
```

**Install the node dependencies with the yarn or npm**
``` 
yarn install
npm install
```

<h2 id="build">Build dependencies</h2>

**Build production and watch the resource page changes**
```
yarn watch
```

**Build production assets**
```
yarn build
```

<h2 id="ignore">Ignored folders and files</h2>

**Folders**
- vendor/
- dist/
- node_modules/
- .cache/
- .parcel-cache/
- .idea/

**Files**
- *.lock


<h2 id="api">REST API</h1>

### AUTHORIZATION
Use Basic Authentication para se autenticar e utilizar a REST API do plugin WooCommerce Payment Links;
<br>
Example: 
```PHP
base64_encode(USER:PASSWORD)
```
### GET
#### Endpoint: 
```
https://{domain}/wp-json/wc-payment-link/links/?{id}
```

Use the endpoint above to make a get request and search for the links registered on your website. You can also use the link ID as a parameter to search for a specific ID.

If necessary, it is possible to provide more information in the body of the request:
```JSON
{
    "page": 1,
    "per_page": 10,
    "order":"ASC",
    "order_by": "name"
}
```

### POST
#### Endpoint:
```
https://{domain}/wp-json/wc-payment-link/links/
```
Use o método POST para cadastrar novos links usando a REST API. Você vai precisar enviar os dados do link través do corpo da requisição: 
```JSON
{
  "name": "New Link",
  "token": "3b356f26-6ea8-47ba-8649-d76c89e498be",
  "coupon": "coupon-code",
  "expire_at": "2024-02-22 00:00:00",
  "products": [
      {
          "product": 43,
          "quantity": 4
      }
  ]
}
```

### PUT
#### Endpoint:
```
https://{domain}/wp-json/wc-payment-link/links/{id}
```
Use o método PUT para atualizar os links já cadastrados usando a REST API. Você vai precisar adiocioar o ID do link como parâmetro de URL e enviar os dados do link través do corpo da requisição:
```JSON
{
  "name": "New Link 02",
  "token": "3b356f26-6ea8-47ba-8649-d76c89e498be",
  "coupon": "coupon-code",
  "expire_at": "2024-02-22 00:00:00",
  "products": [
      {
          "product": 43,
          "quantity": 4
      }
  ]
}
```

### DELETE
#### Endpoint:
```
https://{domain}/wp-json/wc-payment-link/links/{id}
```
Use the DELETE method passing the id of the link you want to remove using the REST API.

<h2 id="tree">File Tree</h2>

```
.
├── LICENSE
├── README.md
├── app
│   ├── API
│   │   ├── Routes
│   │   │   ├── Links.php
│   │   │   └── Route.php
│   │   └── Routes.php
│   ├── Controllers
│   │   ├── Menus
│   │   │   └── Links.php
│   │   ├── Menus.php
│   │   ├── Pages
│   │   │   └── PaymentLink.php
│   │   └── Render
│   │       ├── AbstractRender.php
│   │       └── InterfaceRender.php
│   ├── Core
│   │   ├── Boot.php
│   │   ├── Config.php
│   │   ├── Export.php
│   │   ├── Functions.php
│   │   ├── Uninstall.php
│   │   └── Utils.php
│   ├── Exceptions
│   │   ├── ExpiredTokenException.php
│   │   └── InvalidTokenException.php
│   ├── Helpers
│   │   └── Helper.php
│   ├── Infrastructure
│   │   ├── Bootstrap.php
│   │   ├── Model.php
│   │   └── Repository.php
│   ├── Model
│   │   ├── LinkModel.php
│   │   └── ProductModel.php
│   ├── Repository
│   │   ├── LinkRepository.php
│   │   └── ProductRepository.php
│   ├── Services
│   │   └── WooCommerce
│   │       ├── Checkout
│   │       ├── Logs
│   │       │   └── Logger.php
│   │       ├── Orders
│   │       ├── Thankyou
│   │       ├── Webhooks
│   │       ├── Webhooks.php
│   │       └── WooCommerce.php
│   └── Views
│       ├── Admin
│       │   └── menus
│       │       └── settings
│       │           ├── index.php
│       │           └── modal.php
│       ├── Pages
│       │   └── checkout
│       │       └── index.php
│       └── WooCommerce
├── assets
│   ├── images
│   │   └── icons
│   ├── scripts
│   │   ├── admin
│   │   │   └── menus
│   │   │       └── settings
│   │   │           ├── index.js
│   │   │           └── table.js
│   │   ├── components
│   │   └── theme
│   │       └── pages
│   │           └── checkout
│   │               └── index.js
│   └── styles
│       └── app.css
├── composer.json
├── package.json
├── readme.txt
├── tailwind.config.js
└── wc-payment-link.php
```

