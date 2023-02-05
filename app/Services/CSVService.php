<?php

namespace  App\Services;

use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\Carbon;

class CSVService {
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
        $values = ['Carl', 'is', 'great!'];
        $header = WriterEntityFactory::createRowFromArray($this->header());
        $writer->addRow($header);

        $fromDate = Carbon::createFromFormat('d/m/Y H:i:s',  '01/01/2022 00:00:00');
        $toDate = Carbon::createFromFormat('d/m/Y H:i:s',  '01/01/2023 00:00:00');
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
        $failure = rand(0, 25);
        $first = 0;
        $reoccurring = 0;

        if ($failure) {
            $reoccurring = rand(0, $failure);
            $first = $failure - $reoccurring;
        }

        $downtime = 0;
        if ($failure) {
            $downtime = rand(100, 500);
        }

        return [
            $date,
            $failure,
            $first,
            $reoccurring,
            $downtime
        ];
    }

    public function header()
    {
        return [
            'date',
            'total_error',
            'first_time',
            'reoccurring',
            'downtime'
        ];
    }
}
