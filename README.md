# Mapper

Generates your Elasticsearch mappings from attributes on your classes.

## Example Code

```php
#[Alias('my_documents')]
#[Alias('after_2015', ['filter' => ['term' => ['uploaded' => ['gte' => '2015-01-01']]]])]
#[Setting(['number_of_shards' => 1, 'number_of_replicas' => 3])]
#[Setting(['refresh_interval' => '5s'])]
class MyDocument
{
    #[Field('text')]
    public string $title;
    #[Field('int', ['doc_values' => true])]
    public array $someNumbers;
    #[Subfield('title', 'text')]
    public string $englishTitle;
    #[Field('date')]
    public DateTime $uploaded;
}

$generator = new Mapper\Generator();
$mapping = json_encode($generator->generate(MyDocument::class));
```

This generates a mapping:
```json
{
    "properties": {
        "title": {
            "type": "text",
            "fields": {
                "englishTitle": {
                    "type": "text"
                }
            }
        },
        "someNumbers": {
            "type": "int",
            "doc_values": true
        },
        "uploaded": {
            "type": "date"
        }
    },
    "settings": {
        "number_of_shards": 1,
        "number_of_replicas": 3,
        "refresh_interval": "5s"
    },
    "aliases": {
        "my_documents": {},
        "after_2015": {
            "filter": {
                "term": {
                    "uploaded": {
                        "gte": "2015-01-01"
                    }
                }
            }
        }
    }
}
```

## Development

Install dependencies using `composer`. There are 4 tasks you can run via `composer`: `phpunit`, `code-style`, `static-analysis`, or the combination of all three `test`.
