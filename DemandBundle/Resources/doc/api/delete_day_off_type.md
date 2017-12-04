DELETE Day Off Type
=========

Makes deletion of the day off type

Request Example
--------------

```  http://izuma.loc/api/v1/day-off-types ```

Request Parameters
-----------------

| Parameter      | Required      | Type     | Description                                           |
|:--------------:|:-------------:|:--------:|-------------------------------------------------------|
| **id**         | yes           | integer  | day off type identifier                               |

Statuses of the Answer
--------------

| Code    | Description                                                                 |
|:-------:|-----------------------------------------------------------------------------|
| **200** | Returned in case when the request was successful                            |
| **401** | Returned in case when the user is not authorized                            |
| **404** | Returned in case when item not found                                        |
| **500** | Returned in case when an unexpected error occurred on the server            |

[To the list of API methods](./src/AppBundle/Resources/doc/api/index.md)