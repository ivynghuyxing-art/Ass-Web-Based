<?php 
include '../_base.php';

auth('customer');

if(is_post()){
    $orders_id = post('orders_id');

    $order=$_db->prepare('SELECT * FROM orders WHERE orders_id=? AND user_id=?');
    $order->execute([$orders_id,$_user->user_id]);
    $order = $order->fetch();

    if(!$order){
        temp('info','Order not found');
        redirect('/customer/order.php');
    }

    if(!in_array($order->status, ['Pending','Paid'])){
        temp('info','The order cannot be cancelled!');
        redirect('/customer/order_detail.php?orders_id=' . $orders_id);
    }

    $_db->prepare('UPDATE orders SET status=? WHERE orders_id=?')
        ->execute(['Cancelled',$orders_id]);
    
    temp('info','Order has been cancelled!');
    redirect('/customer/order.php');
}


?>
