<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\PreciosAbasteoController;
use App\Http\Controllers\CyberPuertaController;
use App\Models\Atributo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Categoria;
use App\Models\Especificacion;
use App\Models\imagenProducto;
use App\Models\Marca;
use App\Models\Palabras;
use App\Models\Subcategoria;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Existencias;
use App\Models\Woocommerce;
use Illuminate\Support\Facades\Storage;
use Goutte\Client AS Client2;
use Automattic\WooCommerce\Client as WooClient;
use Automattic\WooCommerce\HttpClient\HttpClientException;

use function PHPUnit\Framework\isNull;

class PreciosController extends Controller
{
    public function index()
    {
        $data['categorias'] = Categoria::distinct('nombre')->where('nombre','not like',"% - 2%")->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['productos'] = '';
        return view('filtros', compact('data'));
    }

    public function getCategorias($id = null)
    {
        // $data = Subcategoria::distinct('nombre')->where('categoria_id',$id)->get();
        // return response()->json($data);
        $sql = "select id,nombre from subcategorias where id IN(select DISTINCT subcategoria_id from productos where categoria_id = ? and estatus = 'Activo') order by nombre asc";
        $data = DB::select($sql, array($id));
        return response()->json($data);
    }

    public function getMarcas($id = null, $id2 = null)
    {
        $sql = "select id,nombre from marcas where id IN(select DISTINCT marca_id from productos where categoria_id = ? and subcategoria_id = ? and estatus = 'Activo') order by nombre asc";
        $data = DB::select($sql, array($id, $id2));
        return response()->json($data);
    }

    public function cotizar(Request $request)
    {
        $preciosAbasteo = new PreciosAbasteoController;
        // $preciosCyberpuerta = new CyberPuertaController;
        $preciosMiPC = new MiPCController;
        $preciosZegucom = new ZegucomController;
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $test3 = $request->get('filtro3');
        if ($test3 == 'z') {
            $data['productos'] = Producto::where('categoria_id', $test)->where('subcategoria_id', $test2)->where('estatus', 'Activo')->get();
        } else {
            $data['productos'] = Producto::where('categoria_id', $test)->where('subcategoria_id', $test2)->where('marca_id', $test3)->where('estatus', 'Activo')->get();
        }
        if (sizeof($data['productos']) > 0) {
            $data['abasteo'] = $preciosAbasteo->cotizar($data['productos']);
            // $data['cyberpuerta'] = $preciosCyberpuerta->cotizar($data['productos']);
            $data['mipc'] = $preciosMiPC->cotizar($data['productos']);
            $data['zegucom'] = $preciosZegucom->cotizar($data['productos']);
            $data['categoria'] = $request->get('filtro1');
            $data['subcategoria'] = $request->get('filtro2');
            // $existencias = new CTConnect;
            // $data['existencias'] = $existencias->existencias($data['productos']);
        }
        $data['categorias'] = Categoria::distinct('nombre')->where('nombre','not like',"% - 2%")->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        return view('filtros', compact('data'));
    }

