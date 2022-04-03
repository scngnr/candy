<?php

namespace Botble\Ecommerce\Http\Controllers\XmlService;

use Botble\Ecommerce\Models\Product as Urunler;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Botble\Ecommerce\Models\XmlService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Botble\Slug\Models\Slug;
use Botble\Ecommerce\Models\ProductCategory;

class XmlController extends Controller
{
  public $databaseImages = [];
  /**
*
*****************************************************************************
*                          Xml Service
* 1. XmlAdd metodu Yeni xml eklemek için kullanılacaktır.
* 2. xmlUpdate metodu Var olan xml güncellemek için kullanılacaktır.
* 3. xmlDelete metodu ekli olan xmli silmek için kullanılacaktır.
******************************************************************************
*/

  public function xmlAdd(Request $request){

    ini_set('memory_limit', '3000M');
    ini_set('max_execution_time', '-1');

    // dd($request->input());
    $XmlServices = new XmlService;

    $XmlServices->xmlAdi         = $request->input('xmlAdi');
    $XmlServices->xmlLinki       = $request->input('xmlLinki');
    $XmlServices->xmlDurum       = 'aktif degil';

    $xml = simplexml_load_file($request->input('xmlLinki'));
    $xml = json_decode(json_encode($xml), true);

    $arrayXmlKeyList = array();

    $xmlKeyCount = count($xml);           //xml ilk key sayısı
    $xmlKeyList   = array_keys($xml);     //xml ilk anahterdeğerleri
    // $xmlKeyList   = array_keys($xml['item']);     //xml ilk anahterdeğerleri
    // $xmlKeyList   = array_keys($xml['item'][0]);     //xml ilk anahterdeğerleri


    //print_r($xmlKeyList);
    for ($i=0; $i < $xmlKeyCount; $i++) {
      if(is_array($xml)){
        $xmlKeyName = $xmlKeyList[$i];                   //yeni array alıncak key değeri seçiliyor
        if(array_key_exists($xmlKeyName, $arrayXmlKeyList)){
          echo "ok 1 - ";
        }else {
          $arrayXmlKeyList[$xmlKeyName] =  array();

          $xmlKeyCount2 = count($xml[$xmlKeyName]);           // bir alt array sayısı alnıyor
          $xmlKeyList2   = array_keys($xml[$xmlKeyName]);     //bir alt array anahterdeğerleri alnıyor

          $XmlServices->xmlUrunAdet = $xmlKeyCount2 ;          //Xml deki urun Sayısını veritabanına kayıt et
            //print_r($xmlKeyList2[1]);
            for ($j=0; $j < $xmlKeyCount2; $j++) {
                $arrayXmlKeyList[$xmlKeyName][$j] =  array();

                $xmlKeyCount3 = count($xml[$xmlKeyName][$j]);
                $xmlKeyList3  = array_keys($xml[$xmlKeyName][$j]);

              for ($k=0; $k < $xmlKeyCount3; $k++) {
                $arrayXmlKeyList[$xmlKeyName][$j][$k] =  array();

                if(array_key_exists($xmlKeyList3[$k], $arrayXmlKeyList[$xmlKeyName][$j][$k])){
                  echo "ok 2 - ";
                }else {
                  $arrayXmlKeyList[$xmlKeyName][$j][$k] = $xmlKeyList3[$k];
                  //array_push($arrayXmlKeyList[$xmlKeyName][$j][$k], $xmlKeyList3[$k]);
                }
              }
            }
        }
      }
      //print_r($xmlKeyList2);
    }

     // print_r(array_keys($arrayXmlKeyList));
     // print_r(array_keys($arrayXmlKeyList['urun']));
     // print_r(array_keys($arrayXmlKeyList['urun'][0]));

     /**
     *****************************************************************************************************************
     *                        Yeni Xml array ile ana xml dosyasındaki tüm keyler toplanıyor.
     *                        sonrasında array unique ile aynı olan keyleri silip database aktarıyoruz.
     *****************************************************************************************************************
     */

     $newXml = array();                                                       //Yeni xml array
     $XmlKeyCount = count($arrayXmlKeyList);                                  //önceki array sayısını alıyoruz.
     $XmlKeyList = array_keys($arrayXmlKeyList);                              //önceki array griş keylerini alıyoruz.

     for ($i=0; $i < $XmlKeyCount ; $i++) {

       $xmlKeyName = $XmlKeyList[$i];                                         // i değişkenine göre arrayda aramak üzere key adını değişkene alıyoruz.

       $XmlKeyCount2 = count($arrayXmlKeyList[$xmlKeyName]);                  //alt arraydan deikendeki key adı ile array sayısını alıyouz
       $XmlKeyList2 = array_keys($arrayXmlKeyList[$xmlKeyName]);              //alt array değiken deki key adı ile array keylerini alıyoruz.


       for ($j=0; $j < $XmlKeyCount2 ; $j++) {

         $newXml = array_merge($newXml, $arrayXmlKeyList[$xmlKeyName][$j] );  //ürün keylerinin olduğu arrayları birleştiriyorum
       }
     }

     //print_r(array_unique($newXml));

     $XmlServices->urunBilgileri = json_encode( array(array_unique($newXml))); //Database aktarırken benzer keyler sildikten sonra json olarak gönderiyruz.
     $XmlServices->save();

    Alert("Xml Eklendi!", "success");
    return redirect()->back();
  }
  public function xmlDelete($xmlId){

    // dd($request->input());
    $xmlService                 = XmlService::find($xmlId);
    $xmlService->delete();
    Alert("Xml Silindi!", "success");
    return redirect()->back();
  }

