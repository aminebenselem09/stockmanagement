<?php
require "./vendor/autoload.php";
require 'C:/xampp/htdocs/vendor/firebase/php-jwt/src/BeforeValidException.php';
require 'C:/xampp/htdocs/vendor/firebase/php-jwt/src/ExpiredException.php';
require 'C:/xampp/htdocs/vendor/firebase/php-jwt/src/JWT.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');  

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("HTTP/1.1 200 OK");
        exit();
    }
include_once 'db.php';
$product = new Database();

$api = $_SERVER['REQUEST_METHOD'];


if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'],'/signup')) {
    $json_data = file_get_contents('php://input');

    $data = json_decode($json_data, true);

    if ($data === null) {
      
        http_response_code(400);
        echo json_encode(array("error" => "Invalid JSON data"));
        exit();
    }

  

    $username = isset($data['username']) ? $product->test_input($data['username']) : null;
    $email = isset($data['email']) ? $product->test_input($data['email']) : null;
    $password = isset($data['password']) ? $product->test_input($data['password']) : null;
   

    if ($username !== null && $email !== null && $password !== null ) {
        if ($product->insertUser($username, $email, $password)) {
            echo $product->message('user added successfully!', false);
        } else {
            echo $product->message('Failed to add a user!', true);
        }
    } else {
        http_response_code(400); 
        echo json_encode(array("error" => "Missing or invalid data"));
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'],'/signin')) {
    $json_data = file_get_contents('php://input');

    $data = json_decode($json_data, true);

    if ($data === null) {
       
    
        http_response_code(400); 
        echo json_encode(array("error" => "Invalid JSON data"));
        exit();
    }

    
    $username = isset($data['username']) ? $product->test_input($data['username']) : null;
    $email = isset($data['email']) ? $product->test_input($data['email']) : null;
    $password = isset($data['password']) ? $product->test_input($data['password']) : null;
    if ($username !== null || $email !== null && $password !== null) {

   $product->generateToken($username,$email,$password);
    }else {
     
        http_response_code(400);
        echo json_encode(array("error" => "Missing or invalid data"));
    }
}

if (!str_contains($_SERVER['REQUEST_URI'],'/signin')&& !str_contains($_SERVER['REQUEST_URI'],'/signup')){
if (!$product->validateToken()){
    header('HTTP/1.1 401 Unauthorized');
    exit;
}else{
   
    
    
    if ($api == 'GET' && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/products') {
        
            $data = $product->fetch();
        
        echo json_encode($data);
    }
    if ($api == 'GET' && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/categories') {
        
        $data = $product->fetchCategories();
    
    echo json_encode($data);
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/addproduct') {
        $json_data = file_get_contents('php://input');
    
        $data = json_decode($json_data, true);
    
        if ($data === null) {
           
            http_response_code(400);
            echo json_encode(array("error" => "Invalid JSON data"));
            exit();
        }
    
        $name = isset($data['name']) ? $product->test_input($data['name']) : null;
        $price = isset($data['price']) ? $product->test_input($data['price']) : null;
        $quantity = isset($data['quantity']) ? $product->test_input($data['quantity']) : null;
        $brand = isset($data['brand']) ? $product->test_input($data['brand']) : null;
        $category_id = isset($data['category_id']) ? $product->test_input($data['category_id']) : null;
    
        if ($name !== null && $price !== null && $quantity !== null && $brand !== null && $category_id !== null) {
            if ($product->insert($name, $price, $quantity, $brand, $category_id)) {
                echo $product->message('Product added successfully!', false);
            } else {
                echo $product->message('Failed to add a product!', true);
            }
        } else {
            http_response_code(400); 
            echo json_encode(array("error" => "Missing or invalid data"));
        }
    }
    
    if ($api == 'PUT' && isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'],'/update?')  ) {
        parse_str($_SERVER['QUERY_STRING'], $query_params);
        
        $id = intval($query_params['id'] ?? '');
        $price = floatval($query_params['price'] ?? '');
        $quantity = intval($query_params['quantity'] ?? '');
        
        if ($id !== null) {
            if ($product->update($price, $quantity, $id)) {
                echo $product->message('Product updated successfully!', false);
            } else {
                echo $product->message('Failed to update the product!', true);
            }
        } else {
            echo $product->message('Product not found!', true);
        }
        return $id;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'],'/deleteproduct?')  ) {
        $query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query_string, $query_params);
    
        $id = intval($query_params['id'] ?? '');
    
        if ($id != null) {
          if ($product->delete($id)) {
            echo $product->message('Product deleted successfully!', false);
          } else {
            echo $product->message('Failed to delete an product!', true);
          }
        } else {
          echo $product->message('Product not found!', true);
        }
      }
    
      if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'],'/catfilter')  ) {
        $query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query_string, $query_params);
    
        $id = intval($query_params['id'] ?? '');
    
        $data = $product->fetchProductsByCategory($id);
        
        echo json_encode($data);
      }


      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '/order')) {
        $query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query_string, $query_params); // Corrected this line
        
        $user_id = intval($query_params['uid'] ?? '');
        $product_id = intval($query_params['pid'] ?? '');
        
       
            if ($product->insertOrder($user_id, $product_id)) {
                echo $product->message('order added successfully!', false);
            } else {
                echo $product->message('Failed to order the product!', true);
            }
        
    }
    
    
    }
    if ($api == 'GET' && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/getorders') {
        
        $data = $product->fetchOrders();
    
    echo json_encode($data);
    }
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'],'/delorder')  ) {
        $query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query_string, $query_params);
    
        $id = intval($query_params['id'] ?? '');
    
        if ($id != null) {
          if ($product->deleteOrderById($id)) {
            echo $product->message('order deleted successfully!', false);
          } else {
            echo $product->message('Failed to delete an order!', true);
          }
        } else {
          echo $product->message('order not found!', true);
        }
      }
      if ($api == 'PUT' && isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'],'/updateorder?')  ) {
        parse_str($_SERVER['QUERY_STRING'], $query_params);
        
        $id = intval($query_params['id'] ?? '');
        
        
        if ($id !== null) {
            if ($product->updateOrder($id)) {
                echo $product->message('Order accepted', false);
            } else {
                echo $product->message('Failed !', true);
            }
        } else {
            echo $product->message('order not found!', true);
        }
        return $id;
    }
}



?>