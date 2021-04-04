<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class CsvResponse extends Response
{
    protected string $data;

    protected string $filename = 'export.csv';

    public function __construct($data = [], $status = 200, $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->setData($data);
    }

    public function setData(array $data): CsvResponse
    {
        $output = fopen('php://temp', 'rb+');

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $this->data = '';
        while ($line = fgets($output)) {
            $this->data .= $line;
        }

        $this->data .= fgets($output);

        return $this->update();
    }

    protected function update(): CsvResponse
    {
        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/csv');
        }

        return $this->setContent($this->data);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename($filename): CsvResponse
    {
        $this->filename = $filename;

        return $this->update();
    }
}