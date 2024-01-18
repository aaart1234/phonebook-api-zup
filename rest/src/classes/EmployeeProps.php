<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/rest/src/classes/EmployeeFields.php");

class EmployeeProps
{
    protected $props = []; // Массив свойств элемента инфоблока
    protected $employeeFields = '';

    public function __construct(EmployeeFields $employeeFields, array $props)
    {
        $this->props = $props;
        $this->employeeFields = $employeeFields;
    }

    private function cleanProperties(array $props): array
    {
        foreach($props as $property => $value)
        {
            $this->props[$property] = htmlentities(trim($value));
        }
        
        $this->props['GUID_ZUP'] = !null == $this->props['GUID_ZUP'] && !empty($this->props['GUID_ZUP']) ? $this->props['GUID_ZUP'] : die('Поле GUID ЗУП должно быть заполнено!');

        return $this->props;
    }

    public function getEmployeeProps()
    {
        $this->props = $this->cleanProperties($this->props);
        return $this->props;
    }
   
}


?>
