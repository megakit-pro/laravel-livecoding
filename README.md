## Installation

```bash
./scripts/install.sh
```

## Tasks

* Implement the `ReviewController` in `app/Http/Controllers/ReviewController.php`
* Add authorization to the `update` and `destroy` methods with the following rules:
    * Only the author of the review can update or delete it
* Run the tests:
  * All tests should pass

## Additional tasks
* Implement forbidden words logic:
  * Add forbidden words to the config file: `config/reviews.php`
  * If a title of a review contains a forbidden word, the review should not be saved and a response with status code 400 should be returned
  * The logic should be applied to the `create` and `update` methods
