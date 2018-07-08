# <center>Math Records Web Service</center>

## Introduction
A RESTFUL API that do basic mathmatical operations on records like addition, subtraction, multiplicationn and division. 

## Examples

Register an account by sending POST request to https://mathrecords.herokuapp.com/api/register with email, name, password, c_password

Response
``` 
{
    "success": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjQ2ODk5NDliYzcxMDMwNGY1OTExMTdjOGU2NjEzYTY3YTA3YjBiNjA5YjZhZWYyNmZjN2VkM2M2OTkwMTY1OTk4MWRkNzkyZGZmMTgzMDUyIn0.eyJhdWQiOiIxIiwianRpIjoiNDY4OTk0OWJjNzEwMzA0ZjU5MTExN2M4ZTY2MTNhNjdhMDdiMGI2MDliNmFlZjI2ZmM3ZWQzYzY5OTAxNjU5OTgxZGQ3OTJkZmYxODMwNTIiLCJpYXQiOjE1MzA5NzYyNDEsIm5iZiI6MTUzMDk3NjI0MSwiZXhwIjoxNTYyNTEyMjQxLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.j3J_ykIkP_-e59wOD3t19tzVF8M0Cr8PtMKBnssN4Z6WKRGQLnKRpIsssC14jkOovVfNjIJ7eRUXvgrdCRr-Q7aTB_LBDkgDUSqAG8tASft6Yu4vxHOYheO1knK9S8XBDr1M8mw7ndwgnu1ZQ-r3sASImJulWIQPizZFe1_46k1uUQYdPmf0NCQwltPBzkAz0_cZ1OVKX5FExyIrEi4ME9HfvAfPCTlqtpU0e_JueQLsWg0ogTn6qJfPuWGmdIcyEKJzTEfFaR7km8SQ0KITkBk8kV0gEmjBiqwJw1iPxr31E-ep_wf6Q-u935eRtpIKEGLjdy-ifm-ZI8Ktx06feAP4G3S2-Qul-DWbWLVxiCGA-qxngtYlXythFfwsilY-b597wo1g0ZykMxnyZnkRPGSAZRPnWU9EYWcT2tXQrByo4SVgWMXMEkAht98daAZRpwBIVL6yKFrzqvZHmYgPwg58bkFiDRoWrey6qv2dbc7JGwrzeExvBhP36oFrp40CMi7VaaS3wR1mJk8RsJX8ZPfPkwe-raDmi_RonMXLeYyPlgXF0FIffoJWFtY2dSWFgVRvQdxoohpUV5sxjAw3hFGEqojzlxS5i-hKlOy7ddRP8xYaDVLdOxbnuxerKISXa0K6xrTyqndL8sE_RhgYUgJMtSJxzkrJMZQClFGe9g0",
        "name": "amir"
    }
}
```

Creating a record by sending POST request to https://mathrecords.herokuapp.com/api/record/create with base value also  header should contain a valid token 

Response
```
{
    "success": {
        "step": 0,
        "operation": "create",
        "value": "50",
        "id": "71128"
    }
}
```

Forking a record of id 21570 by sending GET request to  https://mathrecords.herokuapp.com/api/record/fork/21570 

Response 
```
{
    "success": {
        "id": "90470",
        "value": "200",
        "reference": 21570
    }
}
```



## Routes

| Method  | URI               | Params|Header|Name   |
| ------- | ------------------|-----|--|--------|
|POST|api/register |email, name, password, c_password||Registeration|
|POST|api/login |email, password||Login|
|POST|api/get-details||Authorization, Accept|User Details|
|GET|api/record ||Authorization, Accept|Display Records|
|POST|api/record/create |value|Authorization, Accept|Create Record|
|DELETE|api/record/{record} ||Authorization, Accept|Delete Record|
|GET|api/record/{record}||Authorization, Accept|Record Status|
|PUT|api/record/{record} |value, operation|Authorization, Accept, Content-Type|Perform Operation|
|GET|api/record/history/{record} ||Authorization, Accept|Record History|
|POST|api/record/freeze/{record} ||Authorization, Accept|Freeze Record|
|POST|api/record/unfreeze/{record} ||Authorization, Accept|Unfreeze Record|
|GET|api/record/rollback/{record} ||Authorization, Accept|Rollback|
|GET|api/record/op-count/{record} ||Authorization, Accept|Operations Count|
|GET|api/record/op-count/all ||Authorization, Accept|All Ops Count|
|GET|api/record/fork/{record} ||Authorization, Accept|Fork Record|
| | |

## Notes

Header data must be as follows:
* Authorization = Bearer 'yourToken'
* Accept = application/json
* Content-Type = application/x-www-form-urlencoded

Params data types
* email : String - should follow email structure
* password, c_password, name : String
* value : Numeric
* operation : String - must be [ add, sub, div, mul ]

## Built with
* [Php](http://php.net/) using [Laravel](https://laravel.com) framework