    public function lectura()
    {
        $products = new ProductosController();
        $existencia_producto = 0;
        // $products->limpieza();
        set_time_limit(0);
        $fileExist = Storage::disk('prueba-ftp')->exists('catalogo_xml/productos.json');
        if ($fileExist) {
            Storage::disk('local')->put('public/products.json', Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
        } else {
            dd("No existe");
        }
        //$productos = Storage::get('public/products.json');
        //     set_time_limit(0);
        //     for($i=0;$i<sizeof($productos);$i++){
        //         if($productos[$i]['idCategoria']!=0){

        //             $marca_nueva = Marca::updateOrCreate(
        //                 ['id'=>$productos[$i]['idMarca']],
        //                 [
        //                     'id'=>$productos[$i]['idMarca'],
        //                     'nombre'=>$productos[$i]['marca']
        //                 ]
        //             );
        //             $categoria_nueva = Categoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['categoria']
        //                 ]
        //             );
        //             $subcategoria_nueva = Subcategoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idSubCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['subcategoria']
        //                 ]
        //             );
        //             $producto = Producto::updateOrCreate(
        //                 ['clave_ct'=>$productos[$i]['clave']],
        //                 [
        //                     'marca_id'=>$productos[$i]['idMarca'],
        //                     'subcategoria_id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['nombre'],
        //                     'descripcion_corta'=>$productos[$i]['descripcion_corta'],
        //                     'precio_unitario'=>$productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio']*$productos[$i]['tipoCambio'])*1.16),2,'.',''):number_format(($productos[$i]['precio']*1.16),2,'.',''),
        //                     'sku'=>ltrim($productos[$i]['numParte']),
        //                     'ean'=>$productos[$i]['ean'],
        //                     'upc'=>$productos[$i]['upc'],
        //                     'imagen'=>$productos[$i]['imagen'],
        //                     'existencias'=>$existencia_producto,
        //                     'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
        //                 ]
        //             );
        //             if(!empty($productos[$i]['promociones'])){
        //                 // dd($productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 // date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio']));
        //                 // date('Y-m-d\TH:i:s', $productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 if($productos[$i]['promociones'][0]['tipo']!="porcentaje"){
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>100-($productos[$i]['promociones'][0]['promocion']*100)/$productos[$i]['precio'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }else{
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>$productos[$i]['promociones'][0]['promocion'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }
        //                 // dd($productos[$i]['clave']);
        //             }
        //             $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
        //             for($j=0;$j<sizeof($palabras_clave);$j++){
        //                 $producto = Palabras::updateOrCreate(
        //                     ['clave_ct'=>$productos[$i]['clave'],
        //                     'palabra'=>$palabras_clave[$j]]
        //                 );
        //             }
        //         }
        //     }
        //     dd($productos);
    }

    public function lecturaLocal()
    {
        set_time_limit(0);
        $products = new ProductosController();
        $imagenes = new ImagenesController();
        $existencias = new ExistenciasController();
        $existencia_producto = 0;
        $products->limpieza();
        $existencias->limpieza();
        $productos = json_decode(file_get_contents(storage_path() . "/app/public/productos.json"), true);
        // if(date('d')==01){
        //     $borrarProm = Promocion::where('id','>',0)->delete();
        // }
        for ($i = 0; $i < sizeof($productos); $i++) {
          // for($i=0;$i<10;$i++){
            $existencia_producto = 0;
            if ($productos[$i]['idCategoria'] != 0) {
                // PRUEBA EXISTENCIAS
                $existencia_producto = $this->existencias($productos[$i]);
                // $existencia_producto = $this->existenciasTotales($productos[$i]);
                $existencia_producto2 = $this->existenciasTotalesXalapa($productos[$i]);
                $marca_nueva = Marca::updateOrCreate(
                    ['id' => $productos[$i]['idMarca']],
                    [
                        'id' => $productos[$i]['idMarca'],
                        'nombre' => $productos[$i]['marca']
                    ]
                );
                $categoria_nueva = Categoria::updateOrCreate(
                    ['id' => $productos[$i]['idCategoria']],
                    [
                        'id' => $productos[$i]['idCategoria'],
                        'nombre' => $productos[$i]['categoria']
                    ]
                );
                $subcategoria_nueva = Subcategoria::updateOrCreate(
                    ['id' => $productos[$i]['idSubCategoria']],
                    [
                        'id' => $productos[$i]['idSubCategoria'],
                        'categoria_id' => $productos[$i]['idCategoria'],
                        'nombre' => $productos[$i]['subcategoria']
                    ]
                );
                $producto = Producto::updateOrCreate(
                    ['clave_ct' => $productos[$i]['clave']],
                    [
                        'idProductoCT' => $productos[$i]['idProducto'],
                        'marca_id' => $productos[$i]['idMarca'],
                        'subcategoria_id' => $productos[$i]['idSubCategoria'],
                        'categoria_id' => $productos[$i]['idCategoria'],
                        'nombre' => $productos[$i]['nombre'],
                        'descripcion_corta' => $productos[$i]['descripcion_corta'],
                        'precio_unitario' => $productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio'] * $productos[$i]['tipoCambio']) * 1.16), 2, '.', '') : number_format(($productos[$i]['precio'] * 1.16), 2, '.', ''),
                        'sku' => ltrim($productos[$i]['numParte']),
                        'ean' => $productos[$i]['ean'],
                        'upc' => $productos[$i]['upc'],
                        // 'imagen' => $productos[$i]['imagen'],
                        'existencias' => $existencia_producto,
                        'estatus' => $productos[$i]['activo'] == 1 ? 'Activo' : 'Descontinuado'
                    ]
                );
                if (!empty($productos[$i]['promociones'])) {
                    if ($productos[$i]['promociones'][0]['tipo'] != "porcentaje") {
                        $promocion = Promocion::updateOrCreate(
                            ['clave_ct' => $productos[$i]['clave']],
                            [
                                'descuento' => 100 - ($productos[$i]['promociones'][0]['promocion'] * 100) / $productos[$i]['precio'],
                                'fecha_inicio' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
                                'fecha_fin' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))
                            ]
                        );
                    } else {
                        $promocion = Promocion::updateOrCreate(
                            ['clave_ct' => $productos[$i]['clave']],
                            [
                                'descuento' => $productos[$i]['promociones'][0]['promocion'],
                                'fecha_inicio' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
                                'fecha_fin' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))
                            ]

                        );
                    }
                }
                // $palabras_clave = explode(",", $productos[$i]['descripcion_corta']);
                // for ($j = 0; $j < sizeof($palabras_clave); $j++) {
                //     $producto = Palabras::updateOrCreate(
                //         [
                //             'clave_ct' => $productos[$i]['clave'],
                //             'palabra' => $palabras_clave[$j]
                //         ]
                //     );
                // }
            }
        }
        // $existencias = new CTConnect;
        //         $existencias->existencias($productos);
        // for($i=0;$i<2;$i++){
        //     if($productos[$i]['idCategoria']!=0){
        //         $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
        //         for($j=0;$j<sizeof($palabras_clave);$j++){
        //             $producto = Palabras::updateOrCreate(
        //                 ['clave_ct'=>$productos[$i]['clave'],
        //                 'palabra'=>$palabras_clave[$j]]
        //             );
        //         }
        //     }
        // }
        // dd($productos);
        dd("Archivo cargado");
    }

    public function existencias($productos)
    {
        set_time_limit(0);
        $existencia_producto = 0;
        if (!empty($productos['existencia']['DFA'])) {
            $existencia_producto += $productos['existencia']['DFA'];
        }
        if (!empty($productos['existencia']['D2A'])) {
            $existencia_producto += $productos['existencia']['D2A'];
        }
        if (!empty($productos['existencia']['CAM'])) {
            $existencia_producto += $productos['existencia']['CAM'];
        }
        if (!empty($productos['existencia']['GDL'])) {
            $existencia_producto += $productos['existencia']['GDL'];
        }
        if (!empty($productos['existencia']['ZAC'])) {
            $existencia_producto += $productos['existencia']['ZAC'];
        }
        if (!empty($productos['existencia']['ACA'])) {
          $existencia_producto += $productos['existencia']['ACA'];
        }
        if (!empty($productos['existencia']['QRO'])) {
            $existencia_producto += $productos['existencia']['QRO'];
        }
        if (!empty($productos['existencia']['COL'])) {
          $existencia_producto += $productos['existencia']['COL'];
        }
        if (!empty($productos['existencia']['HMO'])) {
          $existencia_producto += $productos['existencia']['HMO'];
        }
        if (!empty($productos['existencia']['LMO'])) {
          $existencia_producto += $productos['existencia']['LMO'];
        }
        if (!empty($productos['existencia']['CLN'])) {
          $existencia_producto += $productos['existencia']['CLN'];
        }
        if (!empty($productos['existencia']['CHI'])) {
            $existencia_producto += $productos['existencia']['CHI'];
        }
        if (!empty($productos['existencia']['MOR'])) {
          $existencia_producto += $productos['existencia']['MOR'];
        }
        if (!empty($productos['existencia']['VER'])) {
          $existencia_producto += $productos['existencia']['VER'];
        }
        if (!empty($productos['existencia']['CTZ'])) {
          $existencia_producto += $productos['existencia']['CTZ'];
        }
      if (!empty($productos['existencia']['TAM'])) {
          $existencia_producto += $productos['existencia']['TAM'];
      }
      if (!empty($productos['existencia']['PUE'])) {
          $existencia_producto += $productos['existencia']['PUE'];
      }
      if (!empty($productos['existencia']['VHA'])) {
          $existencia_producto += $productos['existencia']['VHA'];
      }
      if (!empty($productos['existencia']['TUX'])) {
          $existencia_producto += $productos['existencia']['TUX'];
      }
      if (!empty($productos['existencia']['MTY'])) {
          $existencia_producto += $productos['existencia']['MTY'];
      }
      if (!empty($productos['existencia']['MID'])) {
          $existencia_producto += $productos['existencia']['MID'];
      }
      if (!empty($productos['existencia']['MAZ'])) {
          $existencia_producto += $productos['existencia']['MAZ'];
      }
      if (!empty($productos['existencia']['CUE'])) {
          $existencia_producto += $productos['existencia']['CUE'];
      }
      if (!empty($productos['existencia']['CUN'])) {
          $existencia_producto += $productos['existencia']['CUN'];
      }
      if (!empty($productos['existencia']['DFP'])) {
          $existencia_producto += $productos['existencia']['DFP'];
      }
      if (!empty($productos['existencia']['ACX'])) {
          $existencia_producto += $productos['existencia']['ACX'];
      }
      if (!empty($productos['existencia']['CEL'])) {
          $existencia_producto += $productos['existencia']['CEL'];
      }
      if (!empty($productos['existencia']['OBR'])) {
          $existencia_producto += $productos['existencia']['OBR'];
      }
      if (!empty($productos['existencia']['DGO'])) {
          $existencia_producto += $productos['existencia']['DGO'];
      }
      if (!empty($productos['existencia']['TRN'])) {
          $existencia_producto += $productos['existencia']['TRN'];
      }
      if (!empty($productos['existencia']['AGS'])) {
          $existencia_producto += $productos['existencia']['AGS'];
      }
      if (!empty($productos['existencia']['SLP'])) {
          $existencia_producto += $productos['existencia']['SLP'];
      }
      if (!empty($productos['existencia']['XLP'])) {
          $existencia_producto += $productos['existencia']['XLP'];
      }
      if (!empty($productos['existencia']['DFT'])) {
          $existencia_producto += $productos['existencia']['DFT'];
      }
      if (!empty($productos['existencia']['CDV'])) {
          $existencia_producto += $productos['existencia']['CDV'];
      }
      if (!empty($productos['existencia']['SLT'])) {
          $existencia_producto += $productos['existencia']['SLT'];
      }
      if (!empty($productos['existencia']['TPC'])) {
          $existencia_producto += $productos['existencia']['TPC'];
      }
      if (!empty($productos['existencia']['TOL'])) {
          $existencia_producto += $productos['existencia']['TOL'];
      }
      if (!empty($productos['existencia']['PAC'])) {
          $existencia_producto += $productos['existencia']['PAC'];
      }
      if (!empty($productos['existencia']['IRA'])) {
          $existencia_producto += $productos['existencia']['IRA'];
      }
      if (!empty($productos['existencia']['OAX'])) {
          $existencia_producto += $productos['existencia']['OAX'];
      }
      if (!empty($productos['existencia']['DFC'])) {
          $existencia_producto += $productos['existencia']['DFC'];
      }
      if (!empty($productos['existencia']['TXL'])) {
          $existencia_producto += $productos['existencia']['TXL'];
      }
      if (!empty($productos['existencia']['URP'])) {
          $existencia_producto += $productos['existencia']['URP'];
      }
      if (!empty($productos['existencia']['CHT'])) {
        $existencia_producto += $productos['existencia']['CHT'];
      }
      if (!empty($productos['existencia']['LEO'])) {
        $existencia_producto += $productos['existencia']['LEO'];
      }
      if (!empty($productos['existencia']['TXA'])) {
        $existencia_producto += $productos['existencia']['TXA'];
      }
        return $existencia_producto;
    }

    public function existenciasTotales($productos)
    {
        set_time_limit(0);
        //dd($productos);
        $existencia_producto = 0;
        $existencia_cedis = 0;
        $existencia_resto = 0;
        if (!empty($productos['existencia']['DFA'])) {
            $existencia_producto += $productos['existencia']['DFA'];
            $existencia_cedis += $productos['existencia']['DFA'];
            $existencias = Existencias::updateOrCreate(
              ['clave_ct' => $productos['clave'],
              'almacen_id' => '34'
            ],
              [
              'clave_ct' => $productos['clave'],
              'almacen_id' => '34',
              'existencias' => $productos['existencia']['DFA']
            ]
            );
        }
        if (!empty($productos['existencia']['D2A'])) {
            $existencia_producto += $productos['existencia']['D2A'];
            $existencia_cedis += $productos['existencia']['D2A'];
            $existencias = Existencias::updateOrCreate(
              ['clave_ct' => $productos['clave'],
              'almacen_id' => '48'
            ],
              [
              'clave_ct' => $productos['clave'],
              'almacen_id' => '48',
              'existencias' => $productos['existencia']['D2A']
            ]
            );
        }
        if (!empty($productos['existencia']['CAM'])) {
            $existencia_producto += $productos['existencia']['CAM'];
            $existencia_resto += $productos['existencia']['CAM'];
            $existencias = Existencias::updateOrCreate(
              ['clave_ct' => $productos['clave'],
              'almacen_id' => '41'
            ],
              [
              'clave_ct' => $productos['clave'],
              'almacen_id' => '41',
              'existencias' => $productos['existencia']['CAM']
            ]
            );
        }
        if (!empty($productos['existencia']['GDL'])) {
            $existencia_producto += $productos['existencia']['GDL'];
            $existencia_resto += $productos['existencia']['GDL'];
            $existencias = Existencias::updateOrCreate(
              ['clave_ct' => $productos['clave'],
              'almacen_id' => '12'
            ],
              [
              'clave_ct' => $productos['clave'],
              'almacen_id' => '12',
              'existencias' => $productos['existencia']['GDL']
            ]
            );
        }
        if (!empty($productos['existencia']['ZAC'])) {
            $existencia_producto += $productos['existencia']['ZAC'];
            $existencia_resto += $productos['existencia']['ZAC'];
            $existencias = Existencias::updateOrCreate(
              ['clave_ct' => $productos['clave'],
              'almacen_id' => '35'
            ],
              [
              'clave_ct' => $productos['clave'],
              'almacen_id' => '35',
              'existencias' => $productos['existencia']['ZAC']
            ]
            );
        }
        if (!empty($productos['existencia']['ACA'])) {
          $existencia_producto += $productos['existencia']['ACA'];
          $existencia_resto += $productos['existencia']['ACA'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '37'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '37',
            'existencias' => $productos['existencia']['ACA']
          ]
          );
      }
      if (!empty($productos['existencia']['QRO'])) {
          $existencia_producto += $productos['existencia']['QRO'];
          $existencia_resto += $productos['existencia']['QRO'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '9'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '9',
            'existencias' => $productos['existencia']['QRO']
          ]
          );
      }
      if (!empty($productos['existencia']['COL'])) {
          $existencia_producto += $productos['existencia']['COL'];
          $existencia_resto += $productos['existencia']['COL'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '17'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '17',
            'existencias' => $productos['existencia']['COL']
          ]
          );
      }
      if (!empty($productos['existencia']['HMO'])) {
          $existencia_producto += $productos['existencia']['HMO'];
          $existencia_resto += $productos['existencia']['HMO'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '1'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '1',
            'existencias' => $productos['existencia']['HMO']
          ]
          );
      }
      if (!empty($productos['existencia']['LMO'])) {
          $existencia_producto += $productos['existencia']['LMO'];
          $existencia_resto += $productos['existencia']['LMO'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '3'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '3',
            'existencias' => $productos['existencia']['LMO']
          ]
          );
      }
      if (!empty($productos['existencia']['CLN'])) {
          $existencia_producto += $productos['existencia']['CLN'];
          $existencia_resto += $productos['existencia']['CLN'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '4'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '4',
            'existencias' => $productos['existencia']['CLN']
          ]
          );
      }
      if (!empty($productos['existencia']['CHI'])) {
          $existencia_producto += $productos['existencia']['CHI'];
          $existencia_resto += $productos['existencia']['CHI'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '7'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '7',
            'existencias' => $productos['existencia']['CHI']
          ]
          );
      }
      if (!empty($productos['existencia']['MOR'])) {
          $existencia_producto += $productos['existencia']['MOR'];
          $existencia_resto += $productos['existencia']['MOR'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '13'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '13',
            'existencias' => $productos['existencia']['MOR']
          ]
          );
      }
      if (!empty($productos['existencia']['VER'])) {
          $existencia_producto += $productos['existencia']['VER'];
          $existencia_resto += $productos['existencia']['VER'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '16'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '16',
            'existencias' => $productos['existencia']['VER']
          ]
          );
      }
      if (!empty($productos['existencia']['CTZ'])) {
          $existencia_producto += $productos['existencia']['CTZ'];
          $existencia_resto += $productos['existencia']['CTZ'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '18'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '18',
            'existencias' => $productos['existencia']['CTZ']
          ]
          );
      }
      if (!empty($productos['existencia']['TAM'])) {
          $existencia_producto += $productos['existencia']['TAM'];
          $existencia_resto += $productos['existencia']['TAM'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '19'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '19',
            'existencias' => $productos['existencia']['TAM']
          ]
          );
      }
      if (!empty($productos['existencia']['PUE'])) {
          $existencia_producto += $productos['existencia']['PUE'];
          $existencia_resto += $productos['existencia']['PUE'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '20'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '20',
            'existencias' => $productos['existencia']['PUE']
          ]
          );
      }
      if (!empty($productos['existencia']['VHA'])) {
          $existencia_producto += $productos['existencia']['VHA'];
          $existencia_resto += $productos['existencia']['VHA'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '21'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '21',
            'existencias' => $productos['existencia']['VHA']
          ]
          );
      }
      if (!empty($productos['existencia']['TUX'])) {
          $existencia_producto += $productos['existencia']['TUX'];
          $existencia_resto += $productos['existencia']['TUX'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '22'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '22',
            'existencias' => $productos['existencia']['TUX']
          ]
          );
      }
      if (!empty($productos['existencia']['MTY'])) {
          $existencia_producto += $productos['existencia']['MTY'];
          $existencia_resto += $productos['existencia']['MTY'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '23'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '23',
            'existencias' => $productos['existencia']['MTY']
          ]
          );
      }
      if (!empty($productos['existencia']['MID'])) {
          $existencia_producto += $productos['existencia']['MID'];
          $existencia_resto += $productos['existencia']['MID'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '25'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '25',
            'existencias' => $productos['existencia']['MID']
          ]
          );
      }
      if (!empty($productos['existencia']['MAZ'])) {
          $existencia_producto += $productos['existencia']['MAZ'];
          $existencia_resto += $productos['existencia']['MAZ'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '27'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '27',
            'existencias' => $productos['existencia']['MAZ']
          ]
          );
      }
      if (!empty($productos['existencia']['CUE'])) {
          $existencia_producto += $productos['existencia']['CUE'];
          $existencia_resto += $productos['existencia']['CUE'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '28'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '28',
            'existencias' => $productos['existencia']['CUE']
          ]
          );
      }
      if (!empty($productos['existencia']['CUN'])) {
          $existencia_producto += $productos['existencia']['CUN'];
          $existencia_resto += $productos['existencia']['CUN'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '32'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '32',
            'existencias' => $productos['existencia']['CUN']
          ]
          );
      }
      if (!empty($productos['existencia']['DFP'])) {
          $existencia_producto += $productos['existencia']['DFP'];
          $existencia_resto += $productos['existencia']['DFP'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '33'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '33',
            'existencias' => $productos['existencia']['DFP']
          ]
          );
      }
      if (!empty($productos['existencia']['ACX'])) {
          $existencia_producto += $productos['existencia']['ACX'];
          $existencia_resto += $productos['existencia']['ACX'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '42'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '42',
            'existencias' => $productos['existencia']['ACX']
          ]
          );
      }
      if (!empty($productos['existencia']['CEL'])) {
          $existencia_producto += $productos['existencia']['CEL'];
          $existencia_resto += $productos['existencia']['CEL'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '46'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '46',
            'existencias' => $productos['existencia']['CEL']
          ]
          );
      }
      if (!empty($productos['existencia']['OBR'])) {
          $existencia_producto += $productos['existencia']['OBR'];
          $existencia_resto += $productos['existencia']['OBR'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '2'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '2',
            'existencias' => $productos['existencia']['OBR']
          ]
          );
      }
      if (!empty($productos['existencia']['DGO'])) {
          $existencia_producto += $productos['existencia']['DGO'];
          $existencia_resto += $productos['existencia']['DGO'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '5'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '5',
            'existencias' => $productos['existencia']['DGO']
          ]
          );
      }
      if (!empty($productos['existencia']['TRN'])) {
          $existencia_producto += $productos['existencia']['TRN'];
          $existencia_resto += $productos['existencia']['TRN'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '6'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '6',
            'existencias' => $productos['existencia']['TRN']
          ]
          );
      }
      if (!empty($productos['existencia']['AGS'])) {
          $existencia_producto += $productos['existencia']['AGS'];
          $existencia_resto += $productos['existencia']['AGS'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '8'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '8',
            'existencias' => $productos['existencia']['AGS']
          ]
          );
      }
      if (!empty($productos['existencia']['SLP'])) {
          $existencia_producto += $productos['existencia']['SLP'];
          $existencia_resto += $productos['existencia']['SLP'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '10'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '10',
            'existencias' => $productos['existencia']['SLP']
          ]
          );
      }
      if (!empty($productos['existencia']['XLP'])) {
          $existencia_producto += $productos['existencia']['XLP'];
          $existencia_cedis += $productos['existencia']['XLP'];
           $existencias = Existencias::updateOrCreate(
             ['clave_ct' => $productos['clave'],
             'almacen_id' => '15'
           ],
             [
             'clave_ct' => $productos['clave'],
             'almacen_id' => '15',
             'existencias' => $productos['existencia']['XLP']
           ]
           );
      }
      if (!empty($productos['existencia']['DFT'])) {
          $existencia_producto += $productos['existencia']['DFT'];
          $existencia_resto += $productos['existencia']['DFT'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '36'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '36',
            'existencias' => $productos['existencia']['DFT']
          ]
          );
      }
      if (!empty($productos['existencia']['CDV'])) {
          $existencia_producto += $productos['existencia']['CDV'];
          $existencia_resto += $productos['existencia']['CDV'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '44'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '44',
            'existencias' => $productos['existencia']['CDV']
          ]
          );
      }
      if (!empty($productos['existencia']['SLT'])) {
          $existencia_producto += $productos['existencia']['SLT'];
          $existencia_resto += $productos['existencia']['SLT'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '14'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '14',
            'existencias' => $productos['existencia']['SLT']
          ]
          );
      }
      if (!empty($productos['existencia']['TPC'])) {
          $existencia_producto += $productos['existencia']['TPC'];
          $existencia_resto += $productos['existencia']['TPC'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '24'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '24',
            'existencias' => $productos['existencia']['TPC']
          ]
          );
      }
      if (!empty($productos['existencia']['TOL'])) {
          $existencia_producto += $productos['existencia']['TOL'];
          $existencia_resto += $productos['existencia']['TOL'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '29'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '29',
            'existencias' => $productos['existencia']['TOL']
          ]
          );
      }
      if (!empty($productos['existencia']['PAC'])) {
          $existencia_producto += $productos['existencia']['PAC'];
          $existencia_resto += $productos['existencia']['PAC'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '30'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '30',
            'existencias' => $productos['existencia']['PAC']
          ]
          );
      }
      if (!empty($productos['existencia']['IRA'])) {
          $existencia_producto += $productos['existencia']['IRA'];
          $existencia_resto += $productos['existencia']['IRA'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '38'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '38',
            'existencias' => $productos['existencia']['IRA']
          ]
          );
      }
      if (!empty($productos['existencia']['OAX'])) {
          $existencia_producto += $productos['existencia']['OAX'];
          $existencia_resto += $productos['existencia']['OAX'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '26'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '26',
            'existencias' => $productos['existencia']['OAX']
          ]
          );
      }
      if (!empty($productos['existencia']['DFC'])) {
          $existencia_producto += $productos['existencia']['DFC'];
          $existencia_resto += $productos['existencia']['DFC'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '39'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '39',
            'existencias' => $productos['existencia']['DFC']
          ]
          );
      }
      if (!empty($productos['existencia']['TXL'])) {
          $existencia_producto += $productos['existencia']['TXL'];
          $existencia_resto += $productos['existencia']['TXL'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '40'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '40',
            'existencias' => $productos['existencia']['TXL']
          ]
          );
      }
      if (!empty($productos['existencia']['URP'])) {
          $existencia_producto += $productos['existencia']['URP'];
          $existencia_resto += $productos['existencia']['URP'];
          $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '43'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '43',
            'existencias' => $productos['existencia']['URP']
          ]
          );
      }
      if (!empty($productos['existencia']['CHT'])) {
        $existencia_producto += $productos['existencia']['CHT'];
        $existencia_resto += $productos['existencia']['CHT'];
        $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '47'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '47',
            'existencias' => $productos['existencia']['CHT']
          ]
          );
      }
      if (!empty($productos['existencia']['LEO'])) {
        $existencia_producto += $productos['existencia']['LEO'];
        $existencia_resto += $productos['existencia']['LEO'];
        $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '11'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '11',
            'existencias' => $productos['existencia']['LEO']
          ]
          );
      }
      if (!empty($productos['existencia']['TXA'])) {
        $existencia_producto += $productos['existencia']['TXA'];
        $existencia_resto += $productos['existencia']['TXA'];
        $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '12'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '12',
            'existencias' => $productos['existencia']['TXA']
          ]
          );
      }
      if (!empty($productos['existencia']['CMT'])) {
        $existencia_producto += $productos['existencia']['CMT'];
        $existencia_resto += $productos['existencia']['CMT'];
        $existencias = Existencias::updateOrCreate(
            ['clave_ct' => $productos['clave'],
            'almacen_id' => '51'
          ],
            [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '51',
            'existencias' => $productos['existencia']['CMT']
          ]
          );
      }
      $existencias = Existencias::updateOrCreate(
        ['clave_ct' => $productos['clave'],
        'almacen_id' => '50'
      ],
        [
        'clave_ct' => $productos['clave'],
        'almacen_id' => '50',
        'existencias' => $existencia_cedis
      ]
      );

      if ($existencia_cedis == 0){
        $existencias = Existencias::updateOrCreate(
          ['clave_ct' => $productos['clave'],
          'almacen_id' => '53'
        ],
        [
        'clave_ct' => $productos['clave'],
        'almacen_id' => '53',
        'existencias' => $existencia_resto
      ]
    );
  }
      return $existencia_producto;
    }

    public function existenciasTotalesXalapa($productos)
    {
      set_time_limit(0);
      //dd($productos);
      $existencia_producto = 0;
      $existencia_cedis = 0;
      $existencia_resto = 0;
      if (!empty($productos['existencia']['DFA'])) {
        $existencia_producto += $productos['existencia']['DFA'];
        $existencia_cedis += $productos['existencia']['DFA'];
        $existencias = Existencias::updateOrCreate(
          ['clave_ct' => $productos['clave'],
            'almacen_id' => '34'
          ],
          [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '34',
            'existencias' => $productos['existencia']['DFA']
          ]
        );
      }
      if (!empty($productos['existencia']['D2A'])) {
        $existencia_producto += $productos['existencia']['D2A'];
        $existencia_cedis += $productos['existencia']['D2A'];
        $existencias = Existencias::updateOrCreate(
          ['clave_ct' => $productos['clave'],
            'almacen_id' => '48'
          ],
          [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '48',
            'existencias' => $productos['existencia']['D2A']
          ]
        );
      }
      if (!empty($productos['existencia']['XLP'])) {
        $existencia_producto += $productos['existencia']['XLP'];
        $existencia_cedis += $productos['existencia']['XLP'];
        $existencias = Existencias::updateOrCreate(
          ['clave_ct' => $productos['clave'],
            'almacen_id' => '15'
          ],
          [
            'clave_ct' => $productos['clave'],
            'almacen_id' => '15',
            'existencias' => $productos['existencia']['XLP']
          ]
        );
      }
      $existencias = Existencias::updateOrCreate(
        ['clave_ct' => $productos['clave'],
          'almacen_id' => '50'
        ],
        [
          'clave_ct' => $productos['clave'],
          'almacen_id' => '50',
          'existencias' => $existencia_cedis
        ]
      );
      return $existencia_producto;
    }

    public function lecturaPrueba()
    {
        $products = new ProductosController();
        $existencia_producto = 0;
        // $products->limpieza();
        set_time_limit(0);
        $fileExist = Storage::disk('prueba-ftp')->exists('catalogo_xml/productos.json');
        if ($fileExist) {
            $size = storage_path('catalogo_xml/productos.json');
            //Storage::disk('local')->put('public/products.json',Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
            Storage::disk('local')->put('public/products-' . filesize($size) . '.json', Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
        } else {
            dd("No existe");
        }
        //     set_time_limit(0);
        //     for($i=0;$i<sizeof($productos);$i++){
        //         if($productos[$i]['idCategoria']!=0){

        //             $marca_nueva = Marca::updateOrCreate(
        //                 ['id'=>$productos[$i]['idMarca']],
        //                 [
        //                     'id'=>$productos[$i]['idMarca'],
        //                     'nombre'=>$productos[$i]['marca']
        //                 ]
        //             );
        //             $categoria_nueva = Categoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['categoria']
        //                 ]
        //             );
        //             $subcategoria_nueva = Subcategoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idSubCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['subcategoria']
        //                 ]
        //             );
        //             $producto = Producto::updateOrCreate(
        //                 ['clave_ct'=>$productos[$i]['clave']],
        //                 [
        //                     'marca_id'=>$productos[$i]['idMarca'],
        //                     'subcategoria_id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['nombre'],
        //                     'descripcion_corta'=>$productos[$i]['descripcion_corta'],
        //                     'precio_unitario'=>$productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio']*$productos[$i]['tipoCambio'])*1.16),2,'.',''):number_format(($productos[$i]['precio']*1.16),2,'.',''),
        //                     'sku'=>ltrim($productos[$i]['numParte']),
        //                     'ean'=>$productos[$i]['ean'],
        //                     'upc'=>$productos[$i]['upc'],
        //                     'imagen'=>$productos[$i]['imagen'],
        //                     'existencias'=>$existencia_producto,
        //                     'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
        //                 ]
        //             );
        //             if(!empty($productos[$i]['promociones'])){
        //                 // dd($productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 // date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio']));
        //                 // date('Y-m-d\TH:i:s', $productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 if($productos[$i]['promociones'][0]['tipo']!="porcentaje"){
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>100-($productos[$i]['promociones'][0]['promocion']*100)/$productos[$i]['precio'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }else{
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>$productos[$i]['promociones'][0]['promocion'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }
        //                 // dd($productos[$i]['clave']);
        //             }
        //             $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
        //             for($j=0;$j<sizeof($palabras_clave);$j++){
        //                 $producto = Palabras::updateOrCreate(
        //                     ['clave_ct'=>$productos[$i]['clave'],
        //                     'palabra'=>$palabras_clave[$j]]
        //                 );
        //             }
        //         }
        //     }
        //     dd($productos);
    }

    public function sitemap(){
        set_time_limit(0);
        $texto1 = '<?xml version="1.0" encoding="utf-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
          <url>
            <loc>https://ehstecnologias.com.mx</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/bocinas-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/controles-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/diademas-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/escritorio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/fuentes-de-poder-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/gabinetes-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/kits-de-teclado-y-mouse-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/mochila-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/motherboards-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/mouse-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/mouse-pads-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/sillas-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/tarjetas-de-video-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/teclados-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/accesorios-para-pcs</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/adaptadores-para-disco-duro</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/adaptadores-usb</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/ergonomia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/herramientas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/kits-para-teclado-y-mouse</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/mouse</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/mouse-pads</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/soportes-para-pcs</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/teclados</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/webcams</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/accesorios-para-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/adaptadores-para-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/bases-enfriadoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/baterias-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/candados-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/docking-station</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/extension-de-garantias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/filtro-de-privacidad</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/fundas-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/fundas-para-tablets</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/mochilas-y-maletines</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/pantallas-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/protectores-para-tablets</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/teclados-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/accesorios-para-camaras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/accesorios-para-celulares</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/audifonos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/cargadores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/controles</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/diademas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/diademas-y-audifonos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/equipo-para-celulares</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/fundas-y-maletines</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/lentes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/limpieza</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/pilas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/plumas-interactivas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/power-banks</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/soportes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-energia/adaptadores-de-energia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-energia/iluminacion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-impresion/accesorios-para-impresoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-impresion/gabinetes-para-impresoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-impresion/mantenimiento</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/bases</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/baterias-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/cables-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/etiquetas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/garantias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/torretas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-de-ethernet</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-inalambricos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-para-audio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-para-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-para-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-usb-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/amplificadores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/antenas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/concentradores-hub</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/convertidor-de-medios</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/tarjetas-para-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/transceptores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento/almacenamiento-externo</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento/discos-duros</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento/ssd</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/almacenamiento-optico</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/discos-duros-externos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/gabinetes-para-discos-duros</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/memorias-flash</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/memorias-usb</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/accesorios-para-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/adaptadores-para-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/audifonos-para-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/cables-lightning</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/imac</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/ipad</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/macbook</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/perifericos-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/bocinas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/home-theaters</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/microfonos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/micro-y-mini-componentes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/reproductores-mp3</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/baterias-banks/bancos-de-bateria</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/baterias-banks/reemplazos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/accesorios-para-cables</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-displayport</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-dvi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-hdmi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-usb-para-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-vga</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/bobinas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-alimentacion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-audio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-energia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-displayport</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-dvi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-hdmi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-kvm</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-serial</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-usb</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-vga</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/convertidores-av</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/herramientas-para-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/jacks</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/all-in-one</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/mini-pc</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/pcs-de-escritorio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/tabletas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras-gaming/laptops-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras-gaming/monitores-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras-gaming/pcs-de-escritorio-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/cabezales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/cartuchos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/cintas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/papeleria</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/refacciones</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/toners</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/credencializacion/consumibles-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/credencializacion/digitalizadores-de-firmas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/digitalizacion-de-imagenes/escaner</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/contactos-inteligentes-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/control-inteligente</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/hub-y-concentadores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/interruptores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/sensores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/auricurales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/camaras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/celulares</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/consolas-y-video-juegos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/pantallas-de-proyeccion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/proyectores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/reproductores-dvd-y-blu-ray</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/smartwatch</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/streaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/televisiones</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/transmisores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/energia/baterias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/energia-solar-y-eolica/inversores-de-energia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/enfriamiento-y-ventilacion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/fuentes-de-poder</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/gabinetes-para-computadoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/lectores-de-memorias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/memorias-ram</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/microprocesadores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/monitores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/motherboards</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/quemadores-dvd-y-bluray</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/esd/licencias-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/impresoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/multifuncionales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/plotters</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/rotuladores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/licenciamiento/sistemas-operativos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/estaciones-de-carga</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/regletas-y-multicontactos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/supresores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/transformadores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/cajones-de-dinero</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/impresoras-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/kit-punto-de-venta</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/lectores-de-codigos-de-barras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/monitores-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/terminales-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/productividad/fpp</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/access-points</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/extensores-de-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/routers</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/switches</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/redes/accesorios-de-redes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/redes/networking</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/accesorios-para-racks</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/gabinetes-de-piso</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/gabinetes-para-montaje</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/racks-modulo</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/respaldo-y-regulacion/no-breaks-y-ups</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/respaldo-y-regulacion/reguladores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/caretas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/cubrebocas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/desinfectantes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/equipo</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/tapetes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/termometros</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad/antivirus</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad/videovigilancia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad-electronica/corporativos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad-electronica/pequenos-negocios</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/sistema-para-puntos-de-venta/software-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/sistemas-operativos/windows</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/software-administrativo/licencias-microsoft</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/software-pos/licencias-cisco</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/solucion-para-servidores/almacenamiento-nas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/solucion-para-servidores/gabinete-para-almacenaje</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/solucion-para-servidores/servidores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-de-sonido</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-de-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-paralelas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-seriales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/workstations/workstations-de-escritorio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <!-- PRODUCTOS ACTIVOS -->';
        Storage::put('sitemap.xml', $texto1);
        $remove = array(" ", "  ", "   ", "    ", "(", ")", "$", "*", "/", ",", "IVA", "Incluido");
        $client = new Client2();
        //dd('noexiste');
        $productos = Producto::where('estatus','=','Activo')
        //$productos = Producto::whereNull('enlace')
        //->where('estatus','=','Activo')
        //->orderBy('enlace')
        ->get([
            'enlace',
            'sku',
            'clave_ct'
        ]);
        //dd($productos[0]);
        for ($i = 0; $i < sizeof($productos); $i++) {
        $texto2 = '';
        //for ($i = 0; $i < 5; $i++) {
            if(($productos[$i]->enlace) == null){
                $cliente = new Client2();
                $sku = $productos[$i]->sku;
                $clavect = $productos[$i]->clave_ct;
                //dd($sku);
                if($sku==""){
                    $sku="NO EXISTE";
                }
                $website2 = $cliente->request('GET', 'https://ehstecnologias.com.mx/productos?b=' . $sku);
                //$website2 = $cliente->request('GET', 'https://ctonline.mx/buscar/productos?b=' . $sku);
                //dd('https://ctonline.mx/buscar/productos?b=' . $sku);
                //$resultado = $website2->filter('.imagen_centrica > a');
                //dd($website2->filter('.content-img > a')->text());
                if ($website2->filter('.content-img > a')->count()>0){
                  $resultado = $website2->filter('.content-img > a');
                  $texto2 = "<url>
                  <loc>".$resultado->attr('href')."</loc>
                  <changefreq>daily</changefreq>
                  <priority>0.5</priority>
                </url>";
                  //dd($texto2);
                  $enlaces = Producto::updateOrCreate(
                    ['clave_ct' => $clavect],
                    [
                      'enlace' => $resultado->attr('href'),
                    ]
                  )
                  ->where('clave_ct', '=', $clavect)
                  ->where('sku', '=', $sku);
                  Storage::append("sitemap.xml", $texto2);
                }else{
                  //dd('Producto No Encontrado');
                }
                //dd($resultado->attr('href'));
            }else{
                $enlace = $productos[$i]->enlace;
                //dd($enlace);
                $texto2 = "<url>
                <loc>".$enlace."</loc>
                <changefreq>daily</changefreq>
                <priority>0.5</priority>
              </url>";
                Storage::append("sitemap.xml", $texto2);
            }
            }
        $texto3 = "</urlset>";
        Storage::append("sitemap.xml", $texto3);
        dd('Sitemap Creado');
  }

  public function enlaces(){
    set_time_limit(0);
    $remove = array(" ", "  ", "   ", "    ", "(", ")", "$", "*", "/", ",", "IVA", "Incluido");
    $client = new Client2();
    //dd('noexiste');
    //$productos = Producto::where('estatus','=','Activo')
    $productos = Producto::whereNull('enlace')
    ->where('estatus','=','Activo')
    //->orderBy('enlace')
    ->get([
      'enlace',
      'sku',
      'clave_ct'
      ]);
    for ($i = 0; $i < sizeof($productos); $i++) {
    //for ($i = 0; $i < 5; $i++) {
      if(($productos[$i]->enlace) == null){
        $cliente = new Client2();
        //$sku = $productos[$i]->sku;
        $sku = 'ES-05002';
        //dd($sku);
        if($sku==""){
          $sku="NO EXISTE";
        }
        $website2 = $cliente->request('GET', 'https://ehstecnologias.com.mx/productos?b=' . $sku);
        //$website2 = $cliente->request('GET', 'https://ctonline.mx/buscar/productos?b=' . $sku);
        //dd('https://ctonline.mx/buscar/productos?b=' . $sku);
        //$resultado = $website2->filter('.imagen_centrica > a');
        //dd($website2->filter('.content-img > a')->text());
        $clavect = $productos[$i]->clave_ct;
        if ($website2->filter('.content-img > a')->count()>0){
          $resultado = $website2->filter('.content-img > a');
          //dd($resultado->attr('href'));
          $enlaces = Producto::updateOrCreate(
            ['clave_ct' => $clavect],
            [
              'enlace' => $resultado->attr('href'),
            ]
          )
          ->where('clave_ct', '=', $clavect)
          ->where('sku', '=', $sku)
          ;
        }else{
          //dd('Producto No Encontrado');
        }
        //dd($resultado->attr('href'));
      }else{
      }
    }
    dd('Enlace Actualizado');
  }

  public function woocommerce()
    {
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $woocommerce = new WooClient(
          'https://www.ehstecnologias.com.mx/',
          'ck_209a05b01fc07d7b4d54c05383b048f9d58c075f',
          'cs_0ef9dc123f35a8b70a76317e30a598332dcd01c6',
          [
            'version' => 'wc/v3',
            'timeout' => 800,
            'query_string_auth' => true,
            'verify_ssl' => false
          ]
        );
        $apiCT = new CTConnect();
        // PARA LOS PRODUCTOS EN WOOCOMMERCE
        $data['productos'] = Woocommerce::Join('productos', 'productos.clave_ct', '=', 'woocommerce.clave_ct')
          ->Join('promociones', 'woocommerce.clave_ct', '=', 'promociones.clave_ct')
          ->where('productos.estatus', 'Activo')
          // ->where('productos.categoria_id', 610)
          // ->where('productos.subcategoria_id', 157)
          // ->where('productos.marca_id', 6)
          // ->where('existencias.almacen_id', '=', 15)
          ->where('productos.existencias', '>', 0)
          ->whereMonth('promociones.updated_at', date('m'))
          ->whereDay('promociones.updated_at', date('d'))
          // ->where('productos.clave_ct', 'CPUDEL9350')
          // ->where('woocommerce.precio_venta', '>', 0)
          // ->skip(90)
          // ->take(10)
          ->orderBy('woocommerce.id','ASC')
          ->get(
              [
              'woocommerce.idWP',
              'productos.clave_ct',
              'productos.existencias',
              'woocommerce.precio_venta',
              'woocommerce.precio_venta_rebajado',
              'woocommerce.fecha_inicio',
              'woocommerce.fecha_fin',
              'productos.precio_unitario',
          ]
        );
        // dd(count($data['productos']));
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
        $dataWP = [
            'stock_quantity' => $data['productos'][$i]['existencias'],
            'regular_price' => $data['productos'][$i]['precio_venta'],
            'sale_price' => $data['productos'][$i]['precio_venta_rebajado'],
            'date_on_sale_from' => $data['productos'][$i]['fecha_inicio'],
            'date_on_sale_to' => $data['productos'][$i]['fecha_fin']
          ];
          // dd($woocommerce->get('products/22739'));
          $producto = $woocommerce->put('products/'.$data['productos'][$i]['idWP'], $dataWP);
          // dd($producto);
        }
          // $producto = $woocommerce->put('products/15472', $dataWP);
          // dd($woocommerce->put('products/'.$data['promociones'][$i]['idWP'], $dataWP));
          // dd($woocommerce->get('products',$params));
          // $producto = $woocommerce->get('products',$params);
          // dd($producto[0]->id);

          // $productos = DB::select("SELECT categorias.nombre AS Categoría, subcategorias.nombre AS Subcategoría, productos.nombre, productos.sku, productos.clave_ct, productos.precio_unitario, existencias.existencias, productos.enlace FROM productos INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategoriaS.id INNER JOIN existencias ON productos.clave_ct = existencias.clave_ct WHERE existencias.almacen_id = 15 AND existencias.existencias > 150 AND productos.estatus = 'Activo';");

          // $productos = json_decode(file_get_contents(storage_path() . "/app/public/productos-woo.json"), true);
          // return $productos;
        dd("Terminado");
    }

    public function woocommerce2()
    {
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $woocommerce = new WooClient(
          'http://ehstecnologias.com.mx/',
          'ck_209a05b01fc07d7b4d54c05383b048f9d58c075f',
          'cs_0ef9dc123f35a8b70a76317e30a598332dcd01c6',
          [
            'version' => 'wc/v3',
          ]
        );
        $data['existencias'] = Producto::join('existencias','existencias.clave_ct','=','productos.clave_ct')
        ->join('woocommerce','woocommerce.clave_ct','=','productos.clave_ct')
        ->where('productos.estatus','Activo')
        ->where('productos.existencias','>',0)
        ->where('existencias.almacen_id', '=', 15)
        ->get([
            'productos.nombre',
            'woocommerce.idWP',
            'productos.existencias as existencias',
            'productos.clave_ct'
        ])
        ->toArray();
        //dd(sizeof($data['existencias']));
        //for ($i = 0; $i < sizeof($data['existencias']); $i++) {
        for ($i = 0; $i < 1; $i++) {
          //dd($data['existencias'][$i]['idWP']);
          if ($data['existencias'][$i]['idWP'] >= 3) {
            $params = [
              //'id' => $data['existencias'][$i]['idWP'],
              'type' => 'external',
              'external_url' => 'https://api.whatsapp.com/send?phone=2283669400&text=Hola,%20quiero%20solicitar%20la%20cotización%20del%20producto:%20%2A'.$data['existencias'][$i]['nombre'].'%2A%20con%20CLAVE:%20%2A'.$data['existencias'][$i]['clave_ct'].'%2A',
              'button_text' => 'Consultar existencias con asesor',
              'stock_quantity' => $data['existencias'][$i]['existencias']
            ];
            $data['woocommerce'] = $woocommerce->put('products/'.$data['existencias'][$i]['idWP'],$params);
          } else {
            $params = [
              'id' => $data['existencias'][$i]['idWP'],
              'type' => 'simple',
              'external_url' => '',
              'button_text' => '',
              'stock_quantity' => $data['existencias'][$i]['existencias']
            ];
            $data['woocommerce'] = $woocommerce->put('products/'.$data['existencias'][$i]['idWP'],$params);
          }
          
        }
        //dd(sizeof($data['existencias']));
        dd("Terminado");
    }

    public function woocommerce1(){
      set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $texto1 = 'Productos con Imagenes Faltantes -'.$fechaR;
        $woocommerce = new WooClient(
          'http://ehstecnologias.com.mx/',
          'ck_209a05b01fc07d7b4d54c05383b048f9d58c075f',
          'cs_0ef9dc123f35a8b70a76317e30a598332dcd01c6',
          [
            'version' => 'wc/v3',
            'timeout' => 2000
          ]
        );
        $productos = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
        ->get([
          'productos.clave_ct'
        ])
        ->toArray();
        // dd(sizeof($productos));
        // dd($productos);
        // $params = [
        //   'per_page'=>100,
        //   'page'=>9
        // ];
        // for ($i = 0; $i < sizeof($productos); $i++) {
        // dd($productos);
        for ($i = 0; $i < 200; $i++) {
          $params = [
            'sku'=> $productos[$i]['clave_ct'],
          ];
          $data['woocommerce']= $woocommerce->get('products',$params);
          // for ($i = 0; $i < sizeof($data['woocommerce']); $i++) {
          if(isset($data['woocommerce'][0]->images[0]->src)){
            $ids = Producto::updateOrCreate(
            ['clave_ct' => $productos[$i]['clave_ct']],
            [
              'enlace'  => $data['woocommerce'][0]->permalink,
              'imagen' => $data['woocommerce'][0]->images[0]->src,
            ]
          );
          $texto2 = $i. ', '.$productos[$i]['clave_ct']. ', - ACTUALIZADO';
            Storage::append("imagenes_faltantes.xml", $texto2);
          }else{
            $texto2 = $i. ', '.$productos[$i]['clave_ct']. ', - ERROR';
            Storage::append("imagenes_faltantes.xml", $texto2);
            // dd($productos[$i]['clave_ct']);

          }
      }
        dd("Listo Todos");
    }

    public function lecturaAtributos()
    {
        set_time_limit(0);
        $productos = json_decode(file_get_contents(storage_path() . "/app/public/productos.json"), true);
        for ($i = 0; $i < sizeof($productos); $i++) {
        // for ($i = 0; $i < 10; $i++) {
            $existencia_producto = 0;
            if ($productos[$i]['especificaciones'] != null) {
                // dd($productos[$i]['especificaciones']);
                for ($h = 0; $h < sizeof($productos[$i]['especificaciones']); $h++) {
                  // dd($productos[$i]['especificaciones'][$h]['tipo']);
                  $atributo_nuevo = Atributo::updateOrCreate(
                    ['nombre' => $productos[$i]['especificaciones'][$h]['tipo']],
                    [
                        'nombre' => $productos[$i]['especificaciones'][$h]['tipo']
                    ]
                  );
                  // dd($atributo_nuevo->id);
                  $especificacion_nueva = Especificacion::updateOrCreate(
                      ['atributo_id' => $atributo_nuevo->id,
                       'clave_ct' => $productos[$i]['clave'],
                       'valor' => $productos[$i]['especificaciones'][$h]['valor']
                      ],
                      [
                        'atributo_id' => $atributo_nuevo->id,
                        'clave_ct' => $productos[$i]['clave'],
                        'valor' => $productos[$i]['especificaciones'][$h]['valor']
                      ]
                  );
                }
                // dd($productos[$i]['especificaciones'][0]['tipo']);
            }
        }
        dd("Archivo cargado");
    }
}

