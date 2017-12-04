GET Day Off Type
=========

Returns a day off type.

Request Example
--------------

```  http://izuma.loc/api/v1/day-off-types/{id} ```

Request Parameters
-----------------

| Parameter         | Required      | Type     | Description                | Note                                  |
|:-----------------:|:-------------:|:--------:|----------------------------|---------------------------------------|
| **id**            | yes           | integer  | day off type identifier    |                                       |

### Result Fields Description
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
{
    "id":28,
    "title":"Fake Title 4",
    "is_disabled":false,
    "period":"month",
    "is_auto":true,
    "days_amount":1,
    "created_at":1510876800
}
```

Statuses of the Answer
--------------

| Code    | Description                                                                 |
|:-------:|-----------------------------------------------------------------------------|
| **200** | Returned in case when the request was successful                            |
| **401** | returned in case when the user is not authorized                            |
| **500** | Returned in case when an unexpected error occurred on the server            |

[To the list of API methods](./src/AppBundle/Resources/doc/api/index.md)