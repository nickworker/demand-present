GET All Day Off Types
=========

Returns a list of use rest day offs.

Request Example
--------------

```  http://izuma.loc//api/v1/rest-day-offs/users/{userId} ```

Request Parameters
-----------------

| Parameter         | Required      | Type     | Description            | Note                                  |
|:-----------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **userId**        | no            | integer  | User identifier        | If omitted will take current user     |

### Result Fields Description
--------------------------

|  Field                    | Type     | Description                                                    | Note                                                          |
|:-------------------------:|:--------:|----------------------------------------------------------------|---------------------------------------------------------------|
| **id**                    | integer  | day off type identifier                                        |                                                               |
| **type**                  | object   | day off type object                                            | Description in a separate table below                         |
| **amount**                | integer  | day off amount                                                 |                                                               |
| **status**                | string   | day off status                                                 | active - changed by day off request, init -changed by parent  |

### The content of the object day off type

|  Field                    | Type     | Description                                                    | Note        |
|:-------------------------:|:--------:|----------------------------------------------------------------|-------------|
| **id**                    | integer  | day off type identifier                                        |             |
| **title**                 | string   | day off type title                                             |             |
| **is_disabled**           | boolean  | checks is day off disabled                                     |             |
| **is_auto**               | boolean  | check is day off auto mode enabled                             |             |

Returned Data Example
--------------------------

```
[
    {
        "type":
        {
            "id":5,
            "title":"CP",
            "is_auto": true,
            "is_disabled": false
        },
        "amount":0,
        "status":"active"
    },
    {
        "type":
        {
            "id":5,
            "title":"CP",
            "is_auto": false,
            "is_disabled": false
        },
        "amount":0,
        "status":"init"
    }
]
```

Statuses of the Answer
--------------

| Code    | Description                                                                 |
|:-------:|-----------------------------------------------------------------------------|
| **200** | Returned in case when the request was successful                            |
| **401** | Returned in case when the user is not authorized                            |
| **500** | Returned in case when an unexpected error occurred on the server            |

[To the list of API methods](./src/AppBundle/Resources/doc/api/index.md)