<?php

class GoodsViewModel extends ViewModel{
     public $viewFields = array(
         'Goods'    =>  array(
             '*',
             '_as'      =>  'G',
             '_type'    =>  'LEFT'
         ),
         'Shop'     =>  array(
             'name' =>  'shop_name',
             'seller'   =>  'shop_seller',
             'url'  =>  'shop_url',
             'taoke_url'    =>  'shop_taoke_url',
             'credit'   =>  'shop_credit',
             '_as'  =>  'S',
             '_on'  =>  'G.shop_id=S.id'
         ),
     );
}
?>