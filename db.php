<?php
	include_once 'config.php';
	require "./vendor/autoload.php";

	use Firebase\JWT\JWT;
	use Firebase\JWT\Key;

	class Database extends Config {
	  public function fetch() {
		$sql = 'SELECT p.id AS id, p.name AS name,p.brand, p.price, p.quantity, p.id, c.cat_name AS cat_name
        FROM product p 
        JOIN category c ON p.category_id = c.id ORDER BY p.id' ;		
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
	
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		$products = array();
		foreach ($rows as $row) {
			
			$product = new stdClass();
			$product->id = $row['id'];
			$product->name = $row['name'];
			$product->price = $row['price'];
			$product->quantity = $row['quantity'];
			$product->brand = $row['brand'];

		
			$category = new stdClass();
			$category->id = $row['id'];
			$category->cat_name = $row['cat_name']; 
	
			$product->category = $category;
	
			$products[] = $product;
		}
	
		return $products;
	}
	
	  public function fetchCategories() {
	    $sql = 'SELECT * FROM category';
	    
	    $stmt = $this->conn->prepare($sql);
		$stmt->execute();

	    $rows = $stmt->fetchAll();
	    return $rows;
	  }

	  public function insert($name, $price, $quantity,$brand,$category_id) {
	    $sql = 'INSERT INTO product (name, price, quantity,brand,category_id) VALUES (:name, :price, :quantity,:brand,:category_id)';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['name' => $name, 'price' => $price, 'quantity' => $quantity,'brand'=>$brand, 'category_id'=>$category_id]);
	    return true;
	  }
	  
	 
	  public function update( $price, $quantity, $id) {
	    $sql = 'UPDATE product SET  price = :price, quantity = :quantity WHERE id = :id';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute([ 'price' => $price, 'quantity' => $quantity, 'id' => $id]);
		if (!$stmt->execute([ 'price' => $price, 'quantity' => $quantity, 'id' => $id])) {
			return false;
		}
		
	    return true;
	  }
