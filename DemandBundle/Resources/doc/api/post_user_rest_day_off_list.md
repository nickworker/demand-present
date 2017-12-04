POST User Rest Day Off
=========

Makes User Rest Day Off Modification

Request Example
--------------

```  http://izuma.loc/api/v1/rest-day-offs/users/{userId} ```

Request Parameters
-----------------

| Parameter                     | Required      | Type     | Description            | Note                                  |
|:-----------------------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **user_day_off_rest_form**    | yes           | object   | Rest Day Off Object    | Description in a separate table below |
| **userId**                    | no            | integer  | User identifier        | If omitted will take current user     |

### The content of the object rest day off

|  Field        | Type      | Description                                                              | Note                                   |
|:-------------:|:---------:|--------------------------------------------------------------------------|----------------------------------------|
| **days**      | array     | day object                                                               |  Description in a separate table below |

### The content of the object day

|  Field        | Type      | Description                                                              | Note        |
|:-------------:|:---------:|--------------------------------------------------------------------------|-------------|
| **type**      | integer   | day off type identifier                                                  |             |
| **amount**    | integer   | days amount                                                              |             |

Request Data Example
---------------------

```
{
"user_day_off_rest_form":
    {
        "days": [
          {
            "type": 1,
            "amount": 1
          },
          {
            "type": 4,
            "amount": 1
          }
        ]
    }
}
```

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
| **system_name**           | string   | day off type system name                                       |             |

Returned Data Example
--------------------------

```
[
    {
        "type":
        {
            "id":5,
            "title":"CP",
            "system_name":"cp"
        },
        "amount":0,
        "status":"active"
    },
    {
        "type":
        {
            "id":5,
            "title":"CP",
            "system_name":"cp"
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
| **400** | Returned in case when invalid data was received                             |
| **401** | returned in case when the user is not authorized                            |
| **500** | Returned in case when an unexpected error occurred on the server            |

[To the list of API methods](./src/AppBundle/Resources/doc/api/index.md)