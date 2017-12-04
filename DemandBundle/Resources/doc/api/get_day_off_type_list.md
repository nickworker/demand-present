GET All Day Off Types
=========

Returns a list of day off types.

Request Example
--------------

```  http://izuma.loc/api/v1/day-off-types?page=1&filter[is_disabled]=yes&filter[is_auto]=no ```

Request Parameters
-----------------

| Parameter         | Required      | Type     | Description                            | Note                                  |
|:-----------------:|:-------------:|:--------:|----------------------------------------|---------------------------------------|
| **page**          | no            | integer  | Page number                            |                                       |
| **is_disabled**   | no            | string   | checks day off type disabled mode      | Allowed "yes" or "no"                 |
| **is_auto**       | no            | string   | checks day off type auto mode          | Allowed "yes" or "no"                 |

### Result Fields Description
--------------------------

|  Field                    | Type      | Description                               | Note                                  |
|:-------------------------:|:---------:|-------------------------------------------|---------------------------------------|
| **list**                  | array     | List of results                           | Description in a separate table below |
| **count_per_page**        | integer   | Number of results on page                 |                                       |
| **count**                 | integer   | results amount                            |                                       |
| **page_count**            | integer   | pages amount                              |                                       |

### An Array list
--------------------------

|  Field                    | Type     | Description                                                    | Note        |
|:-------------------------:|:--------:|----------------------------------------------------------------|-------------|
| **id**                    | integer  | day off type identifier                                        |             |
| **title**                 | string   | day off type title                                             |             |
| **is_disabled**           | boolean  | checks is day off disabled                                     |             |
| **is_auto**               | boolean  | check is day off auto mode enabled                             |             |
| **period**                | string   | day off type period                                            |             |
| **days_amount**           | integer  | day off type days amount                                       |             |
| **created_at**            | date     | day off type creation date                                     |             |

Returned Data Example
--------------------------

```
[
    {
        "id":1,
        "title":"Sans solde",
        "system_name": "without_payment",
        "is_disabled":false,
        "is_auto":true,
        "created_at":1510876801
    },
    {
        "id":28,
        "title":"Fake Title 4",
        "is_disabled":false,
        "period":"month",
        "is_auto":true,
        "days_amount":1,
        "created_at":1510876800
    }
]
```

Statuses of the Answer
--------------

| Code    | Description                                                                 |
|:-------:|-----------------------------------------------------------------------------|
| **200** | Returned in case when the request was successful                            |
| **401** | returned in case when the user is not authorized                            |
| **500** | Returned in case when an unexpected error occurred on the server            |

[To the list of API methods](./src/AppBundle/Resources/doc/api/index.md)