# Elastically, **Elastica** based framework

*This project is a work in progress.*

![Under Construction](https://jolicode.com/media/original/2019/construction.gif "Optional title")

**Feedback welcome!**

Opinionated [Elastica](https://github.com/ruflin/Elastica) based framework to bootstrap PHP and Elasticsearch implementations.

- DTO are first class citizen, you send object for documents, and get objects back, **like an ODM**;
- All indexes are versioned / aliased;
- Mappings are done in YAML;
- Analysis is separated from mappings;
- 100% compatibility with [ruflin/elastica](https://github.com/ruflin/Elastica);
- Designed for Elasticsearch 7+ (no types);
- Extra commands to monitor, update mapping, reindex... Commonly implemented tasks.

## Demo

Quick example of what the library do on top of Elastica:

```php
<?php

// Your own DTO, or one generated by Jane (see below)
class Beer
{
    public $foo;
    public $bar;
}

// Building the Index from a mapping config
use JoliCode\Elastically\Client;
use Elastica\Document;

// New Client object with new options
$client = new Client([
    // Where to find the mappings
    Client::CONFIG_MAPPINGS_DIRECTORY => __DIR__.'/configs',
    // What object to find in each index
    Client::CONFIG_INDEX_CLASS_MAPPING => [
        'beers' => Beer::class,    
    ],
]);

// Class to build Indexes
$indexBuilder = $client->getIndexBuilder();

// Create the Index in Elasticsearch
$index = $indexBuilder->createIndex('beers');

// Set the proper aliases
$indexBuilder->markAsLive($index, 'beers');

// Class to index DTO in an Index
$indexer = $client->getIndexer();

$dto = new Beer();
$dto->bar = 'American Pale Ale';
$dto->foo = 'Hops from Alsace, France';

// Add a document to the queue
$indexer->scheduleIndex('beers', new Document('123', $dto));
$indexer->flush();

// Force index refresh if needed
$indexer->refresh('beers');

// Get the Document (new!)
$results = $client->getIndex('beers')->getDocument('123');

// Get the DTO (new!)
$results = $client->getIndex('beers')->getModel('123');

// Perform a search
$results = $client->getIndex('beers')->search('alsace');

// Get the Elastic Document
$results->getDocuments()[0];

// Get the Elastica compatible Result
$results->getResults()[0];

// Get the DTO 🎉 (new!)
$results->getResults()[0]->getModel();

// Create a new version of the Index "beers"
$index = $indexBuilder->createIndex('beers');

// Set proper aliases
$indexBuilder->markAsLive($index, 'beers');

// Clean the old indices (close the previous one and delete the older)
$indexBuilder->purgeOldIndices('beers');
```

*configs/beers.yaml*

```yaml
# Anything you want, no validation
mappings:
    properties:
        foo:
            type: text
            analyzer: english
            fields:
                keyword:
                    type: keyword
```

## Configuration

This library add custom configurations on top of Elastica's:

### Client::CONFIG_MAPPINGS_DIRECTORY

The directory Elastically is going to look for YAML.

When creating a `foobar` index, a `foobar.yaml` file is expected.

If an `analyzers.yaml` file is present, **all** the indices will get it.

### Client::CONFIG_INDEX_CLASS_MAPPING
 
An array of index name to class FQN.

```php
[
  'indexName' => '\My\AwesomeDTO'
]
```

### Client::CONFIG_SERIALIZER (optional)

A `SerializerInterface` and `DenormalizerInterface` compatible object that will by used both on indexation and search.

Default to Symfony Object Normalizer which can be slow. See below for Jane usage.

*Todo: add custom demo?*

### Client::CONFIG_BULK_SIZE (optional)
    
When running indexation of lots of documents, this setting allow you to fine-tune the number of document threshold. Default to 100.

## Using Jane for DTO and fast Normalizers

To write.

## To be done

- some "todo" in the code
- optional Doctrine connector
- better logger
- optional Symfony integration (DIC)
  - web debug toolbar!
- scripts / commands for common tasks:
  - auto-reindex when the mapping change, handle the aliases and everything
  - micro monitoring for cluster / indexes
  - health-check method

## Sponsors

[![JoliCode](https://jolicode.com/images/logo.svg)](https://jolicode.com)

Open Source time sponsored by JoliCode.
