<?php


namespace Wefabric\Address\Excel;


use Wefabric\Excel\AbstractExcel;

class Address extends AbstractExcel
{
    public static array $headings = [
        'straat',
        'huisnummer',
        'huisnummer toevoeging',
        'postcode',
        'stad',
        'gemeente',
        'provincie',
        'land',
        'lengtegraad',
        'longitude'
    ];

    public function toExcelData(): array
    {
        return [
            'straat' => $this->model->street,
            'huisnummer' => (int)$this->model->housenumber,
            'huisnummer toevoeging' => $this->model->housenumber_addition,
            'postcode' => $this->model->postcode,
            'stad' => $this->model->city,
            'gemeente' => $this->model->municipality,
            'provincie' => $this->model->province,
            'land' => $this->model->city,
            'lengtegraad' => $this->model->latitude,
            'breedtegraad' => $this->model->latitude
        ];
    }
}
