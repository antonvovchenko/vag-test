<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductLabelCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Product;
use App\Http\Requests;
use App\Http\Controllers\ApiController;

class ProductController extends ApiController
{
    public  $_validators = [
        'title' => 'required|max:255|unique:product,title',
        'ndc' => 'unique:product,ndc'
    ];

    public $query_with = ['generic', 'markup_type', 'label_codes' ,'tax'];

    public function __construct()
    {
        $this->model = new Product();
        parent::__construct();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        parent::store($request);

        if ($this->is_success_request === true) {

            //add labels
            $inputData = $request->all();
            if (isset($inputData['label_codes'])) {
                $labelCodes = [];
                foreach ($inputData['label_codes'] as $labelCodeData) {
                    $labelCodes[] = new ProductLabelCode(['label_code_id' => $labelCodeData['id']]);
                }
                $this->item->label_codes()->saveMany($labelCodes);
            }
            //reload relation
            $this->item->load('label_codes');
        }
        return $this->successResponse($this->getSuccessStoreResponseData());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $parentResponse = parent::update($request, $id);

        if ($this->is_success_request === true) {
            //remove old labels
            ProductLabelCode::where('product_id', $this->item->id)->delete();
            //add labels
            $inputData = $request->all();
            if (isset($inputData['label_codes'])) {
                $labelCodes = [];
                foreach ($inputData['label_codes'] as $labelCodeData) {
                    $labelCodes[] = new ProductLabelCode(['label_code_id' => $labelCodeData['id']]);
                }
                $this->item->label_codes()->saveMany($labelCodes);
            }
            //reload relation
            $this->item->load('label_codes');
        }

        return $parentResponse;
    }

    public function applyRequestFilters()
    {
        $title = Input::get('title');
        if ($title && !empty($title)) {
            $this->query->where('title', 'like', '%'.$title.'%');
        }

        $is_generic = Input::get('is_generic');
        if ($is_generic !== null) {
            $this->query->where('is_generic', $is_generic);
        }

        $is_active = Input::get('is_active');
        if ($is_active !== null) {
            $this->query->where('is_active', $is_active);
        }

        $supplier_id = Input::get('supplier_id');
        if ($supplier_id && !empty($supplier_id)) {
            $this->query
                ->with('inventory_items')
                ->whereHas('inventory_items', function ($query) {
                    $query->where('supplier_id', Input::get('supplier_id'));
                });
        }
    }

