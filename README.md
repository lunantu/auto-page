# Lunantu Auto Page

This library to make laravel auto route from view structur.

## Installation

To install this library you need `Composer` and run this command.

```bash
composer requre lunantu/auto-page
```

## Usage

Create a web folder inside views containing folders and blades with the following structure.

Folders and blades inside web
```html
web
|-index.blade.php
|-about.blade.php
|-news
| |-index.blade.php
| |-{title}
|   |-index.blade.php
|-produk
  |-index.blade.php
  |-{name}
    |-detail.blade.php
```
Will result route pattern
```html
/
/about
/news
/news/{title}/
/produk
/produk/{name}/detail
```
Add this code inside route
``` 
AutoPage::route()
```
Paramater in route like {title} can be access from blade file with $title variable.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://github.com/lunantu/auto-page/blob/master/LICENSE)