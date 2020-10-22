# Collective API #

A simple API for reading MySQL databases. 

## Configuration ##

1. Set configuration details in **config.php**, including at least $HOST, $USERNAME, $PASSWORD, $DATABSE. 
2. $APIKEY is optional, but recommended. If $APIKEY is not set in config file it will not be checked for so unnecessary to send with query. 

## Use ## 

1. The API end point is at "ROOT/collective-api/api.php/[TABLE NAME] For example: "https://mysite.com/collective-api/api.php/mydbtable"
2. Parameters are sent to the endpoint as POST and in JSON format. A typical request (in Javascript) would look like: 

```
fetch('http://localhost:8888/collective-api/api.php/mydbtable', {
    method: 'POST', 
    body: JSON.stringify(query),
}).then(data => data.json()).then(data => { 
    console.log(data)
});
```
3. By default the API returns 10 records at a time. You can override this by specifiying a limit in the form [0,10]. Alternatively you can set the "nolimit" parameter to "true" to return all records. **NOTE** if you have a large database this may cause an error. 

## Limitations ##
- Collective API does not write to the database.
- Large databases may return an error if all you attempt to retreive all records at once. Use the "limit" parameter to reduce the query to resolve this. 


## Parameters ##

The API accepts a number of parameters to filter the results of a query. 

The following are valid parameters: 

- "apikey" (string) - Required to access API if API key is set in config.php [optional]
- "info": (true, false) - Return information about the source table [optional]
- "id" (int) - Returns selected row [optional]
- "columns" (array) - Returns selected columns [optional, defaults to *]
- "limit" (array[start,end]) - Returns rows matching these limits [optional]
- "nolimit": (true, false) - Overrides automatic limit on the number of records returned. Large databases may return an error if this is set to true. Use the "limit" filter to reduce the number of results returned. 
- "order" (array[column, order (*ASC or DESC*)]) - Returns sorted response based on column and order [optional]
- "filters" (array{objects}) - Multiple filters for each request (see **"Filters"** below) [optional]

An example JSON query would look like: 
```
{
    apikey: "d1eCE6QsFn4yhZvm4GrdlWwsfzQqNqlJ",
    info: true, 
    columns: ["id", "name", "pm", "expertise", "surname"],
    limit: [0,20],
    order: ["id", "ASC"],
    filters: [ 
        {
        name: "expertise",
        type: "cs", 
        value: "gender"
        },
        {
        name: "id",
        type: "in",
        value: "5,10,20"
        }
    ]
}
```

## Filters ##

The "filters" parameter can take multiple conditions to filter the results of each query. Filters are passed as an array of objects. Each object within the filters have three properties: 
 1 - "name": the filed name
 2 - "type": the matching type
 3 - "value": the value to be matched

 A typical filters parameter would look like: 

```
filters: [
     { 
        name: "expertise",
        type: "contains", 
        value: "health"
     },
     { 
        name: "surname",
        type: "contains", 
        value: "smith"
     }
]
 ```

The available matching types are: 
- "cs": matches part of a string
- "sw": matches string at the start of the target
- "ew": matches string at the end of target
- "eq": exact match
- "lt": less than 
- "gt": greater than
- "lte": less than or equal to
- "gte": greater than or equal to
- "bt": between two values

All filter types can also be made negative by prepending "n" to the available types. So, "ncontains" means "does not contain".

## Prior work & references ##

- PHP_CRUD_API - https://github.com/mevdschee/php-crud-api
