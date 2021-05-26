<?php


namespace Wefabric\Address\Action;


class SplitHousenumberAndAdditionAction
{
    /**
     * @param string $housenumberAndAddition
     * @return array
     */
    public function execute(string $housenumberAndAddition): array
    {
        if(is_numeric($housenumberAndAddition)) {
            return [
                'housenumber' => (int)$housenumberAndAddition,
                'housenumber_addition' => ''
            ];
        }

        $housenumberAndAddition = str_replace(' ', '', $housenumberAndAddition);
        preg_match_all('/(\d*)(.)/m', $housenumberAndAddition, $matches, PREG_SET_ORDER, 0);

        $housenumber = (int)$housenumberAndAddition;
        if(isset($matches[0], $matches[0][1])) {
            $housenumber = (int)$matches[0][1];
        }

        $housenumberAddition = '';
        if(isset($matches[0], $matches[0][2])) {
            $housenumberAddition = $matches[0][2];
        }

        return [
            'housenumber' => $housenumber,
            'housenumber_addition' => $housenumberAddition
        ];
    }
}
