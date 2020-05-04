<?php
  
class ControllerApiZStore extends Controller {
    
   /**
   * возвращает перечень статусов ордеров
   * 
   */
   public function statuses() {
      

        $json = array();
        $json['error']= "" ;  
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
 
                $json['statuses'] = array();
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
             
                foreach ($query->rows as $row) {
                    $json['statuses'][$row['order_status_id']] = $row['name'];
                }
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
    * Список ордеров по статусу
    * 
    */
    public function orders() {
      

        $json = array();
        $json['error']= "" ;
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
                $status_id = $this->request->post['status_id'] ;
                $json['orders'] = array();
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` o    WHERE  o.store_id =  " . (int)$this->config->get('config_store_id') . " and o.order_status_id=".  $status_id   );
             
                foreach ($query->rows as $row) {
                    
                    $products = array();
                
                    
                    $queryi = $this->db->query("SELECT op.name, p.sku,op.price,op.quantity,op.order_product_id FROM `" . DB_PREFIX . "order_product` op  join  `" . DB_PREFIX . "product` p on op.product_id = p.product_id   WHERE  op.order_id=".  $row['order_id']  ); 
                    foreach ($queryi->rows as $rowi) {
     
                        $options = array();

                        $queryo = $this->db->query("SELECT name,value  FROM `" . DB_PREFIX . "order_option`  WHERE   order_id=".  $row['order_id'] ." and order_product_id=". $rowi['order_product_id']  );
                        foreach ($queryo->rows as $rowo) {

                            $options[$rowo['name'] ] =   $rowo['value']  ;
                        }                       

                        $rowi['_options_'] = $options;                      
                       
                        $products[]=$rowi;
                    }
                    
                    if(count($products)==0) continue;
                    $row['_products_']= $products;
                    $json['orders'][] =  $row ;  
                        
                    
                }
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }  
    
    /**
    * обновление статуса ордеров
    * 
    */
    public function updateorder() {
      

        $json = array();
        $json['error']='';
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
                $data = $this->request->post['data'] ;
                $data = str_replace('&quot;','"',$data) ;
               
                $list = json_decode($data,true);
        
                foreach ($list as $order_id=>$status) {
                     $this->db->query("UPDATE `" . DB_PREFIX . "order` o  set o.order_status_id= {$status}   WHERE  o.store_id =  " . (int)$this->config->get('config_store_id') . " and o.order_id=".  $order_id   );
                }
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }      
 
 
   /**
   * возвращает артикулы  товаров  с магазина
   * 
   */
   public function articles() {
      

        $json = array();
        $json['error']= "" ;  
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
 
                $json['articles'] = array();
                $query = $this->db->query("SELECT distinct sku FROM " . DB_PREFIX . "product   ");
             
                foreach ($query->rows as $row) {
                    $json['articles'][] = $row['sku'];
                }
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
   
   /**
   * возвращает список  категоритй  товаров
   * 
   */
   public function cats() {
      

        $json = array();
        $json['error']= "" ;  
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
 
                $json['cats'] = array();
                $sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name   FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";
                $sql .= " GROUP BY cp.category_id order  by  name";
                $query = $this->db->query($sql);
                 
             
                foreach ($query->rows as $row) {
                    $json['cats'][$row['category_id']] = $row['name'];
                }
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
    * импорт новых товаров
    * 
    */
    public function addproducts() {
      

        $json = array();
        $json['error']='';
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
                $language_id = (int)$this->config->get('config_language_id') ;
                $store_id = (int)$this->config->get('config_store_id');
                $category_id = $this->request->post['cat'] ;
                $data = $this->request->post['data'] ;
                $data = str_replace('&quot;','"',$data) ;
               
                $list = json_decode($data,true);
                
 
                foreach($list as $pr){
                     $this->db->query("INSERT INTO `" . DB_PREFIX . "product`   (  sku,quantity,price,status,    date_added) values ('" . $this->db->escape($pr['sku']) . "',{$pr['quantity']},{$pr['price']},0,now())");
                     $product_id = $this->db->getLastId();
                
                     $this->db->query("INSERT INTO " . DB_PREFIX . "product_description  (product_id,language_id,name,description) values ( {$product_id},{$language_id},'" . $this->db->escape($pr['name']) . "',   '" . $this->db->escape($pr['description'])."')");
                     $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store (product_id,store_id) values({$product_id}, {$store_id})");
                     $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category  (product_id,category_id,main_category) values({$product_id}, {$category_id}, 1)");
            
                } 
                 
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }       
    
     /**
     * обновление количества
     * 
     */
     public function updatequantity() {
      

        $json = array();
        $json['error']='';
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
                $data = $this->request->post['data'] ;
                $data = str_replace('&quot;','"',$data) ;
               
                $list = json_decode($data,true);
                
                foreach ($list as $sku=>$quantity) {
    
                    $this->db->query("UPDATE `" . DB_PREFIX . "product`    set quantity= {$quantity}   WHERE  sku =  '" . $this->db->escape($sku) . "'" );
                }
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }    
   
   
     /**
     * обновление  цен
     * 
     */
     public function updateprice() {
      

        $json = array();
        $json['error']='';
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
                $data = $this->request->post['data'] ;
                $data = str_replace('&quot;','"',$data) ;
               
                $list = json_decode($data,true);
        
                foreach ($list as $sku=>$price) {
    
                    $this->db->query("UPDATE `" . DB_PREFIX . "product`    set price= {$price}   WHERE  sku =  '" . $this->db->escape($sku) . "'" );
                }
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }    
    
    
    /**
    * Список  товаров
    * 
    */
    public function getproducts() {
      

        $json = array();
        $json['error']= "" ;
        if (!isset($this->session->data['api_id'])) {
            $json['error']= "Нет доступа" ;
        } else {
           
            try{
                $language_id = (int)$this->config->get('config_language_id') ;
                $store_id = (int)$this->config->get('config_store_id');
           
                $json['products'] = array();
               $sql="SELECT p.sku,p.price,p.image,pd.name,pd.description,p.weight,p.weight_class_id, m.name as manufacturer FROM `" . DB_PREFIX . "product` p  join  `" . DB_PREFIX . "product_description` pd on p.product_id=pd.product_id  left join  `" . DB_PREFIX . "manufacturer` m on p.manufacturer_id=m.manufacturer_id  WHERE  pd.language_id={$language_id}  and p.product_id in(select product_id from " . DB_PREFIX . "product_to_store  where store_id={$store_id} ) ";
            
                $query = $this->db->query($sql)  ;
             
                foreach ($query->rows as $row) {
                    if(strlen($row['sku'])==0)  continue;
                    $json['products'][] =  $row ;  
                    
                }
                
             
            }catch(Exception $e){
               $json['error'] = $e->getMessage(); 
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }  
     
}
