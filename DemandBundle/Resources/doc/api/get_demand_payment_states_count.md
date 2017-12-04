GET All Payment Demands States Count
=========

Returns a list of Payment Demands States Count

Request Example
--------------

```  http://izuma.loc/api/v1/demands/payments/states ```


### Result Fields Description
--------------------------

|  Field                    | Type      | Description                               | Note                                  |
|:-------------------------:|:---------:|-------------------------------------------|---------------------------------------|
| **state**                 | string    | demand state                              |                                       |
| **total**                 | integer   | total                                     |                                       |

Returned Data Example
--------------------------

```
[
    {
        "total":"2",
        "state":"reject"
    },
    {
        "total":"1",
        "state":"waiting_for_drh"
    },
    {
        "total":"7",
        "state":"waiting_for_manager"
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