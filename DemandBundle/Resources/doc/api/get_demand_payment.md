GET Payment Demand
=========

Returns a list of Payment Demand.

Request Example
--------------

```  http://izuma.loc/api/v1/demands/payments/{id} ```

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
| **year**                  | integer   | demand year                                                    |                                          |
| **month**                 | integer   | demand month                                                   |                                          |
| **amount**                | float     | demand amount                                                  |                                          |
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
    "state": "waiting_for_manager",
    "year":1,
    "month":1,
    "amount": 12
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