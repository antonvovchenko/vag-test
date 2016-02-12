<?php

namespace App\Library;

use App\Models\Settings;

class TransactionService
{
    protected static $_instance;

    public $provider;

    /***********CARD*************/
    static public $insurance_providers = [
        'Sagicor',
        'N.H.F.',
        'Medecus',
        'Clico'
    ];
    public $card_bin_number_values_dev = [
        'BCJ' => '000030',
        'NHF' => '000020',
        'EBA' => '000030',
        'FLB' => '000030',
        'CLK' => '000030',
        'MED' => '000090'
    ];
    public $card_provider_numbers_dev = [
        'BCJ' =>        'AX123456',
        'LOJ' =>        'AX123456',
        'EBA' =>        'AX123456',
        'MED' =>        'AX123456',
        'MEDECUS' =>    'AX123456',
        'JADEP' =>      'AX123456',
        'NHF' =>        'AX123456',
        'CLK' =>        'AX123456'
    ];

    public $card_track1_settings = [
        'delimiters' => [
            '%',
            '?'
        ],
        'parts' => [
            [ 'name' => 'bin_number',           'length' => 3, 'start' => 1, 'end' => 3 ],
            [ 'name' => 'group_number',         'length' => 14, 'start' => 5, 'end' => 18],
            [ 'name' => 'subscriber_number',    'length' => 17, 'start' => 20, 'end' => 36 ],
            [ 'name' => 'first_name',           'length' => 20, 'start' => 37, 'end' => 56 ],
            [ 'name' => 'last_name',            'length' => 20, 'start' => 57, 'end' => 76 ]
        ]
    ];
    public $card_track2_settings = [
        'delimiters' => [
            ';',
            '?'
        ],
        'parts' => [
            [ 'name' => 'person_code',          'length' => 3, 'start' => 1, 'end' => 3 ],
            [ 'name' => 'relationship_code',    'length' => 1, 'start' => 4, 'end' => 4 ],
            [ 'name' => 'sex_code',             'length' => 1, 'start' => 5, 'end' => 5 ],
            [ 'name' => 'date_of_birth',        'length' => 8, 'start' => 6, 'end' => 13],
            [ 'name' => 'effective_date',       'length' => 8, 'start' => 14, 'end' => 21 ],
            [ 'name' => 'expiry_date',          'length' => 8, 'start' => 22, 'end' => 29 ],
            [ 'name' => 'card_id',              'length' => 2, 'start' => 30, 'end' => 31 ],
            [ 'name' => 'version_number',       'length' => 2, 'start' => 32, 'end' => 33 ]
        ]
    ];

    public function __construct()
    {
        $this->provider = $this->getProvider();

        foreach ($this->card_provider_numbers_dev as $name=>$value) {
            if ($option = Settings::where('name', $name)->first()) {
                $this->card_provider_numbers[$name] = $option->value;
            } else {
                $this->card_provider_numbers[$name] = $value;
            }
        }

        foreach ($this->card_bin_number_values_dev as $name=>$value) {
            if ($option = Settings::where('name', $name)->first()) {
                $this->card_bin_number_values[$name] = $option->value;
            } else {
                $this->card_bin_number_values[$name] = $value;
            }
        }

    }

    private function __clone(){

    }

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getProvider()
    {
        return new AisProvider($this);
    }