  public function xmlImport (Request $request){

    $urunlerKeysJson = [                                                      //urunler Modelinin barındırdığı keyleri json olarak ekledik
      'pazaryeriBatchId' => '',
      'adi'  => '',
      'markasi'  => '',
      'fiyati'  => '',
      'saticiFiyatı'  => '',
      'katagorisi'  => '',
      'kdv'  => '',
      'paraBirimi'  => '',
      'aciklama'  => '',
      'stokCode'  => '',
      'barcode'  => '',
      'resim'  => '',
      'deci'  => '',
      'stok' => '',
      'varyant'  => '',
      'pazaryeriFiyati'  => '',
      'pazaryeriKategoriBilgileri'  => '',
      'urunDurum'  => '',
      'kaynak'  => ''
    ];

    $urunlerKeysArray = json_decode(json_encode($urunlerKeysJson), true);     //urunler json verisini array a çevir
    $urunlerArrayKeyName = array_keys($urunlerKeysArray);                     //arrayın keylerini eğişkene al
    $frontEndInput = $request->input();                                       //frontEnd inputlarından gelen veriyi değikene al

    for ($i=0; $i < count($urunlerKeysArray); $i++) {
      $KeyName = $urunlerArrayKeyName[$i];                                    //i değişkenine göre urunler dizisinden key değierni al
      if(array_key_exists($KeyName, $frontEndInput)){                         // Urunler key değeri frontEnd dizisinde var mı sorgula
        $urunlerKeysArray[$KeyName] = $request->input($KeyName);              //var ise veritabanına aktaravcağımız arraya ekle
      }
    }

    $urunlerKeysArray['kaynak'] = $request->input('xmlAdi');                  // Arraya son olarak ürünün hangi xmlden dahil edildiğğini ekliyoruz.
    $xmlService                             = XmlService::find($request->id);

    $xmlService->xmlAdi                         = $request->input('xmlAdi');
    $xmlService->xmlLinki                       = $request->input('xmlLinki');
    $xmlService->xmlDurum                       = $request->input('urunDurum');

    $xmlJson                            =  json_decode($xmlService->urunBilgileri);
    //dd($xmlJson);
    $xmlJson[1]                         =  $urunlerKeysArray;


    //dd($request->input());

    // Katagori bilgilerinin alınabilmesi için xml tekrar taranıcak

    $xml = simplexml_load_file($request->input('xmlLinki'),'SimpleXMLElement', LIBXML_NOCDATA);
    $xml = json_decode(json_encode($xml), true);

    $xmlKeyCount = count($xml);           //xml ilk key sayısı
    $xmlKeyList   = array_keys($xml);     //xml ilk anahterdeğerleri

    $xmlJson[2] = json_decode(json_encode($xmlJson[2]), true);
    for ($i=0; $i < $xmlKeyCount; $i++) {
      if(is_array($xml)){
        $xmlKeyName = $xmlKeyList[$i];                   //yeni array alıncak key değeri seçiliyor


          $xmlKeyCount2 = count($xml[$xmlKeyName]);           // bir alt array sayısı alnıyor
          $xmlKeyList2   = array_keys($xml[$xmlKeyName]);     //bir alt array anahterdeğerleri alnıyor

            //print_r($xmlKeyList2[1]);
            for ($j=0; $j < $xmlKeyCount2; $j++) {

                $xmlKeyCount3 = count($xml[$xmlKeyName][$j]);
                $xmlKeyList3  = array_keys($xml[$xmlKeyName][$j]);


                $katagoriKeyName = $xmlJson[1]['katagorisi'];
                $katagori = $xml[$xmlKeyName][$j][$katagoriKeyName];

                //dd(json_decode(json_encode($xmlJson)));

                $xmlJson[2][$j] = $katagori;
            }

      }

      //print_r($xmlKeyList2);
    }
    //dd();

    $xmlJson[2]                         =  array_unique($xmlJson[2]);
    //dd($xmlJson[2] );
    $xmlService->urunBilgileri                 =  json_encode($xmlJson);
    $xmlService->update();

    // dd(json_decode($xml->urunBilgileri));
    Alert("Xml Güncellendi!", "success");
    return redirect('/ayarlar/xml/import');
  }

