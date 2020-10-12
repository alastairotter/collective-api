<?php 
function connectDB() { 
    global $HOST, $USERNAME, $PASSWORD, $DATABASE;
    return $db = new mysqli($HOST, $USERNAME, $PASSWORD, $DATABASE);
   
    if($db->connect_errno > 0){ 
        $res['response'] = "There was an error connecting to the database";
        $res= json_encode($res);
        echo $res;
        exit;
    }
}

// source idea: https://github.com/mevdschee/php-crud-api
function matchingType($type, $value) { 
    
    switch ($type) { 
        case "cs":
            $matching = "LIKE";
            $value = "%" . $value . "%";
            return "$matching '$value'";
        break;
        case "ncs": 
            $matching = "NOT LIKE";
            $value = "%" . $value . "%";
            return "$matching '$value'";
        break;
        case "sw":
            $matching = "LIKE";
            $value = $value . "%";
            return "$matching '$value'";
        break;
        case "nsw": 
            $matching = "NOT LIKE";
            $value = $value . "%";
            return "$matching '$value'";
        break;
        case "ew":
            $matching = "LIKE";
            $value = "%" . $value;
            return "$matching '$value'";
        break;
        case "new": 
            $matching = "NOT LIKE";
            $value = "%" . $value;
            return "$matching '$value'";
        break;
        case "eq":
            $value = $value;
            return "= '$value'";
        break;
        case "neq": 
            $value = $value;
            return "<> '$value'";
        break;
        case "lt":
            $value = $value;
            return "< '$value'";
        break;
        case "nlt": 
            $value = $value;
            return "> '$value'";
        break;
        case "gt":
            $value = $value;
            return "> '$value'";
        break;
        case "ngt": 
            $value = $value;
            return "< '$value'";
        break;
        case "lte":
            $value = $value;
            return "<= '$value'";
        break;
        case "nlte": 
            $value = $value;
            return ">= '$value'";
        break;
        case "gte":
            $value = $value;
            return ">= '$value'";
        break;
        case "ngte": 
            $value = $value;
            return "<= '$value'";
        break;
        case "bt":
            $value = explode(",",$value);
            return "BETWEEN '$value[0]' AND '$value[1]'";
        break;
        case "nbt": 
            $value = explode(",",$value);
            return "NOT BETWEEN '$value[0]' AND '$value[1]'";
        break;
        case "in":
            $value = explode(",", $value);
            $list = "'" . implode("','", $value) . "'";
            return "IN ($list)";
        break;

        case "nin":
            $value = explode(",", $value);
            $list = "'" . implode("','", $value) . "'";
            return "NOT IN ($list)";
        break;


    }
    
    
    
}

?>