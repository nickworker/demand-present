POST Day Off Type
=========

Makes Day Off Type Creation

Request Example
--------------

```  http://izuma.loc/api/v1/day-off-types ```

Request Parameters
-----------------

| Parameter             | Required      | Type     | Description            | Note                                  |
|:---------------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **day_off_type_form** | yes           | object   | Day Off Type Object    | Description in a separate table below |

### The content of the day off type object

|  Field                | Type      | Description                                               | Note                                     |
|:---------------------:|:---------:|-----------------------------------------------------------|------------------------------------------|
| **title**             | string    | day off type title                                        |                                          |
| **is_disabled**       | boolean   | checks is day off disabled                                |                                          |
| **is_auto**           | boolean   | check is day off auto mode enabled                        |                                          |
| **period**            | string    | day off type period                                       | Allowed "month" or "year"                |
| **days_amount**       | integer   | day off type days amount                                  |                                          |
| **created_at**        | date      | day off type creation date                                |                                          |

Request Data Example
---------------------

```
{
"day_off_type_form":
    {
        "title" : "Fake Title",
        "is_auto": 1,
        "period": "year",
        "days_amount": 30,
        "is_disabled": 1
    }
}
```

### Result Fields Description
--------------------------

|  Field            | Type      | Description                                                                                   | Note        |
|:-----------------:|:---------:|-----------------------------------------------------------------------------------------------|-------------|
| **id**            | string    | day off type identifier                                                                       |             |

Returned Data Example
--------------------------

```
{
    "id": 115
}
```

Statuses of the Answer
--------------

| Code    | Description                                                                 |
|:-------:|-----------------------------------------------------------------------------|
| **201** | Returned in case when the request was successful                            |
| **400** | Returned in case when invalid data was received                             |
| **401** | Returned in case when the user is not authorized                            |
| **500** | Returned in case when an unexpected error occurred on the server            |

[To the list of API methods](./src/AppBundle/Resources/doc/api/index.md)