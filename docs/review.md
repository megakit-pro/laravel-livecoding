# Review API

The Review API allows you to create, read, update, and delete reviews.

## Get all reviews

To get all reviews, send a `GET` request to the `/api/reviews` endpoint.

```http request
GET /api/reviews
```

Response (`200 OK`):

```json
{
    "data": [
        {
            "id": 1,
            "author_name": "John Doe",
            "title": "My review",
            "body": "This is my review",
            "rating": 5
        },
        {
            "id": 2,
            "author_name": "John Doe",
            "title": "My second review",
            "body": "This is my second review",
            "rating": 4
        }
    ]
}
```

## Get a latest review

To get a latest review, send a `GET` request to the `/api/reviews/latest` endpoint.

```http request
GET /api/reviews/latest
```

Response (`200 OK`):

```json
{
    "data": {
        "id": 1,
        "author_name": "John Doe",
        "title": "My review",
        "body": "This is my review",
        "rating": 5
    }
}
```

## Create a review

To create a review, send a `POST` request to the `/api/reviews` endpoint with the following parameters:

| Parameter | Type   | Rules                    |
|-----------|--------|--------------------------|
| title     | string | required, max length 50  |
| body      | string | required, max length 500 |
| rating    | int    | required, min 1, max 5   |

```http request
POST /api/reviews
Content-Type: application/json

{
    "title": "My review",
    "body": "This is my review",
    "rating": 5
}
```

Response (`201 Created`):

```json
{
    "data": {
        "id": 1,
        "author_name": "John Doe",
        "title": "My review",
        "body": "This is my review",
        "rating": 5
    }
}
```

## Get the specified review

To get the specified review, send a `GET` request to the `/api/reviews/{id}` endpoint.

```http request
GET /api/reviews/1
```

Response (`200 OK`):

```json
{
    "data": {
        "id": 1,
        "author_name": "John Doe",
        "title": "My review",
        "body": "This is my review",
        "rating": 5
    }
}
```

## Update the specified review

To update the specified review, send a `PUT` request to the `/api/reviews/{id}` endpoint with the following parameters:

| Parameter | Type   | Rules                    |
|-----------|--------|--------------------------|
| title     | string | required, max length 50  |
| body      | string | required, max length 500 |
| rating    | int    | required, min 1, max 5   |

```http request
PUT /api/reviews/1
Content-Type: application/json

{
    "title": "My review",
    "body": "This is my review",
    "rating": 5
}
```

Response (`200 OK`):

```json
{
    "data": {
        "id": 1,
        "author_name": "John Doe",
        "title": "My review",
        "body": "This is my review",
        "rating": 5
    }
}
```

## Delete the specified review

To delete the specified review, send a `DELETE` request to the `/api/reviews/{id}` endpoint.

```http request
DELETE /api/reviews/1
```

Response (`204 No Content`):
