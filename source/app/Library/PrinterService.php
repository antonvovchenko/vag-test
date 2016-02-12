<?php

/*
 *
 * Printer Service Class
 *
 * Usage:
 *
 *  $printerService = new PrinterService();
 *  $printerService->selectPrinter("Samsung SCX-3200 Series");
 *  $printerService->setDocumentTitle("Test Document Title");
 *  $printerService->addContent("Test Document Content");
 *  $printerService->printContent();
 *
 *
 */

namespace App\Library;

class PrinterService
{
    public $ignore_printers = [
        'Fax',
        'Print to Evernote',
        'Microsoft XPS Document Writer',
        'Microsoft Office Document Image Writer'
    ];

    public $printers;
    private $selected_printer;

    private $document_copies_count = 1;
    private $document_orientation = 'Portrait'; //or Landscape
    private $document_title = '';

    private $document_content = "\r\n ";
    private $handle;


    public function __construct()
    {
        //Grab printer list
        $this->getPrinters();
    }

    /**
     * Show Printers Found Connected to this PC/server (except ignore list)
     *
     * @return array
     */
    public function getPrinters()
    {
        if ($this->printers === null) {

            $this->printers = [];

            if (function_exists('printer_list')) {
                $printer_list = printer_list(PRINTER_ENUM_LOCAL);
                foreach ($printer_list as $printer) {
                    if (in_array($printer['NAME'], $this->ignore_printers) == FALSE) {
                        $this->printers[] = $printer;
                    }
                }
            }
        }
        return $this->printers;
    }

    /**
     * Select a Printer to Print to
     *
     * @param string $printer_name Name of Printer
     * @return string
     */
    public function selectPrinter($printer_name = '')
    {
        if ($printer_name == '') {
            return false;
        }

        /* Scan the Printers array, if the printer asked for is found, then select it, reset values to default and return TRUE */
        foreach ($this->printers as $printer) {
            if ($printer['NAME'] == $printer_name) {
                $this->selected_printer = $printer_name;
            }
        }
        return $this->selected_printer;
    }

    /**
     * Set copies count
     *
     * @param integer $copies Copy Count
     * @return integer
     */
    public function setCopiesCount($copies = 0)
    {
        if ($copies != 0) {
            $this->document_copies_count = $copies;
        }
        return $this->document_copies_count;
    }

    /**
     * Set Document Page Orientation
     *
     * @param string $orientation Page Orientation
     * @return string
     */
    public function setOrientation($orientation = '')
    {
        /* If it is not blank, set the orientation */
        if ($orientation != '') {
            $this->document_orientation = ucwords(strtolower($orientation));
        }
        return $this->document_orientation;
    }

    /**
     * Set Document Title
     *
     * @param string $title Page title
     * @return string
     */
    public function setDocumentTitle($title = '')
    {
        if ($title != '') {
            $this->document_title = $title;
        }

        /* Return the current value, changed or not */
        return $this->document_title;
    }

    /**
     * Add content
     *
     * @param string $string Data to save in buffer
     * @return boolean
     */
    public function addContent($string)
    {
        if ($string == '') {
            return false;
        }

        //Replace <br /> to CRLF
        $string = str_replace(array('<br>', '<br />'), "\r\n ", $string);

        $this->document_content = $string;
        return true;
    }

    /**
     * Print out
     *
     * @return boolean
     */
    public function printContent()
    {
        $connect = $this->_connect();
        if ($connect != false) {
            $this->_writeContent();
            $this->_closeConnection();
            return true;
        }

        return false;
    }

    /**
     * Open Printer Connection and set preferences
     *
     * @return boolean|resource
     */
    private function _connect()
    {
        //Check if the printer is already open, if it is, return the handle
        if ($this->handle !== null) {
            return $this->handle;
        }

        // Open the Printer Connection
        $this->handle = printer_open($this->selected_printer);
        if ($this->handle == false) {
            return false;
        }

        // Set Copies
        $this->setOption(PRINTER_COPIES, $this->document_copies_count);

        // Set Orientation
        if ($this->document_orientation == 'Landscape') {
            $this->setOption(PRINTER_ORIENTATION, PRINTER_ORIENTATION_LANDSCAPE);
        } else {
            $this->setOption(PRINTER_ORIENTATION, PRINTER_ORIENTATION_PORTRAIT);
        }

        // Set Title
        if ($this->document_title != '') {
            $this->setOption(PRINTER_TITLE, $this->document_title);
        }

        return $this->handle;
    }

    /**
     * Set printer option, printer must be open for this to work
     *
     * @param integer $option Option to Set
     * @param integer $value Value to give
     * @return boolean
     */
    public function setOption($option, $value)
    {
        return printer_set_option($this->handle, $option, $value);
    }

    /**
     * Write contents to Printer
     *
     * @return void
     */
    private function _writeContent()
    {
        printer_write($this->handle, $this->document_content);
        return;
    }

    /**
     * Close printer connection
     *
     * @return boolean
     */
    private function _closeConnection()
    {
        printer_close($this->handle);
        $this->handle = false;
        return true;
    }


}