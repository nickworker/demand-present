GET All Day Off Demands
=========

Returns a list of Day Off Demands.

Request Example
--------------

```  http://izuma.loc/api/v1/demands/day-offs?page=2&filter[state][]=waiting_for_manager&filter[state][]=waiting_for_drh&filter[user][]=21 ```

Request Parameters
-----------------

| Parameter         | Required      | Type     | Description            | Note                                  |
|:-----------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **page**          | no            | integer  | Page number            |                                       |
| **filter**        | no            | array    | array for filter       | Description in a separate table below |

### An Array of filter

|  Field            | Required      | Type     | Description            | Note                                                                  |
|:-----------------:|:-------------:|:--------:|------------------------|-----------------------------------------------------------------------|
| **state**         | no            | array    | demand states          | Allowed waiting_for_manager, waiting_for_drh, accept_by_drh, reject   |
| **user**          | no            | array    | user identifier        |                                                                       |

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
    "list":[
        {
            "id":14,
            "user":{
                "id":29,
                "first_name":"Colin",
                "last_name":"Maret"
            },
            "state": "waiting_for_manager",
            "started_at":1490821200,
            "ended_at":1490562000,
            "type":{
                "id":3,
                "title":"\u00c9v\u00e8nements familiaux"
            }
        }
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
