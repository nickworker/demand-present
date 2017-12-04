POST Demand Payment
=========

Makes Demand Payment Creation

Request Example
--------------

```  http://izuma.loc/api/v1/demands/payments ```

Request Parameters
-----------------

| Parameter                 | Required      | Type     | Description            | Note                                  |
|:-------------------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **demand_payment_form**   | yes           | object   | Demand Payment Object  | Description in a separate table below |

### The content of the Demand Payment

|  Field                | Type      | Description                                                    | Note                 |
|:---------------------:|:---------:|----------------------------------------------------------------|----------------------|
| **year**              | integer   | demand year                                                    |                      |
| **month**             | integer   | demand month                                                   |                      |
| **amount**            | float     | demand amount                                                  |                      |

Request Data Example
---------------------

```
{
    "demand_payment_form":
    {
        "year" : "2017",
        "month" : "11",
        "amount": 44
    }
}
```
### Result Fields Description
--------------------------

|  Field            | Type      | Description                                                                                   | Note        |
|:-----------------:|:---------:|-----------------------------------------------------------------------------------------------|-------------|
| **id**            | string    | demand identifier                                                                              |             |

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
| **401** | returned in case when the user is not authorized                            |
| **500** | Returned in case when an unexpected error occurred on the server            |

[To the list of API methods](./src/AppBundle/Resources/doc/api/index.md)