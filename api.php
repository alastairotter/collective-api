<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/html; charset=utf-8');
header('Content-Type: application/json');

include_once("config.php");

$res = array();

$db = new mysqli($HOST, $USERNAME, $PASSWORD, $DATABASE);
if($db->connect_errno > 0){ 
   
    $res['response'] = "There was an error connecting to the database";
    $res= json_encode($res);
    echo $res;
    exit;
}

// GET JSON
$json = file_get_contents('php://input');
$data = json_decode($json);

if(property_exists($data, "apikey")) { 
    if($data->apikey != $APIKEY) {
        $res['error'] = "You need a valid API key to access this data";
        $res = json_encode($res);
        echo $res;
        exit;  
    }
}
else { 
    $res['error'] = "You need a valid API key to access this data";
    $res = json_encode($res);
    echo $res;
    exit;
}


$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$uriParts = parse_url($url);
$path = $uriParts['path'];

list($q, $qpath) = explode("api.php", $path);
$table = str_replace("/records/", "", $qpath);



if(property_exists($data, "limit")) { 
    $limit = $data->limit;
}

if(property_exists($data, 'columns')) { 
    $columns = $data->columns;
}

if(property_exists($data, 'order')) { 
    $order = $data->order;
}

if(property_exists($data, 'filters')) { 
    $filters = $data->filters;
}

// get headers
if(property_exists($data, "schema")) { 
    $headers = array();
    $query = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='$table'";
    $query = $db->query($query);
    while($row = $query->fetch_assoc()) { 
            foreach($row as $r) { 
            array_push($headers, $r);
            }       
        }
        $res['schema'] = $headers;
   
}

if(property_exists($data, "id")) { 
    $id = $data->id;
    unset($columns);
    unset($limit);
    unset($order);
    unset($filters);
}

$sql = "SELECT";

// CONSTRUCT QUERY
/////////// COLUMNS
if(isset($columns)) { 
  
    $cols = " ";
    foreach($columns as $c) { 
      
        $cols .= $c . ",";
    }
    $cols = rtrim($cols, ',');
  
    $sql .= $cols;
   
}
else { 
    $sql .= " * ";
}

$sql .= " FROM $table ";

/////////// ID
if(isset($id)) { 
    $sql .= " WHERE id = $id ";
}

/////////// FILTERS
if(isset($filters)) { 
    
    // var_dump($filters);
    $filter = "";
    $matching = "LIKE";
    $value = "";
    $count = count($filters);
    
    $iterate = 1;
    foreach($filters as $f) { 
     
        if($f->type == "contains") { 
            $matching = "LIKE";
            $value = "%" . $f->value . "%";
        }

       $filter .= " $f->name $matching '$value' ";
        if($count > 1 && $iterate < $count) { 
            $filter .= " AND  ";
        }
        $iterate++;
        
        }

       $sql .= " WHERE " . $filter . " ";
    //    if($count > 1 && $count )

   

         
}

/////////// ORDER
if(isset($order)) {
    $sql .= " ORDER BY $order[0] $order[1]";
}

////////// LIMIT
if(isset($limit)) { 
    $sql .= " LIMIT $limit[0], $limit[1]";
}

// query table
$query = $sql;
$query = $db->query($query);
while($row = $query->fetch_assoc()) { 
    foreach($row as $r => $value) { 
        $row[$r] = htmlspecialchars($row[$r]);
    }
    $res['data'][] = $row;
}

if(property_exists($data, "query")) { 
    if($data->query) { 
    $res['query'] = $sql;
    }
}
$res = json_encode($res);
echo $res;

?>