    /**
     * Search NDC
     */
    public function getSearchNdc()
    {
        $text = Input::get('text');

        $lic_url = 'http://intranet.nhf.org.jm/ndcsearch/drug-search.php';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $lic_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "submitBtn=Get%20NDC&inputString=".$text);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode != 200) {
            return $this->errorResponse(['errors' => 'Network error. The source "intranet.nhf.org.jm" is not responsible now.']);
        } else {

//            $response = "<br><table  width='100%'>
//              <tr>
//                 <td width='100' ><b>NDC</b></td>
//                 <td width='300'><b>LABEL_NAME</b></td>
//                 <td width='600'><b>GENERIC NAME</b></td>
//                 <td width='300'><b>MANUFACTURER NAME</b></td>
//                 <td width='300'><b>DOSAGE FORM</b></td>
//                 <td width='150'><b>PACKAGE SIZE</b></td>
//				 <td width='150'><b>PACKAGE_QTY</b></td>
//				 <td width='250'><b>COVERAGE</b></td>
//                 <td width='150'><b>SUBSIDY</b></td>
//              </tr><tr><td>77131300441</td><td>APO-HYDRALAZINE TAB 25MG</td><td>HYDRALAZINE HCL TAB 25
// MG</td><td>APOTEX</td><td>TABS</td><td>100</td><td>1</td><td>NHF  GOJ</td><td>2.52<td></td></tr><tr
//><td>77131300442</td><td>APO-HYDRALAZINE TAB 25MG</td><td>HYDRALAZINE HCL TAB 25 MG</td><td>APOTEX</td
//><td>TABS</td><td>500</td><td>1</td><td>NHF  GOJ</td><td>2.52<td></td></tr></table>
//				<br>";

            $response = str_replace(["\r\n", "\r", "\n"], '', $response);

            preg_match_all('/<table.*?>(.*?)<\/table>/si', $response, $matches);

            $table_content = $matches[1][0];

            preg_match_all('/<tr.*?>(.*?)<\/tr>/si', $table_content, $matchesTds);

            $tds = $matchesTds[1];

            $names = [
                'NDC',
                'LABEL_NAME',
                'GENERIC NAME',
                'MANUFACTURER NAME',
                'DOSAGE FORM',
                'PACKAGE SIZE',
                'PACKAGE_QTY',
                'COVERAGE',
                'SUBSIDY'
            ];
            $items = [];
            foreach ($tds as $key=>$tdHtml) {
                if ($key != 0) {
                    preg_match_all('/<td.*?>(.*?)<\/td>/si', $tdHtml, $matchesTd);
                    $item = [];
                    foreach ($matchesTd[1] as $k=>$td) {
                        $item[] = [
                            'title' => $names[$k],
                            'value' => strip_tags($td)
                        ];
                    }
                    $items[] = $item;
                }
            }
            return $this->successResponse(['items' => $items, 'names' => $names]);
        }

    }

    /**
     * Search NDC
     */
    public function getSearchNdcProducts()
    {
        $text = Input::get('text');

        $lic_url = 'http://intranet.nhf.org.jm/ndcsearch/rpc.php';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $lic_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "queryString=".$text);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode != 200) {
            return $this->errorResponse(['errors' => 'Network error. The source "intranet.nhf.org.jm" is not responsible now.']);
        } else {
//            $response = '<li onClick="fill(\'APO-HYDRALAZINE TAB 25MG\');">APO-HYDRALAZINE TAB 25MG</li><li onClick="fill(\'APO-HYDRALAZINE TAB 25MG\');">APO-HYDRALAZINE TAB 25MG</li><li onClick="fill(\'APO-HYDRALAZINE TAB 50MG\');">APO-HYDRALAZINE TAB 50MG</li><li onClick="fill(\'APO-THIORIDAZINE TAB 25MG\');">APO-THIORIDAZINE TAB 25MG</li><li onClick="fill(\'APO-THIORIDAZINE TAB 25MG\');">APO-THIORIDAZINE TAB 25MG</li><li onClick="fill(\'APO-TRIFLUOPERAZ TAB 1MG\');">APO-TRIFLUOPERAZ TAB 1MG</li><li onClick="fill(\'APO-TRIFLUOPERAZ TAB 5MG\');">APO-TRIFLUOPERAZ TAB 5MG</li><li onClick="fill(\'APO-TRIFLUOPERAZ TAB 5MG\');">APO-TRIFLUOPERAZ TAB 5MG</li><li onClick="fill(\'HYDRALAZINE-ALP TAB 25MG\');">HYDRALAZINE-ALP TAB 25MG</li><li onClick="fill(\'APO-HYDRALAZINE TAB 50MG\');">APO-HYDRALAZINE TAB 50MG</li><li onClick="fill\'\'APO-THIORIDAZINE TAB 50MG\');">APO-THIORIDAZINE TAB 50MG</li><li onClick="fill(\'APO-THIORIDAZINE TAB100MG\');">APO-THIORIDAZINE TAB100MG</li><li onClick="fill(\'SULFADIAZINE-CPP TAB500MG\');">SULFADIAZINE-CPP TAB500MG</li><li onClick="fill(\'PROCARBAZINE-CMB CAP 50MG\');">PROCARBAZINE-CMB CAP 50MG</li><li onClick="fill(\'HYDRALAZINE-COX TAB 25MG\');">HYDRALAZINE-COX TAB 25MG</li><li onClick="fill(\'PLEGOMAZINE TAB 100MG\');">PLEGOMAZINE TAB 100MG</li><li onClick="fill(\'HYDRALAZINE-EVS TAB 25MG\');">HYDRALAZINE-EVS TAB 25MG</li><li onClick="fill(\'ZINNAT SUS 250/5ML\');">ZINNAT SUS 250/5ML</li><li onClick="fill(\'ZINACEF INJ 750MG\');">ZINACEF INJ 750MG</li><li onClick="fill(\'ZINACEF INJ 1.5GM\');">ZINACEF INJ 1.5GM</li><li onClick="fill(\'ZINNAT SUS 125/5M\'\');">ZINNAT SUS 125/5ML</li><li onClick="fill(\'ZINNAT SUS 125/5M\'\');">ZINNAT SUS 125/5ML</li><li onClick="fill(\'ZINNAT SUS 125/5M\'\');">ZINNAT SUS 125/5ML</li><li onClick="fill(\'ZINNAT SUS 250/5ML\');">ZINNAT SUS 250/5ML</li><li onClick="fill(\'ZINNAT TAB 250MG\');">ZINNAT TAB 250MG</li><li onClick="fill(\'ZINNAT TAB 500MG\');">ZINNAT TAB 500MG</li><li onClick="fill(\'HYDRALAZINE-HLS TAB 25MG\');">HYDRALAZINE-HLS TAB 25MG</li><li onClick="fill(\'DACARBAZINE-MDC  INJ100MG\');">DACARBAZINE-MDC  INJ100MG</li><li onClick="fill(\'DACARBAZINE-MDC  INJ200MG\');">DACARBAZINE-MDC  INJ200MG</li><li onClick="fill(\'NOVO-PROMAZINE TAB 25MG\');">NOVO-PROMAZINE TAB 25MG</li><li onClick="fill(\'NOVO-PROMAZINE TAB 100MG\');">NOVO-PROMAZINE TAB 100MG</li><li onClick="fill(\'NOVO-RIDAZINE TAB 25MG\');">NOVO-RIDAZINE TAB 25MG</li><li onClick="fill(\'NOVO-RIDAZINE TAB 50MG\');">NOVO-RIDAZINE TAB 50MG</li><li onClick="fill(\'NOVO-RIDAZINE TAB 100MG\');">NOVO-RIDAZINE TAB 100MG</li><li onClick="fill(\'PYRAZINAMIDE-PMS TAB500MG\');">PYRAZINAMIDE-PMS TAB500MG</li><li onClick="fill(\'ZINDOLIN TAB 500MG\');">ZINDOLIN TAB 500MG</li><li onClick="fill(\'FLAMAZINE CRE 1%\');">FLAMAZINE CRE 1%</li><li onClick="fill(\'FLAMAZINE CRE 1%\');">FLAMAZINE CRE 1%</li><li onClick="fill(\'FLAMAZINE CRE 1%\');">FLAMAZINE CRE 1%</li><li onClick="fill(\'STELAZINE TAB 1MG\');">STELAZINE TAB 1MG</li><li onClick="fill(\'STELAZINE TAB 5MG\');">STELAZINE TAB 5MG</li><li onClick="fill(\'RIDAZIN-TARO TAB 10M\'\');">RIDAZIN-TARO TAB 10MG</li><li onClick="fill(\'RIDAZIN-TARO TAB 25M\'\');">RIDAZIN-TARO TAB 25MG</li><li onClick="fill(\'RIDAZIN-TARO TAB 25MG\');">RIDAZIN-TARO TAB 25MG</li><li onClick="fill(\'RIDAZIN-TARO TAB 100MG\');">RIDAZIN-TARO TAB 100MG</li><li onClick="fill(\'RIDAZIN-TARO TAB 100MG\');">RIDAZIN-TARO TAB 100MG</li><li onClick="fill(\'SULFADIAZINE-WH TAB 500MG\');">SULFADIAZINE-WH TAB 500MG</li>';

            preg_match_all("/(?<=>)[a-zA-Z0-9_\-\s\/.%]*(?=<)/i", $response, $matches);

            $items = [];
            $names = [];
            foreach($matches[0] as $item) {
                $item = trim($item);
                if (!empty($item) && !in_array($item, $names)) {
                    $names[] = $item;
                    $items[] = ['name' => $item];
                }
            }

            return $this->successResponse(['products' => $items]);
        }
    }
}
