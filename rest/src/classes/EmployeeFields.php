<?php

class EmployeeFields
{
    private $sectionId;  // id раздела в который будет помещаться элемент инфоблока
    private $name = '';  // Имя элемента инфоблока

    public function __construct(int | NULL $sectionId, string $name)
    {
        $this->sectionId = $sectionId == NULL ? 2934 : $sectionId;
        $this->name = $name;
    }

    public function getSectionId()
    {
        return htmlentities(trim($this->sectionId));
    }
    
    public function getSectionName()
    {
        return CIBlockSection::GetByID($this->sectionId)->Fetch()['NAME'];
    }

    public function getEmployeeName()
    {
        return htmlentities(trim($this->name));
    } 
   
}

?>
