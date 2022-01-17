<?php

namespace App\Controller;

use Cake\Utility\Security;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
class FilesController extends AppController{

    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Products');
        $this->loadModel('Orders');
    }
    public function index()
    {
        $this->paginate = [
            'limit' => '8'
        ];
        $products = $this->paginate($this->Products->find()->where(['p_status' => '1']));
        $this->set(['products'=>$products, 'title'=>'Sản phẩm hiện có']);
    }

    public function upload($id = null)
    {
        // if($this->request->is('post')){
        //     $image = $this->request->getData('file');
        //     $tmp = $image->getStream()->getMetadata('uri'); // lấy tpm_name
        //     $name = $image->getClientFilename();
        //     $ex = substr(strrchr($name,"."),1);
        //     $path = "upload/".Security::hash($name).".".$ex;
        //     $file = $this->Files->newEmptyEntity();
        //     $file->name = $name;
        //     $file->path = $path;
        //     $file->created_at = date("Y-m-d");
        //     if(move_uploaded_file($tmp, WWW_ROOT.$path)){
        //         $this->Files->save($file);
        //         return $this->redirect(['action'=>'index']);
        //     }
        // }

        // $this->set(array('title'=>'Tải ảnh'));
        $product = $this->Products->find()->where(['id',$id])->first();
        if($product){
            $orderTable = TableRegistry::get('Orders');
            $order = $orderTable->newEmptyEntity();
            $order->id_user = $this->Auth->User('id');
            $order->id_product = $product->id;
            $order->create_at = date('Y-m-d');
            if($orderTable->save($order)){
                echo 'thanh cong';
                exit;
            }
        }
        
    }

    public function delete($id = null)
    {
        $file = $this->Files->get($id);

        $path = WWW_ROOT.$file->path;
        if(unlink($path)){
            $this->Files->delete($file);
            return $this->redirect(['action'=>'index']);
        }
    }
    public function listOrder()
    {
        $orders = $this->Orders->find();
        $products = $this->Products->find();

        $this->set(['title'=>'Danh sách đặt hàng', 'orders'=>$orders, 'products'=>$products]);
    }
    // public function download($id = null)
    // {
    //     $file = $this->Files->get($id);
    //     $path = WWW_ROOT.$file->path;

    //     $response = $this->response->withFile(
    //         $path,
    //         ['download' => true, 'name' => $file->name]
    //     );
    //     return $response;
    //     if($response){
    //         return $this->redirect(['action'=>'index']);
    //     }

    // }

}