## Installation

To install and run the project, run the following commands:

```bash
./scripts/install.sh
```

## Tasks

* Implement methods of the `ReviewController` in `app/Http/Controllers/ReviewController.php`
* Add authorization to the `update` and `destroy` methods with the following rules:
    * Only the author of the review can update or delete it
* Run the tests:
  * All tests must be passed

## API requirements

* Responses must be in JSON format
* JSON must contain only the fields specified in the examples and nothing more
* Responses must contain a status code according to the HTTP standard
* Responses with a successful status code must contain an `data` object
* Input data must be validated in requests for creating and updating reviews 

### Example of a review object:

```json
{
    "id": 1,
    "author_name": "John Doe",
    "title": "My review",
    "body": "This is my review",
    "rating": 5
}
```

* `author_name` - the name of the author of the review (`$review->author->name`)
* `title` - the title of the review (`$review->title`)
* `body` - the text of the review (`$review->body`)
* `rating` - the rating of the review (`$review->rating`)

## Additional tasks

* Implement forbidden words logic:
  * Add forbidden words to the config file: `config/reviews.php`
  * If a `title` of a review contains a forbidden word, the review should not be saved and a response with status code `400 Bad Request` should be returned
  * The logic should be applied to the `create` and `update` methods
