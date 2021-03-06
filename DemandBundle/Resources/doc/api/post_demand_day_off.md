POST Demand Day Off
=========

Makes Demand Day Off Creation

Request Example
--------------

```  http://izuma.loc/api/v1/demands/day-offs ```

Request Parameters
-----------------

| Parameter                 | Required      | Type     | Description            | Note                                  |
|:-------------------------:|:-------------:|:--------:|------------------------|---------------------------------------|
| **demand_day_off_form**   | yes           | object   | Demand Day Off Object  | Description in a separate table below |

### The content of the Demand Day Off

|  Field                | Type      | Description                                                    | Note                             |
|:---------------------:|:---------:|----------------------------------------------------------------|----------------------------------|
| **started_at**        | date      | demand started at date                                         | String in format - dd/MM/yyyy    |
| **ended_at**          | date      | demand ended at   date                                         | String in format - dd/MM/yyyy    |
| **type**              | float     | demand day off type identifier                                 |                                  |

Request Data Example
---------------------

```
{
    "demand_day_off_form":
    {
      "started_at" : "11/12/2017",
      "ended_at" : "12/12/2017",
      "type": 1
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