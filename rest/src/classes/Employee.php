<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/rest/src/classes/EmployeeFields.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/rest/src/classes/EmployeeProps.php");

class Employee
{
    protected $employeeFields;
    protected $employeeProps;
    private $activity = 'Y'; // Активность пользователя. По умолчанию активен.
	

    public function __construct(EmployeeFields $employeeFields, EmployeeProps $employeeProps)
    {
        $this->employeeFields = $employeeFields;
        $this->employeeProps= $employeeProps;
        $this->sectionId   = $this->employeeFields->getSectionId();
		$this->sectionName = $this->employeeFields->getSectionName();
		$this->rootParent  = $this->getRootParent($sectionId);
		$this->name 	   = $this->employeeFields->getEmployeeName();
		$this->props       = $this->employeeProps->getEmployeeProps();
    }

    // Определяем корневого родителя элемента по id родителя
    public function getRootParent($sectionId) {
		do {
			$sectionSectionId = CIBlockSection::GetByID($sectionId)->Fetch()["IBLOCK_SECTION_ID"];
			$sectionId = $sectionSectionId;
			if($sectionSectionId != NULL) {
				$result[] = $sectionSectionId;
			}
			
		} while ($sectionSectionId != NULL);
		if(is_countable($result)) {
			$sectionName = CIBlockSection::GetByID($result[count($result)-1])->Fetch();
			return $sectionName['NAME'];
		}
	}

    // Определяем, существует ли сотрудник в справочнике по свойству GUID_ZUP
    public function isExists(): array | bool
    {
        $arFilter = Array(
            "IBLOCK_ID" => 5,
            "ACTIVE_DATE"=>"Y",
            "PROPERTY_GUID_ZUP" => $this->props['GUID_ZUP']
        );
        $arSelect = array();
        return CIBlockElement::GetList(Array(), $arFilter, false, "", $arSelect)->fetch();
    }

    public function update(array $res, $userId)
    {
        // Если у сотрудника стоит дата увольнения и при этом он активен, сделать не активным
		if((!empty($this->props['DISMISS_DATE']) && $res['ACTIVE'] == 'Y') || (empty($this->props['DISMISS_DATE']) && $res['ACTIVE'] == 'N')) {
			$activity = 'N';
		}

		// Изменяем свойства сотрудника
		$employeeArr = array(
			"MODIFIED_BY"    => $userId, // элемент изменен текущим пользователем
			"PROPERTY_VALUES"=> $this->props,
			"NAME"           => $this->name,
			"ACTIVE"         => $this->activity,
			"IBLOCK_SECTION_ID" => $this->sectionId,
		  );
	  
		  $el = new CIBlockElement;
	  
		  if($employee_id = $el->Update($res['ID'], $employeeArr)) {
			  header("HTTP/1.1 200");
			  echo 'Cотрудник '.$res['NAME'].' с id '. $res['ID'].' обновлен!';
		  }
		  else {
			  echo "Error: ".$el->LAST_ERROR;
		  }
    }

    public function create($userId)
    {
        // Добавляем нового сотрудника
		$employeeArr = array(
			"MODIFIED_BY"    => $userId, // элемент изменен текущим пользователем
			"IBLOCK_SECTION_ID" => $this->sectionId,
			"IBLOCK_ID"      => 5,
			"PROPERTY_VALUES"=> $this->props,
			"NAME"           => $this->name,
			"ACTIVE"         => 'N',            // не активен
		  );

		  //var_dump($employeeArr); die();
	  
		  $el = new CIBlockElement;
	  
		  if($employee_id = $el->Add($employeeArr)) {
			  header("HTTP/1.1 201");
			  echo 'Новый сотрудник добавлен с id'. $employee_id;
		  }
		  else {
			  echo "Error: ".$el->LAST_ERROR;
		  }
    }
}
