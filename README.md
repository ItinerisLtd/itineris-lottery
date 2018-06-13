# Itineris Lottery

Custom post type for lottery results

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Minimum Requirements](#minimum-requirements)
- [Installation](#installation)
- [CSV Importer](#csv-importer)
  - [Requirements](#requirements)
  - [Doesn't Matter](#doesnt-matter)
  - [Empty Rows](#empty-rows)
  - [Encoding](#encoding)
  - [Good Example](#good-example)
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

- PHP v7.1
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

### Requirements

- The first row must be lowercase headers(`draw,prize,ticket`)

### Doesn't Matter

- Column ordering
- Row ordering
- Separator

### Empty Rows

Totally empty rows(e.g: `,,`) will be ignored.
You should double check with the exporter whether these empty rows are bugs.


Rows can't be partially empty.
For example, these rows will make the importation fails:
```csv
draw,prize,ticket
11-Jan-2018,,
,GPB 100 Cash,
,,100002
18-May-2018,,123456
18-May-2018,GPB 100 Cash,
,GPB 100 Cash,200002
```

### Encoding

Preferred encoding is `UTF-8`. 
Non UTF-8 characters will either be converted or stripped.

### Good Example 

```csv
draw,prize,ticket
11-Jan-2018,GPB 999 Cash,123456
11-Jan-2018,GPB 100 Cash,100001
,,
,,
11-Jan-2018,GPB 100 Cash,100002
18-May-2018,GPB 999 Cash,123456
18-May-2018,GPB 100 Cash,200001
18-May-2018,GPB 100 Cash,200002
Christmas Special,Private Jet x1,123456
Christmas Special,Trip to Hong Kong,abcd1234
,,
Christmas Special,Honda Civic,Santa Claus
```

See also: [example.csv](./example.csv)

## Public API

### Rules

Draws, prizes, tickets and results are custom taxonomies and custom post type. 
Although you can **read** them via WordPress functions(e.g: `WP_Query`), you should prefer using [`Repositories`](./src/Repositories) over WordPress functions. 


For **write** operations, only use the followings:
 - the importer page on WP admin dashboard
 - `ResultRepo::findOrCreate`
 - `CSVImporter::import`

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
