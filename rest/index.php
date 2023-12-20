<?

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/rest/src/classes/Employee.php");

if(CModule::IncludeModule("iblock")):
	// Принимаем и очищаем данные сотрудника из запроса
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$employeeFields = new EmployeeFields($_POST['sectionId'], $_POST['name']);
		$employeeProps = new EmployeeProps(
			$employeeFields,
			array(
			'BIRTH_DAY' => $_POST['birthDay'],
			'POSITION' => $_POST['position'], 
			'DEPARTMENT' => "",
			'GUID_ZUP' => $_POST['guidZup'], 
			'DISMISS_DATE' => $_POST['dismissDate']
			)
		);
		$employee = new Employee($employeeFields, $employeeProps);
	} else {
		die('Нет доступа');
	}
	
	if(!empty($res = $employee->isExists())) {
		$employee->update($res, $USER->GetID());
	} else {
		$employee->create($USER->GetID());
	}
endif;


?>