public function fetchProductsByCategory($id) {
    $sql = 'SELECT p.id AS id, p.name AS name,p.brand, p.price, p.quantity, p.id, c.cat_name AS cat_name
        FROM product p 
        JOIN category c ON p.category_id = c.id 
		WHERE p.category_id = :id
		ORDER BY p.id' ;		
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT); 

		$stmt->execute();
	
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		$products = array();
		foreach ($rows as $row) {
			$product = new stdClass();
			$product->id = $row['id'];
			$product->name = $row['name'];
			$product->price = $row['price'];
			$product->quantity = $row['quantity'];
			$product->brand = $row['brand'];

			$category = new stdClass();
			$category->id = $row['id'];
			$category->cat_name = $row['cat_name']; 
	
			$product->category = $category;
	
			$products[] = $product;
		}
	
		return $products;
}


	  public function delete($id) {
	    $sql = 'DELETE FROM product WHERE id = :id';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['id' => $id]);
		if (!$stmt->execute(['id' => $id])) {
			return false;
		}
	    return true;
	  }
	
	 
	  public function generateToken($username,$email,$password){
		 if ( $email !== null  ) {
			$sql = "SELECT id,username,email,password,role FROM user WHERE username =:username";
	    $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
	 
		 } elseif($username !== null ){
			$sql = "SELECT id,username,email,password,role FROM user WHERE email =:email";
			$stmt = $this->conn->prepare($sql);
			$stmt->bindParam(':email', $email);
			$stmt->execute();
	 
		 }
		 $numOfRows= $stmt->rowCount();

		 if($numOfRows > 0){
			 $row = $stmt->fetch(PDO::FETCH_ASSOC);
			 $username = $row['username'];
			 $pass       = $row['password'];
			 $role       = $row['role'];
			 $id       = $row['id'];
			 if($pass==$password)
			 {
				 $secret_key = "TekUpUniversity";
				 $issuer_claim = "localhost"; 
				 $audience_claim = "THE_AUDIENCE";
				 $issuedat_claim = time(); 
				 $notbefore_claim = $issuedat_claim + 10; 
				 $expire_claim = $issuedat_claim + 600000; 
				 $token = array(
					 "iss" => $issuer_claim,
					 "aud" => $audience_claim,
					 "iat" => $issuedat_claim,
					 "exp" => $expire_claim,
				 );
				 $jwtValue = JWT::encode($token, $secret_key,'HS256');
				 echo json_encode(
					 array(
						 "message" => "success",
						 "token" => $jwtValue,
						 "role" => $role,
						 "id" => $id,
						 "expiry" => $expire_claim
					 ));
			 }
			 else{
				 echo json_encode(array("success" => "false"));
			 }
		 }
	  }
	  
	function validateToken(){
		if(!str_contains($_SERVER['REQUEST_URI'],'/signin')&& !str_contains($_SERVER['REQUEST_URI'],'/signup')){
		if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
			header('HTTP/1.0 400 Bad Request');
			echo 'Token not found in request';
			exit;
		}
		$jwt = $matches[1];
	if (! $jwt) {
		header('HTTP/1.0 400 Bad Request');
		exit;
	}
		$secretKey  = 'TekUpUniversity';
		try{
		$token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
		 }catch(\Firebase\JWT\ExpiredException $e){
         echo 'Caught exception: ',  $e->getMessage(), "\n";
    }	
		$now=new DateTimeImmutable();
		if ( $token->exp < $now->getTimestamp())
		{
			header('HTTP/1.1 401 Unauthorized');
			return false;
		} else {
			return true;
		}
	}}
	
	public function insertOrder($user_id,$product_id) {
	

		$sql = "INSERT INTO orders (status) VALUES ('en attente')";
		$stmt = $this->conn->prepare($sql);
		if ($stmt->execute()) {
			$order_id = $this->conn->lastInsertId(); 
	
			
			$sql_order_user = "INSERT INTO order_user (order_id, user_id) VALUES ($order_id, $user_id)";
			$stmt1 = $this->conn->prepare($sql_order_user);
			if (!$stmt1->execute()) {
				echo "Error inserting into order_user table: " . $this->conn->error;
			}
	
				$sql_order_product = "INSERT INTO order_product (order_id, product_id) VALUES ($order_id, $product_id)";
				$stmt2 = $this->conn->prepare($sql_order_product);
				
				if (!$stmt2->execute()) {
					echo "Error inserting into order_product table: " . $this->conn->error;
				}
			
	
			echo "Order created successfully. Order ID: " . $order_id;
			return true;
		} else {
			echo "Error creating order: " . $this->conn->error;
		}
		  }
		  public function insertUser($username, $email, $password) {
			$sql = 'INSERT INTO user (username, email, password,role) VALUES (:username, :email, :password,:role)';
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(['username' => $username, 'email' => $email, 'password' => $password,'role'=>"user"]);
			return true;
		  }
		  public function fetchOrders() {
			try {
				$sql = "SELECT 
							o.id AS order_id, 
							o.status, 
							u.username, 
							p.name AS product_name, 
							p.price
						FROM 
							order_user ou
						INNER JOIN 
							`orders` o ON ou.order_id = o.id
						INNER JOIN 
							user u ON ou.user_id = u.id
						INNER JOIN 
							order_product op ON o.id = op.order_id
						INNER JOIN 
							product p ON op.product_id = p.id";
		
				$stmt = $this->conn->prepare($sql);
				$stmt->execute();
		
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orders = array();

        foreach ($results as $row) {
            $order = new stdClass();
            $order->order_id = $row['order_id'];
            $order->status = $row['status'];
            $order->username = $row['username'];
            $order->product_name = $row['product_name'];
            $order->price = $row['price'];
            $orders[] = $order;
        }

        return $orders;
    } catch (PDOException $e) {
        return json_encode(array("error" => $e->getMessage()));
    }
		}
		
		
		function deleteOrderById($order_id) {
			$sql_delete_order_product = "DELETE FROM order_product WHERE order_id = ?";
			$sql_delete_order = "DELETE FROM orders WHERE id = ?";
			$sql_delete_order_user = "DELETE FROM order_user WHERE order_id = ?";

			$stmt_delete_order_user = $this->conn->prepare($sql_delete_order_user);
			$stmt_delete_order = $this->conn->prepare($sql_delete_order);
			$stmt_delete_order_product = $this->conn->prepare($sql_delete_order_product);

			try {
				$stmt_delete_order_user->execute([$order_id]);

				$stmt_delete_order_product->execute([$order_id]);
				$stmt_delete_order->execute([$order_id]);
		
				return true;
			} catch (Exception $e) {
				return false;
			}
		}
		function updateOrder($orderId) {
			$sql = "SELECT op.product_id, p.quantity, p.price FROM order_product op 
					INNER JOIN product p ON op.product_id = p.id 
					WHERE op.order_id = :orderId";
		
			$stmt = $this->conn->prepare($sql);
			$stmt->bindParam(':orderId', $orderId);
			$stmt->execute();
			$numOfRows = $stmt->rowCount();
			
			if ($numOfRows > 0) {
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$productId = $row["product_id"];
					$newQuantity = $row["quantity"] - 1; 
					$price = $row["price"];
					$sqlUpdateProduct = "UPDATE product SET quantity = :newQuantity WHERE id = :productId";
					$stmtUpdateProduct = $this->conn->prepare($sqlUpdateProduct);
					$stmtUpdateProduct->bindParam(':newQuantity', $newQuantity);
					$stmtUpdateProduct->bindParam(':productId', $productId);
					if ($stmtUpdateProduct->execute()) {
						echo "Product quantity updated successfully.<br>";
					} else {
						echo "Error updating product quantity: ";
					}
				}
			} else {
				echo "No products found for the given order ID.<br>";
			}
		
			$sqlUpdateOrder = "UPDATE orders SET status = 'accepted' WHERE id = :orderId";
			$stmtUpdateOrder = $this->conn->prepare($sqlUpdateOrder);
			$stmtUpdateOrder->bindParam(':orderId', $orderId);
			if ($stmtUpdateOrder->execute()) {
				echo "Order status updated successfully.";
			} else {
				echo "Error updating order status: ";
			}
		}
		
		}
		
		

?>