  public function xmlProductCheck(){
    /*
    *
    * Ürün veri tabanımızda bulunuyor ise güncellenecek.
    * bulunmuyor ise kayıt edilcek
    *
    */

    function slugify($text, string $divider = '-')
    {
      // replace non letter or digits by divider
      $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, $divider);

      // remove duplicate divider
      $text = preg_replace('~-+~', $divider, $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }

    ini_set('max_execution_time', '-1');
    libxml_use_internal_errors(true);                                         //Xml Hatalarını Bastır
    $xmlService   = XmlService::all();
    $xmlCount     = count($xmlService);

    for ($l=0; $l < $xmlCount; $l++) {
      $xml = simplexml_load_file($xmlService[$l]->xmlLinki, 'SimpleXMLElement', LIBXML_NOCDATA);
      $xml = json_decode(json_encode($xml), true);
      //echo "for l";
      if(!$xml){
        $xmlServiceUpdate = xmlService::find($xmlService[$l]->id);
        $xmlServiceUpdate->hata = '1';
        $xmlServiceUpdate->update();
        echo "xmlden hata alındı";                                                          //Xmlden hata alınırsa döngüden çık
      }else {
        if($xmlService[$l]->xmlDurum == 'aktif'){
          $databaseXmlServiceJson           = json_decode($xmlService[$l]->urunBilgileri);
          $databaseXmlServiceArray          = json_decode(json_encode($databaseXmlServiceJson[1]), true);
          $databaseXmlServiceArrayCount     = count($databaseXmlServiceArray);
          $databaseXmlServiceArrayKeyName   = array_keys($databaseXmlServiceArray);

          //print_r($databaseXmlServiceArrayCount);
          $arrayXmlKeyList = array();

          $xmlKeyCount = count($xml);           //xml ilk key sayısı
          $xmlKeyList   = array_keys($xml);     //xml ilk anahterdeğerleri
          // $xmlKeyList   = array_keys($xml['item']);     //xml ilk anahterdeğerleri
          // $xmlKeyList   = array_keys($xml['item'][0]);     //xml ilk anahterdeğerleri

          //print_r($xmlKeyList);
          for ($i=0; $i < $xmlKeyCount; $i++) {
            $xmlKeyName = $xmlKeyList[$i];                   //yeni array alıncak key değeri seçiliyor

              $arrayXmlKeyList[$xmlKeyName] =  array();

              $xmlKeyCount2 = count($xml[$xmlKeyName]);           // bir alt array sayısı alnıyor
              $xmlKeyList2   = array_keys($xml[$xmlKeyName]);     //bir alt array anahterdeğerleri alnıyor
                //print_r($xmlKeyList2[1]);
                echo $xmlKeyCount2 . '-';
                for ($j=0; $j < $xmlKeyCount2; $j++) {
                   echo $j . '-';
                    $arrayXmlKeyList[$xmlKeyName][$j] =  array();

                    $xmlKeyCount3 = count($xml[$xmlKeyName][$j]);
                    $xmlKeyList3  = array_keys($xml[$xmlKeyName][$j]);

                    $databaseXmlArrayKeyList = json_decode($xmlService[$l]->urunBilgileri); //Seçili olan xml bilgilerini yeni arraya alıyoruz.
                    $databaseXmlArrayKeyList = json_decode(json_encode($databaseXmlArrayKeyList[1]), true);  //Seçili olan xml bilgilerini objectto array ile yeni arraya alıyoruz.
                    $databaseXmlArrayKeyName = array_keys($databaseXmlArrayKeyList);

                    // dd($databaseXmlArrayKeyList);
                    $urunler = new Urunler ;
                    $databaseSorgu = 0;


                    for ($k=0; $k < count($databaseXmlArrayKeyList) ; $k++) {

                      $arrayKeyName =   $databaseXmlArrayKeyName[$k];
                      $arrayKeyValue = $databaseXmlArrayKeyList[$arrayKeyName];
                      $urunKontrolValue = $databaseXmlArrayKeyList['sku'];
                      $databasecategory = $databaseXmlArrayKeyList['category'];
                      //print_r(json_decode(json_encode($xml[$xmlKeyName][$j]), true)) ;
                      if($databaseSorgu < 1){
                        $urunlers = Urunler::where('sku', $xml[$xmlKeyName][$j][$urunKontrolValue])->first();
                        $databaseSorgu = 2;
                      }

                      try {
                      //Xml Kategorileri bul ve veritabanına kaydet
                      $kategoriler = explode('>', $xml[$xmlKeyName][$j][$databasecategory]);
                      $findkategori = ProductCategory::where('name', $kategoriler[0] )->first();
                      if($findkategori){


                      }else {
                        $addCat = new ProductCategory;
                        $addCat->name           = $kategoriler[0];
                        $addCat->parent_id      = 0;
                        $addCat->description    = NULL;
                        $addCat->status         ='published';
                        $addCat->order          = 0;
                        $addCat->image          = NULL;
                        $addCat->is_featured    = 0;
                        $addCat->save();

                        $slug = ProductCategory::where('name', $kategoriler[0])->first();

                        if($slug){
                          //Slug veri tabannda ürün id si kayıtlı ise işlem yapma
                          if(Slug::where('reference_id', $slug->id)->first()){

                          }else {
                            $productSlug = new Slug;
                            $productSlug->key                 = slugify($slug->name);
                            $productSlug->reference_type      = 'Botble\Ecommerce\Models\ProductCategory';
                            $productSlug->reference_id        = $slug->id;
                            $productSlug->prefix              = 'product-categories';
                             $productSlug->save();
                          }
                        }
                      }


                      //Bir alt kategori grubu
                      $findkategori       = ProductCategory::where('name', $kategoriler[1] )->first();
                      if($findkategori){


                      }else {
                        $addCat = new ProductCategory;
                        $addCat->name           = $kategoriler[1];

                        $findParentkategori     = ProductCategory::where('name', $kategoriler[0] )->first();
                        $addCat->parent_id      = $findParentkategori->id;
                        $addCat->description    = NULL;
                        $addCat->status         ='published';
                        $addCat->order          = 0;
                        $addCat->image          = NULL;
                        $addCat->is_featured    = 0;
                        $addCat->save();

                        $slug = ProductCategory::where('name', $kategoriler[1])->first();

                        if($slug){
                          //Slug veri tabannda ürün id si kayıtlı ise işlem yapma
                          if(Slug::where('reference_id', $slug->id)->first()){

                          }else {
                            $productSlug = new Slug;
                            $productSlug->key                 = slugify($slug->name);
                            $productSlug->reference_type      = 'Botble\Ecommerce\Models\ProductCategory';
                            $productSlug->reference_id        = $slug->id;
                            $productSlug->prefix              = 'product-categories';
                             $productSlug->save();
                          }
                        }
                      }
                      //Ent alt kategoriler

                        $findkategori = ProductCategory::where('name', $kategoriler[2] )->first();
                        if($findkategori){


                        }else {
                          $addCat = new ProductCategory;
                          $addCat->name           = $kategoriler[2];
                          $findParentkategori     = ProductCategory::where('name', $kategoriler[1] )->first();
                          $addCat->parent_id      = $findParentkategori->id;
                          $addCat->description    = NULL;
                          $addCat->status         ='published';
                          $addCat->order          = 0;
                          $addCat->image          = NULL;
                          $addCat->is_featured    = 0;
                          $addCat->save();

                          $slug = ProductCategory::where('name', $kategoriler[2])->first();

                          if($slug){
                            //Slug veri tabannda ürün id si kayıtlı ise işlem yapma
                            if(Slug::where('reference_id', $slug->id)->first()){

                            }else {
                              $productSlug = new Slug;
                              $productSlug->key                 = slugify($slug->name);
                              $productSlug->reference_type      = 'Botble\Ecommerce\Models\ProductCategory';
                              $productSlug->reference_id        = $slug->id;
                              $productSlug->prefix              = 'product-categories';
                               $productSlug->save();
                            }
                          }
                        }
                      } catch (\Exception $e) {

                      }


                      if($arrayKeyName == 'images'){

                        $images =  $databaseXmlArrayKeyList[$arrayKeyName];

                        for ($ac=0; $ac < count($images); $ac++) {
                          try {
                            // $databaseImages[$ac] = "products\/". $xml[$xmlKeyName][$j][$sku] . "-" . $ac .              ".jpg";

                            if(array_key_exists($resimPath, $xml[$xmlKeyName][$j])){

                              $resimPath = $images[$ac];
                              $sku = $databaseXmlArrayKeyList['sku'];
                              $url = $xml[$xmlKeyName][$j][$resimPath];
                              $contents = file_get_contents($url);

                              Storage::put('/product/'.$xml[$xmlKeyName][$j][$sku] . '-' . $ac .              '.jpg',  $contents);
                              Storage::put('/product/'.$xml[$xmlKeyName][$j][$sku] . '-' . $ac . '-150x150' . '.jpg',  $contents);
                              Storage::put('/product/'.$xml[$xmlKeyName][$j][$sku] . '-' . $ac . '-400x400' . '.jpg',  $contents);
                              Storage::put('/product/'.$xml[$xmlKeyName][$j][$sku] . '-' . $ac . '-800x800' . '.jpg',  $contents);
                            }

                            $this->databaseImages[$ac] = "product\/{$xml[$xmlKeyName][$j][$sku]}-{$ac}.jpg";
                          } catch (\Exception $e) {

                          }
                        }
                      }else {
                        if(array_key_exists($arrayKeyValue, $xml[$xmlKeyName][$j])){
                            // $urunler[$arrayKeyName] = $xml[$xmlKeyName][$j][$arrayKeyValue];

                            // $urunler->kaynak       = $xmlService[$l]->xmlAdi;
                          if(!$urunlers){
                            //echo "- ";
                              $urunler[$arrayKeyName] = $xml[$xmlKeyName][$j][$arrayKeyValue];
                              // $urunler['kaynak']       = $xmlService[$l]->xmlAdi;
                              $urunler->images = json_encode($this->databaseImages);
                              $urunler->with_storehouse_management = 1;
                              $urunler->save();

                              $productControlName = $databaseXmlArrayKeyList['name'];
                              $kategoriler = explode('>', $xml[$xmlKeyName][$j][$databasecategory]);
                              $katagoriCount = count($kategoriler);
                              $ProductCategoryControl = ProductCategory::where('name', $kategoriler[$katagoriCount-1] )->first();

                              $productControl     = Urunler::where('name', $xml[$xmlKeyName][$j][$productControlName])->first();
                              if($productControl){
                                $results = DB::select('select * from ec_product_category_product where product_id = :product_id', ['product_id' => $productControl->id]);
                                if(!$results){
                                  DB::insert('insert into ec_product_category_product (category_id, product_id) values (?, ?)', [$ProductCategoryControl->id, $productControl->id]);
                                }
                                if(Slug::where('reference_id', $productControl->id)->first()){

                                }else {
                                $productName = $databaseXmlArrayKeyList['name'];
                                  $productSlug = new Slug;
                                  $productSlug->key                 = slugify($xml[$xmlKeyName][$j][$productName]);
                                  $productSlug->reference_type      = 'Botble\Ecommerce\Models\Product';
                                  $productSlug->reference_id        = $productControl->id;
                                  $productSlug->prefix              = 'products';
                                   $productSlug->save();
                                }
                              }
                              //Veri tabanı slug kaydı için stok kodu alınıyor

                          }else {

                              $urunler = Urunler::find($urunlers->id);
                              $urunler[$arrayKeyName] = $xml[$xmlKeyName][$j][$arrayKeyValue];
                              // $urunler[$arrayKeyName] = $xml[$xmlKeyName][$j][$arrayKeyValue];
                              // $urunler['kaynak']        = $xmlService[$l]->xmlAdi;
                              $urunler->images = json_encode($this->databaseImages);
                              // $urunler->update();
                          }
                        }
                      }
                    }
                }
          }
        }
      }
    }
  }
}