    public function parseCardInformation($cardInformation)
    {
//        %BCJ TEST           123456789         AIS FIRST NAME      AIS LAST NAME      ?;000111987010120120101999912314701?
//        %BCJ TEST           123456789         AIS FIRST NAME      AIS LAST NAME      ?;001221986020220120101999912314702?
//        %BCJ TEST           123456789         AIS FIRST NAME      AIS LAST NAME      ?;002312010030320100101999912314700?
//        %NHF AISTEST        111222333         AIS FIRST NAME      AIS LAST NAME      ?;001111978040420130101999912314703?
//        %NHF AISTEST        111222444         AIS FIRST NAME      AIS LAST NAME      ?;001121984050520100101999912314701?
//        %MED TEST           123456789         AIS FIRST NAME      AIS LAST NAME      ?;000111987010120120101999912314701?

        $cardInformationData = [
            'card_data_string' => $cardInformation
        ];

        $track1 = mb_strcut(
            $cardInformation,
            mb_strpos($cardInformation, $this->card_track1_settings['delimiters'][0])+1,
            mb_strpos($cardInformation, $this->card_track1_settings['delimiters'][1])-1-mb_strpos($cardInformation, $this->card_track1_settings['delimiters'][0])
        );

        $track2 = mb_strcut(
            $cardInformation,
            mb_strrpos($cardInformation, $this->card_track2_settings['delimiters'][0])+1,
            mb_strpos($cardInformation, $this->card_track2_settings['delimiters'][1])-mb_strpos($cardInformation, $this->card_track2_settings['delimiters'][0])
        );

        if ($track1 && $track2) {
            foreach ($this->card_track1_settings['parts'] as $part) {
                $cardInformationData[$part['name']] = trim(mb_strcut(
                    $track1,
                    $part['start']-1,
                    $part['length']
                ));
            }
            foreach ($this->card_track2_settings['parts'] as $part) {
                $cardInformationData[$part['name']] = trim(mb_strcut(
                    $track2,
                    $part['start']-1,
                    $part['length']
                ));
            }

            //set other values
            //insurance_provider
            $cardInformationData['insurance_provider'] = $this->getInsuranceProviderByBinNumber($cardInformationData['bin_number']);

            //bin_number
            switch ($cardInformationData['bin_number']) {
                case 'BCJ':
                    $cardInformationData['bin_number_value'] = $this->card_bin_number_values['BCJ'];
                    break;
                case 'NHF':
                case 'NHT':
                case 'NH':
                    $cardInformationData['bin_number_value'] = $this->card_bin_number_values['NHF'];
                    break;
                case 'CLK':
                case 'CL':
                    $cardInformationData['bin_number_value'] = $this->card_bin_number_values['CLK'];
                    break;
                case 'EBA':
                    $cardInformationData['bin_number_value'] = $this->card_bin_number_values['EBA'];
                    break;
                case '^FL':
                case '6FL':
                    $cardInformationData['bin_number_value'] = $this->card_bin_number_values['FLB'];
                    break;
                case 'MED':
                case 'GLL':
                case 'HME':
                    $cardInformationData['bin_number_value'] = $this->card_bin_number_values['MED'];
                    break;

                default:
                    $cardInformationData['bin_number_value'] = '';
                    break;
            }

            //provider_number
            switch ($cardInformationData['bin_number']) {
                case 'BCJ':
                    $cardInformationData['provider_number'] = $this->card_provider_numbers['BCJ'];
                    break;
                case 'CLK':
                case 'CL':
                    $cardInformationData['provider_number'] = $this->card_provider_numbers['CLK'];
                    break;
                case 'NHF':
                case 'NHT':
                case 'NH':
                    $cardInformationData['provider_number'] = $this->card_provider_numbers['NHF'];
                    break;
                case 'EBA':
                case '^FL':
                case '6FL':
                    $cardInformationData['provider_number'] = $this->card_provider_numbers['LOJ'];
                    break;
                case 'MED':
                    $cardInformationData['provider_number'] = $this->card_provider_numbers['MED'];
                    break;

                case 'MED':
                    $cardInformationData['provider_number'] = $this->card_provider_numbers['MED'];
                    break;

                default:
                    $cardInformationData['provider_number'] = '';
                    break;
            }

            return $cardInformationData;
        }
    }

    public function adjudicateRequest($prescription)
    {
        return $this->provider->adjudicateRequest($prescription);
    }

    public function reversalRequest($transactionId)
    {
        return $this->provider->reversalRequest($transactionId);
    }

    public function parseAdjudicateRequest($request)
    {
        return $this->provider->parseAdjudicateRequest($request);
    }

    public function parseReversalRequest($request)
    {
        return $this->provider->parseReversalRequest($request);
    }

    public function parseResponse($response, $type)
    {
        return $this->provider->parseResponse($response, $type);
    }

    static public function getInsuranceProviderByBinNumber($bin_number)
    {
        $insurance_provider = '';
        switch ($bin_number) {
            case 'BCJ':
            case 'EBA':
            case '^FL':
            case '6FL':
                $insurance_provider = 'Sagicor';
                break;

            case 'NHF':
            case 'NHT':
            case 'NH':
                $insurance_provider = 'N.H.F.';
                break;

            case 'MED':
            case 'GLL':
            case 'HME':
                $insurance_provider = 'Medecus';
                break;

            case 'CLK':
            case 'CL':
                $insurance_provider = 'Clico';
                break;
        }
        return $insurance_provider;
    }
}
