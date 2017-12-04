GET Day Off Demand
=========

Returns a list of Day Off Demand.

Request Example
--------------

```  http://izuma.loc/api/v1/demands/day-offs/{id} ```

Request Parameters
-----------------

| Parameter         | Required      | Type     | Description            | Note                                  |
|:-----------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **id**            | yes           | integer  | Demand Identifier      |                                       |

### Result Fields Description
--------------------------

|  Field                    | Type      | Description                                                    | Note                                     |
|:-------------------------:|:---------:|----------------------------------------------------------------|------------------------------------------|
| **id**                    | integer   | demand identifier                                              |                                          |
| **started_at**            | integer   | demand started at date                                         | UNIX format                              |
| **ended_at**              | integer   | demand ended at   date                                         | UNIX format                              |
| **type**                  | float     | demand day off type object                                     | Description in a separate table below    |
| **note**                  | string    | demand note                                                    |                                          |
| **user**                  | string    | demand user                                                    | Description in a separate table below    |
| **state**                 | string    | demand state                                                   |                                          |


### Object user

|  Field                    | Type      | Description                                                   | Note  |
|:-------------------------:|:---------:|---------------------------------------------------------------|-------|
| **id**                    | integer   | user identifier                                               |       |
| **last_name**             | string    | user last name                                                |       |
| **first_name**            | string    | user first name                                               |       |

Returned Data Example
--------------------------

```
{
    "id":1,
    "user":{
        "id":26,
        "first_name":"Lionel",
        "last_name":"Clemenson"
    },
    "state":"waiting_for_manager",
    "year":1,
    "month":1
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