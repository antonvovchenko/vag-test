<?php

namespace App\Library;

use App\Models\Settings;
use App\Models\PrescriptionItem;
use App\Models\TransactionRequest;

class AisProvider
{
    protected $ip = '208.131.187.223';
    protected $port = '1678';
    protected $timeout = 15;  //sec
    public $default_additional_query_part = 'BLD SOLUTION';
    public $transaction_service;

    /***********QUERY*************/

    public $reject_codes = [
        '01' => [
            'description' => 'M/I BIN',
            'explanation' => 'Requisite Bin Number not sent in transmission string.'
        ],
        '02' => [
            'description' => 'M/I VERSION NUMBER',
            'explanation' => 'Requisite Version Number not sent in transmission string.'
        ],
        '03' => [
            'description' => 'M/I TRANSACTION CODE',
            'explanation' => 'Requisite Transaction Code not sent in transmission string.'
        ],
        '04' => [
            'description' => 'M/I PROCESSOR CONTROL NUMBER',
            'explanation' => 'Requisite Processor Control Number not sent in transmission string.'
        ],
        '05' => [
            'description' => 'M/I PHARMACY NUMBER',
            'explanation' => 'Requisite Provider Number: (a) has been excluded from inbound transaction; (b) the number is not recognised on the main database; (c) Provider details have not been created on the main database.'
        ],
        '06' => [
            'description' => 'M/I GROUP NUMBER',
            'explanation' => 'Requisite Group Number: (a) Possible data load/entry entry or card encoding corruption. (b) Does not necessarily mean group is not on application, check the strategies tab on the Customer/Client/Group record to ensure that a plan exists and contains a valid coverage date.'
        ],
        '07' => [
            'description' => 'M/I CARDHOLDER ID NUMBER',
            'explanation' => 'Requisite Cardholder ID: (a) Has been excluded from inbound transaction; (b) The number is not recognised on the main database; (c) Cardholder details have not been created on the main database; (d) Possible data entry error or card encoding corruption.'
        ],
        '08' => [
            'description' => 'M/I PERSON CODE',
            'explanation' => 'Requisite Cardholder Person Reference: (a) Has been excluded from inbound transaction; (b) The number is not recognised on the main database; (c) Cardholder details have not been created on the main database.'
        ],
        '09' => [
            'description' => 'M/I BIRTHDATE',
            'explanation' => 'Requisite Date of Birth: (a) Has been excluded from inbound transaction; (b) The number is not recognised on the main database; (c) Cardholder details have not been created on the main database; (d) Possible data entry error or card encoding corruption.'
        ],
        '10' => [
            'description' => 'M/I SEX CODE',
            'explanation' => 'Requisite Sex Code 1=Male, 2=Female: (a) Has been excluded from inbound transaction; (b) The number is not recognised on the main database; (c) Cardholder details have not been created on the main database; (d) Possible data entry error or card encoding corruption.'
        ],
        '11' => [
            'description' => 'M/I RELATIONSHIP CODE',
            'explanation' => 'Requisite Relationship to Subscriber: (a) Has been excluded from inbound transaction; (b) The number is not recognised on the main database; (c) Cardholder details have not been created on the main database; (d) Possible data entry error or card encoding corruption.'
        ],
        '13' => [
            'description' => 'M/I OTHER COVERAGE CODE',
            'explanation' => 'Refers to Coordinated Benefit. An identification code for Primary or Secondary Insurer is either missing or invalid.'
        ],
        '15' => [
            'description' => 'M/I DATE FILLED/DATE OF SERVICE',
            'explanation' => '(a) The Service Date referenced on the inbound claim is too old. (b) Invalid values possibly inserted into date fields.'
        ],
        '16' => [
            'description' => 'M/I PRESCRIPTION NUMBER',
            'explanation' => 'Invoice/Prescription/Sheet Reference is missing.'
        ],
        '18' => [
            'description' => 'M/I METRIC QUANTITY',
            'explanation' => "1. Inappropriate quantity or no quantity has been provided: (a) NonPharmacy - default should always be '1'; (b) Hospital Services - services that are paid per diem should be appropriately represented: (c) Pharmacy - represents the number of items (tablets, tubes, viles etc.) on script. 2. Benefit being claimed has exceeded the stipulated maximum."
        ],
        '19' => [
            'description' => 'M/I DAYS SUPPLY',
            'explanation' => 'Pharmacy - Duration for course of treatment has not been stated.'
        ],
        '20' => [
            'description' => 'Cardholder not on file',
            'explanation' => 'Requisite Cardholder ID: (a) The number is not recognised on the main database; (b) Cardholder details have not been created on the main database; (c) Possible data entry error or card encoding corruption.'
        ],
        '21' => [
            'description' => 'M/I NDC NUMBER',
            'explanation' => '1. POS - Drug claim has been submitted without recognised drug code. 2. Drug code has not been harmonised with main database. NDC = National Drug Code'
        ],
        '22' => [
            'description' => 'M/I DISPENSE AS WRITTEN CODE',
            'explanation' => 'Pharmacy requirement indicating whether the Precriber has issued a preferense for drugs to be "Dispensed As Written". The associated default value is \'0\'.'
        ],
        '23' => [
            'description' => 'M/I INGREDIENT COST',
            'explanation' => 'Primarily makes reference to a missing Charge for service.'
        ],
        '25' => [
            'description' => 'M/I PRESCRIBE ID',
            'explanation' => 'Message generally refers to the Provider rather than Prescriber. (a) Provider number may not be recognised on the main database. (b) Provider record may be stored in an inactive state.'
        ],
        '30' => [
            'description' => 'M/I P.A./M.C. CODE AND NUMBER',
            'explanation' => 'Refers to Prior Authorisation or MAC Codes. Where special payment arrangements have been agreed between Provider and Carrier, the Carrier will issue an authorisation number that should be included in the associated claim transaction.'
        ],
        '39' => [
            'description' => 'M/I DIAGNOSIS CODE',
            'explanation' => 'Applies to non-pharmacy claims. The Requisite diagnosis code is either missing or invalid.'
        ],
        '40' => [
            'description' => 'PHARMACY NOT CONTRACTED WITH PLAN ON DATE OF SERVICE',
            'explanation' => 'The Provider is claiming for service before a contractual arrangement between Provider and Carrier comes into full effect. Provider contract may have been temporarily suspended or terminated.'
        ],
        '41' => [
            'description' => 'SUBMIT BILL TO OTHER PROCESSOR OR PRIMARY PAYOR',
            'explanation' => 'Directs Provider to obtain details of Primary Carrier. Further cover under Coordinated Benefits will require manual submittal with relevant documentation.'
        ],
        '50' => [
            'description' => 'NON-MATCHED PHARMACY NUMBER',
            'explanation' => 'Pharmacy Number not on file or not in network'
        ],
        '51' => [
            'description' => 'NON-MATCHED GROUP NUMBER',
            'explanation' => 'See 06 above or Database Issue Response'
        ],
        '52' => [
            'description' => 'NON-MATCHED CARDHOLDER ID',
            'explanation' => "Cardholder's id not in the database"
        ],
        '53' => [
            'description' => 'NON-MATCHED PERSON CODE',
            'explanation' => 'Invalid person code for member id'
        ],
        '54' => [
            'description' => 'NON-MATCHED NDC NUMBER',
            'explanation' => 'NDC number not in file'
        ],
        '56' => [
            'description' => 'NON-MATCHED PRESCRIBER ID',
            'explanation' => 'Prescriber is not on file'
        ],
        '57' => [
            'description' => 'NON-MATCHED P.A./M.C. NUMBER',
            'explanation' => 'Prior Authorisation not on file'
        ],
        '58' => [
            'description' => 'NON-MATCHED PRIMARY PRESCRIBER',
            'explanation' => 'Primary Prescriber not on file'
        ],
        '59' => [
            'description' => 'NON-MATCHED CLINIC ID',
            'explanation' => 'Referenced Clinic is not on file'
        ],
        '60' => [
            'description' => 'DRUG NOT COVERED FOR PATIENT AGE',
            'explanation' => 'An age restriction has been imposed for requested drug.'
        ],
        '61' => [
            'description' => 'DRUG NOT COVERED FOR PATIENT GENDER',
            'explanation' => 'A drug exclusion on the basis of Cardholder\'s gender.'
        ],
        '64' => [
            'description' => 'CLAIM SUBMITTED DOES NOT MATCH PRIOR AUTHORIZATION',
            'explanation' => 'An invalid Prior Authorisation code has been quoted on the submitted claim.'
        ],
        '65' => [
            'description' => 'PATIENT IS NOT COVERED',
            'explanation' => 'Patient/Denendent: (a) Is not be eligible for requested service; (b) Inbound detail is not present on the main database.'
        ],
        '66' => [
            'description' => 'PATIENT AGE EXCEEDS MAXIMUM AGE',
            'explanation' => 'An age limitation exists for the requested service.'
        ],
        '67' => [
            'description' => 'FILLED BEFORE COVERAGE EFFECTIVE',
            'explanation' => 'Cardholder has presented a card before the policy\'s effective date.'
        ],
        '68' => [
            'description' => 'FILLED AFTER COVERAGE EXPIRED',
            'explanation' => 'Cardholder\'s cover has expired.'
        ],
        '69' => [
            'description' => 'FILLED AFTER COVERAGE TERMINATED',
            'explanation' => 'Cardholder\'s cover has been terminated.'
        ],
        '70' => [
            'description' => 'NDC NOT COVERED',
            'explanation' => 'Exclusion. The requested drug is not covered by Insurance Carrier.'
        ],
        '71' => [
            'description' => 'PRESCRIBER IS NOT COVERED',
            'explanation' => 'Exclusion. Requested service only applies to authorised Prescribers.'
        ],
        '72' => [
            'description' => 'PRIMARY PRESCRIBER IS NOT COVERED',
            'explanation' => 'Carrier Set-Up'
        ],
        '75' => [
            'description' => 'PRIOR AUTHORIZATION REQUIRED',
            'explanation' => 'Benefit exclusion requirement'
        ],
        '76' => [
            'description' => 'PLAN LIMITATIONS EXCEEDED',
            'explanation' => 'Inbound claim has either reached or exceeded allowable benefit limits.'
        ],
        '78' => [
            'description' => 'COST EXCEEDS MAXIMUM',
            'explanation' => 'Carrier Set-Up'
        ],
        '81' => [
            'description' => 'CLAIM TOO OLD',
            'explanation' => 'The inbound transaction has been submitted beyond the stipulated 90 day claim period'
        ],
        '82' => [
            'description' => 'CLAIM IS POST-DATED',
            'explanation' => 'Carrier Set-Up'
        ],
        '83' => [
            'description' => 'DUPLICATE PAID/CAPTURED CLAIM',
            'explanation' => 'Claim details have been entered twice'
        ],
        '84' => [
            'description' => 'CLAIM HAS NOT BEEN PAID/CAPTURED',
            'explanation' => 'Processing problem, could be database issue - reprocess claim'
        ],
        '85' => [
            'description' => 'CLAIM NOT PROCESSED',
            'explanation' => 'Processing problem, could be database issue - reprocess claim'
        ],
        '87' => [
            'description' => 'REVERSAL NOT PROCESSED',
            'explanation' => 'Request to reverse a claim did not go through - resubmit.'
        ],
        '90' => [
            'description' => 'HOST HUNG UP',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        '91' => [
            'description' => 'HOST RESPONSE ERROR',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        '92' => [
            'description' => 'SYSTEM UNAVAILABLE/HOST UNAVAILABLE',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        '95' => [
            'description' => 'TIME OUT',
            'explanation' => 'The time allocated to make a connection to the database has been exceeded.'
        ],
        '98' => [
            'description' => 'CONNECTION TO PAYOR IS DOWN',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        '99' => [
            'description' => 'HOST PROCESSING ERROR',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        'CR' => [
            'description' => 'CARRIER ID NUMBER',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        'E7' => [
            'description' => 'METRIC DECIMAL QUANTITY',
            'explanation' => "1. Inappropriate quantity or no quantity has been provided: (a) NonPharmacy - default should always be '1'; (b) Hospital Services - services that are paid per diem should be appropriately represented: (c) Pharmacy - represents the number of items (tablets, tubes, viles etc.) on script. 2. Benefit being claimed has exceeded the stipulated maximum."
        ],
        'M3' => [
            'description' => 'HOST PA/MC ERROR',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        'M6' => [
            'description' => 'HOST ELIGIBILITY ERROR',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        'M7' => [
            'description' => 'HOST DRUG FILE ERROR',
            'explanation' => 'Database Issue Response - contact AIS. Check missing price schedule/strategy'
        ],
        'M8' => [
            'description' => 'HOST PROVIDER FILE ERROR',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],
        'MZ' => [
            'description' => 'ERROR OVERFLOW',
            'explanation' => 'Database Issue Response - contact AIS.'
        ],

        '79' => [
            'description' => 'Plan limitation exceeded.',
            'explanation' => 'Plan limitation exceeded.'
        ]
    ];

    public $adjudicate_query_settings = [
        'parts' => [
            //user information
            'required1' => [
                [ 'name' => 'bin_number_value',                     'length' => 6, 'type' => 'numeric' ],          //bin_number
                [ 'name' => 'version_release_number',               'length' => 2, 'default' => '3A' ],
                [ 'name' => 'transaction_code',                     'length' => 2, 'default' => '01' ],
                [ 'name' => 'processor_control_number',             'length' => 10, 'default' => 'IDS' ],
                [ 'name' => 'provider_number',                      'length' => 12 ],
                [ 'name' => 'group_number',                         'length' => 15 ],
                [ 'name' => 'subscriber_number',                    'length' => 18 ],
                [ 'name' => 'person_code',                          'length' => 3, 'type' => 'numeric' ],
                [ 'name' => 'date_of_birth',                        'length' => 8 ],
                [ 'name' => 'sex_code',                             'length' => 1 ],
                [ 'name' => 'relationship_code',                    'length' => 1 ],
                [ 'name' => 'other_coverage_code',                  'length' => 1, 'default' => '0' ],
                [ 'name' => 'date_filled',                          'length' => 8, 'default' => 'current_date' ]
            ],
            'optional1' => [],
            //prescription information
            'required2' => [
                [ 'name' => 'prescription_id',                      'length' => 7, 'type' => 'numeric' ],           //prescription_number
                [ 'name' => 'new_refill_code',                      'length' => 2, 'default' => '00', 'type' => 'numeric' ],
                [ 'name' => 'qty',                                  'length' => 5, 'type' => 'numeric' ],           //metric_quantity
                [ 'name' => 'course_days',                          'length' => 3, 'type' => 'numeric' ],           //days_supply
                [ 'name' => 'compound_code',                        'length' => 1, 'default' => '0' ],
                [ 'name' => 'ndc',                                  'length' => 11 ],
                [ 'name' => 'dispense_as_written',                  'length' => 1, 'default' => '1' ],
                [ 'name' => 'ingredient_cost',                      'length' => 12, 'type' => 'numeric' ],          //todo: remove default after discussion
                [ 'name' => 'registration_number',                  'length' => 10 ],                               //prescriber_id
                [ 'name' => 'date_prescription_written',            'length' => 8, 'default' => 'current_date' ],
                [ 'name' => 'refill_allowed_times',                 'length' => 2, 'type' => 'numeric' ],                                //number_refills_authorized
                [ 'name' => 'prescription_origin_code',             'length' => 1, 'default' => '1' ],
                [ 'name' => 'prescription_denial_clarification',    'length' => 2, 'default' => '00' ],
                [ 'name' => 'usual_customary_charge',               'length' => 12, 'default' => '000000000000' ]
            ],
            'optional2' => [
                [ 'name' => 'level_of_service',                     'length' => 4, 'default' => 'DI01' ],
                [ 'name' => 'procedure_code',                       'length' => 12, 'default' => 'G1200' ],
                [ 'name' => 'service_type',                         'length' => 4, 'default' => 'G2X' ],
                [ 'name' => 'service_modifier',                     'length' => 4, 'default' => 'G310' ],
                [ 'name' => 'cause_of_loss_code',                   'length' => 8, 'default' => 'G4E794' ]
            ]
        ]
    ];

    public $adjudicate_response_settings = [
        'parts' => [
            'success' => [
                [ 'name' => 'version_release_number',               'length' => 2 ],
                [ 'name' => 'transaction_code',                     'length' => 2 ],
                [ 'name' => 'response_status_header',               'length' => 1 ],

                [ 'name' => 'field_separator'],

                [ 'name' => 'plan_identification',                   'length' => 15 ],

                [ 'name' => 'group_separator'],

                [ 'name' => 'response_status_prescription',          'length' => 1 ],
                [ 'name' => 'patient_pay_amount',                    'length' => 12, 'type' => 'signed_overpunch' ],
                [ 'name' => 'ingredient_cost_paid',                  'length' => 12, 'type' => 'signed_overpunch' ],
                [ 'name' => 'contract_fee_paid',                     'length' => 12, 'type' => 'signed_overpunch' ],
                [ 'name' => 'sales_tax_paid',                        'length' => 12, 'type' => 'signed_overpunch' ],
                [ 'name' => 'total_amount_paid',                     'length' => 12, 'type' => 'signed_overpunch' ],
                [ 'name' => 'authorization_number',                  'length' => 14 ],
                [ 'name' => 'message',                               'length' => 40 ],

                //optional
                [ 'name' => 'field_separator'],
                [ 'name' => 'accumulated_deductible_amount',          'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'remaining_deductible_amount',            'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'remaining_benefit_amount',               'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'amt_applied_to_periodic_deduct',         'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'amount_of_copay_coinsurance',            'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'amt_attrib_to_prod_selection',           'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'amt_exceed_periodic_benefit_max',        'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'incentive_fee_paid',                     'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'basis_of_reimb_determination',           'length' => 2],
                [ 'name' => 'field_separator'],
                [ 'name' => 'amount_attributed_to_sales_tax',         'length' => 12],
                [ 'name' => 'field_separator'],
                [ 'name' => 'dur_response_data',                      'length' => 160],
                [ 'name' => 'field_separator'],
                [ 'name' => 'additional_message_information',         'length' => 80],

            ],
            'error' => [
                [ 'name' => 'version_release_number',               'length' => 2 ],
                [ 'name' => 'transaction_code',                     'length' => 2 ],
                [ 'name' => 'response_status_header',               'length' => 1 ],

                [ 'name' => 'field_separator'],

                [ 'name' => 'plan_identification',                   'length' => 15 ],

                [ 'name' => 'group_separator'],

                [ 'name' => 'response_status_prescription',          'length' => 1 ],
                [ 'name' => 'reject_count',                          'length' => 2 ],
                [ 'name' => 'reject_code_01',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_02',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_03',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_04',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_05',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_06',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_07',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_08',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_09',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_10',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_11',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_12',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_13',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_14',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_15',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_16',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_17',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_18',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_19',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_20',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'message',                               'length' => 42 ],
            ],
            'errorHostUnavailable' => [
                [ 'name' => 'version_release_number',               'length' => 2 ],
                [ 'name' => 'transaction_code',                     'length' => 2 ],
                [ 'name' => 'response_status_header',               'length' => 1 ],

                [ 'name' => 'field_separator'],

                [ 'name' => 'plan_identification',                   'length' => 8 ],

                [ 'name' => 'group_separator'],

                [ 'name' => 'response_status_prescription',          'length' => 1 ],
                [ 'name' => 'reject_count',                          'length' => 2 ],
                [ 'name' => 'reject_code_01',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_02',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_03',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_04',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_05',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_06',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_07',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_08',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_09',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_10',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_11',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_12',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_13',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_14',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_15',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_16',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_17',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_18',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_19',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_20',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'message',                               'length' => 42 ],

            ]
        ]
    ];

    public $reversal_query_settings = [
        'parts' => [
            [
                [ 'name' => 'bin_number_value',                     'length' => 6,  'type' => 'numeric' ],          //bin_number
                [ 'name' => 'version_release_number',               'length' => 2,  'default' => '3A' ],
                [ 'name' => 'transaction_code',                     'length' => 2,  'default' => '11' ],
                [ 'name' => 'processor_control_number',             'length' => 10, 'default' => 'IDS' ],
                [ 'name' => 'provider_number',                      'length' => 12 ],                             //PHARMACY NUMBER
                [ 'name' => 'date_filled',                          'length' => 8 ],
                [ 'name' => 'prescription_number',                  'length' => 7,  'type' => 'numeric' ],

                [ 'name' => 'field_separator', 'fs_id' => 'G2',     'length' => 3],
                [ 'name' => 'service_type',                         'length' => 2,  'default' => 'X' ],
                [ 'name' => 'field_separator', 'fs_id' => 'C2',     'length' => 3],
                [ 'name' => 'cardholder_id',                        'length' => 18, 'type' => 'numeric' ],
                [ 'name' => 'field_separator', 'fs_id' => 'C3',     'length' => 3],
                [ 'name' => 'person_code',                          'length' => 3,  'type' => 'numeric' ],
            ]
        ]
    ];

    public $reversal_response_settings = [
        'parts' => [
            'success' => [
                [ 'name' => 'version_release_number',               'length' => 2 ],
                [ 'name' => 'transaction_code',                     'length' => 2 ],
                [ 'name' => 'response_status_header',               'length' => 1 ],
                [ 'name' => 'authorization_number',                 'length' => 14 ],
                [ 'name' => 'message',                              'length' => 81 ]
            ],
            'error' => [
                [ 'name' => 'version_release_number',               'length' => 2 ],
                [ 'name' => 'transaction_code',                     'length' => 2 ],
                [ 'name' => 'response_status_header',               'length' => 1 ],

                [ 'name' => 'reject_count',                          'length' => 2 ],
                [ 'name' => 'reject_code_01',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_02',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_03',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_04',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_05',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_06',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_07',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_08',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_09',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'reject_code_10',                        'length' => 2, 'type' => 'reject_code' ],
                [ 'name' => 'message',                               'length' => 73 ],
            ]
        ]
    ];

    public function __construct($transaction_service)
    {
        $this->transaction_service = $transaction_service;
        $this->prescription_item_model = new PrescriptionItem();
    }

    public function sendRequest($data, $prescriptionItem, $card, $type, $adjudicate_request_id = '')
    {
        $remote_socket = 'tcp://'.$this->ip.':'.$this->port;

        $sock = stream_socket_client($remote_socket, $errno, $errstr, $this->timeout);

        fwrite($sock, $data);
        $response = fread($sock, 10000000);
        fclose($sock);
        //log request
        $transactionRequest = new TransactionRequest([
            'ip' => $this->ip,
            'port' => $this->port,
            'socket' => $remote_socket,
            'request' => $data,
            'response' => $response,
            'prescription_item_id' => $prescriptionItem['id'],
            'insurance_card_id' => $card['id'],
            'status' => $this->getRequestStatus($response, $type),
            'errors' => json_encode($this->getRequestErrors($response, $type)),
            'type' => $type,
            'adjudicate_request_id' => $adjudicate_request_id
        ]);

        if ($transactionRequest->save()) {
            return $transactionRequest->getItemData();
        }

        return false;
    }

    public function adjudicateRequest($prescription)
    {
        $response = [];
        foreach ($prescription['prescription_items'] as $prescriptionItemData) {
            $initialCost = CommonService::convertNumber($prescriptionItemData['total']);
            //check if product is RX
            if (!empty($prescriptionItemData['product']['ndc'])) {
                //send request for all cards
                foreach ($this->setNHFCardToTop($prescription['insurance_cards']) as $card) {
                    $transactionRequestResponse = $this->sendRequest(
                        $this->buildAdjudicateQueryString(
                            $prescription,
                            $prescriptionItemData['id'],
                            $card
                        ),
                        $prescriptionItemData,
                        $card,
                        TransactionRequest::TYPE_ADJUDICATE
                    );
                    $response[] = $transactionRequestResponse;

                    if ($transactionRequestResponse['status'] == TransactionRequest::STATUS_SUCCESS) {
                        //get insurance paid
                        $insurance_paid = round((float) $transactionRequestResponse['response_parsed']['total_amount_paid']/100, 2);
                        //get updated prescription item
                        $prescriptionItem = PrescriptionItem::where(['id' => $prescriptionItemData['id']])->first();
                        //update prescription item
                        PrescriptionItem::where('id', $prescriptionItemData['id'])
                            ->update([
                                'insurance' => round(($prescriptionItem->insurance ? $prescriptionItem->insurance : 0) + $insurance_paid, 2),
                                'is_readable' => true
                            ]);
                        //increment from cost
                        $initialCost -= $insurance_paid;
                    }
                    if ($initialCost == 0) {
                        break;
                    }
                }
            }
        }
        return $response;
    }

    public function reversalRequest($transactionId)
    {
        $transactionRequestResponse = [];

        if ($transaction = TransactionRequest::where('id', $transactionId)->first()) {

            $prescriptionItem = PrescriptionItem::where('id', $transaction->prescription_item_id)->first();
            $prescriptionItemData = $prescriptionItem->getItemData();
            $card = $transaction->insurance_card;
            $transactionData = $transaction->getItemData();

            $transactionRequestResponse = $this->sendRequest(
                $this->buildReversalQueryString(
                    $prescriptionItemData,
                    $card->getItemData()
                ),
                $prescriptionItemData,
                $card->getItemData(),
                TransactionRequest::TYPE_REVERSAL,
                $transaction->id
            );

            if ($transactionRequestResponse['status'] == TransactionRequest::STATUS_SUCCESS) {
                //update adjudicate request status
                TransactionRequest::where('id', $transaction->id)->update(['status' => TransactionRequest::STATUS_REVERSED]);
                //get old insurance paid
                $old_insurance_paid = round((float) $transactionData['response_parsed']['total_amount_paid']/100, 2);
                //get updated prescription item
                $prescriptionItemUpdated = PrescriptionItem::where(['id' => $prescriptionItemData['id']])->first();
                //update prescription item
                PrescriptionItem::where('id', $prescriptionItemData['id'])
                    ->update([
                            'insurance' => round(($prescriptionItemUpdated->insurance ? $prescriptionItemUpdated->insurance : 0) - $old_insurance_paid, 2),
                            'is_readable' => false
                        ]);
            }

        }
        return $transactionRequestResponse;
    }

    public function buildAdjudicateQueryString($prescription, $prescriptionItemId, $card)
    {
        $prescriptionItem = PrescriptionItem::where(['id' => $prescriptionItemId])->first();
        $prescriptionItemData = $prescriptionItem->getItemData();

        $card_data = $this->transaction_service->parseCardInformation($card['card_data_string']);
        $query_string = $this->getCompanyNameQueryPart();

        foreach ($this->adjudicate_query_settings['parts'] as $partName=>$part) {

            if (!empty($part)) {

                if ($partName == 'required2') {
                    $query_string .= $this->getQueryGroupSeparator();
                }

                foreach ($part as $item) {

                    if (in_array($partName, ['optional1', 'optional2'])) {
                        $query_string .= $this->getQueryFieldSeparator();
                    }

                    if (isset($item['default'])) {
                        if ($item['default'] == 'current_date') {
                            $value = date('Ymd');
                        } else {
                            $value = $item['default'];
                        }
                    } else {
                        switch ($item['name']) {
                            case 'bin_number_value':
                            case 'provider_number':
                            case 'group_number':
                            case 'subscriber_number':
                            case 'person_code':
                            case 'date_of_birth':
                            case 'sex_code':
                            case 'relationship_code':
                                $value = $card_data[$item['name']];
                                break;

                            case 'prescription_id':
                            case 'qty':
                            case 'course_days':
                            case 'refill_allowed_times':
                                $value = $prescriptionItemData[$item['name']];
                                break;
                            case 'ingredient_cost':
                                $value = (CommonService::convertNumber($prescriptionItemData['total']) - ($prescriptionItemData['insurance'] ? CommonService::convertNumber($prescriptionItemData['insurance']) : 0)) *100;
                                break;

                            case 'ndc':
                                $value = $prescriptionItemData['product'][$item['name']];
                                break;

                            case 'registration_number':
                                $value = $prescription['prescriber'][$item['name']];
                                break;

                            default:
                                $value = '';
                                break;
                        }
                    }
                    $query_string .= $this->increaseLength($value, $item);
                }

            }
        }
        $query_string = $this->addAdditionalData($query_string);
        return $query_string;
    }

    public function buildReversalQueryString($prescriptionItem, $card)
    {
        $card_data = $this->transaction_service->parseCardInformation($card['card_data_string']);
        $query_string = $this->getCompanyNameQueryPart();

        foreach ($this->reversal_query_settings['parts'] as $partName=>$part) {

            if (!empty($part)) {

                foreach ($part as $item) {

                    if (isset($item['default'])) {
                        $value = $item['default'];
                    } else {
                        switch ($item['name']) {
                            case 'bin_number_value':
                            case 'provider_number':
                                $value = $card_data[$item['name']];
                                break;

                            case 'date_filled':
                                $value = date('Ymd', strtotime($prescriptionItem['created_at']));
                                break;
                            case 'prescription_number':
                                $value = $prescriptionItem['prescription_id'];
                                break;

                            case 'cardholder_id':
                                $value = $card['subscriber_number'];
                                break;
                            case 'person_code':
                                $value = $card['person_code'];
                                break;

                            case 'field_separator':
                                $value = $this->getQueryFieldSeparator($item['fs_id']);
                                break;

                            default:
                                $value = '';
                                break;
                        }
                    }
                    $query_string .= $this->increaseLength($value, $item);
                }

            }
        }
        $query_string = $this->addAdditionalData($query_string);
        return $query_string;
    }

    public function addAdditionalData($query_string)
    {
        $query_string .= $this->getQueryEndOfTextSeparator().$this->getQueryEndOfTextSeparator();
        return $query_string;
    }

    public function parseAdjudicateRequest($request)
    {
        $data = [];
        $length = 0;
        $request = mb_substr($request, mb_strlen($this->getCompanyNameQueryPart()));

        foreach ($this->adjudicate_query_settings['parts'] as $partName=>$part) {

            if ($partName == 'required2') {
                $length++;
            }

            if (!empty($part)) {
                foreach ($part as $item) {
                    if (in_array($partName, ['optional1', 'optional2'])) {
                        $length++;
                    }
                    $data[$item['name']] = trim(mb_substr($request, $length, $item['length']));
                    $length += $item['length'];
                }
            }
        }
        return $data;
    }

    public function parseReversalRequest($request)
    {
        $data = [];
        $length = 0;
        $request = mb_substr($request, mb_strlen($this->getCompanyNameQueryPart()));

        foreach ($this->reversal_query_settings['parts'] as $parts) {
            foreach ($parts as $item) {
                if ($item['name'] == 'field_separator') {
                    $length += $item['length'];
                } else {
                    $data[$item['name']] = trim(mb_substr($request, $length, $item['length']));
                    $length += $item['length'];
                }
            }
        }
        return $data;
    }

    public function parseResponse($response, $type)
    {
        if ($type == TransactionRequest::TYPE_ADJUDICATE) {
            return $this->parseAdjudicateResponse($response);
        } else if ($type == TransactionRequest::TYPE_REVERSAL) {
            return $this->parseReversalResponse($response);
        }
    }

    public function parseAdjudicateResponse($response)
    {
        $data = [];
        $length = 0;

        $response = mb_substr($response, mb_strlen($this->getCompanyNameQueryPart()));

        //check undocumented error "Host Unavailable"
        if (mb_strpos($response, 'R0192') !== false) {
            foreach ($this->adjudicate_response_settings['parts']['errorHostUnavailable'] as $item) {
                if ($item['name'] == 'field_separator') {
                    $length += 3;
                } else if ($item['name'] == 'group_separator') {
                    $length += 1;
                } else {
                    $data[$item['name']] = trim(mb_substr($response, $length, $item['length']));
                    $length += $item['length'];
                    if (isset($item['type']) && $item['type'] == 'reject_code') {
                        if ($details = $this->getRejectCodeDetails($data[$item['name']])) {
                            $data[$item['name'].'_details'] = $details;
                        }
                    }
                }
            }
        } else {
            foreach ($this->adjudicate_response_settings['parts']['success'] as $item) {
                if ($item['name'] == 'field_separator') {
                    $length += 3;
                } else if ($item['name'] == 'code_name') {
                    $length += 2;
                } else if ($item['name'] == 'group_separator') {
                    $length += 1;
                } else {
                    if (isset($item['type']) && $item['type'] == 'signed_overpunch') {
                        $data[$item['name']] = $this->parseSignedOverpunch(trim(mb_substr($response, $length, $item['length'])));
                    } else {
                        $data[$item['name']] = trim(mb_substr($response, $length, $item['length']));
                    }
                    $length += $item['length'];
                }
            }

            if ($data['response_status_prescription'] == 'R') {
                $data = [];
                $length = 0;
                foreach ($this->adjudicate_response_settings['parts']['error'] as $item) {
                    if ($item['name'] == 'field_separator') {
                        $length += 3;
                    } else if ($item['name'] == 'group_separator') {
                        $length += 1;
                    } else {
                        $data[$item['name']] = trim(mb_substr($response, $length, $item['length']));
                        $length += $item['length'];
                        if (isset($item['type']) && $item['type'] == 'reject_code') {
                            if ($details = $this->getRejectCodeDetails($data[$item['name']])) {
                                $data[$item['name'].'_details'] = $details;
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    public function parseReversalResponse($response)
    {
        $data = [];
        $length = 0;

        $response = mb_substr($response, mb_strlen($this->getCompanyNameQueryPart()));


        foreach ($this->reversal_response_settings['parts']['success'] as $item) {
            $data[$item['name']] = trim(mb_substr($response, $length, $item['length']));
            $length += $item['length'];
        }


        if ($data['response_status_header'] == 'R') {
            $data = [];
            $length = 0;
            foreach ($this->reversal_response_settings['parts']['error'] as $item) {
                $data[$item['name']] = trim(mb_substr($response, $length, $item['length']));
                $length += $item['length'];
                if (isset($item['type']) && $item['type'] == 'reject_code') {
                    if ($details = $this->getRejectCodeDetails($data[$item['name']])) {
                        $data[$item['name'].'_details'] = $details;
                    }
                }
            }
        }

        return $data;
    }

    public function increaseLength($value, $item)
    {
        $value = (string) $value;
        if (isset($item['length']) && mb_strlen($value) < $item['length']) {
            do {
                //add 0 or empty to value
                if (isset($item['type']) && $item['type'] == 'numeric') {
                    $value = '0'.$value;
                } else {
                    $value .= ' ';
                }
            } while (mb_strlen($value) < $item['length']);
        }
        return $value;
    }

    public function getQueryFieldSeparator($fs_id = false)
    {
        return  chr(28). ($fs_id ? $fs_id : '');
    }

    public function getQueryGroupSeparator()
    {
        return chr(29);
    }

    public function getQueryEndOfTextSeparator()
    {
        return chr(3);
    }

    public function getRejectCodeDetails($code) {
        if ($code == '00') {
            return false;
        } elseif (isset($this->reject_codes[$code])) {
            return $this->reject_codes[$code]['description'].' <br />'. $this->reject_codes[$code]['explanation'];
        }
        return 'Unknown error code';
    }

    public function parseSignedOverpunch($value)
    {
        $signedArray = [
            [ 'initial' => '}', 'value' => '0', 'symbol' => '1' ],
            [ 'initial' => 'J', 'value' => '1', 'symbol' => '-' ],
            [ 'initial' => 'K', 'value' => '2', 'symbol' => '-' ],
            [ 'initial' => 'L', 'value' => '3', 'symbol' => '-' ],
            [ 'initial' => 'M', 'value' => '4', 'symbol' => '-' ],
            [ 'initial' => 'N', 'value' => '5', 'symbol' => '-' ],
            [ 'initial' => 'O', 'value' => '6', 'symbol' => '-' ],
            [ 'initial' => 'P', 'value' => '7', 'symbol' => '-' ],
            [ 'initial' => 'Q', 'value' => '8', 'symbol' => '-' ],
            [ 'initial' => 'R', 'value' => '9', 'symbol' => '-' ],
            [ 'initial' => '{', 'value' => '0', 'symbol' => '+' ],
            [ 'initial' => 'A', 'value' => '1', 'symbol' => '+' ],
            [ 'initial' => 'B', 'value' => '2', 'symbol' => '+' ],
            [ 'initial' => 'C', 'value' => '3', 'symbol' => '+' ],
            [ 'initial' => 'D', 'value' => '4', 'symbol' => '+' ],
            [ 'initial' => 'E', 'value' => '5', 'symbol' => '+' ],
            [ 'initial' => 'F', 'value' => '6', 'symbol' => '+' ],
            [ 'initial' => 'G', 'value' => '7', 'symbol' => '+' ],
            [ 'initial' => 'H', 'value' => '8', 'symbol' => '+' ],
            [ 'initial' => 'I', 'value' => '9', 'symbol' => '+' ]
        ];
        $last_character = mb_substr($value, -1);
        foreach ($signedArray as $signed) {
            if ($signed['initial'] == $last_character) {
                $value = (int) $signed['symbol'] . mb_substr($value, 0 , -1) . $signed['value'];
                break;
            }
        }
        return $value;
    }

    public function getRequestStatus($response, $type)
    {
        $parsedResponse = $this->parseResponse($response, $type);
        $status = TransactionRequest::STATUS_ERROR;

        if ($type == TransactionRequest::TYPE_ADJUDICATE) {
            if (isset($parsedResponse['response_status_prescription']) && $parsedResponse['response_status_prescription'] == 'P') {
                $status = TransactionRequest::STATUS_SUCCESS;
            } elseif (isset($parsedResponse['response_status_prescription']) && $parsedResponse['response_status_prescription'] == 'D') {
                $status = TransactionRequest::STATUS_DUPLICATE;
            }
        } else if ($type == TransactionRequest::TYPE_REVERSAL) {
            if (isset($parsedResponse['response_status_header']) && $parsedResponse['response_status_header'] == 'A') {
                $status = TransactionRequest::STATUS_SUCCESS;
            } elseif (isset($parsedResponse['response_status_header']) && $parsedResponse['response_status_header'] == 'D') {
                $status = TransactionRequest::STATUS_DUPLICATE;
            }
        }

        return $status;
    }

    public function getRequestErrors($response, $type)
    {
        $parsedResponse = $this->parseResponse($response, $type);
        $errors = [];
        if ($this->getRequestStatus($response, $type) == TransactionRequest::STATUS_ERROR) {
            if (isset($parsedResponse['reject_count'])) {
                for ($i = 1; $i <= (int) $parsedResponse['reject_count']; $i++) {
                    $increased_key = $this->increaseLength(
                        $i,
                        [ 'length' => 2, 'type' => 'numeric' ]
                    );
                    if (isset($parsedResponse['reject_code_'.$increased_key])) {
                        $errors[] = [
                            'code' => $parsedResponse['reject_code_'.$increased_key],
                            'error' => $parsedResponse['reject_code_'.$increased_key.'_details']
                        ];
                    }
                }
            }
        }
        return $errors;
    }

    public function setNHFCardToTop($cards)
    {
        usort($cards, function ($a, $b) {
            if ($a['bin_number'] == $b['bin_number'])
                return 0;
            if ($a['bin_number'] == 'NHF' && $b['bin_number'] != 'NHF')
                return -1;
            if ($a['bin_number'] != 'NHF' && $b['bin_number'] == 'NHF')
                return 1;
        });
        return $cards;
    }

    public function getCompanyNameQueryPart()
    {
        if ($option = Settings::where('name', 'company_name')->first()) {
            $company_name = $option->value;
        } else {
            $company_name = $this->default_additional_query_part;
        }
        return $this->increaseLength($company_name, ['length' => 20]);
    }

}
