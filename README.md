# Itineris Lottery

Custom post type for lottery results

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Minimum Requirements](#minimum-requirements)
- [Installation](#installation)
- [CSV Importer](#csv-importer)
  - [Encoding](#encoding)
  - [Default Transformer: 4-column](#default-transformer-4-column)
  - [Custom Transformer](#custom-transformer)
- [Public API](#public-api)
  - [Rules](#rules)
  - [Initializing Repositories](#initializing-repositories)
  - [Getting Terms(Draw, Prize, Ticket)](#getting-termsdraw-prize-ticket)
  - [Getting Results](#getting-results)
  - [Entities\Result](#entities%5Cresult)
- [Expectations](#expectations)
- [Capabilities](#capabilities)
- [Code Style](#code-style)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Minimum Requirements

- PHP v7.2
- WordPress v4.9.5

## Installation

```bash
# composer.json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:ItinerisLtd/itineris-lottery.git"
    }
  ]
}
```

```bash
$ composer require itinerisltd/itineris-lottery
```

## CSV Importer

### Encoding

Preferred encoding is `UTF-8`.
Non UTF-8 characters will either be converted or stripped.

### Default Transformer: 4-column

- The first row must be the headers(`draw,prize,ticket,winner`)
- Rows must contain `draw,prize,ticket`
- Rows without `draw,prize,ticket` will be ignored. You should double check with the exporter whether these rows are bugs
- `winner` defaults to `Anonymous` if not given.

See: [example.csv](./example.csv)

### Custom Transformer

A transformer turns a single CSV row into a [CSV\Record](./src/CSV/Record.php). Define custom transformers when clients refuse to export in 4-column format.

1. Read the comments in [TransformerInterface](./src/CSV/Transformers/TransformerInterface.php)
1. Read the expected string formats of [CSV\Record::__constructor](./src/CSV/Record.php)
1. Implement [TransformerInterface](./src/CSV/Transformers/TransformerInterface.php)
    ```php
    class MyCustomTransformer implements Itineris\Lottery\CSV\Transformers\TransformerInterfacerface
    {
      // Implement TransformerInterface
    }
    ```
1. Register the custom transformer
    ```php
    add_action(Itineris\Lottery\Plugin::PREFIX . 'register_transformers', function (Itineris\Lottery\CSV\TransformerCollection $transformerCollection) {
        $transformerCollection->add(
            'my-example-transformer-123', // Unique ID: Use lower-case alphabet, digits and hyphens only
            'Example - I am description' // Human-readable description.
            new MyCustomTransformer()
        );
    });
    ```

Examples:
 - [FourColumnTransformer](src/CSV/Transformers/FourColumnTransformer.php)
 - [custom-transformer-example](https://github.com/ItinerisLtd/itineris-lottery-custom-transformer-example)

## Public API

### Rules

Draws, prizes, tickets and results are custom taxonomies and custom post type.
Although you can **read** them via WordPress functions(e.g: `WP_Query`), you should prefer using [`Repositories`](./src/Repositories) over WordPress functions.


For **write** operations, only use the followings:
 - the importer page on WP admin dashboard
 - `ResultRepo::findOrCreate`
 - `Importer::import`

Do not attempt to insert a result by other methods **including WordPress functions**.


Except classes under [`Repositories`](./src/Repositories) and [`Entities`](./src/Entities) namespaces, consider all other classes are internal APIs, i.e: don't use them.

### Initializing Repositories

```php
use Itineris\Lottery\Repositories\Factory;

[
    'resultRepo' => $resultRepo,
    'drawRepo' => $drawRepo,
    'prizeRepo' => $prizeRepo,
    'ticketRepo' => $ticketRepo,
] = Factory::make();
```

### Getting Terms(Draw, Prize, Ticket)

```php
$draw = $drawRepo->findByName('Christmas Special');
// If 'Christmas Special' doesn't exist, $draw === null
// Otherwise, $draw is an Entities\Draw instance.

$draw->getName();
// 'Christmas Special'
```

```php
use Itineris\Lottery\Entities\Draw;

$draws = $drawRepo->all();
// Array of Entities\Draw instances.
// Only draws with one or more results will be returned.
// Draws without any results will not be returned.

$drawNames = array_map(function(Draw $draw): string {
    return $draw->getName();
}, $draws);
// ['11-Jan-2018', '18-May-2018', 'Christmas Special'];
```

`Draw`, `Prize` and `Ticket` all work the same way.

### Getting Results

```php
// Get all results of `Christmas Special`.
$christmasSpecialDraw = $drawRepo->findByName('Christmas Special');
$results = $resultRepo->whereTerms($christmasSpecialDraw);
// Array of Entities\Result instances.


// Get all `GPB 100 Cash` results on `11-Jan-2018`.
$draw = $drawRepo->findByName('11-Jan-2018');
$prize = $prizeRepo->findByName('GPB 100 Cash');
$results = $resultRepo->whereTerms($draw, $prize);
// Array of Entities\Result instances.


// Get results by ticket.
$ticket = $ticketRepo->findByName('123456');
$results = $resultRepo->whereTerms($ticket);
// Array of Entities\Result instances.
```

### Entities\Result

```
$resultArray = $resultRepo->whereTerms($xxx);
$result = $resultArray[0];

$result->getDraw();
// An Entities\Draw instance.

$result->getDrawName();
// Same as $result->getDraw()->getName();

$result->getDrawId();
// Same as $result->getDraw()->getId();

// Similar methods:
$result->getPrize();
$result->getPrizeName();
$result->getPrizeId();

$result->getTicket();
$result->getTicketId();
$result->getTicketName();
```

## Expectations

- Less than 100 results per draw
- Less than 500 rows per file when using the CSV importer page
- A caching plugin is caching `WP_Query`([Advanced Post Cache](https://github.com/Automattic/advanced-post-cache/) caches all queries by this plugin)

These are not hard limits.
Maximum number of results per draw and rows per CSV file depends on server resources and configurations.

## Capabilities

To delete `draws`, you need `manage_draws` capability.
By default, nobody is granted this capability.

With great power comes great responsibility:

A `result` must tie to a `draw`. You should only delete **orphan** `draws`.
Otherwise, fatal errors are guaranteed. Thus, `manage_draws` capability is intended for developers only.

## Code Style

Check your code style with `$ composer check-style`. It's a mix of PSR-1, PSR-2, PSR-4 and [WordPress Coding Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards).
Change [ruleset.xml](./ruleset.xml) when necessary.
