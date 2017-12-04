GET All Payment Demands
=========

Returns a list of Payment Demands.

Request Example
--------------

```  http://izuma.loc/api/v1/demands/payments?page=2&filter[state][]=waiting_for_manager&filter[state][]=waiting_for_drh ```

Request Parameters
-----------------

| Parameter         | Required      | Type     | Description            | Note                                  |
|:-----------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **page**          | no            | integer  | Page number            |                                       |
| **filter**        | no            | array    | array for filter       | Description in a separate table below |

### An Array of filter

|  Field            | Required      | Type     | Description            | Note                                                                  |
|:-----------------:|:-------------:|:--------:|------------------------|-----------------------------------------------------------------------|
| **state**         | no            | array    | demand states          | Allowed waiting_for_manager, waiting_for_drh, accept_by_drh, reject |

### Result Fields Description
--------------------------

|  Field                    | Type      | Description                               | Note                                  |
|:-------------------------:|:---------:|-------------------------------------------|---------------------------------------|
| **list**                  | array     | List of results                           | Description in a separate table below |
| **count_per_page**        | integer   | Number of results on page                 |                                       |
| **count**                 | integer   | results amount                            |                                       |
| **page_count**            | integer   | pages amount                              |                                       |

### An Array list

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
    "list":[
        {
            "id":1,
            "user":{
                "id":26,
                "first_name":"Lionel",
                "last_name":"Clemenson"
            },
            "state":"waiting_for_manager",
            "year":1,
            "month":1,
            "amount": 12
        },
    ],
    "count":11,
    "count_per_page":5,
    "page_count":3,
    "name_page_parameter":"page"
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