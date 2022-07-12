<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class ScraperController extends Controller{
    public function index(){
        // $client = new Client();    
        // $website = $client->request('GET', 'https://www.businesslist.com.ng/category/interior-design/city:lagos');
        // return $website->html();
        $client = new Client();
        $website = $client->request('GET', 'https://www.cyberpuerta.mx/index.php?cl=search&searchparam=hd680');
        $companias = $website->filter('.emproduct_right_price_left > .price')->first()->text();
        // dd($companias);
        echo $companias;
        $companies = $website->filter('.emproduct_right_price_left')->each(function ($node) {
            $node->children('.price')->each(function ($child) {
                return $prueba = $child->text();
            });
            // dump($node->text());
            // dd($node->children()->first()->text());
            // return $node->children()->first()->text();
            // return [
            //     'first_item' => $node->children()->eq(0)->text(),
            //     'first_item_again' => $node->children()->first()->text(),
            //     'second_item' => $node->children()->eq(1)->text(),
            //     'last_item' => $node->children()->last()->text(),
            // ];
        
        });
        // print_r($companies);
        // dd($companies);
        // $companies = $website->filter('.company')->each(function ($node) {
        //     return [
        //         'first_item' => $node->children()->eq(0)->text(),
        //         'first_item_again' => $node->children()->first()->text(),
        //         'second_item' => $node->children()->eq(1)->text(),
        //         'last_item' => $node->children()->last()->text(),
        //     ];
        // });
    }
}
