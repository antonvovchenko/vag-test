<?php

namespace App\Library;

use App\Models\Fee;
use App\Models\Inventory;
use App\Models\LabelCode;
use App\Models\Patient;
use App\Models\Prescriber;
use App\Models\Product;
use App\Models\ProductLabelCode;
use App\Models\ProductMarkupType;
use App\Models\Supplier;

class ImportDataService
{
    public function parseDataFromCSV($file)
    {
        $importer = new CsvImporter($file, true);
        $output = [];
        while ($data = $importer->get(0)) {
            $output = $data;
        }

        return $output;
    }

    public function checkData($type, $data, $matches)
    {
        $output = [];
        if (!empty($data)) {
            foreach ($data as $k=>$item) {
                foreach ($matches as $field_name=>$match) {
                    $output[$k][$field_name] = $item[$match];
                }
                switch ($type) {
                    case 'products':
                        $itemDB = Product::where('title', $output[$k]['title'])->first();
                        $output[$k]['status_message'] = $itemDB ? 'Already exist' : 'New item';
                        $output[$k]['status_item'] = $itemDB ? 'error' : 'success';
                        break;
                    case 'inventory':
                        $product = Product::where('title', 'like', '%'.$output[$k]['product_title'].'%')->first();
                        $output[$k]['status_message'] = !$product ? 'Product not found' : 'New item';
                        $output[$k]['status_item'] = !$product ? 'error' : 'success';
                        break;
                    case 'patients':
                        $patient = Patient::where('name', $output[$k]['name'])->first();
                        $output[$k]['status_message'] = $patient ? 'Already exist' : 'New item';
                        $output[$k]['status_item'] = $patient ? 'error' : 'success';
                        break;
                    case 'doctors':
                        $doctor = Prescriber::where('registration_number', $output[$k]['registration_number'])->first();
                        $output[$k]['status_message'] = $doctor ? 'Already exist' : 'New item';
                        $output[$k]['status_item'] = $doctor ? 'error' : 'success';
                        break;
                    case 'categories':
                        $category = ProductMarkupType::where('title', $output[$k]['title'])->first();
                        $output[$k]['status_message'] = $category ? 'Already exist' : 'New item';
                        $output[$k]['status_item'] = $category ? 'error' : 'success';
                        break;
                    case 'label_codes':
                        $label_code = LabelCode::where('title', $output[$k]['title'])->first();
                        $output[$k]['status_message'] = $label_code ? 'Already exist' : 'New item';
                        $output[$k]['status_item'] = $label_code ? 'error' : 'success';
                        break;
                    case 'fees':
                        $fee = Fee::where('card_type', $output[$k]['card_type'])->first();
                        $output[$k]['status_message'] = $fee ? 'Already exist' : 'New item';
                        $output[$k]['status_item'] = $fee ? 'error' : 'success';
                        break;
                    case 'suppliers':
                        $supplier = Supplier::where('name', $output[$k]['name'])->first();
                        $output[$k]['status_message'] = $supplier ? 'Already exist' : 'New item';
                        $output[$k]['status_item'] = $supplier ? 'error' : 'success';
                        break;
                }
            }
        }
        return $output;
    }

    public function importData($type, $data)
    {
        //categories
        if ($type == 'categories' && isset($data['categories'])) {
            foreach ($data['categories'] as $category) {
                if (isset($category['title']) && $category['status_item'] != 'error') {
                    ProductMarkupType::create($category);
                }
            }
        }
        //label_codes
        if ($type == 'label_codes' && isset($data['label_codes'])) {
            foreach ($data['label_codes'] as $label_code) {
                if (isset($label_code['title']) && $label_code['status_item'] != 'error') {
                    LabelCode::create($label_code);
                }
            }
        }
        //fees
        if ($type == 'fees' && isset($data['fees'])) {
            foreach ($data['fees'] as $fee) {
                if (isset($fee['card_type']) && $fee['status_item'] != 'error') {
                    Fee::create($fee);
                }
            }
        }
        //suppliers
        if ($type == 'suppliers' && isset($data['suppliers'])) {
            foreach ($data['suppliers'] as $supplier) {
                if (isset($supplier['name']) && $supplier['status_item'] != 'error') {
                    Supplier::create($supplier);
                }
            }
        }
        //products
        if ($type == 'products' && isset($data['products'])) {
            foreach ($data['products'] as $product) {
                if (isset($product['title']) && $product['status_item'] != 'error') {
                    //find category
                    $product_markup_type = ProductMarkupType::where('title', $product['markup_type'])->first();
                    //or create new
                    if (!$product_markup_type) {
                        $product_markup_type = ProductMarkupType::create(['title' => $product['markup_type']]);
                    }
                    $product['markup_type_id'] = $product_markup_type->id;

                    //save
                    if ($newProduct = Product::create($product)) {
                        //add label codes
                        if (isset($product['label_codes']) && !empty($product['label_codes'])) {
                            $product['label_codes'] = explode(',', $product['label_codes']);
                            $labelCodes = [];
                            foreach ($product['label_codes'] as $labelCodeLabel) {
                                //find label code
                                $labelCode = LabelCode::where('title', $labelCodeLabel)->first();
                                //or create new
                                if (!$labelCode) {
                                    $labelCode = LabelCode::create(['title' => $labelCodeLabel]);
                                }
                                $labelCodes[] = new ProductLabelCode(['label_code_id' => $labelCode->id]);
                            }
                            $newProduct->label_codes()->saveMany($labelCodes);
                        }
                    }
                }
            }
        }
        //patients
        if ($type == 'patients' && isset($data['patients'])) {
            foreach ($data['patients'] as $patient) {
                if (isset($patient['name']) && $patient['status_item'] != 'error') {
                    Patient::create($patient);
                }
            }
        }
        //doctors
        if ($type == 'doctors' && isset($data['doctors'])) {
            foreach ($data['doctors'] as $doctor) {
                if (isset($doctor['first_name']) && isset($doctor['last_name']) && isset($doctor['registration_number']) && $doctor['status_item'] != 'error') {
                    Prescriber::create($doctor);
                }
            }
        }
        //inventory
        if ($type == 'inventory' && isset($data['inventory'])) {
            foreach ($data['inventory'] as $inventory) {
                if (isset($inventory['product_title']) && isset($inventory['quantity']) && isset($inventory['expiry_date']) && $inventory['status_item'] != 'error') {
                    $product = Product::where('title', 'like', '%'.$inventory['product_title'].'%')->first();
                    $supplier = Supplier::where('name', 'like', '%'.$inventory['supplier'].'%')->first();
                    if ($product) {
                        Inventory::create([
                            'product_id' => $product->id,
                            'supplier_id' => $supplier ? $supplier->id : null,
                            'quantity_on' => $inventory['quantity'],
                            'reason' => $inventory['reason'],
                            'expiry_date' => date('Y-m-d H:i:s', strtotime($inventory['expiry_date'])),
                            'type' => Inventory::TYPE_IN
                        ]);
                    }
                }
            }
        }

        return true;
    }

}
