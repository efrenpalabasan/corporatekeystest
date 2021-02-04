<?php
    include("../config.php");

    //initialize PDO connection
    $dsn = "mysql:host=".$hostname.";dbname=".$database;

    $pdo = new PDO($dsn, $MysqlUsername, $MysqlPassword);

    if(isset($_GET['action']) && $_GET['action'] == 'fetchItems'){
        $q_get_items = $pdo->query("SELECT * FROM items");
        $rs_get_items =	$q_get_items->fetchAll(PDO::FETCH_ASSOC);

        $data = array(
                "message" => "ok",
                "data" => $rs_get_items
        );

        echo json_encode($data);
    }

    if(isset($_GET['action']) && $_GET['action'] == 'deleteItem'){

        $message = "ok";

        $id = "";

        if($_POST['id'] != ''){
            $id = $_POST['id'] ;
        }

        $q_deletitem = "DELETE FROM items where id = ".$id;

        if(!$pdo->exec($q_deletitem)){
            $message = "Failed Deleting Record";
        }

        $data = array(
            "message" => $message
        );

        echo json_encode($data);
    }


    if(isset($_GET['action']) && $_GET['action'] == 'getItem'){

        $id = $_POST['id'];

        $q_get_items = $pdo->query("SELECT * FROM items WHERE id = ".$id);
        $rs_get_items =	$q_get_items->fetchAll(PDO::FETCH_ASSOC);

        $data = array(
            "message" => 'ok',
            "data" => $rs_get_items
        );

        echo json_encode($data);
    }


    if(isset($_GET['action']) && $_GET['action'] == 'saveItem'){
        $message = "ok";

            $filename = '';
            if(isset($_FILES['file']['name'])){

                /* Getting file name */
               $oldfilename = $_FILES['file']['name'];

               //generate new filename

               $filename = uniqid()."_".$oldfilename;
         
                /* Location */
               $location = "../uploads/".$filename;
               $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
               $imageFileType = strtolower($imageFileType);
         
                /* Valid extensions */
                $valid_extensions = array("jpg","jpeg","png");
         
              $response = 0;
               /* Check file extension */
              if(in_array(strtolower($imageFileType), $valid_extensions)) {
                /* Upload file */
                if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
                  $response = $location;
                }
             }
          } 
        
          if($_POST['id'] == ''){
            $q_insertitem = "INSERT INTO items (title,thumbnail,filename) VALUES ( ";
            $q_insertitem.= "'".$_POST['title']."', ";    
            $q_insertitem.= "'".addslashes($filename)."', ";    
            $q_insertitem.= "'".addslashes($oldfilename)."' ";    
            $q_insertitem.= ") ";

          }else{
            $update_str = "";  
            if($filename != '' ){
                $update_str = " , thumbnail = '".addslashes($filename)."' , filename = '".addslashes($oldfilename)."' ";
            }

            $q_insertitem = "UPDATE items set title = '".$_POST['title']."' ".$update_str." WHERE id = ".$_POST['id'];

          }

        $pdo->exec($q_insertitem);


        $data = array(
            "message" => $message,
        );

        echo json_encode($data);
    }


?>