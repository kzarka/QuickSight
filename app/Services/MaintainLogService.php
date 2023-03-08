<?php

namespace  App\Services;

use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\Carbon;

class MaintainLogService {

    public $amount = 100;
    /**
     * @throws WriterNotOpenedException
     * @throws IOException
     * @throws InvalidArgumentException
     */
    public function write()
    {
        $filePath = public_path() . '/dump.csv';
        $writer = WriterEntityFactory::createCSVWriter();

        $writer->openToFile($filePath); // write data to a file or to a PHP stream

        /** Shortcut: add a row from an array of values */
        $header = WriterEntityFactory::createRowFromArray($this->header());
        $writer->addRow($header);

        $fromDate = Carbon::createFromFormat('d/m/Y H:i:s',  '02/01/2023 00:00:00');
        $toDate = Carbon::createFromFormat('d/m/Y H:i:s',  '01/04/2023 00:00:00');
        while ($fromDate < $toDate) {
            $data = $this->buildData($fromDate->toDateString());
            $row = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($row);
            $fromDate->addDay();
        }

        $writer->close();
    }

    public function buildData($date)
    {
        $pics = ['Aさん', 'Bさん', 'Cさん'];
        $decreases = [1, 1.5, 2];
        $this->amount = $this->amount - $decreases[rand(0, 2)];
        if ($this->amount < 2.5) {
            $this->amount = 100;
        }

        return [
            $date,
            $pics[rand(0, 2)],
            20,
            $this->amount,
        ];
    }

    public function header()
    {
        return [
            'id', 'pic', 'threshold', 'value'
        ];
    }
}
